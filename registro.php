<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ya está autenticado, redirigir a la página principal si es así
if (isset($_SESSION['usuario'])) {
    header("Location: index.php"); // Cambia 'index.php' por la página a la que deseas redirigir
    exit();
}

// Incluir la conexión a la base de datos
require_once "config/config.php";

// Variables para mensajes de error y valores del formulario
$mensaje = '';
$nombre = '';
$email = '';

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el email ya está registrado
    $sql_check_email = "SELECT id FROM usuarios WHERE email = ?";
    $stmt_check_email = mysqli_prepare($con, $sql_check_email);
    mysqli_stmt_bind_param($stmt_check_email, "s", $email);
    mysqli_stmt_execute($stmt_check_email);
    mysqli_stmt_store_result($stmt_check_email);

    if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
        $mensaje = "Este correo electrónico ya está registrado.";
    } else {
        // Insertar nuevo usuario en la base de datos
        $sql_insert_user = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
        $stmt_insert_user = mysqli_prepare($con, $sql_insert_user);

        // Hash de la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Bind de parámetros
        mysqli_stmt_bind_param($stmt_insert_user, "sss", $nombre, $email, $hashed_password);

        // Ejecutar la consulta
        if (mysqli_stmt_execute($stmt_insert_user)) {
            // Registro exitoso, redirigir al login
            header("Location: login.php"); // Cambia 'login.php' por la página a la que deseas redirigir después del registro
            exit();
        } else {
            // Error al registrar
            $mensaje = "Error al registrar el usuario. Por favor, inténtalo nuevamente.";
        }

        // Cerrar la consulta de inserción
        mysqli_stmt_close($stmt_insert_user);
    }

    // Cerrar la consulta de verificación de email
    mysqli_stmt_close($stmt_check_email);
}

// Cerrar la conexión a la base de datos
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="assets/styles/style_registro.css"> <!-- Estilo CSS para el formulario de registro -->
</head>
<body>
    <section>
        <div class="login-box">
            <h2>Registro</h2>
            <?php if (!empty($mensaje)): ?>
                <p class="error-message"><?php echo $mensaje; ?></p>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="input-box">
                    <input type="text" name="nombre" id="nombre" required value="<?php echo htmlspecialchars($nombre); ?>">
                    <label for="nombre">Nombre completo</label>
                </div>
                <div class="input-box">
                    <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($email); ?>">
                    <label for="email">Correo electrónico</label>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="password" required>
                    <label for="password">Contraseña</label>
                </div>
                <button type="submit">Registrarse</button>
            </form>
            <div class="register-link">
                <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        </div>
    </section>
</body>
</html>
