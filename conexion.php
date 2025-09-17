<?php
$host = "localhost";
$user = "tu_usuario";
$pass = "tu_contraseña";
$db = "hospedaje";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>