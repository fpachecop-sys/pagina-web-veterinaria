ALTER TABLE `citas` ADD INDEX `idx_citas_fecha` (`fecha`);
ALTER TABLE `citas` ADD INDEX `idx_citas_vet_fecha_hora` (`id_veterinario`, `fecha`, `hora`);
ALTER TABLE `citas` ADD COLUMN `slot_activo` TINYINT(1) AS (IF(`estado` = 'Cancelada', NULL, 1)) VIRTUAL;
ALTER TABLE `citas` ADD UNIQUE INDEX `uk_vet_fecha_hora_activo` (`id_veterinario`, `fecha`, `hora`, `slot_activo`);