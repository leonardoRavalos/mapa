<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $user_id = $_POST['user_id'];

    // Verificar si ya existe una entrada para este usuario en la tabla coordenadas
    $query = "SELECT * FROM coordenadas WHERE usuario_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si el usuario ya tiene coordenadas, solo se actualizan
        $query = "UPDATE coordenadas SET lat = ?, lng = ? WHERE usuario_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ddi", $lat, $lng, $user_id);
    } else {
        // Si no existe una entrada, se inserta una nueva
        $query = "INSERT INTO coordenadas (usuario_id, lat, lng) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("idd", $user_id, $lat, $lng);
    }

    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>
