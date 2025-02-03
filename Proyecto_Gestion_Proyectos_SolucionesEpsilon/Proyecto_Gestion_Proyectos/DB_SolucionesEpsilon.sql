
-- Crear la base de datos
CREATE DATABASE SolucionesEpsilon;
GO

USE SolucionesEpsilon;
GO

-- Tabla Estado
CREATE TABLE Estado (
    ID_estado INT IDENTITY(1,1) PRIMARY KEY,
    Nombre_estado NVARCHAR(50) NOT NULL UNIQUE, -- UNIQUE para evitar nombres duplicados
    Descripcion_estado NVARCHAR(250)
);

-- Tabla Usuarios
CREATE TABLE Usuarios (
    ID_usuario INT IDENTITY(1,1) PRIMARY KEY,
    Identificacion NVARCHAR(20) NOT NULL UNIQUE,
    Nombre NVARCHAR(50) NOT NULL,
    Apellido NVARCHAR(100) NOT NULL,
    Correo NVARCHAR(150) NOT NULL UNIQUE,
    Contrasenna NVARCHAR(250) NOT NULL,
    Fecha_creacion DATETIME DEFAULT GETDATE()
);

-- Tabla Rol
CREATE TABLE Rol (
    ID_rol INT IDENTITY(1,1) PRIMARY KEY,
    Nombre_rol NVARCHAR(50) NOT NULL UNIQUE, -- UNIQUE para evitar roles duplicados
    Descripcion_rol NVARCHAR(250)
);

-- Tabla Usuario_Roles
CREATE TABLE Usuario_Roles (
    ID_usuario INT NOT NULL,
    ID_rol INT NOT NULL,
    Fecha_asignacion DATETIME DEFAULT GETDATE(),
    PRIMARY KEY (ID_usuario, ID_rol),
    FOREIGN KEY (ID_usuario) REFERENCES Usuarios(ID_usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_rol) REFERENCES Rol(ID_rol) ON DELETE CASCADE
);

-- Tabla Proyecto
CREATE TABLE Proyecto (
    ID_proyecto INT IDENTITY(1,1) PRIMARY KEY,
    Nombre_proyecto NVARCHAR(150) NOT NULL UNIQUE, -- UNIQUE para proyectos únicos
    Fecha_inicio DATE,
    Fecha_fin DATE,
    Presupuesto_total DECIMAL(12, 2) CHECK (Presupuesto_total >= 0), -- Validación positiva
    Descripcion NVARCHAR(500),
    Prioridad INT NOT NULL CHECK (Prioridad BETWEEN 1 AND 5), -- Validación de rango
    ID_estado INT NOT NULL,
    FOREIGN KEY (ID_estado) REFERENCES Estado(ID_estado)
);

-- Tabla Asignacion_Proyecto
CREATE TABLE Asignacion_Proyecto (
    ID_asignacion INT IDENTITY(1,1) PRIMARY KEY,
    ID_usuario INT NOT NULL,
    ID_proyecto INT NOT NULL,
    ID_rol INT NOT NULL,
    Fecha_asignacion DATETIME DEFAULT GETDATE(),
    ID_estado INT NOT NULL,
    FOREIGN KEY (ID_usuario) REFERENCES Usuarios(ID_usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_proyecto) REFERENCES Proyecto(ID_proyecto) ON DELETE CASCADE,
    FOREIGN KEY (ID_rol) REFERENCES Rol(ID_rol),
    FOREIGN KEY (ID_estado) REFERENCES Estado(ID_estado)
);

-- Tabla Reporte
CREATE TABLE Reporte (
    ID_reporte INT IDENTITY(1,1) PRIMARY KEY,
    Tipo_reporte NVARCHAR(50) NOT NULL,
    Nombre NVARCHAR(150) NOT NULL,
    Descripcion NVARCHAR(250),
    Fecha_creacion DATETIME DEFAULT GETDATE(),
    ID_usuario INT NOT NULL,
    ID_proyecto INT NOT NULL,
    FOREIGN KEY (ID_usuario) REFERENCES Usuarios(ID_usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_proyecto) REFERENCES Proyecto(ID_proyecto) ON DELETE CASCADE
);

