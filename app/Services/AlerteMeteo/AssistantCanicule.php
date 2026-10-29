<?php

namespace App\Services\AlerteMeteo;

use App\Models\AlerteMeteo;
use App\Models\NiveauVigilance;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Assistant canicule : répond aux questions des habitants à partir des données réelles
 * (alertes publiées, prévisions Open-Meteo, coupures de courant).
 *
 * 1. analyser()  : comprend la question (zone, date, intention) par traitement du langage simple.
 * 2. contexte()  : rassemble les données utiles en base et via l'API météo.
 * 3. repondre()  : rédige la réponse avec l'IA Gemini si une clé est configurée,
 *                  sinon (ou en cas d'échec) avec le moteur de réponse local.
 */
class AssistantCanicule
{
    /** Mots-clés (sans accents, en minuscules) de chaque intention. */
    private const INTENTIONS = [
        'sport' => ['sport', 'courir', 'course', 'footing', 'jogging', 'velo', 'foot', 'match', 'marche', 'salle', 'entrainement', 'nager', 'natation'],
        'sortie' => ['sortir', 'sortie', 'dehors', 'plage', 'promen', 'balade', 'marche', 'voyage', 'route', 'conduire'],
        'enfants' => ['enfant', 'bebe', 'ecole', 'petit', 'fils', 'fille'],
        'ages' => ['age', 'vieux', 'vieille', 'grand-mere', 'grand-pere', 'grand mere', 'grand pere', 'senior', 'parents', 'malade'],
        'hydratation' => ['eau', 'boire', 'hydrat', 'soif'],
        'coupure' => ['coupure', 'electricite', 'courant', 'delestage', 'frigo', 'refrigerateur', 'clim', 'climatiseur', 'panne'],
        'fraicheur' => ['frais', 'fraicheur', 'ou aller', 'refuge', 'climatise', 'parc', 'fontaine', 'piscine'],
    ];

    private const JOURS = ['lundi' => 1, 'mardi' => 2, 'mercredi' => 3, 'jeudi' => 4, 'vendredi' => 5, 'samedi' => 6, 'dimanche' => 0];

    public function __construct(private PrevisionChaleurService $meteo)
    {
    }

    /**
     * Point d'entrée : renvoie ['reponse' => string, 'source' => 'ia'|'local', 'liens' => [...]].
     */
    public function repondre(string $question): array
    {
        $analyse = $this->analyser($question);
        $contexte = $this->contexte($analyse);

        $reponse = $this->repondreAvecGemini($question, $analyse, $contexte);
        $source = 'ia';

        if ($reponse === null) {
            $reponse = $this->repondreLocalement($analyse, $contexte);
            $source = 'local';
        }

        return ['reponse' => $reponse, 'source' => $source, 'liens' => $this->liens($analyse, $contexte)];
    }

    // ─── 1. Compréhension de la question ──────────────────────────────────

    /**
     * Extrait de la question : les zones citées, la période visée et les intentions.
     */
    public function analyser(string $question): array
    {
        $texte = ' '.Str::lower(Str::ascii($question)).' ';

        // Zones : nom de la zone ou de son gouvernorat présent dans la question
        $zones = Zone::all()->filter(fn (Zone $z) => Str::contains($texte, Str::lower(Str::ascii($z->nom))));
        if ($zones->isEmpty()) {
            $zones = Zone::all()->filter(fn (Zone $z) => Str::contains($texte, ' '.Str::lower(Str::ascii($z->gouvernorat))));
        }

        [$debut, $fin, $libelle] = $this->periode($texte);

        $intentions = collect(self::INTENTIONS)
            ->filter(fn ($mots) => collect($mots)->contains(fn ($mot) => Str::contains($texte, $mot)))
            ->keys()->all();

        return ['zones' => $zones->values(), 'debut' => $debut, 'fin' => $fin, 'periode' => $libelle, 'intentions' => $intentions];
    }

    /** Période visée : aujourd'hui (par défaut), demain, après-demain, un jour de la semaine, le week-end ou la semaine. */
    private function periode(string $texte): array
    {
        $aujourdhui = today();

        if (Str::contains($texte, ['apres-demain', 'apres demain'])) {
            $jour = $aujourdhui->copy()->addDays(2);

            return [$jour, $jour->copy()->endOfDay(), 'après-demain ('.$jour->translatedFormat('l j F').')'];
        }
        if (Str::contains($texte, 'demain')) {
            $jour = $aujourdhui->copy()->addDay();

            return [$jour, $jour->copy()->endOfDay(), 'demain ('.$jour->translatedFormat('l j F').')'];
        }
        if (Str::contains($texte, ['week-end', 'weekend', 'week end'])) {
            $samedi = $aujourdhui->isWeekend() ? $aujourdhui->copy() : $aujourdhui->copy()->next(Carbon::SATURDAY);

            return [$samedi, $samedi->copy()->next(Carbon::SUNDAY)->endOfDay(), 'ce week-end'];
        }
        if (Str::contains($texte, ['semaine', '7 jours', 'prochains jours'])) {
            return [$aujourdhui, $aujourdhui->copy()->addDays(6)->endOfDay(), 'cette semaine'];
        }
        foreach (self::JOURS as $nom => $numero) {
            if (Str::contains($texte, ' '.$nom)) {
                $jour = $aujourdhui->dayOfWeek === $numero ? $aujourdhui->copy() : $aujourdhui->copy()->next($numero);

                return [$jour, $jour->copy()->endOfDay(), $jour->translatedFormat('l j F')];
            }
        }

        return [$aujourdhui, $aujourdhui->copy()->endOfDay(), "aujourd'hui"];
    }

    // ─── 2. Données utiles ───────────────────────────────────────────────

    /**
     * Alertes publiées sur la période (et les zones citées), prévision météo de la zone,
     * coupures de courant (module Coupures) et alerte la plus grave.
     */
    public function contexte(array $analyse): array
    {
        $alertes = AlerteMeteo::with(['niveauVigilance', 'zone'])
            ->where('date_debut', '<=', $analyse['fin'])
            ->where('date_fin', '>=', $analyse['debut'])
            ->when($analyse['zones']->isNotEmpty(), fn ($q) => $q->whereIn('zone_id', $analyse['zones']->pluck('id')))
            ->get()
            ->sortByDesc(fn ($a) => [$a->niveauVigilance?->ordre, $a->temperature_max])
            ->values();

        // Prévision Open-Meteo pour la première zone citée (si l'API répond)
        $prevision = null;
        if ($zone = $analyse['zones']->first()) {
            try {
                $prevision = $this->meteo->previsions($zone)
                    ->filter(fn ($j) => $j['date']->between($analyse['debut']->copy()->startOfDay(), $analyse['fin']))
                    ->sortByDesc('temperature')->first();
            } catch (\Throwable) {
                // Pas de prévision : l'assistant répond avec les alertes seules
            }
        }

        // Coupures de courant en cours ou prévues (module Coupures de l'équipe), si disponible
        $coupures = collect();
        if (class_exists(\App\Models\Coupure::class) && $analyse['zones']->isNotEmpty()) {
            try {
                $coupures = \App\Models\Coupure::query()->actives()
                    ->whereIn('zone_id', $analyse['zones']->pluck('id'))->with('zone')->limit(3)->get();
            } catch (\Throwable) {
                $coupures = collect();
            }
        }

        // Niveau retenu pour les conseils : le plus grave entre l'alerte publiée et la prévision
        $niveau = collect([$alertes->first()?->niveauVigilance, $prevision['niveau'] ?? null])
            ->filter()->sortByDesc('ordre')->first()
            ?? NiveauVigilance::orderBy('ordre')->first();

        return ['alertes' => $alertes, 'prevision' => $prevision, 'coupures' => $coupures, 'niveau' => $niveau];
    }

    // ─── 3a. Réponse rédigée par l'IA Gemini ──────────────────────────────

    /** Renvoie la réponse de Gemini, ou null si pas de clé ou en cas d'erreur. */
    private function repondreAvecGemini(string $question, array $analyse, array $contexte): ?string
    {
        $cle = config('alertes-meteo.assistant.gemini_key');
        if (! $cle) {
            return null;
        }

        $consignes = "Tu es l'assistant canicule de HeatAlert, une application tunisienne d'alertes chaleur. "
            ."Réponds en français, en 3 à 6 phrases claires, avec un ton bienveillant. "
            ."Base-toi UNIQUEMENT sur les données fournies (alertes, prévision, coupures) ; n'invente aucune alerte ni température. "
            ."Donne un conseil concret adapté à la question et au niveau de vigilance. "
            ."S'il n'y a aucune alerte, dis-le et donne des conseils de prudence généraux. "
            ."En cas de malaise grave, rappelle d'appeler le SAMU (190). Tu peux mettre en gras avec **texte**.";

        try {
            $modele = config('alertes-meteo.assistant.gemini_model');
            $reponse = Http::timeout(config('alertes-meteo.assistant.timeout'))
                ->withHeaders(['x-goog-api-key' => $cle])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$modele}:generateContent", [
                    'systemInstruction' => ['parts' => [['text' => $consignes]]],
                    'contents' => [['role' => 'user', 'parts' => [['text' => $this->donneesPourIa($question, $analyse, $contexte)]]]],
                    'generationConfig' => ['temperature' => 0.4, 'maxOutputTokens' => 600],
                ]);

            $texte = $reponse->successful() ? trim((string) $reponse->json('candidates.0.content.parts.0.text')) : '';
            if ($texte === '') {
                Log::warning('Assistant canicule : réponse Gemini vide ou en erreur', ['statut' => $reponse->status()]);

                return null;
            }

            return $texte;
        } catch (\Throwable $e) {
            Log::warning('Assistant canicule : Gemini injoignable', ['erreur' => $e->getMessage()]);

            return null;
        }
    }

    /** Les données réelles, résumées en texte, envoyées à l'IA avec la question. */
    private function donneesPourIa(string $question, array $analyse, array $contexte): string
    {
        $lignes = ['Date du jour : '.today()->translatedFormat('l j F Y'), 'Question de l’habitant : '.$question,
            'Zones reconnues : '.($analyse['zones']->pluck('nom')->join(', ') ?: 'aucune (toutes les zones)'),
            'Période visée : '.$analyse['periode'], '', 'Alertes publiées sur cette période :'];

        foreach ($contexte['alertes']->take(8) as $a) {
            $lignes[] = "- {$a->zone->nom} : vigilance {$a->niveauVigilance->nom}, {$a->temperature_max} °C, du "
                .$a->date_debut->format('d/m H\hi').' au '.$a->date_fin->format('d/m H\hi')." — {$a->titre}. Consigne : {$a->niveauVigilance->consigne}";
        }
        if ($contexte['alertes']->isEmpty()) {
            $lignes[] = '- aucune alerte publiée';
        }
        if ($p = $contexte['prevision']) {
            $lignes[] = '';
            $lignes[] = 'Prévision Open-Meteo : '.$p['temperature'].' °C le '.$p['date']->translatedFormat('l j F')
                .($p['ressentie'] !== null ? ' (ressentie '.$p['ressentie'].' °C)' : '').', niveau '.($p['niveau']?->nom ?? 'inconnu');
        }
        foreach ($contexte['coupures'] as $c) {
            $lignes[] = "Coupure de courant : {$c->titre} ({$c->zone->nom}), début ".$c->date_debut->format('d/m H\hi');
        }

        return implode("\n", $lignes);
    }

    // ─── 3b. Réponse rédigée par le moteur local ──────────────────────────

    private function repondreLocalement(array $analyse, array $contexte): string
    {
        $alertes = $contexte['alertes'];
        $niveau = $contexte['niveau'];
        $ordre = $niveau?->ordre ?? 1;
        $lieu = $analyse['zones']->isNotEmpty() ? $analyse['zones']->pluck('nom')->join(', ') : 'votre région';
        $phrases = [];

        // Situation
        if ($alertes->isNotEmpty()) {
            $a = $alertes->first();
            $phrases[] = "Pour **{$lieu}**, {$analyse['periode']} : vigilance **{$a->niveauVigilance->nom}** "
                ."avec jusqu'à **{$a->temperature_max} °C** (« {$a->titre} »"
                .($analyse['zones']->isEmpty() ? ", zone {$a->zone->nom}" : '').').';
            if ($alertes->count() > 1) {
                $phrases[] = $alertes->count().' alertes sont publiées sur cette période.';
            }
        } else {
            $phrases[] = "Aucune alerte chaleur n'est publiée pour **{$lieu}** {$analyse['periode']}.";
        }

        if ($p = $contexte['prevision']) {
            $phrases[] = 'Les prévisions annoncent **'.$p['temperature'].' °C**'
                .($p['ressentie'] !== null ? ' (ressentie '.$p['ressentie'].' °C)' : '')
                .', ce qui correspond au niveau '.($p['niveau']?->nom ?? '—').'.';
        }

        // Conseil selon l'intention et la gravité
        $intentions = $analyse['intentions'] ?: ['general'];
        foreach (array_slice($intentions, 0, 2) as $intention) {
            $phrases[] = $this->conseil($intention, $ordre);
        }

        if ($niveau && $ordre >= 2) {
            $phrases[] = 'Consigne officielle ('.$niveau->nom.') : '.$niveau->consigne;
        }

        foreach ($contexte['coupures'] as $c) {
            $phrases[] = "⚡ Attention : coupure de courant signalée à {$c->zone->nom} (« {$c->titre} »).";
        }

        if ($analyse['zones']->isEmpty()) {
            $phrases[] = 'Astuce : précisez votre quartier (ex. « La Marsa », « Ariana ») pour une réponse plus précise.';
        }

        return implode("\n\n", $phrases);
    }

    /** Conseil adapté à l'intention et au niveau de gravité (1 = Vert … 4 = Rouge). */
    private function conseil(string $intention, int $ordre): string
    {
        return match ($intention) {
            'sport' => match (true) {
                $ordre >= 4 => '🏃 **Sport déconseillé** : évitez toute activité physique en extérieur. Si besoin, faites une séance légère en intérieur au frais.',
                $ordre === 3 => '🏃 **Sport à éviter** aux heures chaudes. Si vous y tenez : avant 8h ou après 20h, à intensité réduite, avec de l’eau toutes les 15 minutes.',
                $ordre === 2 => '🏃 **Sport possible** tôt le matin ou en soirée. Évitez 12h–16h et hydratez-vous avant, pendant et après.',
                default => '🏃 **Pas de restriction particulière** : buvez régulièrement et protégez-vous du soleil.',
            },
            'sortie' => $ordre >= 3
                ? '🚶 Limitez vos sorties entre 11h et 17h. Si vous sortez : chapeau, vêtements clairs, eau et restez à l’ombre.'
                : '🚶 Vous pouvez sortir ; privilégiez l’ombre et emportez de l’eau.',
            'enfants' => '👶 Les enfants se déshydratent vite : faites-les boire souvent, gardez-les au frais et ne les laissez jamais dans une voiture.',
            'ages' => '👵 Prenez des nouvelles des personnes âgées ou malades plusieurs fois par jour, aidez-les à boire et à rester au frais.',
            'hydratation' => '💧 Buvez au moins 1,5 L d’eau par jour, même sans soif ; évitez l’alcool et les boissons très sucrées.',
            'coupure' => '⚡ En cas de coupure : gardez le frigo fermé, chargez téléphone et lampe à l’avance, et débranchez les appareils sensibles.',
            'fraicheur' => '🌳 Les points de fraîcheur (parcs, salles climatisées, fontaines) sont recensés dans la rubrique « Points de fraîcheur ».',
            default => $ordre >= 3
                ? '⚠️ Restez au frais, fermez volets et fenêtres en journée et buvez régulièrement.'
                : '✅ Restez attentifs aux bulletins et hydratez-vous régulièrement.',
        };
    }

    /** Liens utiles affichés sous la réponse. */
    private function liens(array $analyse, array $contexte): array
    {
        $liens = [];
        if ($a = $contexte['alertes']->first()) {
            $liens[] = ['texte' => 'Voir l’alerte', 'url' => route('alertes-meteo.show', $a)];
        }
        if ($zone = $analyse['zones']->first()) {
            $liens[] = ['texte' => 'Prévisions de '.$zone->nom, 'url' => route('alertes-meteo.previsions', ['zone_id' => $zone->id])];
        }
        if (in_array('fraicheur', $analyse['intentions'], true) && \Illuminate\Support\Facades\Route::has('points-fraicheur.index')) {
            $liens[] = ['texte' => 'Points de fraîcheur', 'url' => route('points-fraicheur.index')];
        }

        return $liens;
    }
}
