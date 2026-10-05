<?php

namespace App\Services\Entraide;

use App\Models\HelpOffer;
use App\Models\HelpRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Mise en relation intelligente entre demandes et offres d'aide.
 *
 * Score de compatibilité (0 à 100) = combinaison pondérée de :
 *  - la ressemblance des textes (TF-IDF + similarité cosinus)  50 %
 *  - la même catégorie                                         25 %
 *  - le même lieu                                              15 %
 *  - des créneaux qui se chevauchent                           10 %
 */
class MiseEnRelationService
{
    private const POIDS = ['texte' => 0.50, 'categorie' => 0.25, 'lieu' => 0.15, 'creneau' => 0.10];

    /** Score minimal (en %) pour qu'une suggestion soit affichée. */
    public const SCORE_MINIMUM = 20;

    public function __construct(private AnalyseurTexte $analyseur) {}

    /**
     * Offres d'aide les plus adaptées à une demande.
     *
     * @return Collection<int, array{annonce: HelpOffer, score: int, raisons: list<string>}>
     */
    public function offresPour(HelpRequest $demande, int $limite = 3): Collection
    {
        $offres = HelpOffer::with('user')
            ->where('status', 'active')
            ->where('user_id', '!=', $demande->user_id)
            ->where(fn ($q) => $q->whereNull('available_until')->orWhere('available_until', '>=', now()))
            ->get();

        return $this->classer($demande, $demande->needed_from, $demande->needed_until, $offres,
            fn (HelpOffer $offre) => [$offre->available_from, $offre->available_until], $limite);
    }

    /**
     * Demandes d'aide auxquelles l'auteur d'une offre pourrait répondre.
     *
     * @return Collection<int, array{annonce: HelpRequest, score: int, raisons: list<string>}>
     */
    public function demandesPour(HelpOffer $offre, int $limite = 3): Collection
    {
        $demandes = HelpRequest::with('user')
            ->where('status', 'open')
            ->where('user_id', '!=', $offre->user_id)
            ->where(fn ($q) => $q->whereNull('needed_until')->orWhere('needed_until', '>=', now()))
            ->get();

        return $this->classer($offre, $offre->available_from, $offre->available_until, $demandes,
            fn (HelpRequest $demande) => [$demande->needed_from, $demande->needed_until], $limite);
    }

    /**
     * @param  callable(Model): array{0: ?Carbon, 1: ?Carbon}  $creneauDe
     */
    private function classer(Model $source, ?Carbon $debut, ?Carbon $fin, Collection $candidats, callable $creneauDe, int $limite): Collection
    {
        if ($candidats->isEmpty()) {
            return collect();
        }

        // Vocabulaire commun : la source + tous les candidats
        $motsSource = $this->analyseur->motsCles($source->title.' '.$source->description);
        $motsCandidats = $candidats->map(fn (Model $c) => $this->analyseur->motsCles($c->title.' '.$c->description));
        $idf = $this->analyseur->idf([$motsSource, ...$motsCandidats->all()]);
        $vecteurSource = $this->analyseur->vecteur($motsSource, $idf);

        return $candidats
            ->map(function (Model $candidat, int $i) use ($source, $debut, $fin, $creneauDe, $motsSource, $motsCandidats, $idf, $vecteurSource) {
                $raisons = [];

                // 1. Texte
                $texte = $this->analyseur->similarite($vecteurSource, $this->analyseur->vecteur($motsCandidats[$i], $idf));
                $communs = $this->motsCommuns($motsSource, $motsCandidats[$i]);
                if ($communs) {
                    $raisons[] = 'Mots en commun : '.implode(', ', $communs);
                }

                // 2. Catégorie
                $categorie = $source->category && $this->analyseur->normaliser($source->category) === $this->analyseur->normaliser($candidat->category);
                if ($categorie) {
                    $raisons[] = 'Même catégorie';
                }

                // 3. Lieu
                $lieu = $this->memeLieu($source->location, $candidat->location);
                if ($lieu) {
                    $raisons[] = 'Même quartier';
                }

                // 4. Créneau
                [$debutCandidat, $finCandidat] = $creneauDe($candidat);
                $creneau = $this->chevauchement($debut, $fin, $debutCandidat, $finCandidat);
                if ($creneau === 1.0) {
                    $raisons[] = 'Disponible au bon moment';
                }

                $score = self::POIDS['texte'] * $texte
                    + self::POIDS['categorie'] * ($categorie ? 1 : 0)
                    + self::POIDS['lieu'] * ($lieu ? 1 : 0)
                    + self::POIDS['creneau'] * $creneau;

                return ['annonce' => $candidat, 'score' => (int) round($score * 100), 'raisons' => $raisons];
            })
            ->filter(fn (array $suggestion) => $suggestion['score'] >= self::SCORE_MINIMUM)
            ->sortByDesc('score')
            ->take($limite)
            ->values();
    }

    /**
     * Mots-clés partagés (au plus 3), sous leur forme d'origine.
     */
    private function motsCommuns(array $motsA, array $motsB): array
    {
        $racinesB = array_flip(array_column($motsB, 0));
        $communs = [];
        foreach ($motsA as [$racine, $mot]) {
            if (isset($racinesB[$racine])) {
                $communs[$racine] = $mot;
            }
        }

        return array_slice(array_values($communs), 0, 3);
    }

    private function memeLieu(?string $a, ?string $b): bool
    {
        $a = $this->analyseur->normaliser($a);
        $b = $this->analyseur->normaliser($b);

        return $a !== '' && $b !== '' && (str_contains($a, $b) || str_contains($b, $a));
    }

    /**
     * 1 si les créneaux se chevauchent, 0 sinon, 0,5 si l'un des deux n'est pas précisé.
     */
    private function chevauchement(?Carbon $debutA, ?Carbon $finA, ?Carbon $debutB, ?Carbon $finB): float
    {
        if (! $debutA || ! $debutB) {
            return 0.5;
        }

        $finA ??= $debutA->copy()->endOfDay();
        $finB ??= $debutB->copy()->endOfDay();

        return $debutA->lte($finB) && $debutB->lte($finA) ? 1.0 : 0.0;
    }
}
