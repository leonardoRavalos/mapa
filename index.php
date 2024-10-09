<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form id="login-form" method="POST" action="login.php">
                <h2>Iniciar Sesión</h2>
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <select name="tipo" required>
                    <option value="celula">Celula</option>
                    <option value="administrador">Administrador</option>
                </select>
                <button type="submit">Iniciar Sesión</button>
                <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
            </form>
        </div>
    </div>
</body>
</html>
