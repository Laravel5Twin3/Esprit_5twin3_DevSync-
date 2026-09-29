{{-- Messages flash : ->with('success', '...') / ->with('error', '...') --}}
@foreach (['success' => 'check-circle', 'error' => 'exclamation-triangle', 'warning' => 'exclamation-circle', 'info' => 'info-circle'] as $type => $icon)
    @if (session($type))
        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show" role="alert">
            <i class="bi bi-{{ $icon }}"></i> {{ session($type) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
@endforeach
