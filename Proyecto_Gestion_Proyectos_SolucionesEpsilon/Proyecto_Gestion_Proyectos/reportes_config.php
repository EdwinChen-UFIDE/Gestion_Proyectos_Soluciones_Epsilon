<?php
$REPORTES = [
    'proyectos_activos' => [
        'titulo' => 'Proyectos Activos',
        'query' => "SELECT nombre, cliente, fecha_creacion FROM proyectos WHERE estado = 'En progreso'",
        'headers' => ['Nombre', 'Cliente', 'Fecha de Creación'],
        'params' => false
    ],
    'tareas_por_usuario' => [
        'titulo' => 'Tareas por Usuario',
        'query' => "SELECT t.nombre, e.email, t.estado_id FROM tareas t
                    LEFT JOIN usuarios e ON t.usuario_id = e.id
                    WHERE t.usuario_id = ?",
        'headers' => ['Tarea', 'Usuario', 'Estado'],
        'params' => true
    ],
    'tareas_estado' => [
        'titulo' => 'Estado de las Tareas',
        'query' => "SELECT e.nombre, COUNT(t.id) as total FROM estados e
                    LEFT JOIN tareas t ON e.id = t.estado_id
                    GROUP BY e.nombre",
        'headers' => ['Estado', 'Total'],
        'params' => false
    ],
    'usuarios_activos' => [
        'titulo' => 'Usuarios Activos/Inactivos',
        'query' => "SELECT u.email, MAX(h.inicio_sesion) as ultima_sesion FROM usuarios u
                    LEFT JOIN historial_sesiones h ON u.id = h.usuario_id
                    GROUP BY u.email",
        'headers' => ['Usuario', 'Última Sesión'],
        'params' => false
    ],
    'tareas_por_proyecto' => [
        'titulo' => 'Tareas por Proyecto',
        'query' => "SELECT p.nombre AS proyecto, t.nombre AS tarea, e.nombre AS estado
                    FROM tareas t
                    JOIN proyectos p ON t.proyecto_id = p.id
                    JOIN estados e ON t.estado_id = e.id
                    ORDER BY p.nombre, t.estado_id",
        'headers' => ['Proyecto', 'Tarea', 'Estado'],
        'params' => false
    ],
    'evaluaciones_desempeno' => [
        'titulo' => 'Evaluaciones de Desempeño',
        'query' => "SELECT u.nombre, u.apellidos, e.fecha, e.puntuacion, e.horas_trabajadas
                    FROM evaluaciones_desempeno e
                    JOIN usuarios u ON e.usuario_id = u.id
                    ORDER BY e.fecha DESC",
        'headers' => ['Empleado','Apellido', 'Fecha', 'Puntuación', 'Horas Trabajadas'],
        'params' => false
    ],
    'clientes_y_facturas' => [
        'titulo' => 'Clientes y Facturas',
        'query' => "SELECT c.nombre AS cliente, f.fecha_emision, f.monto, f.pagada
                    FROM facturas f
                    JOIN clientes c ON f.cliente_id = c.id
                    ORDER BY c.nombre, f.fecha_emision DESC",
        'headers' => ['Cliente', 'Fecha de Emisión', 'Monto', 'Estado de Pago'],
        'params' => false
    ],
    'transacciones_por_categoria' => [
        'titulo' => 'Transacciones por Categoría',
        'query' => "SELECT c.nombre AS categoria, t.tipo, SUM(t.monto) AS total
                    FROM transacciones t
                    LEFT JOIN categorias_gastos c ON t.categoria_id = c.id
                    GROUP BY c.nombre, t.tipo
                    ORDER BY c.nombre, t.tipo",
        'headers' => ['Categoría', 'Tipo', 'Monto Total'],
        'params' => false
    ],
    'historial_sesiones_usuarios' => [
        'titulo' => 'Historial de Sesiones de Usuarios',
        'query' => "SELECT u.email, h.ip_address, h.navegador, h.inicio_sesion
                    FROM historial_sesiones h
                    LEFT JOIN usuarios u ON h.usuario_id = u.id
                    ORDER BY h.inicio_sesion DESC",
        'headers' => ['Usuario', 'IP', 'Navegador', 'Fecha de Sesión'],
        'params' => false
    ],
    'proyectos_estado' => [
        'titulo' => 'Proyectos y su Estado',
        'query' => "SELECT p.nombre AS proyecto, p.estado AS estado, p.fecha_creacion
                    FROM proyectos p
                    ORDER BY p.fecha_creacion DESC",
        'headers' => ['Proyecto', 'Estado', 'Fecha de Creación'],
        'params' => false
    ],
    'rpa_programacion_facturas' => [
        'titulo' => 'Programación de Facturas RPA',
        'query' => "SELECT c.nombre AS cliente, r.fecha_facturacion, r.activa
                    FROM rpa_programacion_facturas r
                    JOIN clientes c ON r.cliente_id = c.id
                    ORDER BY r.fecha_facturacion DESC",
        'headers' => ['Cliente', 'Fecha de Facturación', 'Activa'],
        'params' => false
    ]
];
?>
