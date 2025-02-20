<?php
session_start(); // Iniciar sesión

// Si el usuario ya ha iniciado sesión, redirigirlo al HomePage
if (isset($_SESSION['user_id'])) {
    header("Location: HomePage.php");
    exit();
}

// Incluye la configuración de la base de datos
require_once 'db_config.php';

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validaciones básicas
    if (empty($email) || empty($password)) {
        $_SESSION['alert'] = ['type' => 'warning', 'message' => 'Por favor, completa todos los campos.'];
        header("Location: login.php");
        exit();
    }

    try {
        // Verificar si el usuario existe en la base de datos
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verificar la contraseña
            if (password_verify($password, $user['password'])) {
                // Iniciar sesión y establecer variables de sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role_id'] = $user['role_id']; // Guardamos el rol para usarlo en la navbar

                // Mensaje de éxito
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Inicio de sesión exitoso.', 'redirect' => 'HomePage.php'];
            } else {
                $_SESSION['alert'] = ['type' => 'error', 'message' => 'Contraseña incorrecta.'];
            }
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'El usuario no está registrado.'];
        }
    } catch (PDOException $e) {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'Error en la consulta: ' . $e->getMessage()];
    }

    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
   // <link rel="stylesheet" href="../CSS/estilos.css"> <!-- Enlazar el CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&family=Roboto+Slab:wght@400&display=swap" rel="stylesheet"> <!-- Incluir la fuente -->
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="form-container"> <!-- Contenedor del formulario -->
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Iniciar Sesión</button>
            <a href='register.php'>Registrarse</a>
        </form>
    </div>

    <!-- Mostrar alertas con SweetAlert2 si hay un mensaje -->
    <?php if (isset($_SESSION['alert'])): ?>
        <script>
            Swal.fire({
                icon: "<?= $_SESSION['alert']['type']; ?>",
                title: "<?= $_SESSION['alert']['message']; ?>",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Aceptar"
            }).then(() => {
                <?php if (!empty($_SESSION['alert']['redirect'])): ?>
                    window.location.href = "<?= $_SESSION['alert']['redirect']; ?>";
                <?php endif; ?>
            });
        </script>
        <?php unset($_SESSION['alert']); // Limpiar la alerta después de mostrarla ?>
    <?php endif; ?>

</body>
</html>

