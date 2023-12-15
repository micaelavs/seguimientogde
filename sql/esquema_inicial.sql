#SIEMPRE SE ACTUALIZARA LA VERSIÓN DE LAS DOS DBs AUNQUE NO SE HAGAN CAMBIOS.
#REMPLAZAR ANTES DE EJECUTAR
# {{{user_mysql}}}  = REEMPLAZAR POR NOMBRE USER QUE EJECUTA.
# {{{db_log}}}      = REEMPLAZAR POR NOMBRE DB LOG.
# {{{db_app}}}      = REEMPLAZAR POR NOMBRE DB APP.

CREATE DATABASE  IF NOT EXISTS `{{{db_log}}}` DEFAULT CHARACTER SET utf8 ;
USE `{{{db_log}}}`;

--
-- Tabla que indexa todas las operacion que entran en el log `_registros_abm`
-- NOTA: cada vez que se agregue una tabla al esquema debe actualizarse el campo tabla_nombre
-- TABLA OBLIGATORIA
--
CREATE TABLE IF NOT EXISTS  `_registros_abm` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned DEFAULT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_operacion` char(1) DEFAULT NULL,
  `id_tabla` bigint(20) unsigned NOT NULL,
  `tabla_nombre` enum('objeto','subtema', 'nota_gde') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fecha_operacion` (`fecha_operacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Tabla de registro de version
-- TABLA OBLIGATORIA
--
CREATE TABLE IF NOT EXISTS  `db_version` (
  `version` mediumint(5) unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- TABLA EJEMPLO
CREATE TABLE IF NOT EXISTS `estados` (  
#CAMPOS DE SEGUIMIENTO
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
#CAMPOS DE LA TABLA TRACKEADA CON SU CAMPO "id" RENOMBRADO PARA EVITAR DUPLICIDAD
  `id_estado` int(11) unsigned NOT NULL,
  `estado` varchar(10) NOT NULL,
  `borrado` tinyint(1), 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Trigger para indexacion
-- TRIGGER EJEMPLO
--
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`estados_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `estados_tg_insert` AFTER INSERT ON `estados` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'estados');
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `objeto` (  
#CAMPOS DE SEGUIMIENTO
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
#CAMPOS DE LA TABLA TRACKEADA CON SU CAMPO "id" RENOMBRADO PARA EVITAR DUPLICIDAD
  `id_objeto` int(11) unsigned NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Trigger para indexacion
-- TRIGGER EJEMPLO
--
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`objeto_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `objeto_tg_insert` AFTER INSERT ON `objeto` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'objeto');
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `subtema` (  
#CAMPOS DE SEGUIMIENTO
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
#CAMPOS DE LA TABLA TRACKEADA CON SU CAMPO "id" RENOMBRADO PARA EVITAR DUPLICIDAD
  `id_subtema` int(10) unsigned NOT NULL,
  `id_objeto` int(10) unsigned NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Trigger para indexacion
-- TRIGGER EJEMPLO
--
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`subtema_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `subtema_tg_insert` AFTER INSERT ON `subtema` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'subtema');
END $$
DELIMITER ;

--
-- NOTA: Aunque el manejo y configuracion de usuarios es externo a la aplicacion, deben registrarse todos lo cambios generados
-- TABLA OBLIGATORIA
--

