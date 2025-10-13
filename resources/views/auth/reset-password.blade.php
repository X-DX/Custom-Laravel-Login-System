<form action="{{ url('/reset-password') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <label>Email:</label>
    <input type="email" name="email" required>
    <label>New Password:</label>
    <input type="password" name="password" required>
    <label>Confirm Password:</label>
    <input type="password" name="password_confirmation" required>
    <button type="submit">Reset Password</button>
</form>
