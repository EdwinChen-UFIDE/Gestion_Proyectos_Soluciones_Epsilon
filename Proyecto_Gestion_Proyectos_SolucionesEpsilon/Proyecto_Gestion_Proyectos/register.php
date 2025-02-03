<?php
// Incluye la configuración de la base de datos
require_once 'db_config.php';

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role_id = 2; // Por defecto, asignamos el rol "user"

    // Validaciones básicas
    if (empty($email) || empty($password)) {
        echo "Por favor, completa todos los campos.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "El formato del correo electrónico no es válido.";
        exit;
    }

    try {
        // Verificar si el correo ya está registrado
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            echo "Este correo electrónico ya está registrado.";
            exit;
        }

        // Cifrar la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar el usuario en la base de datos
        $stmt = $pdo->prepare("INSERT INTO usuarios (email, password, role_id) VALUES (:email, :password, :role_id)");
        $stmt->execute([
            'email' => $email,
            'password' => $hashed_password,
            'role_id' => $role_id
        ]);

        echo "Registro exitoso. Ahora puedes iniciar sesión.";
    } catch (PDOException $e) {
        echo "Error al registrar el usuario: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>
<body>
    <h2>Registro de Usuario</h2>
    <form method="POST" action="">
        <label for="email">Correo Electrónico:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>