-- Tabla Metodo_Pago
CREATE TABLE Metodo_Pago (
    ID_metodo_pago INT IDENTITY(1,1) PRIMARY KEY,
    Nombre_metodo NVARCHAR(50) NOT NULL UNIQUE, -- UNIQUE para evitar métodos repetidos
    Descripcion_metodo NVARCHAR(250)
);

-- Tabla Contabilidad
CREATE TABLE Contabilidad (
    ID_transaccion INT IDENTITY(1,1) PRIMARY KEY,
    Monto DECIMAL(12, 2) NOT NULL CHECK (Monto >= 0), -- Validación positiva
    Fecha_transaccion DATETIME DEFAULT GETDATE(),
    ID_proyecto INT NOT NULL,
    ID_usuario INT NOT NULL,
    ID_metodo_pago INT NOT NULL,
    ID_estado INT NOT NULL,
    FOREIGN KEY (ID_proyecto) REFERENCES Proyecto(ID_proyecto) ON DELETE CASCADE,
    FOREIGN KEY (ID_usuario) REFERENCES Usuarios(ID_usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_metodo_pago) REFERENCES Metodo_Pago(ID_metodo_pago),
    FOREIGN KEY (ID_estado) REFERENCES Estado(ID_estado)
);

-- Tabla Plantilla
CREATE TABLE Plantilla (
    ID_plantilla INT IDENTITY(1,1) PRIMARY KEY,
    Nombre_plantilla NVARCHAR(150) NOT NULL UNIQUE, -- UNIQUE para nombres únicos
    Fecha_creacion DATETIME DEFAULT GETDATE(),
    Es_Publico BIT DEFAULT 0,
    ID_usuario INT NOT NULL,
    ID_estado INT NOT NULL,
    FOREIGN KEY (ID_usuario) REFERENCES Usuarios(ID_usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_estado) REFERENCES Estado(ID_estado)
);

-- Tabla RPA
CREATE TABLE RPA (
    ID_rpa INT IDENTITY(1,1) PRIMARY KEY,
    Nombre_proceso NVARCHAR(150) NOT NULL UNIQUE, -- UNIQUE para evitar procesos duplicados
    Estado BIT DEFAULT 0,
    Fecha_inicio DATETIME,
    Fecha_fin DATETIME,
    ID_usuario INT NOT NULL,
    ID_estado INT NOT NULL,
    Version NVARCHAR(20),
    FOREIGN KEY (ID_usuario) REFERENCES Usuarios(ID_usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_estado) REFERENCES Estado(ID_estado)
);
GO

-- Tabla Auditoria de Usuarios
CREATE TABLE AuditoriaUsuarios (
    ID_auditoria INT IDENTITY(1,1) PRIMARY KEY,
    ID_usuario INT NOT NULL,
    Fecha_evento DATETIME DEFAULT GETDATE(),
    Evento NVARCHAR(50) NOT NULL,
    Detalle NVARCHAR(250)
);
GO

-- ÍNDICES
CREATE NONCLUSTERED INDEX idx_estado ON Proyecto(ID_estado);
CREATE NONCLUSTERED INDEX idx_usuario ON Reporte(ID_usuario);

GO

-- PROCEDIMIENTOS ALMACENADOS
CREATE PROCEDURE sp_AgregarUsuarioConRol
    @Identificacion NVARCHAR(20),
    @Nombre NVARCHAR(50),
    @Apellido NVARCHAR(100),
    @Correo NVARCHAR(150),
    @ID_rol INT
AS
BEGIN
    SET NOCOUNT ON;
    BEGIN TRANSACTION;
    BEGIN TRY
        INSERT INTO Usuarios (Identificacion, Nombre, Apellido, Correo)
        VALUES (@Identificacion, @Nombre, @Apellido, @Correo);
        
        DECLARE @ID_usuario INT = SCOPE_IDENTITY();
        INSERT INTO Usuario_Roles (ID_usuario, ID_rol)
        VALUES (@ID_usuario, @ID_rol);
        
        COMMIT TRANSACTION;
    END TRY
    BEGIN CATCH
        ROLLBACK TRANSACTION;
        THROW;
    END CATCH;
END;
GO

--Registrar Usuario
CREATE PROCEDURE sp_RegistrarUsuario
    @Nombre NVARCHAR(50),
    @Apellido NVARCHAR(100),
    @Correo NVARCHAR(150),
    @Contrasenna NVARCHAR(255), 
    @Fecha_creacion DATETIME
