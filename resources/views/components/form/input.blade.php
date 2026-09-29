{{-- Usage : <x-form.input name="titre" label="Titre" :value="$coupure->titre ?? ''" required /> --}}
@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'help' => null])

<div class="mb-3">
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }} @if ($attributes->has('required'))<span class="text-danger">*</span>@endif
        </label>
    @endif

    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
           value="{{ $type === 'password' ? '' : old($name, $value) }}"
           {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>

    @if ($help)
        <div class="form-text">{{ $help }}</div>
    @endif
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
