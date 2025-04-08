<?php
require_once 'db_config.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
require_once 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Conexión a la BD
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Fecha actual + 3 días
$fechaLimite = date('Y-m-d', strtotime('+3 days'));

// Buscar facturas impagas cuya fecha límite sea en 3 días
$sql = "SELECT f.*, c.nombre, c.correo 
        FROM facturas f 
        JOIN clientes c ON f.cliente_id = c.id 
        WHERE f.pagada = 0 AND f.fecha_limite = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$fechaLimite]);
$facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Enviar correo por cada factura
foreach ($facturas as $factura) {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ariberro03@gmail.com';
        $mail->Password = 'fqym bozq hckx hdkf';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('tucorreo@gmail.com', 'Soluciones Epsilon');
        $mail->addAddress($factura['correo'], $factura['nombre']);

        $mail->isHTML(true);
        $mail->Subject = 'Recordatorio de Pago - Factura pendiente';
        $mail->Body = "
            Estimado/a <strong>{$factura['nombre']}</strong>,<br><br>
            Este es un recordatorio de que su factura con fecha de emisión <strong>{$factura['fecha_emision']}</strong>
            por un monto de <strong>₡" . number_format($factura['monto'], 2) . "</strong> tiene como fecha límite de pago el <strong>{$factura['fecha_limite']}</strong>.<br><br>
            Le agradecemos realizar el pago a tiempo para evitar inconvenientes.<br><br>
            <em>Soluciones Epsilon</em>
        ";

        $mail->send();

        // Opcional: registrar que se envió el recordatorio (log, o campo en la BD)
        echo "Recordatorio enviado a {$factura['correo']}<br>";
    } catch (Exception $e) {
        echo "Error al enviar a {$factura['correo']}: {$mail->ErrorInfo}<br>";
    }
}
