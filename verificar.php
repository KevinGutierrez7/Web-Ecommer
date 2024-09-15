<?php
include 'conexion.php';

// Obtener el JSON enviado desde el cliente
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// Imprimir datos recibidos para depuración
error_log("Datos crudos recibidos: $rawData");
error_log("Datos decodificados: " . print_r($data, true));

// Verificar si json_decode devolvió null
if ($data === null) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar los datos.']);
    exit();
}

// Verificar si los datos necesarios están presentes
if (!isset($data['codigo']) || !isset($data['correo'])) {
    echo json_encode(['success' => false, 'error' => 'Código o correo no proporcionado.']);
    exit();
}

$codigo = $data['codigo'];
$correo = $data['correo'];

// Verificar que el código de verificación y el correo coincidan
$sql = "SELECT * FROM usuario WHERE codigo_verificacion = ? AND correo = ?";
$stmt = $conn->prepare($sql);

// Verificar si la consulta se preparó correctamente
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta.']);
    exit();
}

$stmt->bind_param("ss", $codigo, $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Verificar si el usuario ya está verificado
    if ($row['verificado'] == 1) {
        echo json_encode(['success' => false, 'error' => 'Este correo ya ha sido verificado.']);
    } else {
        // Actualizar el estado de verificación del usuario
        $sql = "UPDATE usuario SET verificado = 1 WHERE codigo_verificacion = ? AND correo = ?";
        $stmt = $conn->prepare($sql);

        // Verificar si la consulta se preparó correctamente
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta de actualización.']);
            exit();
        }

        $stmt->bind_param("ss", $codigo, $correo);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Correo verificado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el estado de verificación.']);
        }
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Código de verificación o correo incorrectos.']);
}

// Cerrar la conexión y liberar recursos
$stmt->close();
$conn->close();
?>
