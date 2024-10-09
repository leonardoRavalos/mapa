<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'celula') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Célula</title>
    <link rel="stylesheet" href="styles.css?v=3">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #eef2f3; /* Color de fondo suave */
        }

        #map {
            width: 100%;
            height: 100vh;
            position: absolute;
            top: 0;
            left: 0;
        }

        /* Menú horizontal */
        .menu {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #ffffff; /* Fondo blanco */
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex; /* Usar flexbox para el menú horizontal */
            gap: 10px; /* Espaciado entre botones */
        }

        .menu button {
            padding: 10px 15px;
            background: #28a745; /* Color verde */
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s, transform 0.2s; /* Añadir efectos de transición */
        }

        .menu button:hover {
            background: #218838; /* Color verde oscuro al pasar el mouse */
            transform: scale(1.05); /* Efecto de aumento en hover */
        }
    </style>
</head>
<body>
    <div class="menu">
        <button id="logout-button">Cerrar sesión</button>
    </div>
    <div id="map"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        mapboxgl.accessToken = 'pk.eyJ1IjoiZ3VpZ281NjciLCJhIjoiY20xOXNqY2o0MTh3ZzJrb2l5amg3OWUwaiJ9.PrF_B7HbcB3B_95mKdsdFA';
        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [-104.894627351769, 21.504914173858914],
            zoom: 12,
            attributionControl: false
        });

        map.addControl(new mapboxgl.NavigationControl());

        var user_id = <?php echo $_SESSION['user_id']; ?>;
        var markers = {};

        <?php if ($_SESSION['user_type'] == 'celula'): ?>
                var user_id = <?php echo $_SESSION['user_id']; ?>;
                var marker;

                function updateLocation(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;

                    fetch('update_location.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'lat=' + lat + '&lng=' + lng + '&user_id=' + user_id
                    });

                    if (!marker) {
                        marker = new mapboxgl.Marker()
                            .setLngLat([lng, lat])
                            .addTo(map);
                    } else {
                        marker.setLngLat([lng, lat]);
                    }

                    map.setCenter([lng, lat]);
                }

                navigator.geolocation.watchPosition(updateLocation, function(error) {
                    console.error("Error de geolocalización: " + error.message);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });

            <?php endif; ?>

        function fetchLocations() {
            fetch('get_locations2.php')
                .then(response => response.json())
                .then(data => {
                    // Remove existing markers
                    Object.values(markers).forEach(marker => marker.remove());
                    markers = {};

                    data.forEach(location => {
    if (location.tipo === 'user') {  // Asegúrate que este tipo sea correcto para la célula
        var marker = new mapboxgl.Marker()
            .setLngLat([location.lng, location.lat])
            .addTo(map);
        
        var popup = new mapboxgl.Popup({ offset: 25 }).setText(location.nombre);
        marker.setPopup(popup);
        markers[location.usuario_id] = marker;
    } else if (location.tipo === 'emergency') {  
        var emergencyMarker = new mapboxgl.Marker({ color: 'red' }) // Marcador rojo para emergencias
            .setLngLat([location.lng, location.lat])
            .addTo(map);
        
        var emergencyPopup = new mapboxgl.Popup({ offset: 25 }).setText(`Emergencia: ${location.nombre}`);
        emergencyMarker.setPopup(emergencyPopup);
        markers[`emergencia_${location.id}`] = emergencyMarker;
    }
});

                })
                .catch(error => console.error('Error al obtener ubicaciones:', error));
        }

        // Llamar a fetchLocations cada segundo
        setInterval(fetchLocations, 1000);

        document.getElementById('logout-button').addEventListener('click', function() {
            fetch('logout.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'index.php'; // Redirigir a la página de inicio
                    } else {
                        console.error('Error al cerrar sesión.');
                    }
                });
        });
    });
</script>

</body>
</html>
