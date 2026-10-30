-- 1. Agregar columna activo si no existe (usando IF NOT EXISTS de MariaDB)
ALTER TABLE `duenos` ADD COLUMN IF NOT EXISTS `activo` TINYINT(1) NOT NULL DEFAULT 1 AFTER `direccion`;
ALTER TABLE `mascotas` ADD COLUMN IF NOT EXISTS `activo` TINYINT(1) NOT NULL DEFAULT 1 AFTER `dni_dueno`;
ALTER TABLE `veterinarios` ADD COLUMN IF NOT EXISTS `activo` TINYINT(1) NOT NULL DEFAULT 1 AFTER `telefono`;

-- 2. Limpiar foreign keys previas si existen
ALTER TABLE `citas` DROP FOREIGN KEY IF EXISTS `citas_ibfk_1`;
ALTER TABLE `citas` DROP FOREIGN KEY IF EXISTS `citas_ibfk_2`;
ALTER TABLE `mascotas` DROP FOREIGN KEY IF EXISTS `mascotas_ibfk_1`;

-- 3. Aplicar ON DELETE RESTRICT para que borrar nunca destruya el historial
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`id_veterinario`) REFERENCES `veterinarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `mascotas`
  ADD CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`dni_dueno`) REFERENCES `duenos` (`dni`) ON DELETE RESTRICT ON UPDATE CASCADE;
