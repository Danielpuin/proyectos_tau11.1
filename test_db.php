<?php
$host = "localhost"; // o la IP de tu servidor
$user = "root";
$pass = "";
$db = "hospedaje";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
echo "¡Conexión exitosa!";
$conn->close();
?>