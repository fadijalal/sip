<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIP - App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body style="margin:0;min-height:100vh;background:#f4f6fb;">
    <div id="app"></div>

    <script>
        window.__AUTH_USER__ = @json(auth()->user());
    </script>
    <script type="module" src="http://localhost:3000/@@vite/client"></script>
    <script type="module" src="http://localhost:3000/src/main.js"></script>
</body>
</html>
