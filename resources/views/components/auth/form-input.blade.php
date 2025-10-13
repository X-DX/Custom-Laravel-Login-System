<div class="form-group">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <div class="input-wrapper {{ $type === 'password' ? 'password-wrapper' : '' }}">
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            value="{{ old($name) }}" 
            {{ $attributes->merge(['class' => 'form-control']) }}  {{-- merge default + custom classes --}}
            autocomplete="{{ $autocomplete ?? 'off' }}"
            required   
        >
        @if($type === 'password')
            <button type="button" class="password-toggle" aria-label="Toggle password visibility" onclick="togglePassword('{{ $id }}', 'toggle-text-{{ $id }}')">
                <span id="toggle-text-{{ $id }}">SHOW</span>
            </button>
        @endif
    </div>
    <span class="error-message" id="{{ $id }}Error"></span>
</div>
<script>
    function togglePassword(inputId, toggleTextId) {
        const passwordInput = document.getElementById(inputId);
        const toggleText = document.getElementById(toggleTextId);

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleText.textContent = "HIDE";
        } else {
            passwordInput.type = "password";
            toggleText.textContent = "SHOW";
        }
    }
</script>