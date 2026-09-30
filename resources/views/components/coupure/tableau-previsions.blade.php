{{-- Tableau zones × jours du risque de coupure prédit par l'IA. Usage : <x-coupure.tableau-previsions :previsions="$previsions" /> --}}
@props(['previsions'])

@php($jours = $previsions->first()['jours'] ?? [])

<div class="table-responsive">
    <table class="table align-middle text-center mb-0">
        <thead class="table-light">
            <tr>
                <th class="text-start">Zone</th>
                @foreach ($jours as $jour)
                    <th>
                        {{ $jour['date']->isToday() ? "Aujourd'hui" : ucfirst($jour['date']->translatedFormat('l')) }}
                        <div class="small fw-normal text-muted">{{ $jour['date']->format('d/m') }}</div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($previsions as $prevision)
                <tr>
                    <td class="text-start">
                        <strong>{{ $prevision['zone']->nom }}</strong>
                        <div class="small text-muted">{{ $prevision['zone']->gouvernorat }}</div>
                    </td>
                    @foreach ($prevision['jours'] as $jour)
                        @php($principal = $jour['facteurs'][0])
                        <td class="bg-{{ $jour['niveau']['couleur'] }} bg-opacity-10"
                            title="Facteur principal : {{ $principal['label'] }} ({{ $principal['contribution'] >= 0 ? 'augmente' : 'diminue' }} le risque)">
                            <div class="fw-bold text-{{ $jour['niveau']['couleur'] }}">{{ round($jour['probabilite'] * 100) }} %</div>
                            <div class="small text-muted"><i class="bi bi-thermometer-half"></i> {{ round($jour['temperature']) }} °C</div>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="small text-muted mt-2">
    <span class="badge bg-success">Faible</span> &lt; 20 %
    <span class="badge bg-warning text-dark ms-2">Modéré</span> 20 – 45 %
    <span class="badge bg-danger ms-2">Élevé</span> ≥ 45 %
    · Survolez une case pour voir le facteur principal.
</div>
