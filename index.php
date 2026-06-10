<?php
// ============================================
// INICIO DE SESIÓN Y CONFIGURACIÓN
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'Modelo/Usuario.php';

// Obtener parámetros
$entidad = $_GET['entidad'] ?? 'home';
$accion = $_GET['accion'] ?? 'ver';

// ============================================
// RUTAS PÚBLICAS
// ============================================
$rutasPublicas = [
    'home' => ['ver'],
    'auth' => ['mostrarLogin', 'procesarLogin', 'mostrarRegistro', 'procesarRegistro', 'logout'],
    'ubicacion' => ['ver'],
    'reclamacion' => ['mostrarFormulario', 'guardar'],
    'sugerencia' => ['mostrarFormulario', 'guardar'],
    'veterinario' => ['verPublico'],
    'video' => ['verPublico']
];

$esRutaPublica = isset($rutasPublicas[$entidad]) &&
                 in_array($accion, $rutasPublicas[$entidad]);

// Si no es ruta pública y no está logueado → redirigir
if (!$esRutaPublica && !Usuario::estaLogueado()) {
    $_SESSION['error'] = 'Debes iniciar sesión para acceder a esta página.';
    header("Location: index.php?entidad=auth&accion=mostrarLogin");
    exit;
}

// ============================================
// RUTAS DE ADMINISTRADOR
// ============================================
$rutasAdministrador = [
    'admin', 'dueno', 'mascota', 'cita',
    'veterinario', 'reporte'
];

if (in_array($entidad, $rutasAdministrador)) {
    if (!Usuario::esAdministrador()) {
        $_SESSION['error'] = 'No tienes permisos para esta sección.';
        header("Location: index.php?entidad=usuario&accion=dashboard");
        exit;
    }
}

// ============================================
// ACCIONES QUE HACEN REDIRECT (EJECUTAR ANTES DEL HTML)
// ============================================
$accionesRedirect = [
    'guardarNuevo', 'actualizar', 'eliminar', 'cambiarEstado',
    'procesarLogin', 'procesarRegistro', 'logout',
    'guardar', 'editarPerfil', 'cambiarPassword', 'cambiarEstadoUsuario'
];

// Si la acción hace redirect, ejecutar antes del HTML
if (in_array($accion, $accionesRedirect) || $entidad === 'auth' || $entidad === 'home') {
    $entidadCapitalizada = ucfirst(strtolower($entidad));
    $archivo = "Controlador/Controlador{$entidadCapitalizada}.php";
    
    if (file_exists($archivo)) {
        require_once $archivo;
        $clase = "Controlador{$entidadCapitalizada}";
        
        if (class_exists($clase)) {
            $controlador = new $clase();
            
            if (method_exists($controlador, $accion)) {
                $controlador->$accion();
                exit; // Importante: detener ejecución después del redirect
            }
        }
    }
}

