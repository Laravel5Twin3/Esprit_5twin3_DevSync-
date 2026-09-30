<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CategoriePoint;
use App\Models\PointFraicheur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PointFraicheurController extends Controller
{
    /**
     * Carte interactive + liste des points de fraîcheur.
     */
    public function index(Request $request): View
    {
        $categories = CategoriePoint::withCount(['pointsFraicheur' => fn($q) => $q->where('actif', true)])
            ->orderBy('nom')
            ->get();

        $query = PointFraicheur::with('categoriePoint')->actifs();

        // Filtre par catégorie
        if ($request->filled('categorie')) {
            $query->where('categorie_point_id', $request->integer('categorie'));
        }

        $points = $query->orderBy('nom')->get();

        // Données GeoJSON pour Leaflet
        $geojson = $this->buildGeoJson($points);

        return view('front.points-fraicheur.index', compact('categories', 'points', 'geojson'));
    }

    /**
     * Détail public d'un point de fraîcheur.
     */
    public function show(PointFraicheur $point): View
    {
        abort_unless($point->actif, 404);
        $point->load('categoriePoint');

        // Points proches (même catégorie, rayon ~5 km)
        $proches = PointFraicheur::with('categoriePoint')
            ->actifs()
            ->where('id', '!=', $point->id)
            ->get()
            ->filter(fn($p) => $p->distanceTo($point->latitude, $point->longitude) < 5)
            ->sortBy(fn($p) => $p->distanceTo($point->latitude, $point->longitude))
            ->take(4)
            ->values();

        return view('front.points-fraicheur.show', compact('point', 'proches'));
    }

    /**
     * API JSON : recommandation IA selon la température actuelle et la position.
     * Utilise l'API Open-Meteo (gratuite, sans clé) pour la météo en temps réel.
     */
    public function recommander(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $userLat = (float) $request->lat;
        $userLng = (float) $request->lng;

        // 1. Récupération de la température actuelle via Open-Meteo
        $temperature = $this->getTemperature($userLat, $userLng);

        // 2. Récupération de tous les points actifs
        $points = PointFraicheur::with('categoriePoint')->actifs()->get();

        // 3. Scoring IA : distance + capacité + type selon chaleur
        $scored = $points->map(function (PointFraicheur $p) use ($userLat, $userLng, $temperature) {
            $distance = $p->distanceTo($userLat, $userLng);
            $score    = 0;

            // Moins c'est loin, mieux c'est (max 50 pts)
            $score += max(0, 50 - ($distance * 10));

            // Capacité (max 20 pts)
            if ($p->capacite) {
                $score += min(20, $p->capacite / 10);
            }

            // Pertinence selon température (max 30 pts)
            $nom = strtolower($p->categoriePoint->nom ?? '');
            if ($temperature >= 35) {
                // Canicule : privilégier salles climatisées
                if (str_contains($nom, 'climatis')) $score += 30;
                elseif (str_contains($nom, 'fontaine')) $score += 20;
                elseif (str_contains($nom, 'parc')) $score += 10;
            } elseif ($temperature >= 28) {
                // Chaud : parcs et fontaines
                if (str_contains($nom, 'fontaine')) $score += 25;
                elseif (str_contains($nom, 'parc')) $score += 20;
                else $score += 10;
            } else {
                $score += 15; // Tous équivalents
            }

            return [
                'id'            => $p->id,
                'nom'           => $p->nom,
                'adresse'       => $p->adresse,
                'categorie'     => $p->categoriePoint->nom ?? '—',
                'couleur'       => $p->categoriePoint->couleur ?? '#0d6efd',
                'icone'         => $p->categoriePoint->icone ?? 'bi-geo-alt',
                'horaires'      => $p->horaires,
                'capacite'      => $p->capacite,
                'latitude'      => $p->latitude,
                'longitude'     => $p->longitude,
                'distance_km'   => round($distance, 2),
                'score'         => round($score, 1),
                'url'           => route('points-fraicheur.show', $p),
            ];
        })
        ->sortByDesc('score')
        ->take(5)
        ->values();

        return response()->json([
            'temperature' => $temperature,
            'recommandations' => $scored,
            'message'     => $this->buildAiMessage($temperature),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers privés
    // ─────────────────────────────────────────────────────────────────────

    private function buildGeoJson($points): string
    {
        $features = $points->map(fn(PointFraicheur $p) => [
            'type' => 'Feature',
            'geometry' => [
                'type'        => 'Point',
                'coordinates' => [(float) $p->longitude, (float) $p->latitude],
            ],
            'properties' => [
                'id'       => $p->id,
                'nom'      => $p->nom,
                'adresse'  => $p->adresse,
                'categorie'=> $p->categoriePoint->nom ?? '—',
                'couleur'  => $p->categoriePoint->couleur ?? '#0d6efd',
                'icone'    => $p->categoriePoint->icone ?? 'bi-geo-alt',
                'horaires' => $p->horaires,
                'capacite' => $p->capacite,
                'url'      => route('points-fraicheur.show', $p),
            ],
        ])->values()->all();

        return json_encode([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    private function getTemperature(float $lat, float $lng): float
    {
        try {
            $url  = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lng}"
                  . "&current_weather=true&temperature_unit=celsius";
            $json = @file_get_contents($url);
            if ($json) {
                $data = json_decode($json, true);
                return $data['current_weather']['temperature'] ?? 30.0;
            }
        } catch (\Throwable) {
            // Silently fallback
        }
        return 30.0; // Valeur par défaut si API indisponible
    }

    private function buildAiMessage(float $temperature): string
    {
        if ($temperature >= 38) {
            return "⚠️ Alerte canicule extrême ({$temperature}°C) ! Privilégiez absolument les salles climatisées et restez hydraté.";
        } elseif ($temperature >= 35) {
            return "🌡️ Canicule ({$temperature}°C) : Cherchez une salle climatisée ou une fontaine. Évitez de sortir entre 12h et 16h.";
        } elseif ($temperature >= 30) {
            return "☀️ Forte chaleur ({$temperature}°C) : Un parc ombragé ou une fontaine vous apportera un grand soulagement.";
        } else {
            return "🌿 Température modérée ({$temperature}°C) : Profitez des espaces verts à proximité.";
        }
    }
}
