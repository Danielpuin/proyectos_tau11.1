<?php
// Es una buena práctica iniciar una sesión para manejar el estado del usuario
session_start();

// Detalles de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
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
$contrasena = $_POST['contrasena'] ?? '';

// Validación básica
if (empty($usuario) || empty($contrasena)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Usuario y contraseña son obligatorios.']);
    exit();
}

// Preparar y buscar al usuario
$stmt = $conn->prepare("SELECT id, usuario, contrasena FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: application/json');

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Verificar la contraseña
    if (password_verify($contrasena, $user['contrasena'])) {
        // Guardar datos en la sesión
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['usuario'] = $user['usuario'];

        // Devolver datos del usuario al frontend
        echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso.', 'userId' => $user['id'], 'userName' => $user['usuario']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos.']);
}

$stmt->close();
$conn->close();
?>