<?php
class pacientes
{
    private $IdPaciente;
    private $IdUsuario;
    private $Nombre;
    private $Cedula;
    private $Edad;
    private $Genero;
    private $Estatura;
    private $Peso;
    private $con;

    function __construct($cn)
    {
        $this->con = $cn;
        //echo "EJECUTANDOSE EL CONSTRUCTOR MARCA<br><br>";
    }

    //******** 3.1 METODO update_consulta() *****************
    public function update_paciente()
    {
        $this->IdPaciente = $_POST['id'];
        $this->IdUsuario = $_POST['IdUsuario'];
        $this->Nombre = $_POST['Nombre'];
        $this->Cedula = $_POST['Cedula'];
        $this->Edad = $_POST['Edad'];
        $this->Genero = $_POST['Genero'];
        $this->Estatura = $_POST['Estatura'];
        $this->Peso = $_POST['Peso'];


        // Verificar si el usuario ya existe en la tabla usuarios
        $resultUsuario = $this->con->query("SELECT IdUsuario FROM usuarios WHERE Nombre = '{$this->IdUsuario}'");
        $rowUsuario = $resultUsuario->fetch_assoc();

        if (!$rowUsuario) {
            // Si el usuario no existe, lo insertamos
            $this->con->query("INSERT INTO usuarios (Nombre,Password, Rol) VALUES ('{$this->IdUsuario}','123' ,'3')");
        }

        // Obtener el IdUsuario del usuario (ya existente o recién insertado)
        $resultUsuario = $this->con->query("SELECT IdUsuario FROM usuarios WHERE Nombre = '{$this->IdUsuario}'");
        $rowUsuario = $resultUsuario->fetch_assoc();
        $idUsuario = $rowUsuario['IdUsuario'];

        $sql = "UPDATE pacientes SET 
        IdUsuario = '{$idUsuario}',
        Nombre='$this->Nombre',
        Cedula='$this->Cedula',
        Edad='$this->Edad',
        Genero='$this->Genero',
        Estatura='$this->Estatura',
        Peso='$this->Peso'
        WHERE IdPaciente=$this->IdPaciente;";

        echo $sql;

        if ($this->con->query($sql)) {
            echo $this->_message_ok("modificó");
        } else {
            echo $this->_message_error("al modificar");
        }
    }



    //******** 3.2 METODO save_consulta() *****************	
    public function save_paciente()
    {
        $this->IdUsuario = $_POST['IdUsuario'];
        $this->Nombre = $_POST['Nombre'];
        $this->Cedula = $_POST['Cedula'];
        $this->Edad = $_POST['Edad'];
        $this->Genero = $_POST['Genero'];
        $this->Estatura = $_POST['Estatura'];
        $this->Peso = $_POST['Peso'];


        // Verificar si el usuario ya existe en la tabla usuarios
        $resultUsuario = $this->con->query("SELECT IdUsuario FROM usuarios WHERE Nombre = '{$this->IdUsuario}'");
        $rowUsuario = $resultUsuario->fetch_assoc();

        if (!$rowUsuario) {
            // Si el usuario no existe, lo insertamos
            $this->con->query("INSERT INTO usuarios (Nombre,Password, Rol) VALUES ('{$this->IdUsuario}','123' ,'2')");
        }

        // Obtener el IdUsuario del usuario (ya existente o recién insertado)
        $resultUsuario = $this->con->query("SELECT IdUsuario FROM usuarios WHERE Nombre = '{$this->IdUsuario}'");
        $rowUsuario = $resultUsuario->fetch_assoc();
        $idUsuario = $rowUsuario['IdUsuario'];

        $sql = "INSERT INTO pacientes VALUES(
            NULL, 
            '{$idUsuario}',
            '$this->Nombre',
            '$this->Cedula',
            '$this->Edad',
            '$this->Genero',
            '$this->Estatura',
            '$this->Peso'
        );";

