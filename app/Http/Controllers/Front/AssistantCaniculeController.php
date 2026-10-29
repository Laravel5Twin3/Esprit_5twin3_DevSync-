<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\AlerteMeteo\AssistantCanicule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Point d'entrée du chatbot « Assistant canicule » (appelé en JavaScript, réponse JSON).
 */
class AssistantCaniculeController extends Controller
{
    public function repondre(Request $request, AssistantCanicule $assistant): JsonResponse
    {
        $donnees = $request->validate(
            ['question' => ['required', 'string', 'min:2', 'max:500']],
            ['question.required' => 'Écrivez votre question.', 'question.max' => 'Votre question est trop longue (500 caractères maximum).'],
        );

        return response()->json($assistant->repondre($donnees['question']));
    }
}
