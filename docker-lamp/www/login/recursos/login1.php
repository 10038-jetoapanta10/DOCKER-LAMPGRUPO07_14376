<?php
ob_start();

session_start();
include("constantes.php");
require_once("validaciones.php");

$conn = conectarBD();

// Lógica adicional para el código 2FA
$mensajeError2FA = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo2FA = $_POST['codigo2FA'] ?? '';
    if (isset($_POST['2faEnabled']) && $_POST['2faEnabled'] === 'on' && $codigo2FA !== "123") {
        $mensajeError2FA = "Código 2FA incorrecto.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sesiones en PHP</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="../css/log-in.css" rel="stylesheet" />
</head>

<body class="bg-light d-flex align-items-center justify-content-center">
    <div class="container text-center">
        <!-- Mensaje de "Log In" -->
        <div class="login-message">
            <h2>Log In</h2>
        </div>

        <form action="" method="POST" class="login-form">
            <div class="form-group">
                <select name="Nombre" class="form-control">
                    <option disabled selected>Selecciona un usuario:</option>
                    <?php
                    require_once("usuarios.php");
                    $usuarios = array();

                    // Modificar la consulta para seleccionar solo los usuarios con rol 2
                    $sql = "SELECT IdUsuario, Nombre, Password, Rol FROM usuarios WHERE Rol = 1";

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $usuario = new Usuarios($row["IdUsuario"], $row["Rol"], $row["Nombre"], $row["Password"], $row["Foto"]);
                            $usuarios[] = $usuario;
                        }

                        foreach ($usuarios as $usuario) {
                            echo "<option value='{$usuario->getNombre()}'>{$usuario->getNombre()}</option>";
                        }
                    } else {
                        echo "No se encontraron resultados.";
                    }

                    $conn->close();
                    ?>
                </select>
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                    <i class="fa fa-user" aria-hidden="true"></i>
                </span>
            </div>

            <div class="form-group">
                <input type="password" placeholder="Contraseña" name="Password" class="form-control">
            </div>

            <!-- Toggle Switch -->
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="toggle2FA" name="2faEnabled">
                <label class="custom-control-label" for="toggle2FA">Activar 2FA</label>
            </div>

            <!-- Campo adicional para el código 2FA, inicialmente oculto -->
            <div id="2faField" class="mt-3" style="display: none;">
                <input type="text" name="codigo2FA" class="form-control" placeholder="Código 2FA" required>
            </div>

            <!-- Mensaje de error para 2FA -->
            <?php if (!empty($mensajeError2FA)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?php echo $mensajeError2FA; ?>
                </div>
            <?php endif; ?>

            <div>
            <button type="submit" class="btn btn-primary mt-2" value="LOGIN">Login</button>
            <class="register-link">
                <a href="../../index.html" class="btn btn-danger">Regresar</a>
            </div>
        </form>
    </div>

    <script>
        var toggle2FA = document.getElementById('toggle2FA');
        var field2FA = document.getElementById('2faField');

        toggle2FA.addEventListener('change', function () {
            field2FA.style.display = toggle2FA.checked ? "block" : "none";
        });
    </script>
</body>

</html>