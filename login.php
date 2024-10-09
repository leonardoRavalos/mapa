<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $password = $_POST['password'];
    $tipo = $_POST['tipo'];

    $sql = "SELECT * FROM usuarios WHERE nombre='$nombre' AND tipo='$tipo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['tipo'];

        // Redirigir según el tipo de usuario
        if ($user['tipo'] === 'celula') {
            header("Location: celulas.php");
        } else {
            header("Location: dashboard.php");
        }
        exit(); // Asegúrate de salir después de redirigir
    } else {
        echo "Nombre o contraseña incorrectos.";
    }
}
$conn->close();
?>
