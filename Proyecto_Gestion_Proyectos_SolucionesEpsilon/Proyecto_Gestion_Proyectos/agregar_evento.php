<?php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO calendario (titulo, fecha_inicio, fecha_fin, tipo) VALUES (?, ?, ?, 'Tarea')");
        $stmt->execute([$titulo, $fecha_inicio, $fecha_fin]);

        echo "Evento agregado correctamente";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
