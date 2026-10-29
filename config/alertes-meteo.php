<?php

/*
|--------------------------------------------------------------------------
| Module Alertes météo : configuration
|--------------------------------------------------------------------------
| L'assistant canicule fonctionne sans aucune clé (analyse locale de la question).
| Pour des réponses rédigées par l'IA Google Gemini, ajouter dans le fichier .env :
|   GEMINI_API_KEY=votre_cle      (gratuite sur https://aistudio.google.com/apikey)
|   GEMINI_MODEL=gemini-2.5-flash (optionnel)
*/

return [
    'assistant' => [
        'gemini_key' => env('GEMINI_API_KEY'),
        'gemini_model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'timeout' => 15,
    ],
];
