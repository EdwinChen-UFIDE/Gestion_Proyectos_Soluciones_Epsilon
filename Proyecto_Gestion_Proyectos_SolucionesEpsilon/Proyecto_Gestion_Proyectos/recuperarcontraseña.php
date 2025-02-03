<?php
require_once 'db_config.php';

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        echo "<script>alert('Por favor, ingresa tu correo.');</script>";
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $password = $user['password'];
            $subject = "Recuperación de Contraseña";
            $message = "Tu contraseña actual es: " . $password;
            $headers = "From: solucionesepsilonproyecto@gmail.com";

            if (mail($email, $subject, $message, $headers)) {
                echo "<script>alert('Tu contraseña ha sido enviada a tu correo electrónico.');</script>";
            } else {
                echo "<script>alert('Error al enviar el correo.');</script>";
            }
        } else {
            echo "<script>alert('El correo no está registrado.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error en la consulta: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
</head>
<body>
    <h2>Recuperar Contraseña</h2>
    <form method="POST" action="">
        <label for="email">Correo Electrónico:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        <button type="submit">Recuperar Contraseña</button>
    </form>
</body>
</html>