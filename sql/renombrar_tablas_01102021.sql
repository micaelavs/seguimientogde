#SIEMPRE SE ACTUALIZARA LA VERSIÓN DE LAS DOS DBs AUNQUE NO SE HAGAN CAMBIOS.
#REMPLAZAR ANTES DE EJECUTAR
# {{{user_mysql}}}  = REEMPLAZAR POR NOMBRE USER QUE EJECUTA.
# {{{db_log}}}      = REEMPLAZAR POR NOMBRE DB LOG.
# {{{db_app}}}      = REEMPLAZAR POR NOMBRE DB APP.

/* CAMBIOS REALIZADOS A LA BD POR NUEVOS REQUERIMIENTOS  */

RENAME TABLE`{{{db_app}}}`.`objeto` TO area,
    `{{{db_app}}}`.`subtema` TO objeto;

ALTER TABLE `{{{db_app}}}`.`objeto`
CHANGE COLUMN  `id_objeto` `id_area` int(10) unsigned NOT NULL;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`objeto_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `area_tg_alta` AFTER INSERT ON `area` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.area(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_area`,`nombre`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.nombre,NEW.borrado);
END $$
DELIMITER ;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`objeto_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `area_tg_modificacion` AFTER UPDATE ON `area` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.area(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_area`,`nombre`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.nombre,NEW.borrado);
END $$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`subtema_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `objeto_tg_alta` AFTER INSERT ON `objeto` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.objeto(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_objeto`,`nombre`,`id_area`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.nombre,NEW.id_area,NEW.borrado);
END $$
DELIMITER ;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`subtema_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `objeto_tg_modificacion` AFTER UPDATE ON `objeto` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.objeto(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_objeto`,`nombre`,`id_area`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.nombre,NEW.id_area,NEW.borrado);
END $$
DELIMITER ;

RENAME TABLE`{{{db_log}}}`.`objeto` TO area,
    `{{{db_log}}}`.`subtema` TO objeto;

ALTER TABLE `{{{db_log}}}`.`objeto`
CHANGE COLUMN  `id_objeto` `id_area` int(10) unsigned NOT NULL;

ALTER TABLE `{{{db_log}}}`.`objeto`
CHANGE COLUMN  `id_subtema` `id_objeto` int(10) unsigned NOT NULL;

DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`objeto_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `area_tg_insert` AFTER INSERT ON `area` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'area');
END $$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`subtema_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `objeto_tg_insert` AFTER INSERT ON `objeto` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'objeto');
END $$
DELIMITER ;


/* SE ANEXA CODIGO DE TABLAS PARA AGREGAR A esquema_inicial.sql POR CAMBIOS DE NUEVOS REQUERIMIENTOS */

CREATE TABLE IF NOT EXISTS `{{{db_log}}}`.`area` (
#CAMPOS DE SEGUIMIENTO
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
#CAMPOS DE LA TABLA TRACKEADA CON SU CAMPO "id" RENOMBRADO PARA EVITAR DUPLICIDAD
  `id_area` int(11) unsigned NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;





DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`area_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `area_tg_insert` AFTER INSERT ON `area` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'area');
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `{{{db_log}}}`.`objeto` (
#CAMPOS DE SEGUIMIENTO
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
#CAMPOS DE LA TABLA TRACKEADA CON SU CAMPO "id" RENOMBRADO PARA EVITAR DUPLICIDAD
  `id_objeto` int(10) unsigned NOT NULL,
  `id_area` int(10) unsigned NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`objeto_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `objeto_tg_insert` AFTER INSERT ON `objeto` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'objeto');
END $$
DELIMITER ;



CREATE TABLE IF NOT EXISTS `{{{db_app}}}`.`area` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`area_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `area_tg_alta` AFTER INSERT ON `area` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.area(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_area`,`nombre`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.nombre,NEW.borrado);
END $$
DELIMITER ;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`area_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `area_tg_modificacion` AFTER UPDATE ON `area` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.area(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_area`,`nombre`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.nombre,NEW.borrado);
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `{{{db_app}}}`.`objeto` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_area` int(10) unsigned NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
  KEY `fk_objeto_1_idx` (`id_area`),
  CONSTRAINT `fk_objeto_1` FOREIGN KEY (`id_area`) REFERENCES `area` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`objeto_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `objeto_tg_alta` AFTER INSERT ON `objeto` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.objeto(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_objeto`,`nombre`,`id_area`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.nombre,NEW.id_area,NEW.borrado);
END $$
DELIMITER ;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`objeto_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `objeto_tg_modificacion` AFTER UPDATE ON `objeto` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.objeto(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_objeto`,`nombre`,`id_area`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.nombre,NEW.id_area,NEW.borrado);
END $$
DELIMITER ;