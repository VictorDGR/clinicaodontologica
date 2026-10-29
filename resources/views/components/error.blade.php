@props(['field'])

@error($field)
    <small class="field-error">{{ $message }}</small>
@enderror
