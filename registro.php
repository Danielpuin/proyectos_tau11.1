<?php
// Detalles de la conexión a la base de datos
$servername = "localhost";
$username = "root"; // Usuario por defecto de XAMPP
$password = ""; // Contraseña por defecto de XAMPP
$dbname = "hospedaje";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit();
}

// Obtener datos de la solicitud POST
$usuario = $_POST['usuario'] ?? '';
$correo = $_POST['correo'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

// Validación básica
if (empty($usuario) || empty($correo) || empty($contrasena)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit();
}

// Verificar si el usuario o correo ya existen
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ? OR correo = ?");
$stmt->bind_param("ss", $usuario, $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'El usuario o correo electrónico ya existe.']);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Hashear la contraseña por seguridad
$contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

// Preparar y ejecutar la inserción
$stmt = $conn->prepare("INSERT INTO usuarios (usuario, correo, contrasena) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $usuario, $correo, $contrasena_hash);

header('Content-Type: application/json');
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => '¡Registro exitoso! Ahora puedes iniciar sesión.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>