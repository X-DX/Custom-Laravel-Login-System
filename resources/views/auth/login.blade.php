
<x-layouts.auth title="Login Form">
    @session('success')
        <div class="alert alert-success" role="alert"> 
            {{ $value }}
        </div>
    @endsession

    @session('error')
        <div class="alert alert-danger" role="alert"> 
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
        
        <x-auth.login-header title="Sign In" subtitle="Enter your credentials" />

        <form class="login-form" id="loginForm" method="POST" action="{{ route('login.post') }}" onsubmit="encrypt(event)" novalidate>
        @csrf
            
            <x-auth.form-input type="email" id="email" name="email" label="Email" autocomplete="email" />
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <x-auth.form-input type="password" id="password" name="password" label="Password" autocomplete="current-password" />
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <div>                
                <label for="captcha">Captcha</label>
                <div class="captcha form-group">
                    <span>{!! captcha_img() !!}</span>
                    <button type="button" id="reload">↻</button>
                </div>
                <input type="text" name="captcha" required>
                @error('captcha')
                    <span class="alert alert-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror                
            </div>

            <div class="form-options">
                <div class="checkbox-wrapper">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember" class="checkbox-label">
                        <div class="checkbox-box"></div>
                        <span>Remember me</span>
                    </label>
                </div>
                <a href="{{ url('/forgot-password') }}" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="login-btn">
                <span class="btn-text">SIGN IN</span>
                <div class="btn-loader">
                    <div class="loader-bar"></div>
                    <div class="loader-bar"></div>
                    <div class="loader-bar"></div>
                </div>
            </button>
        </form>

        <div class="divider"><span>OR</span></div>

        <div class="social-login">
            <button type="button" class="social-btn"><span class="social-text">GOOGLE</span></button>
            <button type="button" class="social-btn"><span class="social-text">GITHUB</span></button>
        </div>

        <div class="signup-link">
            <span>No account? </span>
            <a href="{{ url('/registration') }}">Create one</a>
        </div>

        <x-auth.success-message />

    </x-auth.login-card>

</x-layouts.auth>
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
</script>

@if(session('force_login'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            title: "Already Logged In",
            text: "This account is already logged in elsewhere. Do you want to force logout the other session?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, force login",
            cancelButtonText: "No, cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit hidden form to force login
                document.getElementById('forceLoginForm').submit();
            }
        });
    </script>

    <form id="forceLoginForm" action="{{ route('force.login') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">
        <input type="hidden" name="password" value="{{ session('password') }}">
    </form>
@endif


<script>
    async function encrypt(event){
        event.preventDefault(); // Prevent default submission
        const pwd = document.getElementById("password");
        const form = document.getElementById("loginForm");
        
        // Hash with SHA-512
        const hash = await sha512(pwd.value);
        pwd.value = hash;

        form.submit();
    }

    async function sha512(str) {
    const buffer = await crypto.subtle.digest("SHA-512", new TextEncoder().encode(str));
    const hashArray = Array.from(new Uint8Array(buffer));
    return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
}
</script>
