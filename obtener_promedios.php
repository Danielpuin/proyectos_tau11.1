<?php
// Este script no necesita sesión, ya que los promedios son datos públicos.

// 1. Detalles de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hospedaje";

// 2. Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

$response = ['success' => false, 'promedios' => []];

// 3. Verificar conexión y ejecutar consulta
if (!$conn->connect_error) {
    $sql = "SELECT
              tipo_establecimiento,
              establecimiento_id,
              AVG(puntuacion) AS promedio,
              COUNT(id) AS total_votos
            FROM
              calificaciones
            GROUP BY
              tipo_establecimiento,
              establecimiento_id";

    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $key = $row['tipo_establecimiento'] . '_' . $row['establecimiento_id'];
            $response['promedios'][$key] = [
                'promedio' => (float)$row['promedio'],
                'votos' => (int)$row['total_votos']
            ];
        }
        $response['success'] = true;
    }
    $conn->close();
}

header('Content-Type: application/json');
echo json_encode($response);
?>