<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css?v=3">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.2.0/mapbox-gl-draw.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.2.0/mapbox-gl-draw.css" rel="stylesheet" />
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css" rel="stylesheet" />
    <style>
    /* Estilos generales */
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

    /* Contenedores */
    #emergency-form-container, #emergency-list-container, #assign-route-container {
        position: absolute;
        bottom: 20px;
        right: 10px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        max-width: 300px;
        display: none; /* Mantener oculto inicialmente */
    }

    /* Encabezados */
    #emergency-form-container h2, #emergency-list-container h2, #assign-route-container h2 {
        margin-top: 0;
        color: #343a40; /* Color de texto más oscuro */
    }

    /* Etiquetas y campos de entrada */
    #emergency-form-container label, #emergency-list-container label, #assign-route-container label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #495057; /* Color de texto */
    }

    #emergency-form-container input[type="text"], #assign-route-container select {
        width: 100%;
        padding: 10px;
        margin-bottom: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        transition: border-color 0.3s;
    }

    #emergency-form-container input[type="text"]:focus, #assign-route-container select:focus {
        border-color: #28a745; /* Cambio de color al enfocar */
        outline: none; /* Eliminar el contorno */
    }

    /* Botones de formulario */
    #emergency-form-container button, #assign-route-container button {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s, transform 0.2s; /* Añadir efectos de transición */
    }

    #emergency-form-container button:hover, #assign-route-container button:hover {
        background-color: #218838;
        transform: scale(1.05); /* Efecto de aumento en hover */
    }

    /* Tarjetas */
    .card {
        background: white;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 10px;
        margin: 5px 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s; /* Añadir efectos de transición */
    }

    .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); /* Sombra al pasar el mouse */
    }

    .card button {
        background: red;
        color: white;
        border: none;
        padding: 5px;
        cursor: pointer;
        border-radius: 5px;
        transition: background 0.3s; /* Añadir efectos de transición */
    }

    .card button:hover {
        background: darkred;
    }
</style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            mapboxgl.accessToken = 'pk.eyJ1IjoiZ3VpZ281NjciLCJhIjoiY20xOXNqY2o0MTh3ZzJrb2l5amg3OWUwaiJ9.PrF_B7HbcB3B_95mKdsdFA';
            var map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v11',
                center: [-104.894627351769, 21.504914173858914],
                zoom: 12
            });
            var draw = new MapboxDraw({
                displayControlsDefault: false,
                controls: {
                    polygon: true,
                    trash: true
                }
            });
            map.addControl(draw);
            var user_id = <?php echo $_SESSION['user_id']; ?>;
            

            map.addControl(new mapboxgl.NavigationControl());

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

            // Function to save the drawn polygon
            document.getElementById('save-polygon').addEventListener('click', function() {
                var data = draw.getAll();
                if (data.features.length > 0) {
                    var coordinates = JSON.stringify(data.features[0].geometry.coordinates);
                    fetch('save_polygon.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'user_id=' + user_id + '&coordinates=' + encodeURIComponent(coordinates)
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Polígono guardado con éxito!');
                        } else {
                            alert('Error al guardar el polígono.');
                        }
                    });
                } else {
                    alert('Dibuja un polígono antes de guardarlo.');
                }
            });


            var markers = {};
            var emergencyMarkers = {};// Asegúrate de que esta variable esté inicializada adecuadamente.
            let popupsActive = false; // Estado para controlar la visibilidad de los popups

// Función para alternar el estado de los popups
function togglePopups() {
    popupsActive = !popupsActive;
    document.getElementById('togglePopups').textContent = popupsActive ? 'Desactivar Etiquetas' : 'Activar Etiquetas';
}

function fetchLocations() {
    fetch('get_locations.php')
        .then(response => response.json())
        .then(data => {
            // Remove existing markers
            Object.values(emergencyMarkers).forEach(({ marker }) => marker.remove());
            emergencyMarkers = {};

            data.forEach(location => {
                if (location.tipo === 'emergency') {
                    var marker = new mapboxgl.Marker({ color: 'red' })
                        .setLngLat([location.lng, location.lat])
                        .addTo(map);

                    var popupContent = `<strong>${location.nombre}</strong><br>Teléfono: ${location.telefono || 'No disponible'}`;
                    var popup = new mapboxgl.Popup({ offset: 25 }).setHTML(popupContent);

                    // Store the marker and popup together
                    emergencyMarkers[location.id] = { marker, popup };

                    // Attach the popup to the marker
                    marker.setPopup(popup);

                    // Mostrar/ocultar el popup según el estado
                    if (popupsActive) {
                        marker.togglePopup(); // Muestra el popup si está activo
                    }
                } else {
                    if (markers[location.usuario_id]) {
                        markers[location.usuario_id].setLngLat([location.lng, location.lat]);
                    } else {
                        var marker = new mapboxgl.Marker()
                            .setLngLat([location.lng, location.lat])
                            .addTo(map);

                        var popup = new mapboxgl.Popup({ offset: 25 }).setText(location.nombre);
                        markers[location.usuario_id] = marker;
                        marker.setPopup(popup);
                    }
                }
            });
        });
}

