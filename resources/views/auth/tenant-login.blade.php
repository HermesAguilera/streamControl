<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Empresa</title>
</head>
<body>
    <h1>Iniciar sesion</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('tenant.login.store') }}">
        @csrf

        <label for="email">Correo</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>

        <label for="password">Contrasena</label>
        <input id="password" type="password" name="password" required>

        <label for="remember">
            <input id="remember" type="checkbox" name="remember" value="1"> Recordarme
        </label>

        <button type="submit">Iniciar sesion</button>
    </form>
</body>
</html>
