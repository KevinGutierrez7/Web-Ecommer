<?php
// Incluir el archivo de conexión
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombreCompleto = $_POST['nombreCompleto'];
    $usuario = $_POST['usuario'];
    $correo = $_POST['correo'];
    $contrasenaRegistro = $_POST['contrasenaRegistro'];

    // Verificar si el correo electrónico ya está registrado
    $sql = "SELECT * FROM usuario WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "El correo electrónico ya está registrado.";
    } else {
        // Cifrar la contraseña antes de guardarla
        $hashed_contrasena = password_hash($contrasenaRegistro, PASSWORD_BCRYPT);

        // Insertar los datos del usuario en la base de datos
        $sql = "INSERT INTO usuario (correo, nom_comp, n_usuario, contrasena) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $correo, $nombreCompleto, $usuario, $hashed_contrasena);

        if ($stmt->execute()) {
           

            echo "<script>
                alert('Registro exitoso!');
                window.location='index.html';
                </script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
    $stmt->close();
}
$conn->close();
?>
