-- 1. Asegurar unicidad en usuarios.dni y duenos.id_usuario
ALTER TABLE `usuarios` ADD UNIQUE INDEX `idx_usuarios_dni_unique` (`dni`);
ALTER TABLE `duenos` ADD UNIQUE INDEX `idx_duenos_id_usuario_unique` (`id_usuario`);

-- 2. Permitir actualizar DNI en cascada hacia mascotas
ALTER TABLE `mascotas` DROP FOREIGN KEY `mascotas_ibfk_1`;
ALTER TABLE `mascotas` 
  ADD CONSTRAINT `mascotas_ibfk_1` 
  FOREIGN KEY (`dni_dueno`) REFERENCES `duenos` (`dni`) 
  ON DELETE CASCADE ON UPDATE CASCADE;

-- 3. Tabla para control de fuerza bruta en login
CREATE TABLE IF NOT EXISTS `login_intentos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(100) NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `intentos` INT(11) NOT NULL DEFAULT 1,
    `bloqueado_hasta` TIMESTAMP NULL DEFAULT NULL,
    `ultimo_intento` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_email_ip` (`email`, `ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
