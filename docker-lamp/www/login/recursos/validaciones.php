<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once("constantes.php");

function validarPersona($usuario, $Password) {
    if (empty($Password)) {
        return "La contraseña no puede estar vacía";
    }

    // Conectar a la base de datos
    $conexion = conectarBD();
    $query_consult = "SELECT * FROM usuarios WHERE LOWER(Nombre) = '$usuario' AND Password = '$Password'";
    $result = mysqli_query($conexion, $query_consult);

    if ($result) {
        $rows = mysqli_num_rows($result);

        mysqli_free_result($result);
        mysqli_close($conexion);

        if ($rows > 0) {
            return "Usuario validado correctamente";
        } else {
            return "Usuario o contraseña incorrectos"; // Usuario o contraseña incorrectos
        }
    } else {
        mysqli_close($conexion);
        return "Error en la consulta";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Nombre'], $_POST['Password'])) {
        $Nombre = $_POST['Nombre'];
        $Password = $_POST['Password'];

        $mensajeError = validarPersona($Nombre, $Password);

        if ($mensajeError === "Usuario validado correctamente") {
            $_SESSION['Nombre'] = $Nombre;

            $conexion = conectarBD();
            $query_rol = "SELECT Rol FROM usuarios WHERE LOWER(Nombre) = '$Nombre'";
            $result_rol = mysqli_query($conexion, $query_rol);

            if ($result_rol) {
                $row = mysqli_fetch_assoc($result_rol);
                $rol = $row['Rol'];

                switch ($rol) {
                    case 1: // Admin
                        header("Location: index.php");
                        break;
                    case 2: // Medico
                        header("Location: panel1.php");
                        break;
                    case 3: // Paciente
                        header("Location: panel2.php");
                        break;
                    default:
                        break;
                }

                exit();
            } else {
                echo '<div class="alert alert-danger" role="alert">Error al obtener el rol del usuario.</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">' . $mensajeError . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">Por favor, ingrese usuario y contraseña.</div>';
    }
}
?>
