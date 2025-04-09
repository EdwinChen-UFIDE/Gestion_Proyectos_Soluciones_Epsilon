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

// Obtener proyectos
$stmt = $pdo->query("SELECT id, nombre FROM proyectos");
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener el proyecto seleccionado
$proyecto_id = isset($_GET['proyecto_id']) ? (int)$_GET['proyecto_id'] : null;

// Obtener tareas del proyecto seleccionado
$sql = "SELECT t.id, t.nombre, t.descripcion, t.estado_id, e.nombre AS estado, u.email AS responsable
        FROM tareas t
        JOIN estados e ON t.estado_id = e.id
        LEFT JOIN usuarios u ON t.usuario_id = u.id
        WHERE t.proyecto_id = :proyecto_id
        ORDER BY t.estado_id, t.fecha_vencimiento";
$stmt = $pdo->prepare($sql);
$stmt->execute(['proyecto_id' => $proyecto_id]);
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
        background: #fff; 
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); 
    }

    /* cada columna del Kanban */
    .kanban-column {
        flex: 1;
        background: #f1f1f1; 
        padding: 15px;
        border-radius: 8px;
        min-height: 400px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); 
    }

    /* Títulos de las columnas */
    .kanban-column h4 {
        text-align: center;
        font-weight: bold;
        font-size: 1.2rem;
        color: #333; 
        margin-bottom: 15px;
    }

    .kanban-item {
        background: #ffffff; 
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); 
        transition: transform 0.2s; 
    }

    .kanban-item:hover {
        transform: scale(1.02); 
    }

    /* Títulos de las tareas */
    .kanban-item strong {
        font-size: 1.1rem;
        color: #333;
        font-weight: 600; 
    }

    /* Descripción de las tareas */
    .kanban-item p {
        font-size: 0.9rem;
        color: #666;
        margin-top: 5px;
    }

    /* Responsables de las tareas */
    .kanban-item small {
        display: block;
        font-size: 0.8rem;
        color: #888;
        margin-top: 10px;
    }

    /* Botón para agregar tareas */
    #formulario-tarea button {
        background: #28a745;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s ease;
    }

    #formulario-tarea button:hover {
        background: #218838;
    }

    /* Formulario para agregar nuevas tareas */
    #formulario-tarea input, 
    #formulario-tarea textarea, 
    #formulario-tarea select {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
    }

    #formulario-tarea input[type="text"]:focus,
    #formulario-tarea textarea:focus,
    #formulario-tarea select:focus {
        border-color: #28a745;
        outline: none;
    }

    select {
        background: #f8f9fa;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
        font-size: 1rem;
    }

    select:focus {
        border-color: #28a745;
    }
    #formulario-tarea button {
        background: #007bff; 
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: background 0.3s ease, transform 0.3s ease;
    }

    #formulario-tarea button:hover {
        background: #0056b3;
        transform: scale(1.05); 
    }

    #formulario-tarea button:active {
        background: #004085; 
        transform: scale(1); 
    }

    #formulario-tarea button:focus {
        outline: none;
        box-shadow: 0 0 10px rgba(38, 143, 255, 0.8); 
    }
    #agregar-tarea-btn {
    background-color: #007bff; 
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: background 0.3s ease, transform 0.3s ease; 
}

#agregar-tarea-btn:hover {
    background-color: #0056b3; 
    transform: scale(1.05); 
}

#agregar-tarea-btn:active {
    background-color: #004085;
    transform: scale(1); 
}

#agregar-tarea-btn:focus {
    outline: none;
    box-shadow: 0 0 10px rgba(38, 143, 255, 0.8); 
}
</style>

</head>
<body>
    <?php
    MostrarNavbar();
    ?>

<div class="container">
        <h2 class="text-center my-4">Tablero de Tareas</h2>

        <!-- Selección de Proyecto -->
        <form method="GET" action="board.php">
            <label for="proyecto">Seleccionar Proyecto:</label>
            <select name="proyecto_id" id="proyecto" onchange="this.form.submit()">
                <option value="">Seleccione un proyecto</option>
                <?php foreach ($proyectos as $proyecto): ?>
                    <option value="<?= $proyecto['id'] ?>" <?= $proyecto['id'] == $proyecto_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($proyecto['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Mostrar formulario solo si hay un proyecto seleccionado -->
        <?php if ($proyecto_id): ?>
            <button id="agregar-tarea-btn" onclick="mostrarFormulario()">Agregar Tarea</button>

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
                <button onclick="registrar_tarea(<?= $proyecto_id ?>)">Guardar</button>
            </div>
        <?php endif; ?>

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
    </div>
    <script>

        function mostrarFormulario() {
            document.getElementById("formulario-tarea").style.display = "block";
            }

        function registrar_tarea(proyectoId) {
            let nombre = document.getElementById("nombre").value.trim();
            let descripcion = document.getElementById("descripcion").value.trim();
            let estado = document.getElementById("estado").value;
            let usuario = document.getElementById("usuario").value;

            if (!nombre || !descripcion) {
                alert("Todos los campos son obligatorios.");
                return;
            }

            fetch("registrar_tarea.php", {
                method: "POST",
                body: new URLSearchParams({ nombre, descripcion, estado, usuario, proyecto_id: proyectoId }),
                headers: { "Content-Type": "application/x-www-form-urlencoded" }
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    location.reload(); // Recargar la página para actualizar la lista de tareas
                }
            })
            .catch(error => console.error("Error:", error));
        }
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