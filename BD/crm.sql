-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 17-10-2021 a las 01:18:59
-- Versión del servidor: 5.7.31
-- Versión de PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `crm`
--

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `actualizar_precio_producto`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_precio_producto` (`n_cantidad` INT, `n_precio` DECIMAL(10,2), `codigo` INT)  BEGIN
    	DECLARE nueva_existencia int;
        DECLARE nuevo_total decimal(10,2);
        DECLARE nuevo_precio decimal(10,2);
        
        DECLARE cant_actual int;
        DECLARE pre_actual decimal(10,2);
        
        DECLARE actual_existencia int;
        DECLARE actual_precio decimal(10,2);
        
        SELECT precio,existencia INTO actual_precio,actual_existencia FROM producto WHERE codproducto = codigo;
        SET nueva_existencia = actual_existencia + n_cantidad;
        SET nuevo_total = (actual_existencia * actual_precio) + (n_cantidad * n_precio);
        SET nuevo_precio = nuevo_total / nueva_existencia;
        
        UPDATE producto SET existencia = nueva_existencia, precio = nuevo_precio WHERE codproducto = codigo;
        
        SELECT nueva_existencia,nuevo_precio;
        
END$$

DROP PROCEDURE IF EXISTS `add_detalle_temp`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle_temp` (`codigo` INT, `cantidad` INT, `token_user` VARCHAR(50))  BEGIN
    	DECLARE precio_actual decimal(10,2);
        SELECT precio INTO precio_actual FROM producto WHERE codproducto = codigo;
        
        INSERT INTO detalle_temp(token_user,codproducto,cantidad,precio_venta) 			           VALUES(token_user,codigo,cantidad,precio_actual);
        
        SELECT tmp.correlativo, tmp.codproducto, p.descripcion, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp INNER JOIN producto p
        ON tmp.codproducto = p.codproducto
        WHERE tmp.token_user = token_user;
        
        END$$

DROP PROCEDURE IF EXISTS `anular_factura`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `anular_factura` (IN `no_factura` INT)  BEGIN
    	DECLARE existe_factura int;
        DECLARE registros int;
        DECLARE a int;
        
        DECLARE cod_producto int;
        DECLARE cant_producto int;
        DECLARE existencia_actual int;
        DECLARE nueva_existencia int;
        
        SET existe_factura = (SELECT COUNT(*) FROM factura WHERE nofactura = no_factura and estatus = 1);
        
        IF existe_factura > 0 THEN 
        	CREATE TEMPORARY TABLE tbl_tmp(
                id bigint NOT null AUTO_INCREMENT PRIMARY key,
                cod_prod bigint,
                cant_prod int);
                
                SET a = 1;
                
                SET registros =(SELECT COUNT(*) FROM detallefactura WHERE nofactura = no_factura);
                
                IF registros > 0 THEN
                	INSERT INTO tbl_tmp(cod_prod,cant_prod) SELECT codproducto,cantidad FROM detallefactura WHERE 									nofactura = no_factura;
                    
                    WHILE a <= registros DO
                    	SELECT cod_prod,cant_prod INTO cod_producto,cant_producto FROM tbl_tmp WHERE id = a;
                        SELECT existencia INTO existencia_actual FROM producto WHERE codproducto = cod_producto;
                        SET nueva_existencia = existencia_actual + cant_producto;
                        UPDATE producto SET existencia = nueva_existencia WHERE codproducto = cod_producto;
                        
                        SET a=a+1;
                    END WHILE;
                    
                    	UPDATE factura SET estatus = 2 WHERE nofactura = no_factura;
                        DROP TABLE tbl_tmp;
                        SELECT * FROM factura WHERE nofactura = no_factura;
                    
                
                END IF;
        ELSE
        	SELECT 0 factura;
       	END IF;
    
    
    END$$

