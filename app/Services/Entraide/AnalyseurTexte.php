<?php

namespace App\Services\Entraide;

use Illuminate\Support\Str;

/**
 * Traitement automatique du langage (français) : découpe un texte en mots-clés,
 * le transforme en vecteur TF-IDF et mesure la ressemblance entre deux textes (similarité cosinus).
 */
class AnalyseurTexte
{
    /** Mots trop fréquents pour être porteurs de sens. */
    private const MOTS_VIDES = [
        'les', 'des', 'une', 'aux', 'est', 'sont', 'que', 'qui', 'quoi', 'dans', 'pour', 'par', 'sur', 'sous',
        'avec', 'sans', 'mais', 'ou', 'donc', 'car', 'pas', 'plus', 'moins', 'tres', 'peu', 'tout', 'tous',
        'toute', 'toutes', 'mon', 'ton', 'son', 'mes', 'tes', 'ses', 'notre', 'votre', 'leur', 'leurs', 'nos',
        'vos', 'ces', 'cet', 'cette', 'celui', 'celle', 'ceux', 'elle', 'elles', 'ils', 'nous', 'vous', 'moi',
        'toi', 'lui', 'peux', 'peut', 'pouvez', 'pourrait', 'aurais', 'aurai', 'avoir', 'etre', 'suis', 'fait',
        'faire', 'comme', 'aussi', 'bien', 'encore', 'deja', 'quelques', 'quelque', 'chez', 'entre', 'vers',
        'depuis', 'pendant', 'apres', 'avant', 'ainsi', 'alors', 'besoin', 'aide', 'aider', 'aides', 'voisin',
        'voisins', 'quelqu', 'personne', 'personnes', 'semaine', 'jour', 'jours', 'cela', 'ceci', 'une', 'ete',
        'ideale', 'ideal', 'petit', 'petite', 'petits', 'petites', 'autre', 'autres', 'mieux',
    ];

    /** Suffixes retirés pour regrouper les mots d'une même famille (déménager / déménagement). */
    private const SUFFIXES = ['ements', 'ement', 'ations', 'ation', 'euses', 'euse', 'eurs', 'eur', 'ers', 'er', 'es', 'e', 's', 'x'];

    /**
     * Découpe un texte en racines de mots significatifs.
     * Retourne [racine => mot d'origine] pour pouvoir afficher les mots en commun.
     *
     * @return list<array{0: string, 1: string}> paires [racine, mot]
     */
    public function motsCles(?string $texte): array
    {
        $texte = Str::of((string) $texte)->ascii()->lower()->replaceMatches('/[^a-z]+/', ' ')->trim();

        $mots = [];
        foreach (explode(' ', (string) $texte) as $mot) {
            if (strlen($mot) < 3 || in_array($mot, self::MOTS_VIDES, true)) {
                continue;
            }
            $mots[] = [$this->racine($mot), $mot];
        }

        return $mots;
    }

    /**
     * Fréquence documentaire inverse : un mot rare dans l'ensemble des annonces pèse plus lourd.
     *
     * @param  list<list<array{0: string, 1: string}>>  $documents
     * @return array<string, float>
     */
    public function idf(array $documents): array
    {
        $presence = [];
        foreach ($documents as $mots) {
            foreach (array_unique(array_column($mots, 0)) as $racine) {
                $presence[$racine] = ($presence[$racine] ?? 0) + 1;
            }
        }

        $n = count($documents);

        return array_map(fn (int $df) => log(($n + 1) / ($df + 1)) + 1, $presence);
    }

    /**
     * Vecteur TF-IDF d'un document.
     *
     * @param  list<array{0: string, 1: string}>  $mots
     * @return array<string, float>
     */
    public function vecteur(array $mots, array $idf): array
    {
        if (! $mots) {
            return [];
        }

        $vecteur = [];
        foreach (array_count_values(array_column($mots, 0)) as $racine => $nombre) {
            $vecteur[$racine] = ($nombre / count($mots)) * ($idf[$racine] ?? 1.0);
        }

        return $vecteur;
    }

    /**
     * Similarité cosinus entre deux vecteurs : 1 = mêmes mots-clés, 0 = aucun mot commun.
     */
    public function similarite(array $a, array $b): float
    {
        $produit = 0.0;
        foreach ($a as $racine => $poids) {
            $produit += $poids * ($b[$racine] ?? 0.0);
        }

        $normes = sqrt(array_sum(array_map(fn ($v) => $v ** 2, $a))) * sqrt(array_sum(array_map(fn ($v) => $v ** 2, $b)));

        return $normes > 0 ? $produit / $normes : 0.0;
    }

    /**
     * Normalise une valeur courte (catégorie, lieu) : « Déménagement » et « demenagement » deviennent égales.
     */
    public function normaliser(?string $valeur): string
    {
        return (string) Str::of((string) $valeur)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', ' ')->trim();
    }

    private function racine(string $mot): string
    {
        foreach (self::SUFFIXES as $suffixe) {
            if (str_ends_with($mot, $suffixe) && strlen($mot) - strlen($suffixe) >= 4) {
                return substr($mot, 0, -strlen($suffixe));
            }
        }

        return $mot;
    }
}
