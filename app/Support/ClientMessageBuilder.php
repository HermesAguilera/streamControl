<?php

namespace App\Support;

use App\Models\Perfil;

class ClientMessageBuilder
{
    public static function buildDeliveryMessage(Perfil $perfil): string
    {
        $plataforma = trim((string) ($perfil->plataforma?->nombre ?? 'tu plataforma'));
        $correo = trim((string) ($perfil->correo_cuenta ?: '-'));
        $contrasena = trim((string) ($perfil->contrasena_cuenta ?: '-'));
        $numeroPerfil = trim((string) ($perfil->nombre_perfil ?: '-'));
        $pin = trim((string) ($perfil->pin ?: '-'));
        $fechaInicio = $perfil->fecha_inicio?->format('d/m/Y') ?? '-';
        $fechaVencimiento = $perfil->fecha_caducidad_cuenta?->format('d/m/Y')
            ?? $perfil->fecha_corte?->format('d/m/Y')
            ?? '-';

        return "¡Hola! 👋 ¡Gracias por tu compra! Aquí tienes los detalles de tu acceso a {$plataforma}\n"
            . "📧 Correo: {$correo}\n"
            . "🔑 Contraseña: {$contrasena}\n"
            . "👤 Perfil asignado: Perfil #{$numeroPerfil}\n"
            . "📌 PIN de perfil: {$pin}\n"
            . "🗓️ Fecha de inicio: {$fechaInicio}\n"
            . "📅 Fecha de corte: {$fechaVencimiento}\n"
            . "¡Muchas gracias por confiar en nosotros para tu entretenimiento! Disfruta tu contenido. 🍿✨";
    }

    public static function buildExpiryReminderMessage(Perfil $perfil): string
    {
        $plataforma = trim((string) ($perfil->plataforma?->nombre ?? 'No definida'));
        $correo = trim((string) ($perfil->correo_cuenta ?: '-'));
        $diasRestantes = $perfil->dias_restantes;
        $tiempoRestante = is_numeric($diasRestantes)
            ? (((int) $diasRestantes) === 1 ? '1 día' : ((int) $diasRestantes) . ' días')
            : '2 días';

        return "Hola! 👋 Solo un recordatorio de que tu perfil vence muy pronto.\n\n"
            . "📺 Plataforma: {$plataforma}\n"
            . "📧 Cuenta: {$correo}\n"
            . "⏳ Tiempo restante: {$tiempoRestante}\n\n"
            . "Evita perder tu historial y recomendaciones renovando a tiempo. ¡Gracias por tu preferencia! 🎬💳";
    }

    public static function buildExpiryTodayAlertMessage(Perfil $perfil): string
    {
        $plataforma = trim((string) ($perfil->plataforma?->nombre ?? 'No definida'));
        $correo = trim((string) ($perfil->correo_cuenta ?: '-'));

        return "Hola! 👋 Tu acceso está a punto de expirar. Hoy es tu último día de servicio.\n"
            . "📺 Plataforma: {$plataforma}\n"
            . "📧 Cuenta: {$correo}\n"
            . "⚠️ Estado: Vence hoy\n\n"
            . "Para mantener tu perfil activo y no perder el acceso, por favor realiza tu renovación antes de las 04:00 PM.\n\n"
            . "¡No te quedes sin acceso a tu contenido favorito! 🍿⏳";
    }

    public static function buildExpiryMessage(Perfil $perfil): string
    {
        $diasRestantes = $perfil->dias_restantes;

        if (is_numeric($diasRestantes) && (int) $diasRestantes === 0) {
            return self::buildExpiryTodayAlertMessage($perfil);
        }

        return self::buildExpiryReminderMessage($perfil);
    }
}
