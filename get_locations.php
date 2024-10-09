<?php
header('Content-Type: application/json');
include 'db_connection.php';

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conn->connect_error]));
}

// Consulta para obtener ubicaciones de células
$query_locations = "SELECT c.id AS usuario_id, c.lat, c.lng, u.nombre
                     FROM coordenadas c
                     JOIN usuarios u ON c.usuario_id = u.id
                     WHERE u.tipo = 'celula'";
$result_locations = $conn->query($query_locations);

if (!$result_locations) {
    die(json_encode(['error' => 'Error en la consulta de ubicaciones: ' . $conn->error]));
}

$locations = array();

while ($row = $result_locations->fetch_assoc()) {
    $locations[] = array(
        'tipo' => 'celula',
        'usuario_id' => $row['usuario_id'],
        'lat' => $row['lat'],
        'lng' => $row['lng'],
        'nombre' => $row['nombre']
    );
}

// Consulta para obtener emergencias
$query_emergencies = "SELECT id, nombre, lat, lng, telefono FROM emergencias";
$result_emergencies = $conn->query($query_emergencies);

if (!$result_emergencies) {
    die(json_encode(['error' => 'Error en la consulta de emergencias: ' . $conn->error]));
}

while ($row = $result_emergencies->fetch_assoc()) {
    $locations[] = array(
        'tipo' => 'emergency',
        'id' => $row['id'],
        'lat' => $row['lat'],
        'lng' => $row['lng'],
        'nombre' => $row['nombre'],
        'telefono' => $row['telefono']
    );
}

echo json_encode($locations);
$conn->close();
?>