AS
BEGIN
    SET NOCOUNT ON;

    -- Verificar si el correo o identificación ya existen
    IF EXISTS (SELECT 1 FROM Usuarios WHERE Correo = @Correo)
    BEGIN
        RAISERROR('El correo ya está registrado.', 16, 1);
        RETURN -1;
    END
    ELSE
    BEGIN
    -- Insertar al nuevo usuario con la contraseña encriptada
    INSERT INTO Usuarios ( Nombre, Apellido, Correo, Contrasenna, Fecha_creacion)
    VALUES (@Nombre, @Apellido, @Correo, @Contrasenna, GETDATE());
    RETURN 1;
    END
END;

--Login
CREATE PROCEDURE sp_Login
    @Correo NVARCHAR(150),
    @Contrasenna NVARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT ID_usuario, Nombre, Correo, Contrasenna, Fecha_creacion
    FROM Usuarios
        WHERE Correo = @Correo;
END;
--Consultar todos usuarios
CREATE PROCEDURE [dbo].[sp_ConsultarUsuarios]
AS
BEGIN
    SELECT * FROM Usuarios;
END
--Consultar usuario por ID
CREATE PROCEDURE [dbo].[sp_GetUserById]
    @Id INT
AS
BEGIN
    SELECT * FROM Usuarios WHERE ID_usuario = @Id;
END
-- Eliminar Usuario
CREATE PROCEDURE [dbo].[sp_EliminarUsuario]
    @Id INT
AS
BEGIN
    DELETE FROM Usuarios WHERE ID_usuario = @Id;
END

-- DATOS INICIALES
INSERT INTO Estado (Nombre_estado, Descripcion_estado) 
VALUES ('Activo', 'Estado activo'), ('Inactivo', 'Estado inactivo');

INSERT INTO Rol (Nombre_rol, Descripcion_rol) 
VALUES ('Administrador', 'Rol con acceso total'), ('Usuario', 'Rol estándar');

INSERT INTO Metodo_Pago (Nombre_metodo, Descripcion_metodo) 
VALUES ('Tarjeta de Crédito', 'Pago con tarjeta de crédito'), ('Transferencia Bancaria', 'Pago por transferencia');

GO

-- TRIGGERS
CREATE TRIGGER trg_InsertarUsuarioRol
ON Usuarios
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    INSERT INTO AuditoriaUsuarios (ID_usuario, Evento, Detalle)
    SELECT 
        ID_usuario,
        'INSERT',
        CONCAT('Usuario insertado con correo: ', Correo)
    FROM INSERTED;
END;
GO

-- VISTAS
CREATE VIEW vw_ProyectosUsuarios
AS
SELECT 
    p.ID_proyecto,
    p.Nombre_proyecto,
    p.Fecha_inicio,
    p.Fecha_fin,
    e.Nombre_estado,
    u.Nombre + ' ' + u.Apellido AS Usuario_Asignado
FROM Proyecto p
LEFT JOIN Asignacion_Proyecto ap ON p.ID_proyecto = ap.ID_proyecto --en caso de que haya proyectos sin asignación de usuarios
LEFT JOIN Usuarios u ON ap.ID_usuario = u.ID_usuario --en caso de que haya proyectos sin asignación de usuarios
JOIN Estado e ON p.ID_estado = e.ID_estado;

--Vista de usuarios
CREATE VIEW vw_Usuarios
AS
SELECT ID_usuario, Identificacion, Nombre, Apellido, Correo, Fecha_creacion
FROM Usuarios






-- Crear base de datos
CREATE DATABASE IF NOT EXISTS soluciones_epsilon;
USE soluciones_epsilon;

-- Crear tabla de roles
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Insertar roles predefinidos
INSERT IGNORE INTO roles (nombre) VALUES
('admin'),
('user');

-- Crear tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- Crear tabla para el historial de sesiones
CREATE TABLE IF NOT EXISTS historial_sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    navegador VARCHAR(255) NOT NULL,
    inicio_sesion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Insertar usuario administrador por defecto
INSERT IGNORE INTO usuarios (email, password, role_id) VALUES
('admin@example.com', PASSWORD('Admin@123'), 1); -- Contraseña: Admin@123