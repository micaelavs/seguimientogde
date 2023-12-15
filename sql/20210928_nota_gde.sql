ALTER TABLE `{{{db_log}}}`.`nota_gde` 
ADD COLUMN `cant_dias` INT(10) NOT NULL DEFAULT 0 AFTER `fecha_vencimiento`;

ALTER TABLE `{{{db_app}}}`.`nota_gde` 
ADD COLUMN `cant_dias` INT(10) NOT NULL DEFAULT 0 AFTER `fecha_vencimiento`;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`nota_gde_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `nota_gde_tg_alta` AFTER INSERT ON `nota_gde` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.nota_gde(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_nota_gde`,`id_subtema`,`id_objeto`,`nota`,`fecha_recepcion`,
`fecha_vencimiento`,`cant_dias`,`fecha_accion`,`tipo`,`estado`,`remitente`,`id_reparticion`,`reparticion`,`referencia`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_subtema,NEW.id_objeto,NEW.nota,NEW.fecha_recepcion,
NEW.fecha_vencimiento,NEW.cant_dias,NEW.fecha_accion,NEW.tipo,NEW.estado,NEW.remitente,NEW.id_reparticion,NEW.reparticion,NEW.referencia,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`nota_gde_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `nota_gde_tg_modificacion` AFTER UPDATE ON `nota_gde` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.nota_gde(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_nota_gde`,`id_subtema`,`id_objeto`,`nota`,`fecha_recepcion`,
`fecha_vencimiento`,`cant_dias`,`fecha_accion`,`tipo`,`estado`,`remitente`,`id_reparticion`,`reparticion`,`referencia`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.id_subtema,NEW.id_objeto,NEW.nota,NEW.fecha_recepcion,
NEW.fecha_vencimiento,NEW.cant_dias,NEW.fecha_accion,NEW.tipo,NEW.estado,NEW.remitente,NEW.id_reparticion,NEW.reparticion,NEW.referencia,NEW.borrado);
END$$
DELIMITER ;



