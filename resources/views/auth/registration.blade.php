<x-layouts.auth title="Login Form">
    @session('success')
        <div class="alert alert-success" role="alert"> 
            {{ $value }}
        </div>
    @endsession

    <x-auth.login-card>
        
        <x-auth.login-header title="Registration" subtitle="Enter your credentials" />

        <form action="{{ route('register.post') }}" method="POST" class="login-form" id="loginForm" novalidate>
        @csrf
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <x-auth.form-input type="text" id="username" name="username" label="Username" autocomplete="name"  class="form-control @error('username') is-invalid @enderror"/>
            @error('username')
                <span class="text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <x-auth.form-input type="email" id="email" name="email" label="Email" autocomplete="email" class="form-control @error('email') is-invalid @enderror" />
            @error('email')
                <span class="text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <x-auth.form-input type="password" id="password" name="password" label="Password" autocomplete="current-password" class="form-control @error('password') is-invalid @enderror"/>
            @error('password')
                <span class="text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror  
            

            <x-auth.form-input type="password" id="password_confirmation" name="password_confirmation" label="Password Confirmation" autocomplete="current-password" class="form-control @error('password_confirmation') is-invalid @enderror"/>
            @error('password_confirmation')
                <span class="text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <div>
                <label for="captcha">Captcha</label>
                <div class="captcha">
                    <span>{!! captcha_img() !!}</span>
                    <button type="button" id="reload">↻</button>
                </div>
                <input type="text" name="captcha" required>
                @error('captcha')
                    <span class="text-red-500" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
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