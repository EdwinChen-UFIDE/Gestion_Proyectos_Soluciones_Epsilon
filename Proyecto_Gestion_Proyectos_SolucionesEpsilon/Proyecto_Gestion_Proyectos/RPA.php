<?php
Session_start();
require_once 'db_config.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
require_once 'PHPMailer/Exception.php';
require_once 'dompdf/autoload.inc.php';
include 'Plantilla.php';

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
    $monto = floatval($_POST['monto_personalizado']);
    $fecha_limite = date('Y-m-d', strtotime('+7 days', strtotime($fecha)));
    $activa = isset($_POST['activa']) ? 1 : 0;

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

    $pdo->prepare("INSERT INTO facturas (cliente_id, fecha_emision, monto, descripcion, fecha_limite, generado_por_rpa, enviada, pagada) 
                   VALUES (?, ?, ?, ?, ?, 1, 0, 0)")
        ->execute([$cliente_id, $hoy, $monto, $descripcion, $fecha_limite]);

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

    <style>
        .form-container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 25px auto;
            width: 80%;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 12px 20px;
            align-items: center;
        }

        .form-grid label {
            font-weight: bold;
        }

        .form-grid input[type="text"],
        .form-grid input[type="date"],
        .form-grid input[type="number"],
        .form-grid select {
            width: 100%;
            padding: 6px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-actions {
            grid-column: 2 / 3;
            text-align: right;
            margin-top: 10px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 25px;
        }

        .btn-group button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        .btn-group button:hover {
            background-color: #0056b3;
        }

        .form-submit {
            background-color: #28a745;
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        .form-submit:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <?php MostrarNavbar(); ?>

    <div class="form-container">
        <h2 style="text-align:center;">Enviar Factura</h2>

        <div class="btn-group" style="margin-top: 15px;">
            <a href="crear_cliente.php"><button>Registrar Cliente</button></a>
            <a href="reporte_mensual.php"><button>Ver Reporte Mensual</button></a>
            <a href="historial_facturas.php"><button>Ver Historial de Facturas</button></a>
        </div>

        <form method="POST" class="form-grid">
            <label for="cliente_id">Cliente:</label>
            <select name="cliente_id" id="cliente_id" required>
                <option value="">Seleccione un cliente</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="fecha_facturacion">Fecha de Facturación:</label>
            <input type="date" name="fecha_facturacion" id="fecha_facturacion" required>

            <label for="monto_personalizado">Monto a Pagar:</label>
            <input type="number" name="monto_personalizado" id="monto_personalizado" min="1" step="0.01" required>

            <label>Estado:</label>
            <div><input type="checkbox" name="activa" checked> Activa</div>

            <div class="form-actions">
                <button type="submit" name="programar" class="form-submit">Enviar Factura</button>
            </div>
        </form>
    </div>

    <?php if (isset($mensaje_exito) && $mensaje_exito): ?>
        <script>
            Swal.fire({
                title: 'Proceso Finalizado',
                text: '<?= $mensaje_exito ?>',
                icon: '<?= $envio_exitoso ? 'success' : 'warning' ?>'
            });
        </script>
    <?php endif; ?>

    <?php if (isset($error_envio) && $error_envio): ?>
        <script>
            Swal.fire({
                title: 'Error de envío',
                html: 'PHPMailer dijo:<br><?= addslashes($error_envio) ?>',
                icon: 'error'
            });
        </script>
    <?php endif; ?>
</body>


</html>