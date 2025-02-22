<?php
session_start();
require_once 'db_config.php';
include 'plantilla.php';
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
// Obtener tareas con detalles de estado y empleado
$sql = "SELECT t.id, t.nombre, t.descripcion, t.estado_id, e.nombre AS estado, u.email AS responsable
        FROM tareas t
        JOIN estados e ON t.estado_id = e.id
        LEFT JOIN usuarios u ON t.usuario_id = u.id
        ORDER BY t.estado_id, t.fecha_vencimiento";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$estados = [
    1 => "Por Hacer",
    2 => "En Progreso",
    3 => "Completado"
];

$tareasPorEstado = [
    1 => [], // Por Hacer
    2 => [], // En Progreso
    3 => []  // Completado
];

// Organizar tareas en sus respectivos estados
foreach ($tareas as $tarea) {
    $estadoId = $tarea['estado_id'];
    $tareasPorEstado[$estadoId][] = $tarea;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablero de Tareas</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&family=Roboto+Slab:wght@400&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.css">
    <style>
        .kanban-board {
            display: flex;
            gap: 20px;
            padding: 20px;
        }
        .kanban-column {
            flex: 1;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            min-height: 400px;
        }
        .kanban-item {
            background: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <?php
    MostrarNavbar();
    ?>

    <div class="container">
    <h2 class="text-center my-4">Tablero de Tareas</h2>
        <button onclick="mostrarFormulario()">Agregar Tarea</button>

        <!-- Formulario oculto -->
        <div id="formulario-tarea" style="display: none;">
    <input type="text" id="nombre" placeholder="Nombre de la tarea" required>
    <textarea id="descripcion" placeholder="Descripción" required></textarea>
    <select id="estado">
        <option value="1">Por Hacer</option>
        <option value="2">En Progreso</option>
        <option value="3">Completado</option>
    </select>
    <select id="usuario">
    <option value="">Seleccione un responsable</option>
    <?php
    $stmt = $pdo->query("SELECT id, email FROM usuarios");
    while ($usuario = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='{$usuario['id']}'>{$usuario['email']}</option>";
    }
    ?>
</select>
    <button onclick="agregarTarea()">Guardar</button>
    </div>
    <div class="kanban-board">
    <?php foreach ($tareasPorEstado as $estadoId => $tareas): ?>
        <div class="kanban-column" data-estado="<?= $estadoId ?>">
            <h4 class="text-center"><?= $estados[$estadoId] ?></h4>
            <?php foreach ($tareas as $tarea): ?>
                <div class="kanban-item" data-id="<?= $tarea['id'] ?>">
                    <strong><?= htmlspecialchars($tarea['nombre']) ?></strong>
                    <p><?= htmlspecialchars($tarea['descripcion']) ?></p>
                    <small>Responsable: <?= htmlspecialchars($tarea['responsable'] ?? 'Sin asignar') ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
    <script src="../JS/script.js" defer></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    let columns = document.querySelectorAll('.kanban-column'); // Obtener todas las columnas
    let drake = dragula([...columns]); // Inicializar Dragula en las columnas

    drake.on('drop', function (el, target, source, sibling) {
        let taskId = el.getAttribute('data-id'); // ID de la tarea arrastrada
        let estadoId = target.getAttribute('data-estado'); // Nuevo estado 

        // Enviar la actualización del estado a la base de datos
        fetch('actualizar_estado_tareas.php', {
            method: 'POST',
            body: JSON.stringify({ id: taskId, estado_id: estadoId }),
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta del servidor:", data); 

            if (data.error) {
                alert("Error al actualizar la tarea: " + data.error);
            } else {
                console.log("Estado actualizado correctamente.");
            }
        })
        .catch(error => {
            console.error("Error en la petición fetch:", error);
        });
    });
});
    </script>