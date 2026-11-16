<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido</title>
</head>
<body style="font-family: Arial, sans-serif; text-align: center; padding: 20px; background-color: #f8f9fa;">
    <div style="max-width: 500px; margin: auto; background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #dee2e6;">
        <h2 style="color: #212529;">¡Hola, {{ $user->name }}!</h2>
        <p style="color: #495057;">Tu cuenta ha sido creada exitosamente en el Portal de Prácticas Web UTT.</p>
        <p style="color: #495057;">Correo registrado: <strong>{{ $user->email }}</strong></p>
        <p style="color: #495057;">Rol asignado: <strong style="text-transform: uppercase;">{{ $user->rol }}</strong></p>
    </div>
</body>
</html>