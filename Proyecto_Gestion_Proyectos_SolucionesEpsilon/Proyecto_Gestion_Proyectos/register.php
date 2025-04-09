<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
session_start();
require_once 'db_config.php';
Include 'Plantilla.php';
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $fecha_nacimiento = trim($_POST['fecha_nacimiento']);
    $cedula = trim($_POST['cedula']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role_id = 2;

    if (empty($nombre) || empty($apellidos) || empty($fecha_nacimiento) || empty($cedula) || empty($telefono) || empty($email) || empty($password)) {
        $_SESSION['mensaje'] = "Por favor, completa todos los campos.";
        $_SESSION['mensaje_tipo'] = "error";
        header("Location: register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['mensaje'] = "El formato del correo electrónico no es válido.";
        $_SESSION['mensaje_tipo'] = "error";
        header("Location: register.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['mensaje'] = "Este correo electrónico ya está registrado.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: register.php");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellidos, fecha_nacimiento, cedula, telefono, email, password, role_id) VALUES (:nombre, :apellidos, :fecha_nacimiento, :cedula, :telefono, :email, :password, :role_id)");
        $stmt->execute([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fecha_nacimiento,
            'cedula' => $cedula,
            'telefono' => $telefono,
            'email' => $email,
            'password' => $hashed_password,
            'role_id' => $role_id
        ]);

        $_SESSION['mensaje'] = "¡Registro exitoso!";
        $_SESSION['mensaje_tipo'] = "success";
        header("Location: register.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al registrar el usuario: " . $e->getMessage();
        $_SESSION['mensaje_tipo'] = "error";
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="../CSS/estilos.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&family=Roboto+Slab:wght@400&display=swap" rel="stylesheet"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php MostrarNavbar(); ?>
    <div class="form-container"> 
        <h2>Registro de Usuario</h2>

        <form method="POST" action="register.php">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" required>

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

            <label for="cedula">Cédula:</label>
            <input type="text" id="cedula" name="cedula" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <div class="btn-container">
                <button type="submit" class="btn">Registrar</button>
                <button type="button" class="btn" onclick="window.location.href='login.php';">Iniciar Sesión</button>
            </div>
        </form>
    </div>

    <!-- Mostrar SweetAlert -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <script>
            Swal.fire({
                title: '<?= $_SESSION['mensaje_tipo'] === "success" ? "¡Éxito!" : "¡Error!" ?>',
                text: '<?= $_SESSION['mensaje'] ?>',
                icon: '<?= $_SESSION['mensaje_tipo'] ?>',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                <?php if ($_SESSION['mensaje_tipo'] === "success"): ?>
                    window.location = "login.php";
                <?php endif; ?>
            });
        </script>
        <?php unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo']); ?>
    <?php endif; ?>
</body>
</html>
