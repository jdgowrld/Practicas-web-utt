<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prácticas Web - UTT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ Auth::check() ? route('home') : route('login') }}">
                UTT | Prácticas Web
            </a>

            @auth
            <div class="d-flex align-items-center gap-3">
                <span class="text-light small d-none d-md-inline">
                    {{ Auth::user()->name }} 
                    <span class="badge bg-primary text-uppercase ms-1">{{ Auth::user()->rol }}</span>
                </span>
                <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm">Menú Principal</a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Salir</button>
                </form>
            </div>
            @endauth
        </div>
    </nav>

    <main class="py-4 flex-grow-1">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>