<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emergencia_id = $_POST['emergencia_id'];

    require 'db_connection.php'; // Conexión a la base de datos

    $stmt = $conn->prepare("SELECT lat, lng FROM emergencias WHERE id = ?");
    $stmt->bind_param('i', $emergencia_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(['lat' => null, 'lng' => null]);
    }

    $stmt->close();
    $conn->close();
}
?>