DROP PROCEDURE IF EXISTS `dataDashboard`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `dataDashboard` ()  BEGIN
    	
        DECLARE clientes int;
        DECLARE proveedores int;
        DECLARE productos int;
        DECLARE ventas int;
        
        SELECT COUNT(*) INTO clientes FROM cliente WHERE estatus !=10;
        SELECT COUNT(*) INTO proveedores FROM proveedor WHERE estatus !=10;
        SELECT COUNT(*) INTO productos FROM producto WHERE estatus !=10;
        SELECT COUNT(*) INTO ventas FROM factura WHERE estatus !=10;
        
        SELECT clientes,proveedores,productos,ventas;
    
    END$$

DROP PROCEDURE IF EXISTS `del_detalle_temp`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `del_detalle_temp` (`id_detalle` INT, `token` VARCHAR(50))  BEGIN
    	DELETE FROM detalle_temp WHERE correlativo = id_detalle;
        SELECT tmp.correlativo,tmp.codproducto,p.descripcion,tmp.cantidad,tmp.precio_venta FROM detalle_temp tmp
        INNER JOIN producto p
        ON tmp.codproducto = p.codproducto
        WHERE tmp.token_user = token;
   END$$

DROP PROCEDURE IF EXISTS `procesar_venta`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_venta` (IN `cod_usuario` INT, IN `cod_cliente` INT, IN `token` VARCHAR(50))  BEGIN

	DECLARE factura int;
    
    DECLARE registros int;
    DECLARE total decimal(10,2);
    
    DECLARE nueva_existencia int;
    DECLARE existencia_actual int;
    
    DECLARE tmp_cod_producto int;
    DECLARE tmp_cant_producto int;
    DECLARE a int;
    SET a = 1;
    
    CREATE TEMPORARY TABLE tbl_tmp_tokenuser(
        
        id bigint NOT null AUTO_INCREMENT PRIMARY KEY,
        cod_prod bigint,
        cant_prod int);
        
    SET registros = (SELECT COUNT(*) FROM detalle_temp WHERE token_user = token);
    
    IF registros > 0 THEN
    
    INSERT INTO tbl_tmp_tokenuser(cod_prod,cant_prod) SELECT codproducto,cantidad FROM detalle_temp WHERE token_user = token;
    
    INSERT INTO factura(usuario,codcliente) VALUES(cod_usuario,cod_cliente);
    SET factura = LAST_INSERT_ID();
    
    INSERT INTO detallefactura(nofactura,codproducto,cantidad,precio_venta) SELECT (factura) as nofactura, codproducto,cantidad,precio_venta FROM detalle_temp
    WHERE token_user = token;
    
    WHILE a <= registros DO
    SELECT cod_prod,cant_prod INTO tmp_cod_producto,tmp_cant_producto FROM tbl_tmp_tokenuser WHERE id = a;
    
    SELECT existencia INTO existencia_actual FROM producto WHERE codproducto = tmp_cod_producto;
    
    SET nueva_existencia = existencia_actual - tmp_cant_producto;
    UPDATE producto SET existencia = nueva_existencia WHERE codproducto = tmp_cod_producto;
    
    SET a=a+1;
    
    END WHILE;
    
    SET total = (SELECT SUM(cantidad * precio_venta) FROM detalle_temp WHERE token_user = token);
    UPDATE factura SET totalfactura = total WHERE nofactura = factura;
    
    DELETE FROM detalle_temp WHERE token_user = token;
    TRUNCATE TABLE tbl_tmp_tokenuser;
    SELECT * FROM factura WHERE nofactura = factura;
     
    ELSE
    	SELECT 0;
    
    END IF;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `idcliente` int(11) NOT NULL AUTO_INCREMENT,
  `nit` int(11) DEFAULT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `telefono` int(11) DEFAULT NULL,
  `direccion` text,
  `dateadd` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idcliente`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idcliente`, `nit`, `nombre`, `telefono`, `direccion`, `dateadd`, `usuario_id`, `estatus`) VALUES
(12, 1112225458, 'Camila Gallardo Gomez', 312548745, 'Calle 33 # 45-98', '2021-09-29 21:28:53', 1, 1),
(13, 29699898, 'mery gonzalez ', 2674448, 'calle 2 A # 2-01', '2021-09-30 18:19:16', 1, 1),
(18, 545124512, 'Camilo Marroquin', 666666, 'Calle 32 # 20-32', '2021-10-01 19:15:14', 1, 1),
(19, 267451245, 'Eucaris Gonzalez', 2671176, 'Diagonal 4B # 55-24', '2021-10-03 13:38:24', 8, 1),
(20, 6969696, 'Carmen Villalobos', 555888, 'Manzana D casa 76', '2021-10-03 13:39:07', 9, 1),
(21, 296998987, 'Pepito Perez Gomez', 666, 'Calle 55 ', '2021-10-03 17:31:56', 1, 1),
(22, 66666, 'Diablo Dominguez', 6666666, 'El infierno', '2021-10-03 17:39:51', 1, 1),
(23, 88888, 'Damaris Gonzalez', 333333, 'calle 34 # 3-44', '2021-10-06 20:38:31', 1, 1),
(24, 1236458, 'camila gomez', 669955, 'calle 20 # 20-01', '2021-10-16 14:21:37', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

DROP TABLE IF EXISTS `configuracion`;
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nit` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `telefono` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `direccion` text NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `nit`, `nombre`, `razon_social`, `telefono`, `email`, `direccion`, `iva`) VALUES
(1, '94544955-5', 'El Bunker de la Tecnologia', 'El Bunker de la Tecnologia', 3882438, 'bunker_de_la_tecnologia@gmail.com', 'Centro Comercial Pasarela Local 162-169 Santiago de Cali ', '19.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallefactura`
--

DROP TABLE IF EXISTS `detallefactura`;
CREATE TABLE IF NOT EXISTS `detallefactura` (
  `correlativo` bigint(11) NOT NULL AUTO_INCREMENT,
  `nofactura` bigint(11) DEFAULT NULL,
  `codproducto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`correlativo`),
  KEY `codproducto` (`codproducto`),
  KEY `nofactura` (`nofactura`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `detallefactura`
--

INSERT INTO `detallefactura` (`correlativo`, `nofactura`, `codproducto`, `cantidad`, `precio_venta`) VALUES
(1, 1, 13, 1, '200000.00'),
(2, 1, 15, 1, '10000.00'),
(3, 1, 17, 1, '20000.00'),
(4, 2, 13, 1, '200000.00'),
(5, 2, 14, 1, '50000.00'),
(6, 2, 15, 1, '10000.00'),
(7, 3, 18, 1, '100000.00'),
(8, 3, 17, 1, '20000.00'),
(10, 4, 13, 1, '200000.00'),
(11, 4, 14, 1, '50000.00'),
(13, 5, 14, 1, '50000.00'),
(14, 5, 13, 1, '200000.00'),
(16, 6, 15, 10, '10000.00'),
(17, 7, 14, 1, '50000.00'),
(18, 7, 15, 1, '10000.00'),
(20, 8, 13, 1, '200000.00'),
(21, 9, 13, 1, '200000.00'),
(22, 10, 18, 1, '100000.00'),
(23, 11, 16, 1, '120000.00'),
(24, 12, 15, 1, '10000.00'),
(25, 13, 16, 1, '120000.00'),
(26, 14, 16, 1, '120000.00'),
(27, 14, 15, 1, '10000.00'),
(29, 15, 14, 1, '50000.00'),
(30, 16, 14, 1, '50000.00'),
(31, 17, 16, 1, '120000.00'),
(32, 18, 16, 1, '120000.00'),
(33, 19, 16, 1, '120000.00'),
(34, 20, 15, 1, '10000.00'),
(35, 21, 15, 1, '10000.00'),
(36, 22, 17, 1, '20000.00'),
(37, 23, 17, 1, '20000.00'),
(38, 24, 16, 1, '120000.00'),
(39, 25, 16, 1, '120000.00'),
(40, 26, 17, 1, '20000.00'),
(41, 27, 17, 1, '20000.00'),
(42, 28, 15, 1, '10000.00'),
(43, 29, 14, 1, '50000.00'),
(44, 30, 15, 1, '10000.00'),
(45, 31, 16, 1, '120000.00'),
(46, 32, 15, 1, '10000.00'),
(47, 33, 16, 1, '120000.00'),
(48, 34, 14, 1, '50000.00'),
(49, 35, 17, 1, '20000.00'),
(50, 36, 17, 3, '20000.00'),
(51, 37, 17, 1, '20000.00'),
(52, 38, 17, 1, '20000.00'),
(53, 39, 17, 1, '20000.00'),
(54, 40, 17, 1, '20000.00'),
(55, 41, 17, 1, '20000.00'),
(56, 42, 17, 1, '20000.00'),
(57, 43, 17, 1, '20000.00'),
(58, 43, 17, 1, '20000.00'),
(60, 44, 17, 1, '20000.00'),
(61, 45, 17, 1, '20000.00'),
(62, 45, 13, 1, '200000.00'),
(64, 46, 17, 1, '20000.00'),
(65, 47, 17, 10, '20000.00'),
(66, 47, 18, 10, '100000.00'),
(68, 48, 17, 20, '20000.00'),
(69, 49, 17, 1, '20000.00'),
(70, 50, 18, 2, '100000.00'),
(71, 51, 18, 2, '100000.00'),
(72, 52, 19, 50, '50000.00'),
(73, 53, 18, 1, '100000.00'),
(74, 54, 13, 10, '200000.00'),
(75, 55, 17, 1, '20000.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_temp`
--

DROP TABLE IF EXISTS `detalle_temp`;
CREATE TABLE IF NOT EXISTS `detalle_temp` (
  `correlativo` int(11) NOT NULL AUTO_INCREMENT,
  `token_user` varchar(50) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  PRIMARY KEY (`correlativo`),
  KEY `nofactura` (`token_user`),
  KEY `codproducto` (`codproducto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas`
--

DROP TABLE IF EXISTS `entradas`;
CREATE TABLE IF NOT EXISTS `entradas` (
  `correlativo` int(11) NOT NULL AUTO_INCREMENT,
  `codproducto` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`correlativo`),
  KEY `codproducto` (`codproducto`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `entradas`
--

INSERT INTO `entradas` (`correlativo`, `codproducto`, `fecha`, `cantidad`, `precio`, `usuario_id`) VALUES
(23, 13, '2021-10-02 17:27:53', 10, '20000.00', 8),
(24, 13, '2021-10-02 17:28:21', 20, '20000.00', 8),
(25, 13, '2021-10-02 17:28:56', 5, '10000.00', 8),
(26, 13, '2021-10-02 17:29:31', 5, '100.00', 8),
(28, 13, '2021-10-02 17:34:38', 10, '60000.00', 8),
(29, 13, '2021-10-02 17:34:47', 5, '100.00', 8),
(33, 14, '2021-10-03 11:22:30', 10, '50000.00', 1),
(34, 15, '2021-10-03 11:28:47', 20, '10000.00', 1),
(35, 16, '2021-10-03 11:29:04', 10, '120000.00', 1),
(36, 17, '2021-10-03 11:29:24', 60, '20000.00', 1),
(37, 18, '2021-10-03 11:31:39', 20, '100000.00', 1),
(38, 19, '2021-10-06 20:41:15', 100, '50000.00', 1),
(39, 19, '2021-10-06 20:46:20', 500, '100000.00', 1),
(40, 20, '2021-10-07 21:57:51', 50, '220000.00', 8),
(41, 20, '2021-10-07 21:58:44', 50, '220000.00', 1),
(42, 21, '2021-10-16 11:58:04', 10, '200000.00', 1),
(43, 21, '2021-10-16 11:58:29', 20, '200000.00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

DROP TABLE IF EXISTS `factura`;
CREATE TABLE IF NOT EXISTS `factura` (
  `nofactura` bigint(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario` int(11) DEFAULT NULL,
  `codcliente` int(11) DEFAULT NULL,
  `totalfactura` decimal(10,2) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`nofactura`),
  KEY `usuario` (`usuario`),
  KEY `codcliente` (`codcliente`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`nofactura`, `fecha`, `usuario`, `codcliente`, `totalfactura`, `estatus`) VALUES
(26, '2021-10-04 22:07:11', 1, 22, '20000.00', 0),
(27, '2021-10-04 22:07:28', 1, 22, '20000.00', 2),
(28, '2021-10-04 22:12:20', 1, 22, '10000.00', 1),
(29, '2021-10-04 22:12:44', 1, 22, '50000.00', 1),
(30, '2021-10-04 22:13:18', 1, 22, '10000.00', 1),
(31, '2021-10-04 22:17:29', 1, 22, '120000.00', 1),
(32, '2021-10-04 22:17:46', 1, 22, '10000.00', 1),
(33, '2021-10-04 22:18:04', 1, 22, '120000.00', 1),
(34, '2021-10-04 22:18:26', 1, 22, '50000.00', 1),
(35, '2021-10-04 22:21:44', 1, 22, '20000.00', 1),
(36, '2021-10-05 19:19:10', 1, 22, '60000.00', 1),
(37, '2021-10-05 19:54:50', 1, 22, '20000.00', 1),
(38, '2021-10-05 19:55:26', 1, 22, '20000.00', 1),
(39, '2021-10-05 19:56:55', 1, 22, '20000.00', 1),
(40, '2021-10-05 19:57:47', 1, 22, '20000.00', 1),
(41, '2021-10-05 19:59:47', 1, 22, '20000.00', 1),
(42, '2021-10-05 20:03:50', 1, 22, '20000.00', 2),
(43, '2021-10-05 20:15:38', 1, 22, '40000.00', 2),
(44, '2021-10-05 20:32:26', 1, 22, '20000.00', 2),
(45, '2021-10-05 20:34:25', 1, 22, '220000.00', 2),
(46, '2021-10-05 20:36:00', 1, 22, '20000.00', 2),
(47, '2021-10-05 21:48:59', 1, 13, '1200000.00', 2),
(48, '2021-10-05 22:28:33', 1, 18, '400000.00', 2),
(49, '2021-10-06 18:20:59', 1, 13, '20000.00', 2),
(50, '2021-10-06 19:05:29', 1, 13, '200000.00', 2),
(51, '2021-10-06 19:07:12', 1, 13, '200000.00', 2),
(52, '2021-10-06 20:41:58', 1, 23, '2500000.00', 2),
(53, '2021-10-06 21:41:37', 1, 22, '100000.00', 2),
(54, '2021-10-06 22:11:20', 1, 20, '2000000.00', 2),
(55, '2021-10-06 22:19:19', 1, 13, '20000.00', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

DROP TABLE IF EXISTS `producto`;
CREATE TABLE IF NOT EXISTS `producto` (
  `codproducto` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) DEFAULT NULL,
  `proveedor` int(11) DEFAULT NULL,
  `precio` int(100) DEFAULT NULL,
  `existencia` int(11) DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT '1',
  `foto` text,
  PRIMARY KEY (`codproducto`),
  KEY `proveedor` (`proveedor`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`codproducto`, `descripcion`, `proveedor`, `precio`, `existencia`, `date_add`, `usuario_id`, `estatus`, `foto`) VALUES
(13, 'Licuadora', 1, 200000, 49, '2021-10-02 17:27:53', 8, 1, 'img_producto.png'),
(14, 'Teclado Gamers', 5, 50000, 2, '2021-10-03 11:22:30', 1, 1, 'img_producto.png'),
(15, 'Audifonos Gamers', 2, 10000, 0, '2021-10-03 11:28:47', 1, 1, 'img_producto.png'),
(16, 'Chasis Gamers', 2, 120000, 0, '2021-10-03 11:29:04', 1, 1, 'img_producto.png'),
(17, 'Monitor LCD ', 2, 20000, 46, '2021-10-03 11:29:24', 1, 1, 'img_producto.png'),
(18, 'Monitor 20\"', 4, 100000, 18, '2021-10-03 11:31:39', 1, 1, 'img_producto.png'),
(19, 'Adidas', 10, 95455, 600, '2021-10-06 20:41:15', 1, 1, 'img_a926a3298cb773c6b433f0544001fcf8.jpg'),
(20, 'Nevera Inteligente', 7, 220000, 100, '2021-10-07 21:57:51', 8, 1, 'img_producto.png'),
(21, 'PDA', 1, 200000, 30, '2021-10-16 11:58:04', 1, 1, 'img_producto.png');

--
-- Disparadores `producto`
--
DROP TRIGGER IF EXISTS `entradas_A_I`;
DELIMITER $$
CREATE TRIGGER `entradas_A_I` AFTER INSERT ON `producto` FOR EACH ROW BEGIN
		INSERT INTO entradas (codproducto,cantidad,precio,usuario_id)
		VALUES(new.codproducto,new.existencia,new.precio,new.usuario_id);
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

DROP TABLE IF EXISTS `proveedor`;
CREATE TABLE IF NOT EXISTS `proveedor` (
  `codproveedor` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor` varchar(100) DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` bigint(11) DEFAULT NULL,
  `direccion` text,
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codproveedor`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`codproveedor`, `proveedor`, `contacto`, `telefono`, `direccion`, `date_add`, `usuario_id`, `estatus`) VALUES
(1, 'Pisco Tics', 'Luis Emilio', 3164558575, 'diagonal 4B # 15-24', '2021-09-29 20:08:10', 1, 1),
(2, 'Tecnologia Damaris', 'Carla Damaris ZuÃ±iga', 3124552133, 'Carrera 45 # 5-45', '2021-09-29 20:57:57', 1, 1),
(3, 'Tecno Tecno', 'Camilo Giraldo', 2745898, 'Calle 33 #12-12', '2021-09-29 21:33:33', 1, 1),
(4, 'Camilo Comput Tics', 'Camilo Rojas', 2759878, 'Calle 4B # 4-31', '2021-09-30 18:20:02', 1, 1),
(5, 'Intel Tecnologia', 'Lina Marulanda', 2664587, 'Calle 5 # 5-44', '2021-09-30 18:37:00', 1, 1),
(6, 'Carton Colombia', 'Harold Moreno', 5183000, 'Avenida yumbo', '2021-09-30 18:48:58', 1, 1),
(7, 'Celsia', 'Arturo Calle', 5184000, 'Calle 32 # 12-33', '2021-09-30 19:00:50', 1, 1),
(8, 'Ledacom', 'Danilo', 2675548, 'pueblo perdido', '2021-09-30 21:08:02', 1, 1),
(9, 'Casa del Pandebono', 'Carmen Villeta', 2674548, 'Calle 5 # 6-43', '2021-10-03 13:40:04', 8, 1),
(10, 'Tiendas Tecnologicas San Pedro', 'Carlitos Vaca Aguirre', 999555, 'Pueblo Perdido', '2021-10-06 20:39:46', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

DROP TABLE IF EXISTS `rol`;
CREATE TABLE IF NOT EXISTS `rol` (
  `idrol` int(11) NOT NULL AUTO_INCREMENT,
  `rol` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`idrol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idrol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Supervisor'),
(3, 'Vendedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `idusuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `usuario` varchar(15) DEFAULT NULL,
  `clave` varchar(100) DEFAULT NULL,
  `rol` int(11) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idusuario`),
  KEY `rol` (`rol`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `rol`, `estatus`) VALUES
(1, 'Oscar Echeverry Gonzalez', 'oscar@gmail.com', 'admin', '9d87d0e415b28ccc638a396e58e705d3', 1, 1),
(8, 'Paula Echeverry', 'paula@gmail.com', 'paula', '5ed5802afdace39f7cff607e04e8cae3', 2, 1),
(9, 'Pablo Portilla Gonzalez', 'pablo@gmail.com', 'pablo', '202cb962ac59075b964b07152d234b70', 3, 1),
(10, 'jota te', 'j@gmail.com', 'jota', '202cb962ac59075b964b07152d234b70', 3, 1);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD CONSTRAINT `detallefactura_ibfk_2` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD CONSTRAINT `detalle_temp_ibfk_2` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD CONSTRAINT `entradas_ibfk_1` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`codcliente`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`proveedor`) REFERENCES `proveedor` (`codproveedor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `proveedor_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `rol` (`idrol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
