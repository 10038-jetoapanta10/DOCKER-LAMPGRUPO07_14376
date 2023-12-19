<?php
ob_start();

session_start();
include("constantes.php");
require_once("validaciones.php");

$conn = conectarBD();
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
          $sql = "SELECT IdUsuario, Nombre, Password FROM usuarios";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $usuario = new Usuarios($row["IdUsuario"],$row["Rol"] ,$row["Nombre"], $row["Password"], $row["Foto"]);
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
      <button type="submit" class="btn" value="LOGIN">Login</button>
      <div class="register-link">
        <p>Quieres salir? <a href="../../index.html">Regresar</a></p>
      </div>

    </form>
    <?php
    ?>


  </div>

  <script src="js/bootstrap.min.js"></script>
</body>

</html>
