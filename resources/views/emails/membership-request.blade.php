<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud de Membresía</title>
</head>
<body>
    <div style="font-family: sans-serif; line-height: 1.6; color: #333;">
        <h2 style="color: #1e40af;">Nueva Solicitud de Membresía</h2>
        <p>Se ha recibido una nueva solicitud de membresía a través del sitio web.</p>
        <hr style="border: 1px solid #ddd; margin: 20px 0;">
        <p><strong>Plan Solicitado:</strong> {{ $formData['plan_name'] }}</p>
        <p><strong>Nombre:</strong> {{ $formData['name'] }}</p>
        <p><strong>Correo Electrónico:</strong> {{ $formData['email'] }}</p>
        <hr style="border: 1px solid #ddd; margin: 20px 0;">
    </div>
</body>
</html>