<?php
class especialidades
{
    private $IdEsp;
    private $Descripcion;
    private $Dias;
    private $Franja_HI;
    private $Franja_HF;
    private $con;

    function __construct($cn)
    {
        $this->con = $cn;
        //echo "EJECUTANDOSE EL CONSTRUCTOR MARCA<br><br>";
    }

    //******** 3.1 METODO update_consulta() *****************
    public function update_especialidades()
    {
        $this->IdEsp = isset($_POST['id']) ? $_POST['id'] : null;
        $this->Descripcion = isset($_POST['Descripcion']) ? $_POST['Descripcion'] : null;
        $this->Dias = isset($_POST['Dias']) ? $_POST['Dias'] : null;
        $this->Franja_HI = isset($_POST['Franja_HI']) ? $_POST['Franja_HI'] : null;
        $this->Franja_HF = isset($_POST['Franja_HF']) ? $_POST['Franja_HF'] : null;

        $sql = "UPDATE especialidades SET 
            Descripcion = '$this->Descripcion',
            Dias = '" . (is_array($this->Dias) ? implode(",", $this->Dias) : $this->Dias) . "',
            Franja_HI = '$this->Franja_HI',
            Franja_HF = '$this->Franja_HF'
            WHERE IdEsp=$this->IdEsp;";

        echo $sql;

        if ($this->con->query($sql)) {
            echo $this->_message_ok("modificó");
        } else {
            echo $this->_message_error("al modificar");
        }
    }




