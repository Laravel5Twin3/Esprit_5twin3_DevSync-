{{-- Assistant canicule : bulle de chat flottante. Usage : <x-alerte-meteo.assistant /> --}}
<div id="assistant-canicule" class="ha-assistant">
    <button type="button" class="ha-assistant-bouton btn btn-ha rounded-circle shadow" aria-label="Ouvrir l’assistant canicule"
            aria-expanded="false" aria-controls="assistant-panneau">
        <i class="bi bi-chat-dots-fill fs-4"></i>
    </button>

    <div id="assistant-panneau" class="ha-assistant-panneau card shadow-lg border-0 d-none" role="dialog" aria-label="Assistant canicule">
        <div class="card-header ha-assistant-entete text-white d-flex align-items-center gap-2">
            <i class="bi bi-robot fs-5"></i>
            <div class="flex-grow-1">
                <div class="fw-semibold">Assistant canicule</div>
                <div class="small opacity-75">Réponses basées sur les alertes réelles</div>
            </div>
            <button type="button" class="btn-close btn-close-white ha-assistant-fermer" aria-label="Fermer"></button>
        </div>

        <div class="card-body ha-assistant-messages" aria-live="polite">
            <div class="ha-msg ha-msg-bot">Bonjour 👋 Je réponds à vos questions sur la chaleur dans votre quartier, à partir des alertes et des prévisions réelles.</div>
            <div class="d-flex flex-wrap gap-1 ha-assistant-suggestions">
                @foreach (['Je peux faire du sport demain à La Marsa ?', 'Quelle est la situation aujourd’hui ?', 'Comment protéger mes enfants à Ariana ?', 'Risque de coupure ce week-end à Bab Souika ?'] as $suggestion)
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill ha-suggestion">{{ $suggestion }}</button>
                @endforeach
            </div>
        </div>

        <form class="card-footer bg-white d-flex gap-2 ha-assistant-form">
            <input type="text" class="form-control" name="question" maxlength="500" placeholder="Posez votre question…"
                   aria-label="Votre question" autocomplete="off" required>
            <button class="btn btn-ha" type="submit" aria-label="Envoyer"><i class="bi bi-send-fill"></i></button>
        </form>
    </div>
</div>

@once
    @push('styles')
        <style>
            .ha-assistant { position: fixed; right: 1.25rem; bottom: 1.25rem; z-index: 1080; }
            .ha-assistant-bouton { width: 3.6rem; height: 3.6rem; }
            .ha-assistant-panneau { position: absolute; right: 0; bottom: 4.4rem; width: min(380px, calc(100vw - 2.5rem)); max-height: min(560px, calc(100vh - 7rem)); display: flex; flex-direction: column; overflow: hidden; }
            .ha-assistant-entete { background: linear-gradient(90deg, var(--ha-primary-dark, #c2410c), var(--ha-primary, #e8590c)); }
            .ha-assistant-messages { overflow-y: auto; flex: 1; background: #fffaf6; }
            .ha-msg { max-width: 88%; padding: .55rem .8rem; border-radius: 1rem; margin-bottom: .6rem; white-space: pre-line; font-size: .92rem; }
            .ha-msg-bot { background: #fff; border: 1px solid #f1e3d8; border-bottom-left-radius: .25rem; }
            .ha-msg-user { background: var(--ha-primary, #e8590c); color: #fff; margin-left: auto; border-bottom-right-radius: .25rem; }
            .ha-msg-source { font-size: .72rem; opacity: .65; margin-top: .3rem; }
            .ha-msg a { color: var(--ha-primary-dark, #c2410c); }
        </style>
    @endpush

    @push('scripts')
        <script>
            (() => {
                const racine = document.getElementById('assistant-canicule');
                const panneau = racine.querySelector('.ha-assistant-panneau');
                const bouton = racine.querySelector('.ha-assistant-bouton');
                const messages = racine.querySelector('.ha-assistant-messages');
                const form = racine.querySelector('.ha-assistant-form');
                const champ = form.querySelector('input');
                const url = @js(route('alertes-meteo.assistant'));
                const jeton = document.querySelector('meta[name="csrf-token"]').content;

                const basculer = (ouvrir) => {
                    panneau.classList.toggle('d-none', !ouvrir);
                    bouton.setAttribute('aria-expanded', ouvrir);
                    if (ouvrir) champ.focus();
                };
                bouton.addEventListener('click', () => basculer(panneau.classList.contains('d-none')));
                racine.querySelector('.ha-assistant-fermer').addEventListener('click', () => basculer(false));

                // Le texte est échappé, puis seul **gras** est transformé (pas d'injection HTML possible)
                const formater = (texte) => {
                    const div = document.createElement('div');
                    div.textContent = texte;
                    return div.innerHTML.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                };

                const ajouter = (contenu, classe, html = false) => {
                    const bulle = document.createElement('div');
                    bulle.className = 'ha-msg ' + classe;
                    if (html) bulle.innerHTML = contenu; else bulle.textContent = contenu;
                    messages.appendChild(bulle);
                    messages.scrollTop = messages.scrollHeight;
                    return bulle;
                };

                const envoyer = async (question) => {
                    question = question.trim();
                    if (!question) return;
                    racine.querySelector('.ha-assistant-suggestions')?.remove();
                    ajouter(question, 'ha-msg-user');
                    champ.value = '';
                    const attente = ajouter('…', 'ha-msg-bot');

                    try {
                        const reponse = await fetch(url, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': jeton },
                            body: JSON.stringify({ question }),
                        });
                        const donnees = await reponse.json();
                        if (!reponse.ok) throw new Error(donnees.message || 'Erreur');

                        let html = formater(donnees.reponse);
                        if (donnees.liens?.length) {
                            html += '<div class="mt-2 d-flex flex-wrap gap-2">' + donnees.liens.map(l => {
                                const a = document.createElement('a');
                                a.href = l.url; a.textContent = '→ ' + l.texte; a.className = 'small';
                                return a.outerHTML;
                            }).join('') + '</div>';
                        }
                        html += '<div class="ha-msg-source">' + (donnees.source === 'ia' ? 'Rédigé par l’IA Gemini à partir des données HeatAlert' : 'Analyse automatique des alertes HeatAlert') + '</div>';
                        attente.innerHTML = html;
                    } catch (e) {
                        attente.textContent = 'Désolé, je n’ai pas pu répondre (' + e.message + '). Réessayez dans un instant.';
                    }
                    messages.scrollTop = messages.scrollHeight;
                };

                form.addEventListener('submit', (e) => { e.preventDefault(); envoyer(champ.value); });
                racine.querySelectorAll('.ha-suggestion').forEach(b => b.addEventListener('click', () => envoyer(b.textContent)));
            })();
        </script>
    @endpush
@endonce
