<?php
// Incluir el archivo de conexión
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Consultar si el usuario existe
    $sql = "SELECT * FROM usuario WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verificar si la contraseña ingresada coincide
        if (password_verify($contrasena, $user['contrasena'])) {
            // Inicio de sesión exitoso
            echo "Inicio de sesión exitoso. Bienvenido, " . $user['correo'] . ".";
            
             
            // Redirigir a dashboard.php
            //header("Location: dashboard.php");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "El correo electrónico no está registrado.";
    }
    $stmt->close();
}
$conn->close();
?>