CREATE TABLE IF NOT EXISTS  `usuarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned DEFAULT NULL,
  `fecha_operacion` timestamp NULL DEFAULT NULL,
  `tipo_operacion` varchar(1) DEFAULT NULL,
  `id_usuario_panel` int(10) unsigned NOT NULL,
  `id_rol` int(10) unsigned NOT NULL,
  `username` varchar(30) NOT NULL,
  `metadata` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- TRIGGER OBLIGATORIO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`usuarios_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `usuarios_tg_insert` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'usuarios');
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `nota_gde` (
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
  `id_nota_gde` int(10) unsigned NOT NULL,
  `id_objeto` int(10) unsigned NOT NULL,
  `id_subtema` int(10) unsigned NOT NULL,
  `nota` varchar(50) NOT NULL,
  `fecha_recepcion` DATE NULL DEFAULT NULL,
  `fecha_vencimiento` DATE NULL DEFAULT NULL,
  `fecha_accion` DATE NULL DEFAULT NULL,
  `tipo` tinyint(1) NOT NULL DEFAULT 0,
  `remitente` varchar(200) NOT NULL,
  `reparticion` varchar(200) NOT NULL,
  `referencia` varchar(200) NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TRIGGER OBLIGATORIO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`nota_gde_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `nota_gde_tg_insert` AFTER INSERT ON `nota_gde` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'nota_gde');
END $$
DELIMITER ;

CREATE DATABASE  IF NOT EXISTS `{{{db_app}}}` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `{{{db_app}}}`;

--
-- Tabla de registro de version
-- TABLA OBLIGATORIA
--
CREATE TABLE IF NOT EXISTS `db_version` (
  `version` mediumint(5) unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `objeto` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`objeto_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `objeto_tg_alta` AFTER INSERT ON `objeto` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.objeto(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_objeto`,`nombre`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.nombre,NEW.borrado);
END $$
DELIMITER ;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`objeto_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `objeto_tg_modificacion` AFTER UPDATE ON `objeto` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.objeto(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_objeto`,`nombre`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.nombre,NEW.borrado);
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `subtema` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_objeto` int(10) unsigned NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`subtema_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `subtema_tg_alta` AFTER INSERT ON `subtema` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.subtema(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_subtema`,`nombre`,`id_objeto`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.nombre,NEW.id_objeto,NEW.borrado);
END $$
DELIMITER ;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`subtema_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `subtema_tg_modificacion` AFTER UPDATE ON `subtema` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.subtema(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_subtema`,`nombre`,`id_objeto`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.nombre,NEW.id_objeto,NEW.borrado);
END $$
DELIMITER ;


CREATE TABLE IF NOT EXISTS `nota_gde` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_objeto` int(10) unsigned NOT NULL,
  `id_subtema` int(10) unsigned NOT NULL,
  `nota` varchar(50) NOT NULL,
  `fecha_recepcion` DATE NULL DEFAULT NULL,
  `fecha_vencimiento` DATE NULL DEFAULT NULL,
  `fecha_accion` DATE NULL DEFAULT NULL,
  `tipo` tinyint(1) NOT NULL DEFAULT 0,
  `remitente` varchar(200) NOT NULL,
  `reparticion` varchar(200) NOT NULL,
  `referencia` varchar(200) NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`nota_gde_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `nota_gde_tg_alta` AFTER INSERT ON `nota_gde` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.nota_gde(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_nota_gde`,`id_subtema`,`id_objeto`,`nota`,`fecha_recepcion`,
`fecha_vencimiento`,`fecha_accion`,`tipo`,`remitente`,`reparticion`,`referencia`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_subtema,NEW.id_objeto,NEW.nota,NEW.fecha_recepcion,
NEW.fecha_vencimiento,NEW.fecha_accion,NEW.tipo,NEW.remitente,NEW.reparticion,NEW.referencia,NEW.borrado);
END $$
DELIMITER ;


DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`nota_gde_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `nota_gde_tg_modificacion` AFTER UPDATE ON `nota_gde` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.nota_gde(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_nota_gde`,`id_subtema`,`id_objeto`,`nota`,`fecha_recepcion`,
`fecha_vencimiento`,`fecha_accion`,`tipo`,`remitente`,`reparticion`,`referencia`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.id_subtema,NEW.id_objeto,NEW.nota,NEW.fecha_recepcion,
NEW.fecha_vencimiento,NEW.fecha_accion,NEW.tipo,NEW.remitente,NEW.reparticion,NEW.referencia,NEW.borrado);
END $$
DELIMITER ;


-- INSERT Obligatorio en las versiones, no asi en los script de desarrollo.
INSERT INTO {{{db_app}}}.db_version VALUES('1.0', now());
INSERT INTO {{{db_log}}}.db_version VALUES('1.0', now());
