@props([
    'name' => 'password',
    'placeholder' => '',
    'required' => false,
    'inputClass' => 'dm-input',
    'autocomplete' => null,
])

<div class="relative">
    <input
        type="password"
        name="{{ $name }}"
        class="{{ $inputClass }} pr-11"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
    >
    <button
        type="button"
        data-password-toggle
        aria-label="Tampilkan password"
        data-show-label="Tampilkan password"
        data-hide-label="Sembunyikan password"
        class="absolute right-2 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-[6px] text-slate-500 hover:bg-blue-50 hover:text-[#2385dd]"
    >
        <span data-password-icon-show>
            <x-diagnomed.icon name="eye" />
        </span>
        <span data-password-icon-hide class="hidden">
            <x-diagnomed.icon name="eye-off" />
        </span>
    </button>
</div>
