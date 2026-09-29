{{-- Usage : <x-form.select name="zone_id" label="Zone" :options="$zones->pluck('nom', 'id')" :value="$coupure->zone_id ?? null" /> --}}
@props(['name', 'label' => null, 'options' => [], 'value' => null, 'placeholder' => '-- Choisir --'])

<div class="mb-3">
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }} @if ($attributes->has('required'))<span class="text-danger">*</span>@endif
        </label>
    @endif

    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->class(['form-select', 'is-invalid' => $errors->has($name)]) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) old($name, $value) === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
