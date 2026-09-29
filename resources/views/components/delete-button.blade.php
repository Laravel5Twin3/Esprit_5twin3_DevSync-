{{-- Usage : <x-delete-button :action="route('admin.coupures.destroy', $coupure)" /> --}}
@props(['action', 'label' => 'Supprimer', 'confirm' => 'Confirmer la suppression ?'])

<form method="POST" action="{{ $action }}" class="d-inline" onsubmit="return confirm(@js($confirm))">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->merge(['class' => 'btn btn-sm btn-outline-danger']) }}>
        <i class="bi bi-trash"></i> {{ $label }}
    </button>
</form>
