<?php
// Incluir la conexión a la base de datos
include 'conexion.php'; // Asegúrate de que este archivo contenga los datos de conexión correctos

// Verificar si el formulario ha sido enviado y si el campo 'codigo_verificacion' existe
if (isset($_POST['codigo_verificacion'])) {
    // Obtener el código de verificación ingresado por el usuario
    $codigo_ingresado = $_POST['codigo_verificacion'];

    // Preparar la consulta SQL para verificar el código
    $stmt = $conn->prepare("SELECT codigo_verificacion, verificado FROM usuario WHERE codigo_verificacion = ?");
    $stmt->bind_param("s", $codigo_ingresado); // "s" indica que el parámetro es una cadena (string)
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró el código
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['verificado'] == 1) {
            echo "Este código ya ha sido verificado anteriormente.";
        } else {
            // Preparar la consulta para actualizar el estado de verificación
            $update_stmt = $conn->prepare("UPDATE usuario SET verificado = 1 WHERE codigo_verificacion = ?");
            $update_stmt->bind_param("s", $codigo_ingresado);
            if ($update_stmt->execute()) {
                //echo "Código verificado exitosamente.";
                echo '<script>
                        alert("Código verificado exitosamente.");
                        window.location="index.html";
                      </script>';

            } else {
                echo "Error actualizando el estado de verificación.";
            }
            $update_stmt->close();
        }
    } else {
        echo "El código de verificación es incorrecto.";
    }

    $stmt->close();
} 

// Cerrar la conexión
$conn->close();
?>