// ============================================
// AQUÍ COMIENZA EL HTML (solo para páginas de visualización)
// ============================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria Ralah Pets</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary: #16a085;
      --primary-dark: #0e7664;
      --secondary: #3498db;
      --accent: #f39c12;
      --dark: #2c3e50;
      --white: #ffffff;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      color: var(--dark);
    }

    /* Header */
    header {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 20px rgba(0,0,0,0.1);
      padding: 20px 0;
      margin-bottom: 30px;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    header h2 {
      color: var(--primary);
      text-align: center;
      margin-bottom: 15px;
      font-weight: 700;
      font-size: 2em;
    }

    nav {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 10px;
    }

    nav a {
      color: var(--dark);
      text-decoration: none;
      padding: 12px 24px;
      font-weight: 500;
      border-radius: 8px;
      transition: all 0.3s ease;
      position: relative;
    }

    nav a:hover {
      color: var(--primary);
      background: rgba(22, 160, 133, 0.1);
    }

    nav a::after {
      content: '';
      position: absolute;
      bottom: 5px;
      left: 50%;
      transform: translateX(-50%);
      width: 0;
      height: 2px;
      background: var(--primary);
      transition: width 0.3s ease;
    }

    nav a:hover::after {
      width: 70%;
    }

    /* Main Container */
    main {
      max-width: 1200px;
      margin: 0 auto;
      background-color: var(--white);
      padding: 35px;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.15);
      animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Footer */
    footer {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      color: var(--dark);
      text-align: center;
      padding: 25px 20px;
      margin-top: 40px;
      box-shadow: 0 -2px 20px rgba(0,0,0,0.1);
    }

    footer p {
      font-weight: 500;
      margin: 0;
    }

    /* Alerts */
    .alert {
      border-radius: 12px;
      padding: 20px;
      margin: 20px 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
      header h2 {
        font-size: 1.5em;
      }

      nav {
        flex-direction: column;
        align-items: center;
      }

      nav a {
        width: 200px;
        text-align: center;
      }

      main {
        padding: 20px;
      }
    }
  </style>
</head>
<body>

    <header>
        <h2>🐾 Veterinaria Ralah Pets</h2>
        <nav>
            <a href="index.php?entidad=home&accion=ver">Inicio</a>

            <?php if (Usuario::estaLogueado()): ?>

                <?php if (Usuario::esAdministrador()): ?>
                    <!-- Menú para ADMINISTRADORES -->
                    <a href="index.php?entidad=admin&accion=dashboard">Dashboard Admin</a>
                    <a href="index.php?entidad=dueno&accion=listar">Dueños</a>
                    <a href="index.php?entidad=mascota&accion=listar">Mascotas</a>
                    <a href="index.php?entidad=cita&accion=listar">Citas</a>
                    <a href="index.php?entidad=veterinario&accion=listar">Veterinarios</a>
                    <a href="index.php?entidad=reporte&accion=ver">Reportes</a>
                    <a href="index.php?entidad=video&accion=listar">Videos</a>
                    <a href="index.php?entidad=reclamacion&accion=listar">Reclamaciones</a>
                    <a href="index.php?entidad=sugerencia&accion=listar">Sugerencias</a>
                <?php else: ?>
                    <!-- Menú para USUARIOS NORMALES -->
                    <a href="index.php?entidad=usuario&accion=dashboard">Mi cuenta</a>
                    <a href="index.php?entidad=video&accion=verPublico">Videos</a>
                    <a href="index.php?entidad=reclamacion&accion=mostrarFormulario">📝 Libro Reclamaciones</a>
                    <a href="index.php?entidad=sugerencia&accion=mostrarFormulario">💡 Sugerencias</a>
                    <a href="index.php?entidad=ubicacion&accion=ver">📍 Ubicación</a>
                <?php endif; ?>

                <a href="index.php?entidad=auth&accion=logout">Cerrar sesión</a>

            <?php else: ?>

                <a href="index.php?entidad=auth&accion=mostrarLogin">Iniciar sesión</a>

            <?php endif; ?>


        </nav>
    </header>

<main class="container">
<?php
// ============================================
// ENRUTAMIENTO PARA PÁGINAS DE VISUALIZACIÓN
// ============================================

$entidadCapitalizada = ucfirst(strtolower($entidad));
$archivo = "Controlador/Controlador{$entidadCapitalizada}.php";

if (file_exists($archivo)) {
    require_once $archivo;
    $clase = "Controlador{$entidadCapitalizada}";

    if (class_exists($clase)) {
        $controlador = new $clase();

        if (method_exists($controlador, $accion)) {
            $controlador->$accion();
        } else {
            echo "<div class='alert alert-warning'>Acción <strong>$accion</strong> no encontrada.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Clase $clase no encontrada.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Controlador $archivo no existe.</div>";
}
?>
</main>

<footer>
    <p>© <?= date('Y') ?> Veterinaria Ralah Pets</p>
</footer>

</body>
</html>