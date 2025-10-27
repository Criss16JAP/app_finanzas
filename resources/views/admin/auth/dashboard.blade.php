<!DOCTYPE html>
<html lang="es">
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Bienvenido al Panel de Administración</h1>

    <p>Hola, {{ Auth::guard('staff')->user()->name }}</p>

    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">Cerrar Sesión</button>
    </form>
</body>
</html>
