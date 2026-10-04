<?php

namespace Database\Seeders;

use App\Models\HelpRequest;
use App\Models\HelpResponse;
use App\Models\User;
use Illuminate\Database\Seeder;

class HelpResponseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $requests = HelpRequest::all();

        if ($users->count() < 4 || $requests->count() < 4) {
            $this->command->warn(
                'Il faut au moins 4 utilisateurs et 4 demandes.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Demande #1 : plusieurs personnes répondent
        |--------------------------------------------------------------------------
        */

        $request = $requests[0];

        HelpResponse::create([
            'help_request_id' => $request->id,
            'user_id' => $users[0]->id,
            'message' => 'Je peux venir aider samedi matin. Je peux également porter les meubles.',
            'status' => 'accepted',
        ]);

        HelpResponse::create([
            'help_request_id' => $request->id,
            'user_id' => $users[1]->id,
            'message' => 'Je suis disponible et je peux venir avec ma voiture.',
            'status' => 'accepted',
        ]);

        HelpResponse::create([
            'help_request_id' => $request->id,
            'user_id' => $users[2]->id,
            'message' => 'Je peux aider à porter les cartons.',
            'status' => 'rejected',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Demande #2
        |--------------------------------------------------------------------------
        */

        $request = $requests[1];

        HelpResponse::create([
            'help_request_id' => $request->id,
            'user_id' => $users[1]->id,
            'message' => 'Je peux faire les courses demain matin.',
            'status' => 'pending',
        ]);

        HelpResponse::create([
            'help_request_id' => $request->id,
            'user_id' => $users[2]->id,
            'message' => 'Je suis également disponible demain.',
            'status' => 'pending',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Demande #3
        |--------------------------------------------------------------------------
        */

        $request = $requests[2];

        HelpResponse::create([
            'help_request_id' => $request->id,
            'user_id' => $users[0]->id,
            'message' => 'Je peux vous aider avec votre ordinateur.',
            'status' => 'accepted',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Demande #4
        |--------------------------------------------------------------------------
        */

        $request = $requests[3];

        HelpResponse::create([
            'help_request_id' => $request->id,
            'user_id' => $users[3]->id,
            'message' => 'Je peux venir samedi matin pour vous aider.',
            'status' => 'pending',
        ]);
    }
}
