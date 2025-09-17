<?php
session_start();

// 1. Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para calificar.']);
    exit();
}

// 2. Obtener los datos enviados desde el frontend (en formato JSON)
$data = json_decode(file_get_contents('php://input'), true);

$usuario_id = $_SESSION['id'];
$establecimiento_id = $data['establecimiento_id'] ?? 0;
$tipo = $data['tipo'] ?? '';
$puntuacion = $data['puntuacion'] ?? 0;

// 3. Validar los datos
if (empty($establecimiento_id) || !in_array($tipo, ['hotel', 'cabana']) || $puntuacion < 1 || $puntuacion > 5) {
    header('Content-Type: application/json');
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Datos de calificación inválidos.']);
    exit();
}

// 4. Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hospedaje";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit();
}

// 5. Preparar la consulta para insertar o actualizar la calificación
// ON DUPLICATE KEY UPDATE se activa gracias a la UNIQUE KEY que creamos en la tabla
$stmt = $conn->prepare(
    "INSERT INTO calificaciones (usuario_id, establecimiento_id, tipo_establecimiento, puntuacion)
     VALUES (?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE puntuacion = VALUES(puntuacion)"
);
$stmt->bind_param("iisi", $usuario_id, $establecimiento_id, $tipo, $puntuacion);

header('Content-Type: application/json');
if ($stmt->execute()) {
    // Después de guardar, obtener el nuevo promedio para devolverlo al frontend
    $stmt_avg = $conn->prepare(
        "SELECT AVG(puntuacion) AS promedio, COUNT(id) AS total_votos
         FROM calificaciones
         WHERE establecimiento_id = ? AND tipo_establecimiento = ?"
    );
    $stmt_avg->bind_param("is", $establecimiento_id, $tipo);
    $stmt_avg->execute();
    $result_avg = $stmt_avg->get_result()->fetch_assoc();

    echo json_encode([
        'success' => true,
        'message' => '¡Gracias por tu calificación!',
        'nuevoPromedio' => (float)$result_avg['promedio'],
        'totalVotos' => (int)$result_avg['total_votos']
    ]);

    $stmt_avg->close();
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar la calificación.']);
}

$stmt->close();
$conn->close();
?>