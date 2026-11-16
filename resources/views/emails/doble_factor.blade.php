<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Código de Seguridad</title>
</head>
<body style="font-family: Arial, sans-serif; text-align: center; padding: 20px; background-color: #f8f9fa;">
    <div style="max-width: 500px; margin: auto; background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #dee2e6;">
        <h2 style="color: #212529;">Verificación de Administrador</h2>
        <p style="color: #495057;">Ingresa el siguiente PIN de 6 dígitos para completar tu inicio de sesión:</p>
        
        <div style="background: #e9ecef; padding: 15px; margin: 20px 0; font-size: 28px; font-weight: bold; letter-spacing: 6px; border-radius: 6px;">
            {{ $codigo }}
        </div>

        <p style="color: #dc3545; font-size: 14px;"><strong>Importante:</strong> Este código tiene una vigencia máxima de <strong>5 minutos</strong>.</p>
        
        <hr style="margin: 25px 0; border: 0; border-top: 1px solid #dee2e6;">
        
        <p style="color: #495057; font-size: 14px;">También puedes validar tu acceso directamente desde este enlace temporal:</p>
        <a href="{{ $urlFirmada }}" style="display: inline-block; background: #0d6efd; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            Verificar Acceso Ahora
        </a>
    </div>
</body>
</html>