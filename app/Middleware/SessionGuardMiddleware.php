<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repositories\UsuarioRepository;
use App\Support\Database;
use App\Support\Session;

final class SessionGuardMiddleware
{
    public static function verifyActiveUser(): void
    {
        Session::start();
        $userId = Session::get('usuario_id');
        if ($userId === null) {
            return;
        }

        $repo = new UsuarioRepository(Database::getConnection());
        $user = $repo->findById((int) $userId);

        if ($user === null || (int) $user['activo'] !== 1) {
            Session::destroy();
            Session::start();
            Session::setFlash('error', 'Tu cuenta ha sido desactivada o ya no existe.');
            header('Location: index.php?entidad=auth&accion=mostrarLogin');
            exit();
        }

        // Sincronizar rol en caso de cambio administrativo
        Session::set('usuario_rol', (string) $user['rol']);
    }
}
