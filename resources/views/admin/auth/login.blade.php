<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    </head>
<body>
    <h2>Login de Administración</h2>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div style="margin-top: 1rem;">
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div style="margin-top: 1rem;">
            <label for="remember">
                <input id="remember" type="checkbox" name="remember">
                <span>Recordarme</span>
            </label>
        </div>

        <div style="margin-top: 1rem;">
            <button type="submit">
                Iniciar Sesión
            </button>
        </div>
    </form>
</body>
</html>
