<?php
header('Content-Type: application/json');
session_start();

// Verifica si el usuario está autenticado y es un administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'administrador') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Incluye el archivo de conexión a la base de datos
require_once 'db_connection.php';

// Consulta para obtener las emergencias
$sql = "SELECT id, nombre, lat, lng, telefono FROM emergencias";
$result = $conn->query($sql);

// Inicializa el array para las emergencias
$emergencias = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $emergencias[] = $row;
    }
}

// Devuelve las emergencias en formato JSON
echo json_encode($emergencias);

// Cierra la conexión
$conn->close();
?>
