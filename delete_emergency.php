<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'administrador') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        // Prepara y ejecuta la consulta para eliminar la emergencia
        $stmt = $conn->prepare("DELETE FROM emergencias WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la emergencia.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'ID de emergencia no válido.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido.']);
}

$conn->close();
?>
