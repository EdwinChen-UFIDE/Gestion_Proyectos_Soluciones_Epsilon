<?php
Session_start();
require_once 'db_config.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
require_once 'PHPMailer/Exception.php';
require_once 'dompdf/autoload.inc.php';
Include 'Plantilla.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;


// Conexión
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$hoy = date('Y-m-d');
$mensaje_exito = false;
$envio_exitoso = false;
$error_envio = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['programar'])) {
    $cliente_id = $_POST['cliente_id'];
    $fecha = $_POST['fecha_facturacion'];
    $monto = isset($_POST['monto_personalizado']) ? floatval($_POST['monto_personalizado']) : 0;
    $activa = isset($_POST['activa']) ? 1 : 0;

    if ($monto <= 0) {
        echo "<script>
            Swal.fire({
                title: 'Monto inválido',
                text: 'Debe ingresar un monto mayor a cero.',
                icon: 'warning'
            });
        </script>";
        exit;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM rpa_programacion_facturas WHERE cliente_id = ?");
    $stmt->execute([$cliente_id]);
    $existe = $stmt->fetchColumn();

    if ($existe) {
        $pdo->prepare("UPDATE rpa_programacion_facturas SET fecha_facturacion = ?, activa = ? WHERE cliente_id = ?")
            ->execute([$fecha, $activa, $cliente_id]);
    } else {
        $pdo->prepare("INSERT INTO rpa_programacion_facturas (cliente_id, fecha_facturacion, activa) VALUES (?, ?, ?)")
            ->execute([$cliente_id, $fecha, $activa]);
    }

    $descripcion = "Factura generada automáticamente el " . $hoy;

    $pdo->prepare("INSERT INTO facturas (cliente_id, fecha_emision, monto, descripcion, generado_por_rpa, enviada) 
                   VALUES (?, ?, ?, ?, 1, 0)")
        ->execute([$cliente_id, $hoy, $monto, $descripcion]);

    $stmt = $pdo->prepare("SELECT correo, nombre FROM clientes WHERE id = ?");
    $stmt->execute([$cliente_id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente && $cliente['correo']) {
        try {
            $dompdf = new Dompdf();
            $html = "
                <h2>Factura - Soluciones Epsilon</h2>
                <p><strong>Cliente:</strong> {$cliente['nombre']}</p>
                <p><strong>Fecha:</strong> $hoy</p>
                <p><strong>Monto:</strong> ₡" . number_format($monto, 2) . "</p>
                <p><strong>Descripción:</strong> $descripcion</p>
            ";
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $pdf = $dompdf->output();
            $archivo_pdf = "Factura_{$cliente_id}_$hoy.pdf";
            file_put_contents($archivo_pdf, $pdf);

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ariberro03@gmail.com';
            $mail->Password = 'fqym bozq hckx hdkf';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('ariberro03@gmail.com', 'Soluciones Epsilon');
            $mail->addAddress($cliente['correo'], $cliente['nombre']);

            $mail->isHTML(true);
            $mail->Subject = 'Factura generada automáticamente';
            $mail->Body = "Estimado/a <strong>{$cliente['nombre']}</strong>,<br><br>
                           Adjuntamos su factura correspondiente al día <strong>$hoy</strong>.<br><br>
                           Gracias por su preferencia.<br><br>
                           <em>Soluciones Epsilon</em>";

            $mail->addAttachment($archivo_pdf);
            $mail->send();
            unlink($archivo_pdf);

            $envio_exitoso = true;
            $pdo->prepare("UPDATE facturas SET enviada = 1 WHERE cliente_id = ? AND fecha_emision = ?")
                ->execute([$cliente_id, $hoy]);
        } catch (Exception $e) {
            $error_envio = $mail->ErrorInfo;
        }
    }

    $mensaje_exito = "Factura generada, guardada y " . ($envio_exitoso ? "enviada correctamente." : "no pudo ser enviada.");
}

$clientes = $pdo->query("SELECT * FROM clientes")->fetchAll(PDO::FETCH_ASSOC);
$historial = $pdo->query("SELECT f.*, c.nombre FROM facturas f JOIN clientes c ON f.cliente_id = c.id 
    WHERE generado_por_rpa = 1 ORDER BY f.fecha_emision DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>RPA - Facturación</title>
    <link rel="stylesheet" href="../CSS/contabilidad.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php MostrarNavbar(); ?>

<div class="main-container">

    <div class="form-container">
        <a href="crear_cliente.php"><button class="btn-add">Registrar Nuevo Cliente</button></a>
    </div>

    <div class="form-container">
        <h2>Buscar Cliente por ID</h2>
        <label>ID del Cliente:</label>
        <input type="number" id="buscar_id" oninput="buscarCliente()" placeholder="Ingrese ID..."><br>
        <label>Nombre:</label>
        <input type="text" id="nombre_cliente" readonly>
    </div>

    <div class="form-container">
        <h2>Enviar Factura</h2>
        <form method="POST">
            <label>Cliente:</label>
            <select name="cliente_id" required>
                <option value="">Seleccione un cliente</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select><br>

            <label>Fecha de Facturación:</label>
            <input type="date" name="fecha_facturacion" required><br>

            <label>Monto a Pagar:</label>
            <input type="number" name="monto_personalizado" min="1" step="0.01" required><br>

            <label><input type="checkbox" name="activa" checked> Activa</label><br>

            <button type="submit" name="programar" class="btn-submit">Enviar Factura</button>
        </form>
    </div>

    <div class="form-container">
        <h2>Historial de Facturas</h2>
        <table border="1" cellpadding="8" cellspacing="0" style="width:100%;">
            <tr>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Descripción</th>
                <th>Estado</th>
            </tr>
            <?php foreach ($historial as $h): ?>
                <tr>
                    <td><?= htmlspecialchars($h['nombre']) ?></td>
                    <td><?= $h['fecha_emision'] ?></td>
                    <td>₡<?= number_format($h['monto'], 2) ?></td>
                    <td><?= htmlspecialchars($h['descripcion']) ?></td>
                    <td><?= $h['enviada'] ? '📧 Enviada' : '❌ No enviada' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<script>
function buscarCliente() {
    const id = document.getElementById("buscar_id").value;
    if (id === "") {
        document.getElementById("nombre_cliente").value = "";
        return;
    }

    fetch("buscar_cliente.php?id=" + id)
        .then(res => res.json())
        .then(data => {
            if (data && data.nombre) {
                document.getElementById("nombre_cliente").value = data.nombre;
            } else {
                document.getElementById("nombre_cliente").value = "";
                Swal.fire({
                    title: "Cliente no encontrado",
                    text: "El ID ingresado no está registrado.",
                    icon: "warning"
                });
            }
        });
}

<?php if ($mensaje_exito): ?>
Swal.fire({
    title: 'Proceso Finalizado',
    text: '<?= $mensaje_exito ?>',
    icon: '<?= $envio_exitoso ? 'success' : 'warning' ?>'
});
<?php endif; ?>

<?php if ($error_envio): ?>
Swal.fire({
    title: 'Error al enviar el correo',
    html: 'PHPMailer dijo:<br><small><?= addslashes($error_envio) ?></small>',
    icon: 'error'
});
<?php endif; ?>
</script>
</body>
</html>