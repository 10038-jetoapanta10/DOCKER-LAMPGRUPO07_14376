<?php
  define("SERVER","localhost");
  define("USER","root");
  define("PASS","123");
  define("BD","veris");
  define("PATH","../../imagenes/Usuarios/");

  function conectarBD() {
    $conn = new mysqli(SERVER, USER, PASS, BD);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
  }
 ?>