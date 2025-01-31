-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS SolucionesEpsilon;
USE SolucionesEpsilon;

-- Tabla Estado
CREATE TABLE Estado (
    ID_estado INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_estado VARCHAR(50) NOT NULL UNIQUE,
    Descripcion_estado VARCHAR(250)
) ENGINE=InnoDB;

-- Tabla Usuarios
CREATE TABLE Usuarios (
    ID_usuario INT AUTO_INCREMENT PRIMARY KEY,
    Identificacion VARCHAR(20) NOT NULL UNIQUE,
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(100) NOT NULL,
    Correo VARCHAR(150) NOT NULL UNIQUE,
    Contrasenna VARCHAR(250) NOT NULL,
    Fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla Rol
CREATE TABLE Rol (
    ID_rol INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_rol VARCHAR(50) NOT NULL UNIQUE,
    Descripcion_rol VARCHAR(250)
) ENGINE=InnoDB;

-- Tabla Usuario_Roles
CREATE TABLE Usuario_Roles (
    ID_usuario INT NOT NULL,
    ID_rol INT NOT NULL,
    Fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ID_usuario, ID_rol),
    FOREIGN KEY (ID_usuario) REFERENCES Usuarios(ID_usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_rol) REFERENCES Rol(ID_rol) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla Proyecto
CREATE TABLE Proyecto (
    ID_proyecto INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_proyecto VARCHAR(150) NOT NULL UNIQUE,
    Fecha_inicio DATE,
    Fecha_fin DATE,
    Presupuesto_total DECIMAL(12, 2) CHECK (Presupuesto_total >= 0),
    Descripcion VARCHAR(500),
    Prioridad INT NOT NULL CHECK (Prioridad BETWEEN 1 AND 5),
    ID_estado INT NOT NULL,
    FOREIGN KEY (ID_estado) REFERENCES Estado(ID_estado)
) ENGINE=InnoDB;

-- Tabla Asignacion_Proyecto
CREATE TABLE Asignacion_Proyecto (
    ID_asignacion INT AUTO_INCREMENT PRIMARY KEY,
    ID_usuario INT NOT NULL,
    ID_proyecto INT NOT NULL,
    ID_rol INT NOT NULL,
    Fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    ID_estado INT NOT NULL,
    FOREIGN KEY (ID_usuario) REFERENCES Usuarios(ID_usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_proyecto) REFERENCES Proyecto(ID_proyecto) ON DELETE CASCADE,
    FOREIGN KEY (ID_rol) REFERENCES Rol(ID_rol),
    FOREIGN KEY (ID_estado) REFERENCES Estado(ID_estado)
) ENGINE=InnoDB;

-- Tabla Reporte
CREATE TABLE Reporte (
    ID_reporte INT AUTO_INCREMENT PRIMARY KEY,
    Tipo_reporte VARCHAR(50) NOT NULL,
    Nombre VARCHAR(150) NOT NULL,
    Descripcion VARCHAR(250),
    Fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    ID_usuario INT NOT NULL,
    ID_proyecto INT NOT NULL,
    FOREIGN KEY (ID_usuario) REFERENCES Usuarios(ID_usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_proyecto) REFERENCES Proyecto(ID_proyecto) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla Metodo_Pago
CREATE TABLE Metodo_Pago (
    ID_metodo_pago INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_metodo VARCHAR(50) NOT NULL UNIQUE,
    Descripcion_metodo VARCHAR(250)
) ENGINE=InnoDB;

-- Tabla Contabilidad
CREATE TABLE Contabilidad (
    ID_transaccion INT AUTO_INCREMENT PRIMARY KEY,
    Monto DECIMAL(12, 2) NOT NULL CHECK (Monto >= 0),
    Fecha_transaccion DATETIME DEFAULT CURRENT_TIMESTAMP,
    ID_proyecto INT NOT NULL,
    ID_usuario INT NOT NULL,
    ID_metodo_pago INT NOT NULL,
    ID_estado INT NOT NULL,
    FOREIGN KEY (ID_proyecto) REFERENCES Proyecto(ID_proyecto) ON DELETE CASCADE,
    FOREIGN KEY (ID_usuario) REFERENCES Usuarios(ID_usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_metodo_pago) REFERENCES Metodo_Pago(ID_metodo_pago),
    FOREIGN KEY (ID_estado) REFERENCES Estado(ID_estado)
) ENGINE=InnoDB;

-- Tabla Plantilla
CREATE TABLE Plantilla (
    ID_plantilla INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_plantilla VARCHAR(150) NOT NULL UNIQUE,
    Fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    Es_Publico BIT DEFAULT 0,
    ID_usuario INT NOT NULL,
    ID_estado INT NOT NULL,
    FOREIGN KEY (ID_usuario) REFERENCES Usuarios(ID_usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_estado) REFERENCES Estado(ID_estado)
) ENGINE=InnoDB;

-- Tabla RPA
CREATE TABLE RPA (
    ID_rpa INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_proceso VARCHAR(150) NOT NULL UNIQUE,
    Estado BIT DEFAULT 0,
    Fecha_inicio DATETIME,
    Fecha_fin DATETIME,
    ID_usuario INT NOT NULL,
    ID_estado INT NOT NULL,
    Version VARCHAR(20),
    FOREIGN KEY (ID_usuario) REFERENCES Usuarios(ID_usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_estado) REFERENCES Estado(ID_estado)
) ENGINE=InnoDB;

-- Tabla Auditoria de Usuarios
CREATE TABLE AuditoriaUsuarios (
    ID_auditoria INT AUTO_INCREMENT PRIMARY KEY,
    ID_usuario INT NOT NULL,
    Fecha_evento DATETIME DEFAULT CURRENT_TIMESTAMP,
    Evento VARCHAR(50) NOT NULL,
    Detalle VARCHAR(250)
) ENGINE=InnoDB;

-- ÍNDICES
CREATE INDEX idx_estado ON Proyecto(ID_estado);
CREATE INDEX idx_usuario ON Reporte(ID_usuario);

-- PROCEDIMIENTOS ALMACENADOS
DELIMITER $$

CREATE PROCEDURE sp_AgregarUsuarioConRol(
    IN p_Identificacion VARCHAR(20),
    IN p_Nombre VARCHAR(50),
    IN p_Apellido VARCHAR(100),
    IN p_Correo VARCHAR(150),
    IN p_ID_rol INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    
    INSERT INTO Usuarios (Identificacion, Nombre, Apellido, Correo)
    VALUES (p_Identificacion, p_Nombre, p_Apellido, p_Correo);
    
    SET @ID_usuario = LAST_INSERT_ID();
    
    INSERT INTO Usuario_Roles (ID_usuario, ID_rol)
    VALUES (@ID_usuario, p_ID_rol);
    
    COMMIT;
END$$

CREATE PROCEDURE sp_RegistrarUsuario(
    IN p_Nombre VARCHAR(50),
    IN p_Apellido VARCHAR(100),
    IN p_Correo VARCHAR(150),
    IN p_Contrasenna VARCHAR(255),
    IN p_Fecha_creacion DATETIME
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF (SELECT COUNT(*) FROM Usuarios WHERE Correo = p_Correo) > 0 THEN
        SIGNAL SQLSTATE '45000' -- Codigo de error genercio en caso de sucder--
        SET MESSAGE_TEXT = 'El correo ya está registrado.';
    ELSE
        INSERT INTO Usuarios (Nombre, Apellido, Correo, Contrasenna, Fecha_creacion)
        VALUES (p_Nombre, p_Apellido, p_Correo, p_Contrasenna, CURRENT_TIMESTAMP);
    END IF;
END$$

CREATE PROCEDURE sp_Login(
    IN p_Correo VARCHAR(150),
    IN p_Contrasenna VARCHAR(255)
)
BEGIN
    SELECT ID_usuario, Nombre, Correo, Contrasenna, Fecha_creacion
    FROM Usuarios
    WHERE Correo = p_Correo;
END$$

CREATE PROCEDURE sp_ConsultarUsuarios()
BEGIN
    SELECT * FROM Usuarios;
END$$

CREATE PROCEDURE sp_GetUserById(IN p_Id INT)
BEGIN
    SELECT * FROM Usuarios WHERE ID_usuario = p_Id;
END$$

CREATE PROCEDURE sp_EliminarUsuario(IN p_Id INT)
BEGIN
    DELETE FROM Usuarios WHERE ID_usuario = p_Id;
END$$

DELIMITER ;

-- TRIGGERS
DELIMITER $$

CREATE TRIGGER trg_InsertarUsuarioRol
AFTER INSERT ON Usuarios
FOR EACH ROW
BEGIN
    INSERT INTO AuditoriaUsuarios (ID_usuario, Evento, Detalle)
    VALUES (NEW.ID_usuario, 'INSERT', CONCAT('Usuario insertado con correo: ', NEW.Correo));
END$$

DELIMITER ;

-- VISTAS
CREATE VIEW vw_ProyectosUsuarios AS
SELECT 
    p.ID_proyecto,
    p.Nombre_proyecto,
    p.Fecha_inicio,
    p.Fecha_fin,
    e.Nombre_estado,
    CONCAT(u.Nombre, ' ', u.Apellido) AS Usuario_Asignado
FROM Proyecto p
LEFT JOIN Asignacion_Proyecto ap ON p.ID_proyecto = ap.ID_proyecto
LEFT JOIN Usuarios u ON ap.ID_usuario = u.ID_usuario
JOIN Estado e ON p.ID_estado = e.ID_estado;

CREATE VIEW vw_Usuarios AS
SELECT ID_usuario, Identificacion, Nombre, Apellido, Correo, Fecha_creacion
FROM Usuarios;

-- DATOS INICIALES
INSERT INTO Estado (Nombre_estado, Descripcion_estado) 
VALUES ('Activo', 'Estado activo'), ('Inactivo', 'Estado inactivo');

INSERT INTO Rol (Nombre_rol, Descripcion_rol) 
VALUES ('Administrador', 'Rol con acceso total'), ('Usuario', 'Rol estándar');

INSERT INTO Metodo_Pago (Nombre_metodo, Descripcion_metodo) 
VALUES ('Tarjeta de Crédito', 'Pago con tarjeta de crédito'), ('Transferencia Bancaria', 'Pago por transferencia');