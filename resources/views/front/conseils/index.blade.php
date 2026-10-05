@extends('layouts.front')

@section('title', 'Conseils et prévention')

@section('hero')
<div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #fd7e14 0%, #dc3545 100%);">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">💡 Conseils et prévention</h1>
        <p class="lead mb-0">Gestes simples pour affronter la canicule et les coupures de courant</p>
    </div>
</div>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-3">
        <div class="card shadow-sm sticky-top" style="top: 1rem;">
            <div class="card-header fw-semibold"><i class="bi bi-funnel"></i> Filtrer</div>
            <div class="card-body p-2">
                <a href="{{ route('conseils.index') }}"
                   class="btn btn-sm w-100 mb-2 {{ !request('categorie') && !request('public') ? 'btn-dark' : 'btn-outline-secondary' }}">
                    Tous
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('conseils.index', ['categorie' => $cat->id]) }}"
                       class="btn btn-sm w-100 mb-2 {{ request('categorie') == $cat->id ? 'text-white' : 'btn-outline-secondary' }}"
                       style="{{ request('categorie') == $cat->id ? 'background-color:' . $cat->couleur . '; border-color:' . $cat->couleur : '' }}">
                        <i class="bi {{ $cat->icone }}"></i>
                        {{ $cat->nom }}
                        <span class="badge bg-white text-dark ms-1">{{ $cat->conseils_count }}</span>
                    </a>
                @endforeach

                <hr>
                <div class="small text-muted px-1 mb-2">Public</div>
                @foreach(\App\Models\Conseil::PUBLICS as $key => $label)
                    <a href="{{ route('conseils.index', array_filter(['categorie' => request('categorie'), 'public' => $key])) }}"
                       class="btn btn-sm w-100 mb-2 {{ request('public') === $key ? 'btn-dark' : 'btn-outline-secondary' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        @if($conseils->isEmpty())
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                Aucun conseil trouvé pour ce filtre.
            </div>
        @else
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body py-3">
                    <label for="conseil-live-search" class="form-label fw-semibold mb-2">
                        <i class="bi bi-search"></i> Rechercher un conseil
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input
                            type="search"
                            id="conseil-live-search"
                            class="form-control"
                            placeholder="Rechercher un conseil..."
                            autocomplete="off"
                            spellcheck="false"
                        >
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small id="conseil-search-count" class="text-muted">{{ $conseils->count() }} conseils trouvés</small>
                    </div>
                </div>
            </div>

            <div id="conseil-search-empty" class="alert alert-warning" hidden>
                <i class="bi bi-exclamation-triangle"></i>
                Aucun conseil ne correspond à votre recherche.
            </div>

            <div id="conseil-card-grid" class="row row-cols-1 row-cols-md-2 g-3">
                @foreach($conseils as $conseil)
                <div
                    class="col js-conseil-card"
                    data-title="{{ $conseil->titre }}"
                    data-category="{{ $conseil->categorieConseil->nom ?? '' }}"
                    data-content="{{ $conseil->contenu }}"
                >
                    <x-card class="h-100">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:48px; height:48px; background-color: {{ $conseil->categorieConseil->couleur ?? '#fd7e14' }};">
                                <i class="bi {{ $conseil->categorieConseil->icone ?? 'bi-lightbulb' }} text-white fs-5"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap gap-1 mb-1">
                                    <span class="badge rounded-pill" style="background-color: {{ $conseil->categorieConseil->couleur ?? '#fd7e14' }}">
                                        <span class="js-hl-category">{{ $conseil->categorieConseil->nom ?? '—' }}</span>
                                    </span>
                                    <span class="badge bg-{{ $conseil->badgePriorite() }}">{{ $conseil->labelPriorite() }}</span>
                                </div>
                                <h6 class="mb-1 fw-bold">
                                    <a href="{{ route('conseils.show', $conseil) }}" class="text-decoration-none text-dark">
                                        <span class="js-hl-title">{{ $conseil->titre }}</span>
                                    </a>
                                </h6>
                                <p class="small text-muted mb-2 js-hl-content" data-excerpt="{{ $conseil->resume ?? Str::limit($conseil->contenu, 110) }}">{{ $conseil->resume ?? Str::limit($conseil->contenu, 110) }}</p>
                                <span class="small text-muted">
                                    <i class="bi bi-people"></i> {{ $conseil->labelPublicCible() }}
                                </span>
                            </div>
                        </div>
                    </x-card>
                </div>
                @endforeach
            </div>

            @if($conseils->hasPages())
                <div id="conseil-pagination" class="d-flex justify-content-center mt-4">
                    {{ $conseils->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .js-conseil-card mark {
        background-color: #fff3cd;
        color: inherit;
        padding: 0 .12em;
        border-radius: .15rem;
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const input = document.getElementById('conseil-live-search');
    if (!input) {
        return;
    }

    const cards = Array.from(document.querySelectorAll('.js-conseil-card'));
    const emptyAlert = document.getElementById('conseil-search-empty');
    const counter = document.getElementById('conseil-search-count');
    const pagination = document.getElementById('conseil-pagination');

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function normalize(value) {
        return String(value)
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();
    }

    function findRanges(original, query) {
        const chars = Array.from(original);
        const needle = normalize(query);
        if (!needle) {
            return [];
        }

        let haystack = '';
        const map = [];
        chars.forEach(function (char, index) {
            const piece = normalize(char);
            for (let i = 0; i < piece.length; i += 1) {
                haystack += piece[i];
                map.push(index);
            }
        });

        const ranges = [];
        let from = 0;
        while (from <= haystack.length - needle.length) {
            const at = haystack.indexOf(needle, from);
            if (at === -1) {
                break;
            }
            ranges.push([map[at], map[at + needle.length - 1] + 1]);
            from = at + needle.length;
        }
        return ranges;
    }

    function highlight(original, query) {
        if (!query) {
            return escapeHtml(original);
        }
        const chars = Array.from(original);
        const ranges = findRanges(original, query);
        if (!ranges.length) {
            return escapeHtml(original);
        }
        let html = '';
        let cursor = 0;
        ranges.forEach(function (range) {
            html += escapeHtml(chars.slice(cursor, range[0]).join(''));
            html += '<mark>' + escapeHtml(chars.slice(range[0], range[1]).join('')) + '</mark>';
            cursor = range[1];
        });
        html += escapeHtml(chars.slice(cursor).join(''));
        return html;
    }

    function restoreField(el, original) {
        el.textContent = original;
    }

    function applyHighlight(el, original, query) {
        el.innerHTML = highlight(original, query);
    }

    function matches(card, query) {
        const blob = [
            card.dataset.title || '',
            card.dataset.content || '',
            card.dataset.category || '',
        ].join('\n');
        return normalize(blob).includes(normalize(query));
    }

    function render(query) {
        const term = query.trim();
        let visible = 0;

        cards.forEach(function (card) {
            const titleEl = card.querySelector('.js-hl-title');
            const contentEl = card.querySelector('.js-hl-content');
            const categoryEl = card.querySelector('.js-hl-category');
            if (!titleEl || !contentEl || !categoryEl) {
                return;
            }

            const title = card.dataset.title || '';
            const category = card.dataset.category || categoryEl.textContent;
            const excerptText = contentEl.getAttribute('data-excerpt') || '';

            if (!term) {
                card.hidden = false;
                restoreField(titleEl, title);
                restoreField(categoryEl, category);
                restoreField(contentEl, excerptText);
                visible += 1;
                return;
            }

            const show = matches(card, term);
            card.hidden = !show;
            if (show) {
                visible += 1;
                applyHighlight(titleEl, title, term);
                applyHighlight(categoryEl, category, term);
                applyHighlight(contentEl, excerptText, term);
            } else {
                restoreField(titleEl, title);
                restoreField(categoryEl, category);
                restoreField(contentEl, excerptText);
            }
        });

        if (counter) {
            counter.textContent = visible === 1 ? '1 conseil trouvé' : visible + ' conseils trouvés';
        }
        if (emptyAlert) {
            emptyAlert.hidden = visible !== 0;
        }
        if (pagination) {
            pagination.hidden = Boolean(term);
        }
    }

    input.addEventListener('input', function () {
        render(input.value);
    });
})();
</script>
@endpush
