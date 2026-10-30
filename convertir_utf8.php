<?php

$dirs = ["app", "tests", "Controlador", "Modelo", "Vista/veterinarios"];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;

    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

    foreach ($it as $file) {
        if ($file->isFile() && $file->getExtension() === "php") {
            $path = $file->getPathname();
            $content = file_get_contents($path);

            // Eliminar BOM si ya existe
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

            // Si no está en UTF-8 válido, convertir desde Windows-1252
            if (!mb_check_encoding($content, 'UTF-8')) {
                $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
                file_put_contents($path, $content);
                echo "Convertido a UTF-8: $path\n";
            } else {
                // Si ya era UTF-8, asegurarse de guardar sin BOM
                file_put_contents($path, $content);
            }
        }
    }
}

echo "Proceso de conversión completado.\n";