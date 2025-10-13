<form action="{{ url('/forgot-password') }}" method="POST">
    @csrf
    <label>Email:</label>
    <input type="email" name="email" required>
    <button type="submit">Send Reset Link</button>
</form>

@if (session('message'))
    <p>{{ session('message') }}</p>
@endif
