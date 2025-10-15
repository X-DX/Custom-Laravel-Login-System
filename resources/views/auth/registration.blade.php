<x-layouts.auth title="Login Form">
    @session('success')
        <div class="alert alert-success" role="alert"> 
            {{ $value }}
        </div>
    @endsession
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-auth.login-card>
        
        <x-auth.login-header title="Registration" subtitle="Enter your credentials" />

        <form action="{{ route('register.post') }}" method="POST" class="login-form" id="loginForm" onsubmit="encryptform(event)" novalidate>
        @csrf
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <x-auth.form-input type="text" id="username" name="username" label="Username" value="{{ old('username') }}" autocomplete="name"  class="form-control"/>
            @error('username')
                <span class="text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <x-auth.form-input type="email" id="email" name="email" label="Email" value="{{ old('email') }}" autocomplete="email" class="form-control" />
            @error('email')
                <span class="text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <x-auth.form-input type="password" id="password" name="password" label="Password" autocomplete="current-password" class="form-control"/>
            <div id="password-strength" class="text-sm mt-1"></div>
            @error('password')
                <span class="text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror  

            <x-auth.form-input type="password" id="password_confirmation" name="password_confirmation" label="Password Confirmation" autocomplete="current-password" class="form-control"/>
            <div id="password-match" class="text-sm mt-1"></div>
            @error('password_confirmation')
                <span class="text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <div class="mb-3">
                <label for="captcha" class="form-label">Captcha</label>
                <div class="captcha d-flex align-items-center mb-2">
                    <span class="me-2">{!! captcha_img() !!}</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="reload">
                    ↻
                    </button>
                </div>
                <input type="text" class="form-control" name="captcha" placeholder="Enter Captcha" required>

                @error('captcha')
                    <div class="text-danger mt-1">
                    <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>


            <button type="submit" class="login-btn">
                <span class="btn-text">SIGN Up</span>
                <div class="btn-loader">
                    <div class="loader-bar"></div>
                    <div class="loader-bar"></div>
                    <div class="loader-bar"></div>
                </div>
            </button>
        </form>

        <div class="signup-link">
            <span>Already account? </span>
            <a href="{{url('/login')}}">SIgn In</a>
        </div>

        <x-auth.success-message />

    </x-auth.login-card>

</x-layouts.auth>
<style>
#password-strength, #password-match {
    font-weight: bold;
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#reload').click(function () {
        $.ajax({
            type: 'GET',
            url: '/captcha-refresh',
            success: function (data) {
                $(".captcha span").html(data.captcha);
            }
        });
    });

    async function encryptform(event){
        event.preventDefault(); // Prevent default submission

        const pwd = document.getElementById("password");
        const cpwd = document.getElementById("password_confirmation");
        const form = document.getElementById("loginForm");
        
        // Hash with SHA-512
        const hash1 = await sha512(pwd.value);
        const hash2 = await sha512(cpwd.value);

        pwd.value = hash1;
        cpwd.value = hash2;

        form.submit();
    }

    async function sha512(str) {
        const buffer = await crypto.subtle.digest("SHA-512", new TextEncoder().encode(str));
        const hashArray = Array.from(new Uint8Array(buffer));
        return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    }

    const pwdInput = document.getElementById('password');
    const cpwdInput = document.getElementById('password_confirmation');
    const strengthText = document.getElementById('password-strength');
    const matchText = document.getElementById('password-match');

    pwdInput.addEventListener('input', updateStrength);
    cpwdInput.addEventListener('input', checkMatch);

    function updateStrength(){
        const value = pwdInput.value;
        let strength = 0;

        // Password strength rules
        if (value.length >= 8) strength++; // Length
        if (/[A-Z]/.test(value)) strength++; // Uppercase letter
        if (/[a-z]/.test(value)) strength++; // Lowercase letter
        if (/[0-9]/.test(value)) strength++; // Number
        if (/[^A-Za-z0-9]/.test(value)) strength++; // Special character

        // Decide the strength level
        let msg = '';
        let color = '';
        switch (strength) {
            case 0:
            case 1:
                msg = 'Very Weak';
                color = 'red';
                break;
            case 2:
                msg = 'Weak';
                color = 'orange';
                break;
            case 3:
                msg = 'Medium';
                color = '#d1c500';
                break;
            case 4:
                msg = 'Strong';
                color = 'green';
                break;
            case 5:
                msg = 'Very Strong';
                color = 'darkgreen';
                break;
        }
        strengthText.textContent = msg;
        strengthText.style.color = color;

        // Re-check confirmation in case password changes
        checkMatch();
    }
    function checkMatch() {
        if (cpwdInput.value === '') {
            matchText.textContent = '';
            return;
        }
        if (pwdInput.value === cpwdInput.value) {
            matchText.textContent = 'Passwords match ✅';
            matchText.style.color = 'green';
        } else {
            matchText.textContent = 'Passwords do not match ❌';
            matchText.style.color = 'red';
        }
    }
</script>
