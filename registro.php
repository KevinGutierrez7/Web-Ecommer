<?php
// Incluir el archivo de conexión
include 'conexion.php';
require 'vendor/autoload.php'; // Cargar PHPMailer con Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombreCompleto = $_POST['nombreCompleto'];
    $usuario = $_POST['usuario'];
    $correo = $_POST['correo'];
    $contrasenaRegistro = $_POST['contrasenaRegistro'];
    $confirmarContrasena = $_POST['confirmarContrasena'];

    // Validar que las contraseñas coincidan
    if ($contrasenaRegistro !== $confirmarContrasena) {
        echo "<script>alert('Las contraseñas no coinciden.'); window.history.back();</script>";
        exit();
    }

    // Validar que la contraseña sea segura
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $contrasenaRegistro)) {
        echo "<script>alert('La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un símbolo.'); window.history.back();</script>";
        exit();
    }

    // Generar un código de verificación aleatorio
    $codigoVerificacion = rand(100000, 999999);

    // Verificar si el correo electrónico ya está registrado
    $sql = "SELECT * FROM usuario WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    
    // Verifica si se ha preparado correctamente la consulta
    if (!$stmt) {
        echo "Error al preparar la consulta: " . $conn->error;
        exit();
    }

    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "El correo electrónico ya está registrado.";
    } else {
        // Cifrar la contraseña antes de guardarla
        $hashed_contrasena = password_hash($contrasenaRegistro, PASSWORD_BCRYPT);

        // Insertar los datos del usuario en la base de datos
        $sql = "INSERT INTO usuario (correo, nom_comp, n_usuario, contrasena, codigo_verificacion) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // Verifica si se ha preparado correctamente la consulta
        if (!$stmt) {
            echo "Error al preparar la consulta de inserción: " . $conn->error;
            exit();
        }

        $stmt->bind_param("sssss", $correo, $nombreCompleto, $usuario, $hashed_contrasena, $codigoVerificacion);

        if ($stmt->execute()) {
            // Enviar correo de verificación
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'infcorreodepracticas1258@gmail.com';
                $mail->Password = '*************';  // Recuerda no compartir la contraseña real
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Destinatario y contenido
                $mail->setFrom('infcorreodepracticas1258@gmail.com', 'ArteCom');
                $mail->addAddress($correo);
                $mail->isHTML(true);
                $mail->Subject = 'Código de verificación';
                $mail->Body = "Tu código de verificación es: $codigoVerificacion";

                $mail->send();
                // Redirigir a la página de verificación
                header("Location: verificacion.php");
                exit();
            } catch (Exception $e) {
                echo "Error al enviar el correo: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error al ejecutar la consulta de inserción: " . $stmt->error;
        }

        $stmt->close();
    }
    $conn->close();
}
?>
