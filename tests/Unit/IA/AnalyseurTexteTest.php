<?php

namespace Tests\Unit\IA;

use App\Services\Entraide\AnalyseurTexte;
use PHPUnit\Framework\TestCase;

/**
 * Analyse de texte de la mise en relation (module Entraide).
 */
class AnalyseurTexteTest extends TestCase
{
    private AnalyseurTexte $analyseur;

    protected function setUp(): void
    {
        $this->analyseur = new AnalyseurTexte;
    }

    public function test_les_mots_d_une_meme_famille_sont_regroupes(): void
    {
        $racines = fn (string $texte) => array_column($this->analyseur->motsCles($texte), 0);

        $this->assertSame($racines('déménagement'), $racines('déménager'));
        $this->assertSame($racines('meubles'), $racines('meuble'));
    }

    public function test_les_mots_vides_sont_ignores(): void
    {
        $this->assertSame([], $this->analyseur->motsCles("J'ai besoin d'aide pour les voisins"));
    }

    public function test_des_textes_proches_sont_plus_similaires(): void
    {
        $demande = $this->analyseur->motsCles('Faire mes courses au supermarché');
        $offreProche = $this->analyseur->motsCles('Je fais les courses au supermarché du quartier');
        $offreLointaine = $this->analyseur->motsCles('Réparation d\'ordinateur et imprimante');

        $idf = $this->analyseur->idf([$demande, $offreProche, $offreLointaine]);
        $v = fn ($mots) => $this->analyseur->vecteur($mots, $idf);

        $this->assertGreaterThan(
            $this->analyseur->similarite($v($demande), $v($offreLointaine)),
            $this->analyseur->similarite($v($demande), $v($offreProche)),
        );
        $this->assertSame(0.0, $this->analyseur->similarite($v($demande), $v($offreLointaine)));
    }

    public function test_normalisation_ignore_accents_et_majuscules(): void
    {
        $this->assertSame($this->analyseur->normaliser('Déménagement'), $this->analyseur->normaliser('demenagement'));
    }
}
