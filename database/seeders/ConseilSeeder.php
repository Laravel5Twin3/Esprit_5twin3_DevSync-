<?php

namespace Database\Seeders;

use App\Models\CategorieConseil;
use App\Models\Conseil;
use Illuminate\Database\Seeder;

class ConseilSeeder extends Seeder
{
    public function run(): void
    {
        $conseils = [
            [
                'categorie'    => 'Hydratation et santé',
                'titre'        => 'Boire régulièrement, avant d\'avoir soif',
                'resume'       => 'En période de canicule, la soif arrive trop tard. Prévoyez de l\'eau à portée de main.',
                'contenu'      => "Buvez de l'eau tout au long de la journée, même sans sensation de soif. Évitez l'alcool et les boissons très sucrées, qui déshydratent. Gardez une bouteille au réfrigérateur et proposez à boire aux personnes qui vous entourent, surtout les enfants et les personnes âgées.",
                'public_cible' => 'tous',
                'priorite'     => 'urgent',
                'actif'        => true,
            ],
            [
                'categorie'    => 'Hydratation et santé',
                'titre'        => 'Reconnaître un coup de chaleur',
                'resume'       => 'Maux de tête, confusion, peau chaude et sèche : agissez sans attendre.',
                'contenu'      => "Les signes d'alerte : forte fièvre, maux de tête, nausées, confusion, peau chaude. Placez la personne à l'ombre, déshabillez-la légèrement, aspergez-la d'eau tiède et appelez les secours si l'état ne s'améliore pas. Ne donnez pas de médicaments contre la fièvre sans avis médical.",
                'public_cible' => 'tous',
                'priorite'     => 'urgent',
                'actif'        => true,
            ],
            [
                'categorie'    => 'Économie d\'énergie',
                'titre'        => 'Reporter les appareils énergivores',
                'resume'       => 'Lave-linge, four et climatisation en même temps saturent le réseau aux heures de pointe.',
                'contenu'      => "Évitez d'utiliser le four, le lave-linge et le lave-vaisselle entre 12h et 16h, lorsque le réseau est le plus sollicité. Décalez ces usages en soirée. Éteignez les veilles et débranchez les chargeurs inutiles.",
                'public_cible' => 'tous',
                'priorite'     => 'important',
                'actif'        => true,
            ],
            [
                'categorie'    => 'Économie d\'énergie',
                'titre'        => 'Régler la climatisation sans surconsommer',
                'resume'       => 'Un écart de 5 à 7 °C avec l\'extérieur suffit pour rester confortable.',
                'contenu'      => "Réglez la climatisation autour de 26 °C. Fermez portes et fenêtres, et entretenez les filtres. Un ventilateur consomme beaucoup moins : combinez-le avec des stores baissés plutôt que de pousser le climatiseur au maximum.",
                'public_cible' => 'tous',
                'priorite'     => 'info',
                'actif'        => true,
            ],
            [
                'categorie'    => 'Coupures de courant',
                'titre'        => 'Préparer un kit délestage',
                'resume'       => 'Lampe, radio, power bank et médicaments : anticipez avant la coupure.',
                'contenu'      => "Gardez à portée : lampes de poche, piles, batterie externe chargée, radio, bouteilles d'eau et une petite réserve de denrées non périssables. Notez les numéros utiles sur papier. Débranchez les appareils sensibles (ordinateur, TV) pour éviter les surtensions au rétablissement du courant.",
                'public_cible' => 'tous',
                'priorite'     => 'important',
                'actif'        => true,
            ],
            [
                'categorie'    => 'Coupures de courant',
                'titre'        => 'Protéger le réfrigérateur pendant une coupure',
                'resume'       => 'N\'ouvrez la porte que si c\'est indispensable : le froid se conserve plusieurs heures.',
                'contenu'      => "Laissez le réfrigérateur et le congélateur fermés. Un congélateur plein conserve le froid plus longtemps. Consommez en priorité les aliments les plus sensibles une fois le courant revenu, et jetez ce qui a dépassé la température de sécurité.",
                'public_cible' => 'tous',
                'priorite'     => 'info',
                'actif'        => true,
            ],
            [
                'categorie'    => 'Personnes vulnérables',
                'titre'        => 'Surveiller les personnes âgées du quartier',
                'resume'       => 'Un appel ou une visite en début d\'après-midi peut éviter une urgence.',
                'contenu'      => "Prenez des nouvelles des voisins isolés, surtout entre 12h et 16h. Proposez de l'eau, un passage dans un lieu climatisé, ou de faire les courses. N'hésitez pas à alerter les secours si la personne est confuse, très faible ou refuse de boire.",
                'public_cible' => 'personnes_agees',
                'priorite'     => 'urgent',
                'actif'        => true,
            ],
            [
                'categorie'    => 'Personnes vulnérables',
                'titre'        => 'Protéger les nourrissons et les enfants',
                'resume'       => 'Jamais un enfant dans une voiture au soleil, même quelques minutes.',
                'contenu'      => "Habillez les enfants de vêtements légers, faites-les boire souvent, et évitez les sorties aux heures les plus chaudes. Un enfant ne doit jamais rester dans un véhicule stationné. Privilégiez les pièces les plus fraîches et les points de fraîcheur proches.",
                'public_cible' => 'enfants',
                'priorite'     => 'urgent',
                'actif'        => true,
            ],
            [
                'categorie'    => 'Habitat et équipements',
                'titre'        => 'Fermer les volets aux heures chaudes',
                'resume'       => 'Occulter le soleil le matin limite fortement la température intérieure.',
                'contenu'      => "Dès le matin, fermez volets, stores et rideaux sur les façades ensoleillées. Aérez tôt le matin et tard le soir, lorsque l'air extérieur est plus frais. Évitez de cuisiner aux heures de pointe pour ne pas ajouter de chaleur dans le logement.",
                'public_cible' => 'tous',
                'priorite'     => 'important',
                'actif'        => true,
            ],
            [
                'categorie'    => 'Habitat et équipements',
                'titre'        => 'Utiliser le ventilateur sans risque',
                'resume'       => 'Au-delà de 35 °C, un ventilateur seul ne suffit plus : cherchez un lieu plus frais.',
                'contenu'      => "Le ventilateur aide à évaporer la transpiration, mais il ne refroidit pas l'air. Au-delà d'environ 35 °C, orientez-vous vers une pièce climatisée, une bibliothèque ou un point de fraîcheur. Humidifiez légèrement un linge devant le ventilateur pour un effet plus marqué.",
                'public_cible' => 'malades_chroniques',
                'priorite'     => 'info',
                'actif'        => true,
            ],
        ];

        foreach ($conseils as $data) {
            $categorie = CategorieConseil::where('nom', $data['categorie'])->first();
            if (! $categorie) {
                continue;
            }

            Conseil::firstOrCreate(
                ['titre' => $data['titre']],
                [
                    'categorie_conseil_id' => $categorie->id,
                    'resume'               => $data['resume'],
                    'contenu'              => $data['contenu'],
                    'public_cible'         => $data['public_cible'],
                    'priorite'             => $data['priorite'],
                    'actif'                => $data['actif'],
                ]
            );
        }

        CategorieConseil::all()->each(function (CategorieConseil $cat) {
            Conseil::factory()
                ->count(2)
                ->create(['categorie_conseil_id' => $cat->id, 'actif' => true]);
        });
    }
}
