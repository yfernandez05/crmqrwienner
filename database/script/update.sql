----------------------------------------
----------------------------------------
---------- ATENCIONES ------------------
----------------------------------------
----------------------------------------
CREATE TABLE `atenciones` (
  `idatencion` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `fechaatencion` timestamp NOT NULL DEFAULT current_timestamp(),
  `comentario` varchar(700) NOT NULL,
  `idtipoatencion` int(11) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT current_timestamp(),
  `userupdate` varchar(60) DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tipoatenciones` (
  `idtipoatencion` int(11) NOT NULL,
  `tipoatencion` varchar(150) NOT NULL,
  `backgroundColor` varchar(100) DEFAULT '#d3d3d3',
  `textColor` varchar(100) DEFAULT '#000',
  `estado` char(1) NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT current_timestamp(),
  `userupdate` varchar(60) DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `atenciones`
  ADD PRIMARY KEY (`idatencion`),
  ADD KEY `fk_iduser_atenciones` (`iduser`),
  ADD KEY `fk_idtipoatencion_atenciones` (`idtipoatencion`),
  ADD KEY `fk_idcliente_atenciones` (`idcliente`);

ALTER TABLE `atenciones`
  MODIFY `idatencion` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tipoatenciones`
  ADD PRIMARY KEY (`idtipoatencion`),
  ADD UNIQUE KEY `ak_tipoatencion_tipoatenciones` (`tipoatencion`,`estado`);

ALTER TABLE `tipoatenciones`
  MODIFY `idtipoatencion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `atenciones`
  ADD CONSTRAINT `fk_idcliente_atenciones` FOREIGN KEY (`idcliente`) REFERENCES `clientes` (`idcliente`),
  ADD CONSTRAINT `fk_idtipoatencion_atenciones` FOREIGN KEY (`idtipoatencion`) REFERENCES `tipoatenciones` (`idtipoatencion`),
  ADD CONSTRAINT `fk_iduser_atenciones` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`);


-- ----------------------------------
-- ----------------------------------
-- ------- STORE PROCEDURE ----------
-- ----------------------------------
-- ----------------------------------
-- ----
DROP procedure IF EXISTS `SP_RP_REGISTROS_ANIOEGERSO`;
DELIMITER ;;
 CREATE PROCEDURE `SP_RP_REGISTROS_ANIOEGERSO`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
    IN p_import INT
)
BEGIN
	IF p_idevento > 0 THEN
		SELECT
			cl.anioegreso, COUNT(ec.idcliente) AS total
			FROM clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
			WHERE 	cl.estado = 'A'
			AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
			AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY cl.anioegreso;
    ELSE
		SELECT
			anioegreso, COUNT(idcliente) AS total
			FROM clientes
			WHERE 	estado = 'A'
			AND idcampania = p_idcampania
            AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
			AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY anioegreso;
	END IF;
END ;;
DELIMITER ;

DROP procedure IF EXISTS `SP_RP_REGISTROS_SEDES`;
DELIMITER ;;
CREATE  PROCEDURE `SP_RP_REGISTROS_SEDES`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
    IN p_import INT
)
BEGIN
	IF p_idevento > 0 THEN
		SELECT
			cl.colegio, COUNT(ec.idcliente) AS total
			FROM clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
			WHERE 	cl.estado = 'A'
			AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
			AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY cl.colegio;

    ELSE
		SELECT
			colegio, COUNT(idcliente) AS total
			FROM clientes
			WHERE 	estado = 'A'
			AND idcampania = p_idcampania
            AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
			AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY colegio;
	END IF;
END ;;
DELIMITER ;

DROP procedure IF EXISTS `SP_REGSITROSFECHAS_CLIENTES`;
DELIMITER ;;
CREATE PROCEDURE `SP_REGSITROSFECHAS_CLIENTES`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
	IN p_asistencia INT,
	IN p_import INT
)
BEGIN
    IF p_idevento > 0 THEN
        -- Consulta con filtro por p_idevento
        SELECT
            cl.colegio,
            DATE_FORMAT(ec.fecharegistro, "%Y-%m-%d %H:00:00") AS fechaGeneral,
            COUNT(ec.idcliente) AS total
        FROM
            clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
        WHERE
            cl.estado = 'A'
            AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
            AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
        GROUP BY
            cl.colegio, DATE_FORMAT(ec.fecharegistro, "%Y-%m-%d %H:00:00")
        ORDER BY
            fechaGeneral ASC;
    ELSE
        -- Consulta sin filtro por p_idevento
        SELECT
            cl.colegio,
            DATE_FORMAT(cl.fecharegistro, "%Y-%m-%d %H:00:00") AS fechaGeneral,
            COUNT(cl.idcliente) AS total
        FROM
            clientes cl
        WHERE
            cl.estado = 'A'
            AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
            AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
        GROUP BY
            cl.colegio, DATE_FORMAT(cl.fecharegistro, "%Y-%m-%d %H:00:00")
        ORDER BY
            fechaGeneral ASC;
    END IF;
