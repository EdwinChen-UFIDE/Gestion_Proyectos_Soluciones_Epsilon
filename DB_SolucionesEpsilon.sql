
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
JOIN Asignacion_Proyecto ap ON p.ID_proyecto = ap.ID_proyecto
JOIN Usuarios u ON ap.ID_usuario = u.ID_usuario
JOIN Estado e ON p.ID_estado = e.ID_estado;