// Asignar el evento al botón
document.getElementById('togglePopups').addEventListener('click', togglePopups);

// Llamar a fetchLocations cada segundo
setInterval(fetchLocations, 1000);


            <?php if ($_SESSION['user_type'] == 'administrador'): ?>
                document.getElementById('add-emergency-form').addEventListener('submit', function(event) {
                    event.preventDefault();
                    var nombre = document.getElementById('emergency-name').value;
                    var lat = parseFloat(document.getElementById('emergency-lat').value);
                    var lng = parseFloat(document.getElementById('emergency-lng').value);
                    var telefono = document.getElementById('emergency-phone').value || '';

                    fetch('add_emergency.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'nombre=' + encodeURIComponent(nombre) + '&lat=' + lat + '&lng=' + lng + '&telefono=' + encodeURIComponent(telefono)
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fetchLocations();
                            document.getElementById('emergency-form-container').style.display = 'none';
                        } else {
                            console.error('Error al agregar la emergencia.');
                        }
                    });
                });

                document.getElementById('delete-emergency-button').addEventListener('click', function() {
    fetch('get_emergencies.php')
        .then(response => response.json())
        .then(data => {
            var container = document.getElementById('emergency-list');
            container.innerHTML = '';
            data.forEach(emergency => {
                var card = document.createElement('div');
                card.className = 'card';
                card.innerHTML = `
                    <strong>${emergency.nombre}</strong><br>
                    Lat: ${emergency.lat}<br>
                    Lng: ${emergency.lng}<br>
                    Teléfono: ${emergency.telefono}<br>
                    <button data-id="${emergency.id}">Eliminar</button>
                `;
                container.appendChild(card);
            });
        });

    document.getElementById('emergency-list').addEventListener('click', function(event) {
        if (event.target.tagName === 'BUTTON') {
            var id = event.target.getAttribute('data-id');
            fetch('delete_emergency.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + id
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchLocations();
                    event.target.parentElement.remove();
                    // Ocultar el contenedor después de eliminar
                    document.getElementById('emergency-list-container').style.display = 'none';
                } else {
                    console.error('Error al eliminar la emergencia.');
                }
            });
        }
    });
});
            <?php endif; ?>

            document.getElementById('toggle-emergency-form').addEventListener('click', function() {
                var form = document.getElementById('emergency-form-container');
                form.style.display = form.style.display === 'block' ? 'none' : 'block';
            });

            document.getElementById('delete-emergency-button').addEventListener('click', function() {
                var form = document.getElementById('emergency-list-container');
                form.style.display = form.style.display === 'block' ? 'none' : 'block';
            });

            document.getElementById('logout-button').addEventListener('click', function() {
                fetch('logout.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'index.php';
                        }
                    });
            });
        });
    </script>
    

</head>
<body>
    <div id="map"></div>

    <div class="menu">
    <?php if ($_SESSION['user_type'] == 'administrador'): ?>
        <button id="toggle-emergency-form">Agregar Emergencia</button>
        
            <button id="delete-emergency-button">Eliminar Emergencias</button>
            <button id="togglePopups">Activar Etiquetas</button>
            <button id="save-polygon">Guardar Polígono</button>
        
        <button id="logout-button">Cerrar Sesión</button>
    </div>

    <div id="emergency-form-container">
        <h2>Agregar Emergencia</h2>
        <form id="add-emergency-form">
            <label for="emergency-name">Nombre:</label>
            <input type="text" id="emergency-name" required>
            <label for="emergency-lat">Latitud:</label>
            <input type="text" id="emergency-lat" required>
            <label for="emergency-lng">Longitud:</label>
            <input type="text" id="emergency-lng" required>
            <label for="emergency-phone">Teléfono:</label>
            <input type="text" id="emergency-phone">
            <button type="submit">Agregar</button>
        </form>
    </div>

    <div id="emergency-list-container">
        <h2>Lista de Emergencias</h2>
        <div id="emergency-list"></div>
    </div>
    

    <?php endif; ?>

    

</body>
</html>