END ;;


DROP procedure IF EXISTS `SP_REGSITROSFECHAS_CLIENTES_ASISTIDO`;
DELIMITER ;;
CREATE PROCEDURE `SP_REGSITROSFECHAS_CLIENTES_ASISTIDO`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
	IN p_asistencia INT,
	IN p_import INT
)
BEGIN
    IF p_idevento > 0 THEN
        -- Consulta con filtro por p_idevento
        SELECT
            cl.colegio,
            DATE_FORMAT(ec.fechaasistencia, "%Y-%m-%d %H:00:00") AS fechaGeneral,
            COUNT(ec.idcliente) AS total
        FROM
            clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
        WHERE
            cl.estado = 'A'
            AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
            AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
        GROUP BY
            cl.colegio, DATE_FORMAT(ec.fechaasistencia, "%Y-%m-%d %H:00:00")
        ORDER BY
            fechaGeneral ASC;
    ELSE
        -- Consulta sin filtro por p_idevento
        SELECT
            cl.colegio,
            DATE_FORMAT(cl.fechaasistencia, "%Y-%m-%d %H:00:00") AS fechaGeneral,
            COUNT(cl.idcliente) AS total
        FROM
            clientes cl
        WHERE
            cl.estado = 'A'
            AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
            AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
        GROUP BY
            cl.colegio, DATE_FORMAT(cl.fechaasistencia, "%Y-%m-%d %H:00:00")
        ORDER BY
            fechaGeneral ASC;
    END IF;
END ;;

DROP procedure IF EXISTS `SP_RP_REGISTROS_CARRERAS`;
DELIMITER ;;
CREATE PROCEDURE `SP_RP_REGISTROS_CARRERAS`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
	IN p_import INT
)
BEGIN
	IF p_idevento > 0 THEN

        SELECT
			cl.carrera, COUNT(ec.idcliente) AS total
			FROM clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
			WHERE 	cl.estado = 'A'
			AND cl.idcampania = p_idcampania
			AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
			AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY cl.carrera;

    ELSE
		SELECT
			carrera, COUNT(idcliente) AS total
			FROM clientes
			WHERE 	estado = 'A'
			AND idcampania = p_idcampania
			AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
			AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY carrera;
	END IF;
END ;;
DELIMITER ;


