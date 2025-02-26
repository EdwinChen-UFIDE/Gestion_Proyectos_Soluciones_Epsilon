<?php
session_start();
include 'db_config.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener la URL de la plantilla seleccionada
$template_url = $_GET['url'] ?? '';

if (empty($template_url)) {
    die("Error: No se recibió una URL de plantilla.");
}

// Guardar la plantilla en la base de datos
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("UPDATE usuarios SET template_seleccionado = :template WHERE id = :id");
$stmt->execute(['template' => $template_url, 'id' => $user_id]);

// Guardar en sesión y redirigir a la plantilla
$_SESSION['template_selected'] = $template_url;
header("Location: " . $template_url);
exit();
?>