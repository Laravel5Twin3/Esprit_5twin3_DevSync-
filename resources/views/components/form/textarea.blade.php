{{-- Usage : <x-form.textarea name="description" label="Description" :value="$coupure->description ?? ''" rows="4" /> --}}
@props(['name', 'label' => null, 'value' => null])

<div class="mb-3">
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }} @if ($attributes->has('required'))<span class="text-danger">*</span>@endif
        </label>
    @endif

    <textarea id="{{ $name }}" name="{{ $name }}"
              {{ $attributes->merge(['rows' => 3])->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>{{ old($name, $value) }}</textarea>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
