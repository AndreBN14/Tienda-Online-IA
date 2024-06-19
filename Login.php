<?php
session_start();

// Variable para almacenar el mensaje de error
$error_message = "";

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir el archivo de configuración de la base de datos
    require_once('config/config.php');

    // Verificar si la conexión a la base de datos está disponible
    if (isset($con) && $con) {
        // Obtener los datos del formulario
        $nombre_usuario = $_POST['nombre_usuario'];
        $contraseña = $_POST['contraseña'];

        // Consulta preparada para buscar al usuario por nombre de usuario
        $sql = "SELECT id, nombre, password FROM usuarios WHERE nombre = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $nombre_usuario);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        // Verificar si se encontró al usuario
        if ($user) {
            // Verificar la contraseña
            if (password_verify($contraseña, $user['password'])) {
                // Iniciar sesión y almacenar datos del usuario en variables de sesión
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                header('Location: index.php'); // Redirigir al usuario a la página principal
                exit();
            } else {
                $error_message = "La contraseña ingresada es incorrecta.";
            }
        } else {
            $error_message = "El nombre de usuario ingresado no existe.";
        }

        // Cerrar la consulta
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Error al conectar con la base de datos.";
    }

    // Cerrar la conexión a la base de datos
    if (isset($con) && $con) {
        mysqli_close($con);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/styles/Style_Login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <section>   
        <div class="login-box">
            <form action="" method="POST">
                <h2>Iniciar Sesión</h2>
                <?php if (!empty($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <div class="input-box">
                    <span class="icon"><ion-icon name="person-circle"></ion-icon></span>
                    <input type="text" name="nombre_usuario" required>
                    <label>Usuario</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                    <input type="password" name="contraseña" required>
                    <label>Contraseña</label>
                </div>
                <button type="submit">Ingresar</button>
                <div class="register-link">
                    <p>¿No estás registrado? <a href="registro.php">Regístrate</a></p>
                </div>
            </form>
        </div>
    </section>
        
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
