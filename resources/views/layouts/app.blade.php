<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPPL - Gestión Integral para Plantas Lácteas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .pricing-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        .hero {
            background: linear-gradient(rgba(255, 255, 255, 0.85), rgba(255, 255, 255, 0.7)), url('https://images.unsplash.com/photo-1603415526960-f7e04188794a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
        }
        .feature-icon {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        .nav-scrolled {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(8px);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .section-nav-link.active {
            position: relative;
            font-weight: 700;
            color: #1e40af; /* Color azul más oscuro para el activo */
        }
        .section-nav-link.active::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -5px;
            transform: translateX(-50%);
            width: 50%;
            height: 3px;
            background-color: #1e40af;
            border-radius: 9999px;
        }
        .scroll-reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .scroll-reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <nav id="navbar" class="fixed top-0 w-full z-50 flex justify-between items-center px-6 py-4 bg-white bg-opacity-70 backdrop-blur-sm transition-all duration-300 border-b border-gray-200">
        <div class="container mx-auto max-w-7xl flex justify-between items-center">
            <a href="#inicio" class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="SPPL Logo" class="mr-3 w-32">
            </a>
            <div class="space-x-4 flex items-center">
                <a href="#inicio" class="px-4 py-2 text-blue-700 hover:bg-blue-100 rounded-full transition font-semibold section-nav-link">Inicio</a>
                <a href="#modulos" class="px-4 py-2 text-blue-700 hover:bg-blue-100 rounded-full transition font-semibold section-nav-link">Módulos</a>
                <a href="#planes" class="px-4 py-2 text-blue-700 hover:bg-blue-100 rounded-full transition font-semibold section-nav-link">Planes</a>
                <a href="{{ url('/admin/login') }}" class="px-4 py-2 bg-blue-700 text-white rounded-full hover:bg-blue-600 transition font-semibold">Login</a>
            </div>
        </div>
    </nav>

    {{-- Aquí se insertará el contenido de cada página --}}
    @yield('content')

    <footer class="bg-gray-900 text-gray-400 py-8 text-center text-sm">
        <div class="container mx-auto">
            <p>&copy; {{ date('Y') }} SPPL. Todos los derechos reservados.</p>
            <p class="mt-1">San Marcos de Colon, Choluteca Department, Honduras</p>
        </div>
    </footer>

    <script>
        // El JavaScript también se puede mover aquí para que esté en todas las páginas
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('nav-scrolled');
            } else {
                navbar.classList.remove('nav-scrolled');
            }
        });

        // Para la página principal, el resaltado de enlaces funciona.
        // Para las páginas internas, necesitarás un script diferente.
    </script>
</body>
</html>