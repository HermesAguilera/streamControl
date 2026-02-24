<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Mensaje de Contacto</title>
</head>
<body>
    <div style="font-family: sans-serif; line-height: 1.6; color: #333;">
        <h2 style="color: #1e40af;">Nuevo Mensaje del Formulario de Contacto</h2>
        <p><strong>De:</strong> {{ $formData['name'] }}</p>
        <p><strong>Correo:</strong> {{ $formData['email'] }}</p>
        <hr style="border: 1px solid #ddd; margin: 20px 0;">
        <p><strong>Mensaje:</strong></p>
        <p>{{ $formData['message'] }}</p>
    </div>
</body>
</html>