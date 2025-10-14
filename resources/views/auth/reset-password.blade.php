<form action="{{ url('/reset-password') }}" method="POST" id="forgetform" onsubmit="encryptform(event)">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <label>Email:</label>
    <input type="email" name="email" required>
    <label>New Password:</label>
    <input type="password" name="password" id="password" required>
    <label>Confirm Password:</label>
    <input type="password" name="password_confirmation" id="password_confirmation" required>
    <button type="submit">Reset Password</button>
</form>
<script>
    async function encryptform(event){
        event.preventDefault(); // Prevent default submission

        const pwd = document.getElementById("password");
        const cpwd = document.getElementById("password_confirmation");
        const form = document.getElementById("forgetform");
        
        // Hash with SHA-512
        const hash = await sha512(pwd.value);
        pwd.value = hash;
        cpwd.value = hash;
        form.submit();
    }

    async function sha512(str) {
        const buffer = await crypto.subtle.digest("SHA-512", new TextEncoder().encode(str));
        const hashArray = Array.from(new Uint8Array(buffer));
        return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    }
</script>