<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\HelpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpRequestController extends Controller
{
    public function index()
    {
        $requests = HelpRequest::with('user')
            ->withCount('responses')
            ->latest()
            ->paginate(12);

        return view(
            'front.entraide.demandes.index',
            compact('requests')
        );
    }

    public function create()
    {
        return view(
            'front.entraide.demandes.create'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'needed_from' => [
                'nullable',
                'date',
            ],

            'needed_until' => [
                'nullable',
                'date',
                'after_or_equal:needed_from',
            ],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'open';

        HelpRequest::create($validated);

        return redirect()
            ->route('help-requests.index')
            ->with(
                'success',
                'Votre demande d’aide a été publiée.'
            );
    }

    public function show(HelpRequest $helpRequest)
    {
        $helpRequest->load([
            'user',
            'responses.user',
        ]);

        return view(
            'front.entraide.demandes.show',
            compact('helpRequest')
        );
    }

    public function edit(HelpRequest $helpRequest)
    {
        $this->authorizeOwner($helpRequest);

        return view(
            'front.entraide.demandes.edit',
            compact('helpRequest')
        );
    }

    public function update(
        Request $request,
        HelpRequest $helpRequest
    ) {
        $this->authorizeOwner($helpRequest);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'needed_from' => [
                'nullable',
                'date',
            ],

            'needed_until' => [
                'nullable',
                'date',
                'after_or_equal:needed_from',
            ],

            'status' => [
                'required',
                'in:open,in_progress,completed,cancelled',
            ],
        ]);

        $helpRequest->update($validated);

        return redirect()
            ->route(
                'help-requests.show',
                $helpRequest
            )
            ->with(
                'success',
                'Votre demande a été modifiée.'
            );
    }

    public function destroy(HelpRequest $helpRequest)
    {
        $this->authorizeOwner($helpRequest);

        $helpRequest->delete();

        return redirect()
            ->route('help-requests.index')
            ->with(
                'success',
                'Votre demande a été supprimée.'
            );
    }

    private function authorizeOwner(
        HelpRequest $helpRequest
    ): void {
        abort_unless(
            $helpRequest->user_id === Auth::id(),
            403
        );
    }
}
