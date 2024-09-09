<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "base_ecomer"; // Asegúrate de que este nombre coincida con el de la base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

?>