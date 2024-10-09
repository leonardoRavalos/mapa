<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conexión a la base de datos
    include 'db_connection.php';

    // Escapar las variables para evitar inyección SQL
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $lat = mysqli_real_escape_string($conn, $_POST['lat']);
    $lng = mysqli_real_escape_string($conn, $_POST['lng']);
    $telefono = !empty($_POST['telefono']) ? mysqli_real_escape_string($conn, $_POST['telefono']) : NULL;

    // Insertar la nueva emergencia en la tabla de emergencias
    $sql = "INSERT INTO emergencias (nombre, lat, lng, telefono) VALUES ('$nombre', '$lat', '$lng', '$telefono')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al insertar en la base de datos']);
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($conn);
}
?>
