<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Auth Page' }}</title>
    <link rel="stylesheet" href="{{ asset('auth/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <div class="login-container">
        {{ $slot }}
    </div>

    <script src="{{ asset('auth/form-utils.js') }}"></script>
    <script src="{{ asset('script.js') }}"></script>
</body>
</html>
