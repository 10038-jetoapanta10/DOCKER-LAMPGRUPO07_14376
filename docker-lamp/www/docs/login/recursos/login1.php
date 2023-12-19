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
    <link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/login.css">

    <!-- Agrega los estilos del formulario -->
    <link rel="stylesheet" type="text/css" href="../css/main.css">
</head>

<body class="bg-light d-flex align-items-center justify-content-center">
    <div class="container text-center">
        <form action="" method="POST">

            <h1>Login</h1>
            <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
            <select name="Nombre" class="form-control">
                    <option disabled selected>Escoje un usuario: </option>
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

            <div class="input-box">
                <input type="password" placeholder="Password" name="Password" class="form-control">
                <i class="bx bxs-lock-alt"></i>
            </div>

           
            <!-- Botón para activar la simulación de 2FA -->
            <button type="button" id="toggle2FA" class="btn btn-secondary mt-3">Activar 2FA</button>
            <input type="hidden" name="2faEnabled" id="2faEnabled" value="off">

            <!-- Campo adicional para el código 2FA, inicialmente oculto -->
            <div id="2faField" style="display: none;">
                <input type="text" name="codigo2FA" class="form-control mt-2" placeholder="Código 2FA" required>
            </div>

            <!-- Mensaje de error para 2FA -->
            <?php if (!empty($mensajeError2FA)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $mensajeError2FA; ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary mt-3" value="LOGIN">Login</button>
            <div class="register-link">
                <p>Quieres salir? <a href="../../index.html">Regresar</a></p>
            </div>
        </form>
    </div>

    <script src="js/bootstrap.min.js"></script>
    <script>
        var toggle2FA = document.getElementById('toggle2FA');
        var field2FA = document.getElementById('2faField');
        var input2FAEnabled = document.getElementById('2faEnabled');

        toggle2FA.addEventListener('click', function() {
            if (field2FA.style.display === "none") {
                field2FA.style.display = "block";
                toggle2FA.textContent = 'Desactivar 2FA';
                input2FAEnabled.value = 'on';
            } else {
                field2FA.style.display = "none";
                toggle2FA.textContent = 'Activar 2FA';
                input2FAEnabled.value = 'off';
            }
        });
    </script>
</body>
</html>