-- -----------------------------
-- -----------------------------
-- -----------------------------
CREATE TABLE `buttonmessages` (
  `idbutton` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `backgroundColor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '#d3d3d3',
  `textColor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '#000',
  `descriptionwhatsapp` text COLLATE utf8mb4_unicode_ci NULL,
  `estado` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `userinsert` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'sistemas@padinsolutions.com',
  `dateinsert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateupdate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idbutton`),
  UNIQUE KEY `ak_name_buttonmessages` (`name`,`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------
-- ----------
-- ----------
-- ----------

DROP procedure IF EXISTS `SP_RP_ASISTENCIA_SEDE`;
DELIMITER ;;
CREATE PROCEDURE `SP_RP_ASISTENCIA_SEDE`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100)
)
BEGIN

    IF p_idevento > 0 THEN
        -- Consulta con filtro por p_idevento
        SELECT
			cl.colegio, COUNT(CASE WHEN ec.asistencia = '1' THEN 1 END) AS cantidad_asistencias
		FROM clientes cl INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
		WHERE cl.estado = 'A'
            AND cl.idcampania = p_idcampania
            AND ec.idevento = p_idevento
            AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY colegio;

    ELSE
		SELECT
			colegio, COUNT(CASE WHEN asistencia = '1' THEN 1 END) AS cantidad_asistencias
		FROM clientes
		WHERE 	estado = 'A'
            AND idcampania = p_idcampania
            AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY colegio;
    END IF;

END;;
DELIMITER ;;





Alter table clientes add column tipodocumento varchar(50) not null;
Alter table evento_cliente add column tipodocumento varchar(50) not null;
ALTER TABLE clientes add column dni char(24) DEFAULT NULL;
ALTER TABLE evento_cliente add column dni char(24) DEFAULT NULL;
ALTER TABLE clientes add column anioegreso char(10) DEFAULT NULL;
ALTER TABLE evento_cliente add column anioegreso char(10) DEFAULT NULL;

ALTER TABLE evento_cliente DROP COLUMN tipoparticipante;
ALTER TABLE evento_cliente DROP COLUMN edad;
ALTER TABLE evento_cliente DROP COLUMN origen;

ALTER TABLE evento_cliente DROP COLUMN tipoparticipante;
ALTER TABLE evento_cliente DROP COLUMN edad;
ALTER TABLE evento_cliente DROP COLUMN origen;


ALTER TABLE evento_cliente
ADD UNIQUE KEY `unique_evento_cliente` (`idevento`, `idcliente`);

----
----
DROP procedure IF EXISTS `SP_RP_REGISTROS_PROCEDENCIAS`;
DELIMITER ;;
CREATE PROCEDURE `SP_RP_REGISTROS_PROCEDENCIAS`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
	IN p_import INT
)
BEGIN
	IF p_idevento > 0 THEN

        SELECT
			cl.procedencia, COUNT(ec.idcliente) AS total
			FROM clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
			WHERE cl.estado = 'A'
			AND cl.idcampania = p_idcampania
			AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
			AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY cl.procedencia;

    ELSE
		SELECT
			procedencia, COUNT(idcliente) AS total
			FROM clientes
			WHERE estado = 'A'
			AND idcampania = p_idcampania
			AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
			AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY procedencia;
	END IF;
END ;;
DELIMITER ;


ALTER TABLE clientes DROP COLUMN prefijo;
ALTER TABLE evento_cliente DROP COLUMN prefijo;


DROP procedure IF EXISTS `SP_RP_REGISTROS_ENVIONOTIFACION`;
DELIMITER //
CREATE PROCEDURE `SP_RP_REGISTROS_ENVIONOTIFACION`(
    IN p_idcampania INT
)
BEGIN
    SELECT
        ta.tipoatencion,
        DATE_FORMAT(a.fechaatencion, '%Y-%m-%d') AS fecha_atencion,
        COUNT(a.idatencion) AS total_atenciones
    FROM
        atenciones a
    JOIN
        clientes c ON a.idcliente = c.idcliente
    JOIN
        tipoatenciones ta ON a.idtipoatencion = ta.idtipoatencion
    WHERE
        a.idtipoatencion = 4
        AND c.idcampania = p_idcampania
        AND c.estado = 'A'
    GROUP BY
        fecha_atencion, ta.tipoatencion
    ORDER BY
        fecha_atencion ASC, ta.tipoatencion ASC;
END //
DELIMITER ;



DROP procedure IF EXISTS `SP_RP_REGISTROS_GRADO`;
DELIMITER //
CREATE PROCEDURE `SP_RP_REGISTROS_GRADO`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
    IN p_import INT
)
BEGIN
	IF p_idevento > 0 THEN
		SELECT
			cl.grado, COUNT(ec.idcliente) AS total
			FROM clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
			WHERE cl.estado = 'A'
            AND cl.grado IS NOT NULL
			AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
			AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY cl.grado;

    ELSE
		SELECT
			grado, COUNT(idcliente) AS total
			FROM clientes
			WHERE estado = 'A'
            AND grado IS NOT NULL
			AND idcampania = p_idcampania
            AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
			AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY grado;
	END IF;
END //
DELIMITER ;



DROP procedure IF EXISTS `SP_RP_REGISTROS_ANIOEGRESO`;
DELIMITER //
CREATE PROCEDURE `SP_RP_REGISTROS_ANIOEGRESO`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
    IN p_import INT
)
BEGIN
	IF p_idevento > 0 THEN
		SELECT
			cl.anioegreso, COUNT(ec.idcliente) AS total
			FROM clientes cl
            INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
			WHERE 	cl.estado = 'A'
            AND cl.anioegreso IS NOT NULL
			AND cl.idcampania = p_idcampania
            AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
            AND ec.idevento = p_idevento
			AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY cl.anioegreso;

    ELSE
		SELECT
			anioegreso, COUNT(idcliente) AS total
			FROM clientes
			WHERE 	estado = 'A'
            AND anioegreso IS NOT NULL
			AND idcampania = p_idcampania
            AND (p_asistencia = 0 OR asistencia = p_asistencia)
            AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
			AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci)
		GROUP BY anioegreso;
	END IF;
END //
DELIMITER ;


ALTER TABLE clientes ADD COLUMN `ismatricula` int NOT NULL DEFAULT '0';
ALTER TABLE evento_cliente ADD COLUMN `ismatricula` int NOT NULL DEFAULT '0';
ALTER TABLE clientes add column `uuidmatriculaimportacion` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL;
ALTER TABLE evento_cliente add column `uuidmatriculaimportacion` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL;

DROP PROCEDURE IF EXISTS `SP_RP_REGISTROSMATRICULADOS`;
DELIMITER //
CREATE PROCEDURE `SP_RP_REGISTROSMATRICULADOS`(
    IN p_idevento INT,
    IN p_idcampania INT,
    IN p_colegio VARCHAR(100),
    IN p_asistencia INT,
    IN p_import INT
)
BEGIN
    IF p_idevento > 0 THEN
        SELECT
            'Matriculados' AS colegio,  -- Asigna '-' en lugar de NULL
            COUNT(CASE WHEN cl.ismatricula = 1 THEN 1 END) AS total
        FROM clientes cl
        INNER JOIN evento_cliente ec ON cl.idcliente = ec.idcliente
        WHERE cl.estado = 'A'
        AND cl.idcampania = p_idcampania
        AND (p_asistencia = 0 OR ec.asistencia = p_asistencia)
        AND ec.idevento = p_idevento
        AND (p_colegio IS NULL OR p_colegio = '' OR cl.colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci);

    ELSE
        SELECT
            'Matriculados' AS colegio,
            COUNT(CASE WHEN ismatricula = 1 THEN 1 END) AS total
        FROM clientes
        WHERE estado = 'A'
        AND idcampania = p_idcampania
        AND (p_asistencia = 0 OR asistencia = p_asistencia)
        AND (p_asistencia = 1 OR (p_import = 0 OR (p_import = 1 AND uuidimportacion IS NULL)) )
        AND (p_colegio IS NULL OR p_colegio = '' OR colegio COLLATE utf8mb4_unicode_ci = p_colegio COLLATE utf8mb4_unicode_ci);
    END IF;
END //
DELIMITER ;

ALTER TABLE users ADD COLUMN `idcampania` int NULL AFTER `idcuenta`;



ALTER TABLE eventos
ADD COLUMN mensaje_exito TEXT NULL AFTER idcampania,
ADD COLUMN mensaje_error TEXT NULL AFTER mensaje_exito;

-- ===================================
-- TABLA PARA PÁGINAS DINÁMICAS
-- Sistema BuscaTuQR
-- ===================================

DROP TABLE IF EXISTS `paginas_dinamicas`;

CREATE TABLE `paginas_dinamicas` (
  `idpagina` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` VARCHAR(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` TEXT COLLATE utf8mb4_unicode_ci NULL,
  `idcampania` INT UNSIGNED NULL,
  `estado_general` CHAR(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `estado` CHAR(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `userinsert` VARCHAR(50) COLLATE utf8mb4_unicode_ci NULL DEFAULT 'sistema',
  `dateinsert` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userupdate` VARCHAR(50) COLLATE utf8mb4_unicode_ci NULL,
  `dateupdate` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idpagina`),
  UNIQUE KEY `uk_slug_paginas_dinamicas` (`slug`),
  KEY `idx_slug_estado` (`slug`, `estado_general`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla para control de páginas dinámicas públicas';

-- Insertar registro inicial para BuscaTuQR
INSERT INTO `paginas_dinamicas`
(`slug`, `titulo`, `descripcion`, `estado_general`, `estado`, `userinsert`, `dateinsert`)
VALUES
('buscatuqr', 'Consulta de Códigos QR', 'Página para buscar y validar códigos QR activos.', 'A', 'A', 'sistema', NOW());

-- Verificar inserción
SELECT * FROM paginas_dinamicas WHERE slug = 'buscatuqr';

ALTER TABLE `paginas_dinamicas`
ADD COLUMN `api_token` VARCHAR(100) NULL;

-- Insertar registro para página de registro
INSERT INTO `paginas_dinamicas`
(`slug`, `titulo`, `descripcion`, `idcampania`, `api_token`, `estado_general`, `estado`, `userinsert`, `dateinsert`)
VALUES
('registro', 'Registro de Participantes', 'Formulario de registro público para eventos.', 16, 'wiener_registro_2026_secret_token', 'A', 'A', 'sistema', NOW());

-- ALTER TABLE clientes ADD COLUMN prefijo VARCHAR(50) NULL;
-- ALTER TABLE evento_cliente ADD COLUMN prefijo VARCHAR(50) NULL;


ALTER TABLE clientes ADD COLUMN `es_padre_familia` VARCHAR(4) DEFAULT 'No';
ALTER TABLE clientes ADD COLUMN `padre_nombres` VARCHAR(150) NULL;
ALTER TABLE clientes ADD COLUMN `padre_apellidos` VARCHAR(150) NULL;
ALTER TABLE clientes ADD COLUMN `padre_celular` VARCHAR(12) NULL;
ALTER TABLE clientes ADD COLUMN `padre_email` VARCHAR(150) NULL;