    //******** 3.2 METODO save_consulta() *****************	
    public function save_especialidades()
    {
        // Check if the keys exist in the $_POST array before using them
        $this->Descripcion = isset($_POST['Descripcion']) ? $_POST['Descripcion'] : null;
        $this->Dias = isset($_POST['Dias']) ? $_POST['Dias'] : null;
        $this->Franja_HI = isset($_POST['Franja_HI']) ? $_POST['Franja_HI'] : null;
        $this->Franja_HF = isset($_POST['Franja_HF']) ? $_POST['Franja_HF'] : null;

        // Convert array values to strings for SQL query
        $descripcion = $this->con->real_escape_string($this->Descripcion);
        $dias = is_array($this->Dias) ? implode("", $this->Dias) : $this->Dias;
        $franja_HI = $this->con->real_escape_string($this->Franja_HI);
        $franja_HF = $this->con->real_escape_string($this->Franja_HF);

        $sql = "INSERT INTO especialidades VALUES(
            NULL, 
            '$descripcion',
            '$dias', 
            '$franja_HI',
            '$franja_HF'
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

    //para los dias
//     private function _get_checkboxes($arreglo, $nombre, $defecto)
// {
//     $html = '<table border=0 align="left">';

    //     // CODIGO NECESARIO EN CASO QUE EL USUARIO NO SE ESCOJA UNA OPCION
//     foreach ($arreglo as $etiqueta) {
//         $html .= '<tr>';
//         $html .= '<td>' . $etiqueta . '</td>';
//         $html .= '<td>';

    //         if ($defecto == NULL) {
//             // OPCION PARA GRABAR UN NUEVO consulta (id=0)
//             $html .= '<input type="checkbox" value="' . $etiqueta . '" name="' . $nombre . '[]" checked/></td>';
//         } else {
//             // OPCION PARA MODIFICAR UN consulta EXISTENTE
//             $isChecked = in_array($etiqueta, $defecto) ? 'checked' : '';
//             $html .= '<input type="checkbox" value="' . $etiqueta . '" name="' . $nombre . '[]" ' . $isChecked . '/></td>';
//         }

    //         $html .= '</tr>';
//     }
//     $html .= '</table>';
//     return $html;
// }

    private function _get_checkboxes($arreglo, $nombre, $defecto)
    {
        $html = '<table border=0 align="left">';

        // CODIGO NECESARIO EN CASO QUE EL USUARIO NO SE ESCOJA UNA OPCION
        foreach ($arreglo as $etiqueta) {
            $html .= '<tr>';
            $html .= '<td>' . $etiqueta . '</td>';
            $html .= '<td>';

            if (!is_array($defecto)) {
                // $defecto is a string, convert it to an array
                $defecto = str_split($defecto);
            }

            // OPCION PARA GRABAR UN NUEVO consulta (id=0)
            $isChecked = in_array($etiqueta, $defecto) ? 'checked' : '';
            $html .= '<input type="checkbox" value="' . $etiqueta . '" name="' . $nombre . '[]" ' . $isChecked . '/></td>';

            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }


    //************* PARTE II ******************	

    public function get_form($id = NULL)
    {

        if ($id == NULL) {
            $this->Descripcion = NULL;
            $this->Dias = NULL;
            $this->Franja_HI = NULL;
            $this->Franja_HF = NULL;

            // $flag = NULL;  //VARIABLES AUXILIARES
            $op = "new";
        } else {

            $sql = "SELECT * FROM especialidades WHERE IdEsp=$id;";
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

                $this->Descripcion = $row['Descripcion'];
                $this->Dias = $row['Dias'];
                $this->Franja_HI = $row['Franja_HI'];
                $this->Franja_HF = $row['Franja_HF'];
                //$flag = "enabled";
                $op = "update";
            }
        }
        $Dias = ['L', 'M', 'X', 'J', 'V'];


        $html = '        
        <form name="Form_Consulta" method="POST" action="especialidades.php" enctype="multipart/form-data">
            <!-- Agrego dos líneas -> hidden oculto -->
            <input type="hidden" name="id" value="' . $id . '">
            <input type="hidden" name="op" value="' . $op . '">
            <div class="container mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header" style="background-color: #FFA500; color: white;">
                                DATOS ESPECIALIDADES
                            </div>
                            <div class="card-body">
                            <div class="form-group row">
                                <label for="Descripcion" class="col-md-4 col-form-label">Descripcion:</label>
                                <div class="col-md-8">
                                    <input type="text"  class="form-control" size="15" name="Descripcion" value="' . $this->Descripcion . '" required>
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label for="Edad" class="col-md-4 col-form-label">Dias:</label>
                                <div class="col-md-8">
                                ' . $this->_get_checkboxes($Dias, "Dias", $this->Dias) . '
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="HI" class="col-md-4 col-form-label">Hora Inicio:</label>
                                <div class="col-md-8">
                                    <input type="time"  class="form-control" size="15" name="Franja_HI" value="' . $this->Franja_HI . '" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="HF" class="col-md-4 col-form-label">Hora Final:</label>
                                <div class="col-md-8">
                                    <input type="time"  class="form-control" size="15" name="Franja_HF" value="' . $this->Franja_HF . '" required>
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
                            <th colspan="8" class="text-center">Lista de Medicamentos</th>
                        </tr>
                        <tr>
                            <th colspan="8" class="text-center align-middle" ><a class="btn btn-outline-success" href="medicamentos.php?d=' . $d_new_final . '">Nuevo</a></th>
                        </tr>
                        <tr>
                            <th>Descripcion</th>
                            <th>Dias</th>
                            <th>Hora Inicio</th>
                            <th>Hora Final</th>
                            <th colspan="5">Acciones</th>
                        </tr>
                    </thead>
                <tbody>
            </div>';

        $sql = "SELECT IdEsp, Descripcion, Dias, Franja_HI, Franja_HF
        FROM especialidades;
        ";

        $res = $this->con->query($sql);

        if (!$res) {
            die('Error in query: ' . $this->con->error);
        }

        while ($row = $res->fetch_assoc()) {
            $d_del = "del/" . $row['IdEsp'];
            $d_del_final = base64_encode($d_del);
            $d_act = "act/" . $row['IdEsp'];
            $d_act_final = base64_encode($d_act);
            $d_det = "det/" . $row['IdEsp'];
            $d_det_final = base64_encode($d_det);

            // Access the correct keys from $row for Hora Inicio and Hora Final
            $html .= '
                <tr>
                    <td>' . $row['Descripcion'] . '</td>
                    <td>' . $row['Dias'] . '</td>
                    <td>' . $row['Franja_HI'] . '</td>
                    <td>' . $row['Franja_HF'] . '</td>
                    <td class="text-center"><button class="btn btn-danger"><a href="especialidades.php?d=' . $d_del_final . '">Borrar</a></button></td>
                    <td class="text-center"><button class="btn btn-warning"><a href="especialidades.php?d=' . $d_act_final . '">Actualizar</a></button></td>
                    <td class="text-center"><button class="btn btn-info"><a href="especialidades.php?d=' . $d_det_final . '">Detalle</a></button></td>
                </tr>';
        }
        $html .= '</table>';
        return $html;
    }


    public function get_detail_especialidades($id)
    {
        $sql = "SELECT * FROM especialidades WHERE IdEsp=$id;";

        $res = $this->con->query($sql);

        if (!$res) {
            die('Error in query: ' . $this->con->error);
        }

        $row = $res->fetch_assoc();

        // Check if a row was retrieved
        if (!$row) {
            $mensaje = "No se encontró la especialidad con IdEsp = " . $id;
            echo $this->_message_error($mensaje);
            return;
        }

        $html = '
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header" style="background-color: #FFA500; color: white;">
                            DATOS MEDICAMENTOS
                        </div>
                
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <td>Descripcion:</td>
                                    <td>' . $row['Descripcion'] . '</td>
                                </tr>
                                <tr>
                                    <td>Dias:</td>
                                    <td>' . $row['Dias'] . '</td>
                                </tr>
                                <tr>
                                    <td>Hora Inicio:</td>
                                    <td>' . $row['Franja_HI'] . '</td>
                                </tr>
                                <tr>
                                    <td>Hora Final:</td>
                                    <td>' . $row['Franja_HF'] . '</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <a href="especialidades.php" class="btn btn-primary">Regresar</a>
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


    public function delete_especialidades($id)
    {
        $sql = "DELETE FROM especialidades WHERE IdEsp=$id;";
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
				<th><a href="especialidades.php">Regresar</a></th>
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
				<th><a href="especialidades.php">Regresar</a></th>
			</tr>
		</table>';
        return $html;
    }

    //**************************	

} // FIN SCRPIT