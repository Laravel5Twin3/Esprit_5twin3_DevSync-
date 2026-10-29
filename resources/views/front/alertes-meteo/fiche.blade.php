{{-- Fiche A4 d'une alerte, à imprimer ou à télécharger en PDF (page autonome, sans le layout du site). --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alerte {{ $alerte->niveauVigilance->nom }} – {{ $alerte->titre }} | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"
          integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <style>
        body { background: #e9ecef; }
        .fiche { width: 210mm; min-height: 297mm; margin: 1.5rem auto; background: #fff; padding: 16mm 15mm; box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.15); }
        .fiche-bandeau { border-radius: .75rem; padding: 1.4rem 1.6rem; }
        .fiche-temp { font-size: 3.6rem; font-weight: 800; line-height: 1; }
        .fiche h2 { font-size: 1.05rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; color: #c2410c; margin-top: 1.4rem; }
        .barre-actions { position: sticky; top: 0; z-index: 10; }
        @media print {
            body { background: #fff; }
            .barre-actions { display: none !important; }
            .fiche { margin: 0; box-shadow: none; width: auto; min-height: auto; padding: 0; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        @page { size: A4; margin: 14mm; }
    </style>
</head>
<body>
    <div class="barre-actions bg-dark text-white py-2">
        <div class="container d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ route('alertes-meteo.show', $alerte) }}" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
            <span class="me-auto small">Fiche d’alerte prête à imprimer ou à afficher dans votre résidence</span>
            <button type="button" class="btn btn-light btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Imprimer</button>
            <button type="button" class="btn btn-warning btn-sm" id="telecharger-pdf"><i class="bi bi-file-earmark-pdf"></i> Télécharger le PDF</button>
        </div>
    </div>

    <div class="fiche" id="fiche">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-bold fs-4" style="color:#c2410c"><i class="bi bi-thermometer-sun"></i> HeatAlert</div>
            <div class="small text-muted text-end">Fiche n° {{ $alerte->id }}<br>Éditée le {{ now()->translatedFormat('j F Y à H\hi') }}</div>
        </div>

        <div class="fiche-bandeau" style="background: {{ $alerte->niveauVigilance->couleur }}; color: {{ $alerte->niveauVigilance->couleur_texte }}">
            <div class="d-flex justify-content-between align-items-center gap-3">
                <div>
                    <div class="text-uppercase fw-bold small">Vigilance {{ $alerte->niveauVigilance->nom }} · {{ $alerte->statut_label }}</div>
                    <div class="fs-3 fw-bold">{{ $alerte->titre }}</div>
                    <div><i class="bi bi-geo-alt"></i> {{ $alerte->zone->nom }} ({{ $alerte->zone->gouvernorat }})</div>
                </div>
                <div class="fiche-temp">{{ rtrim(rtrim(number_format($alerte->temperature_max, 1, ',', ''), '0'), ',') }}°</div>
            </div>
        </div>

        <h2><i class="bi bi-calendar-event"></i> Période</h2>
        <p class="mb-0">Du <strong>{{ $alerte->date_debut->translatedFormat('l j F Y à H\hi') }}</strong>
            au <strong>{{ $alerte->date_fin->translatedFormat('l j F Y à H\hi') }}</strong>.</p>

        <h2><i class="bi bi-megaphone"></i> Message</h2>
        <p>{{ $alerte->message }}</p>

        <h2><i class="bi bi-shield-check"></i> Les bons réflexes</h2>
        <div class="p-3 rounded" style="background:#fff4ec; border-left: 5px solid {{ $alerte->niveauVigilance->couleur }}">
            {{ $alerte->niveauVigilance->consigne }}
        </div>
        <ul class="mt-3 mb-0">
            <li>Buvez au moins 1,5 L d’eau par jour, même sans soif.</li>
            <li>Fermez volets et fenêtres en journée, aérez la nuit.</li>
            <li>Prenez des nouvelles des personnes âgées, isolées ou malades.</li>
            <li>En cas de coupure de courant : gardez le réfrigérateur fermé et débranchez les appareils sensibles.</li>
        </ul>

        <h2><i class="bi bi-bar-chart-steps"></i> Échelle de vigilance</h2>
        <table class="table table-sm table-bordered align-middle mb-0">
            <caption class="visually-hidden">Échelle des niveaux de vigilance canicule</caption>
            <thead class="table-light">
                <tr>
                    <th scope="col"><span class="visually-hidden">Couleur</span></th>
                    <th scope="col">Niveau</th>
                    <th scope="col">Températures</th>
                    <th scope="col">Consigne</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($niveaux as $niveau)
                <tr @if ($niveau->id === $alerte->niveau_vigilance_id) class="fw-bold" @endif>
                    <td style="width: 6px; background: {{ $niveau->couleur }}"></td>
                    <td style="width: 22%">{{ $niveau->nom }} @if ($niveau->id === $alerte->niveau_vigilance_id) ◀ @endif</td>
                    <td style="width: 20%">{{ $niveau->plage }}</td>
                    <td class="small">{{ $niveau->consigne }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-end mt-4 pt-3 border-top small text-muted">
            <div>
                <strong>Urgences :</strong> SAMU 190 · Protection civile 198<br>
                Points de fraîcheur et alertes à jour sur HeatAlert.
            </div>
            <div class="text-end">{{ route('alertes-meteo.show', $alerte) }}</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.2/dist/html2pdf.bundle.min.js"
            integrity="sha384-aBc0BOllaGWrQx51DYt978St/L7B+21jzNGc4N/jnGD0NxGwj8S/ftRgW0AkXIak" crossorigin="anonymous"></script>
    <script>
        document.getElementById('telecharger-pdf').addEventListener('click', () => {
            html2pdf().set({
                margin: 8,
                filename: @js('alerte-'.$alerte->id.'-'.\Illuminate\Support\Str::slug($alerte->zone->nom).'.pdf'),
                image: { type: 'jpeg', quality: 0.95 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
            }).from(document.getElementById('fiche')).save();
        });
    </script>
</body>
</html>