        if ($this->con->query($sql)) {
            echo $this->_message_ok("guardó");
        } else {
            echo $this->_message_error("guardar");
        }
    }

    //******** 3.3 METODO _get_name_File() *****************	

    private function _get_name_file($nombre_original, $tamanio)
    {
        $tmp = explode(".", $nombre_original); //Divido el nombre por el punto y guardo en un arreglo
        $numElm = count($tmp); //cuento el número de elemetos del arreglo
        $ext = $tmp[$numElm - 1]; //Extraer la última posición del arreglo.
        $cadena = "";
        for ($i = 1; $i <= $tamanio; $i++) {
            $c = rand(65, 122);
            if (($c >= 91) && ($c <= 96)) {
                $c = NULL;
                $i--;
            } else {
                $cadena .= chr($c);
            }
        }
        return $cadena . "." . $ext;
    }
    //TODO VA IGUAL EN ESTA PARTE

    //************* PARTE I ********************
    //Aquí se agregó el parámetro:  $defecto/
    private function _get_combo_db($tabla, $valor, $etiqueta, $nombre, $defecto)
    {
        $html = '<select name="' . $nombre . '">';
        $sql = "SELECT $valor, $etiqueta FROM $tabla;";
        $res = $this->con->query($sql);

        while ($row = $res->fetch_assoc()) {
            $html .= ($defecto == $row[$valor]) ? '<option value="' . $row[$valor] . '" selected>' . $row[$etiqueta] . '</option>' . "\n" : '<option value="' . $row[$valor] . '">' . $row[$etiqueta] . '</option>' . "\n";
        }

        $html .= '</select>';
        return $html;
    }

    /*Aquí se agregó el parámetro:  $defecto
private function _get_combo_anio($nombre,$anio_inicial,$defecto){
    $html = '<select name="' . $nombre . '">';
    $anio_actual = date('Y');
    for($i=$anio_inicial;$i<=$anio_actual;$i++){
        $html .= ($i == $defecto)? '<option value="' . $i . '" selected>' . $i . '</option>' . "\n":'<option value="' . $i . '">' . $i . '</option>' . "\n";
    }
    $html .= '</select>';
    return $html;
}*/

    //Aquí se agregó el parámetro:  $defecto*
    private function _get_radio($arreglo, $nombre, $defecto)
    {

        $html = '
		<table border=0 align="left">';

        //CODIGO NECESARIO EN CASO QUE EL USUARIO NO SE ESCOJA UNA OPCION

        foreach ($arreglo as $etiqueta) {
            $html .= '
			<tr>
				<td>' . $etiqueta . '</td>
				<td>';

            if ($defecto == NULL) {
                // OPCION PARA GRABAR UN NUEVO consulta (id=0)
                $html .= '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>';

            } else {
                // OPCION PARA MODIFICAR UN consulta EXISTENTE
                $html .= ($defecto == $etiqueta) ? '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>' : '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '"/></td>';
            }

            $html .= '</tr>';
        }
        $html .= '
		</table>';
        return $html;
    }
    //************* PARTE II ******************	

    public function get_form($id = NULL)
    {

        if ($id == NULL) {
            $this->IdUsuario = NULL;
            $this->Nombre = NULL;
            $this->Cedula = NULL;
            $this->Edad = NULL;
            $this->Genero = NULL;
            $this->Estatura = NULL;
            $this->Peso = NULL;

            // $flag = NULL;  //VARIABLES AUXILIARES
            $op = "new";
        } else {

            $sql = "SELECT * FROM pacientes WHERE IdPaciente=$id;";
            $res = $this->con->query($sql);
            $row = $res->fetch_assoc();

            $num = $res->num_rows;
            if ($num == 0) {
                $mensaje = "tratar de actualizar la consulta con id= " . $id;
                echo $this->_message_error($mensaje);
            } else {

                // ** TUPLA ENCONTRADA **
                echo "<br>TUPLA <br>";
                echo "<pre>";
                print_r($row);
                echo "</pre>";

                $this->IdUsuario = $row['IdUsuario'];
                $this->Nombre = $row['Nombre'];
                $this->Cedula = $row['Cedula'];
                $this->Edad = $row['Edad'];
                $this->Genero = $row['Genero'];
                $this->Estatura = $row['Estatura'];
                $this->Peso = $row['Peso'];
                //$flag = "enabled";
                $op = "update";
            }
        }
        $Genero = [
            "Fenemino",
            "Masculino"
        ];
        $html = '        
        <form name="Form_Consulta" method="POST" action="paciente.php" enctype="multipart/form-data">
            <!-- Agrego dos líneas -> hidden oculto -->
            <input type="hidden" name="id" value="' . $id . '">
            <input type="hidden" name="op" value="' . $op . '">
            <div class="container mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                             <div class="card-header" style="background-color: #FFA500; color: white;">
                             DATOS DE PACIENTES
                            </div>
                            <div class="card-body">
                            <div class="form-group row">
                                <label for="Nombre" class="col-md-4 col-form-label">Paciente:</label>
                                <div class="col-md-8">
                                    <input type="text"  class="form-control" size="15" name="Nombre" value="' . $this->Nombre . '" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="IdUsuario" class="col-md-4 col-form-label">Nombre de Usuario:</label>
                                <div class="col-md-8">
                                    <input type="text"  class="form-control" size="15" name="IdUsuario" value="' . $this->IdUsuario . '" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="Cedula" class="col-md-4 col-form-label">Cedula:</label>
                                <div class="col-md-8">
                                    <input type="text"  class="form-control" size="15" name="Cedula" value="' . $this->Cedula . '" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="Edad" class="col-md-4 col-form-label">Edad:</label>
                                <div class="col-md-8">
                                    <input type="text"  class="form-control" size="15" name="Edad" value="' . $this->Edad . '" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="Edad" class="col-md-4 col-form-label">Genero:</label>
                                <div class="col-md-8">
                                ' . $this->_get_radio($Genero, "Genero", $this->Genero) . '
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="Estatura" class="col-md-4 col-form-label">Estatuta:</label>
                                <div class="col-md-8">
                                    <input type="text"  class="form-control" size="15" name="Estatura" value="' . $this->Estatura . '" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="Peso" class="col-md-4 col-form-label">Peso:</label>
                                <div class="col-md-8">
                                    <input type="text"  class="form-control" size="15" name="Peso" value="' . $this->Peso . '" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-8 offset-md-4">
                                    <input type="submit" class="btn btn-primary" name="Guardar" value="GUARDAR">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>';
        return $html;
    }

    public function get_list()
    {
        $d_new = "new/0";
        $d_new_final = base64_encode($d_new);
        $html = '
        <div class="container mt-5">
		<div class="table-responsive">
		<button onclick="window.location.href=\'../index.html\'" class="btn btn-primary">Regresar</button>
			<table class="table table-hover table-bordered table-striped text-center" align="middle">
				<thead>
				<tr class="active" style="background-color: #FFA500; color: white;">
				<th colspan="10" class="text-center">Lista de Pacientes</th>
			</tr>
			<tr>
				<th colspan="10" class="text-center align-middle" ><a class="btn btn-outline-success" href="pacientes.php?d=' . $d_new_final . '">Nuevo</a></th>
			</tr>
                    <tr>
                        <th>Usuario</th>
                        <th>Paciente</th>
                        <th>Cedula</th>
                        <th>Edad</th>
                        <th>Genero</th>
                        <th>Estatura</th>
                        <th>Peso</th>
                        <th colspan="5">Acciones</th>
                    </tr>
                </thead>
            <tbody>
        </div>';
        $sql = "SELECT
        p.IdPaciente,
        u.Nombre AS NombreUsuario,
        p.Nombre AS NombrePaciente,
        p.Cedula,
        p.Edad,
        p.Genero,
        p.Estatura,
        p.Peso
    FROM
        pacientes p
    INNER JOIN
        usuarios u ON (p.IdUsuario = u.IdUsuario);";

        $res = $this->con->query($sql);

        if (!$res) {
            die('Error in query: ' . $this->con->error);
        }

        while ($row = $res->fetch_assoc()) {
            $d_del = "del/" . $row['IdPaciente'];
            $d_del_final = base64_encode($d_del);
            $d_act = "act/" . $row['IdPaciente'];
            $d_act_final = base64_encode($d_act);
            $d_det = "det/" . $row['IdPaciente'];
            $d_det_final = base64_encode($d_det);
            $html .= '
            <tr>
                <td>' . $row['NombreUsuario'] . '</td>
                <td>' . $row['NombrePaciente'] . '</td>
                <td>' . $row['Cedula'] . '</td>
                <td>' . $row['Edad'] . '</td>
                <td>' . $row['Genero'] . '</td>
                <td>' . $row['Estatura'] . '</td>
                <td>' . $row['Peso'] . '</td>
                <td class="text-center"><button class="btn btn-danger"><a href="paciente.php?d=' . $d_del_final . '">Borrar</a></button></td>
                <td class="text-center"><button class="btn btn-warning"><a href="paciente.php?d=' . $d_act_final . '">Actualizar</a></button></td>
                <td class="text-center"><button class="btn btn-info"><a href="paciente.php?d=' . $d_det_final . '">Detalle</a></button></td>
            </tr>';
        }
        $html .= '</table>';
        return $html;
    }

    public function get_detail_paciente($id)
    {
        $sql = "SELECT
        p.IdPaciente,
        u.Nombre AS NombreUsuario,
        p.Nombre AS NombrePaciente,
        p.Cedula,
        p.Edad,
        p.Genero,
        p.Estatura,
        p.Peso
        FROM
            pacientes p
        INNER JOIN
            usuarios u ON (p.IdUsuario = u.IdUsuario)
        WHERE p.IdPaciente=$id;";

        $res = $this->con->query($sql);
        $row = $res->fetch_assoc();

        $num = $res->num_rows;


        //Si es que no existiese ningun registro debe desplegar un mensaje 
        //$mensaje = "tratar de eliminar el consulta con id= ".$id;
        //echo $this->_message_error($mensaje);
        //y no debe desplegarse la tablas

        if ($num == 0) {
            $mensaje = "tratar de editar el consulta con id= " . $id;
            echo $this->_message_error($mensaje);
        } else {
            $html = '
            <div class="container mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header" style="background-color: #FFA500; color: white;">
                                DETALLES PACIENTES
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <td>Usuario:</td>
                                        <td>' . $row['NombreUsuario'] . '</td>
                                    </tr>
                                    <tr>
                                        <td>Paciente:</td>
                                        <td>' . $row['NombrePaciente'] . '</td>
                                    </tr>
                                    <tr>
                                        <td>Cedula:</td>
                                        <td>' . $row['Cedula'] . '</td>
                                    <tr>
                                        <td>Genero:</td>
                                        <td>' . $row['Genero'] . '</td>
                                    </tr>
                                    <tr>
                                        <td>Edad:</td>
                                        <td>' . $row['Edad'] . '</td>
                                    </tr>
                                    <tr>
                                        <td>Estatura:</td>
                                        <td>' . $row['Estatura'] . '</td>
                                    </tr>
                                    <tr>
                                        <td>Peso:</td>
                                        <td>' . $row['Peso'] . '</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <a href="paciente.php" class="btn btn-primary">Regresar</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
            return $html;
        }
    }

    public function delete_paciente($id)
    {
        $sql = "DELETE FROM pacientes WHERE IdPaciente=$id;";
        if ($this->con->query($sql)) {
            echo $this->_message_ok("ELIMINÓ");
        } else {
            echo $this->_message_error("eliminar");
        }
    }

    //*************************	

    private function _message_error($tipo)
    {
        $html = '
		<table border="0" align="center">
			<tr>
				<th>Error al ' . $tipo . '. Favor contactar a .................... </th>
			</tr>
			<tr>
				<th><a href="paciente.php">Regresar</a></th>
			</tr>
		</table>';
        return $html;
    }


    private function _message_ok($tipo)
    {
        $html = '
		<table border="0" align="center">
			<tr>
				<th>El registro se  ' . $tipo . ' correctamente</th>
			</tr>
			<tr>
				<th><a href="paciente.php">Regresar</a></th>
			</tr>
		</table>';
        return $html;
    }

    //**************************	

} // FIN SCRPIT