<?php
session_start();
require_once 'db_config.php';
include 'Plantilla.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Proyectos</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php MostrarNavbar(); ?>

<div class="form-container">
    <h2>Placeholder temporal para desarrollo futuro (Calendario)</h2>
    <div id="calendario"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendario');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        events: 'obtener_eventos.php', // Carga eventos desde la BD
        selectable: true,
        select: function(info) {
            let titulo = prompt("Ingrese el nombre del evento:");
            if (titulo) {
                fetch('agregar_evento.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        titulo: titulo,
                        fecha_inicio: info.startStr,
                        fecha_fin: info.endStr
                    })
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    calendar.refetchEvents();
                });
            }
        }
    });

    calendar.render();
});
</script>

</body>
</html>
