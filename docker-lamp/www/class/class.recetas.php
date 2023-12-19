<?php
class recetas
{
    private $IdReceta;
    private $IdConsulta;
    private $IdMedicamento;
    private $Cantidad;
    private $con;

    function __construct($cn)
    {
        $this->con = $cn;
        //echo "EJECUTANDOSE EL CONSTRUCTOR MARCA<br><br>";
    }

    //******** 3.1 METODO update_consulta() *****************
    public function update_receta()
    {
        $this->IdReceta = $_POST['id'];
        $this->IdConsulta = $_POST['consulta'];
        $this->IdMedicamento = $_POST['medicamento'];
        $this->Cantidad = $_POST['cantidad'];

        $sql = "UPDATE recetas SET 
                IdConsulta='$this->IdConsulta',
                IdMedicamento='$this->IdMedicamento',
                Cantidad='$this->Cantidad'
                WHERE IdReceta = $this->IdReceta;";

        echo $sql;

        if ($this->con->query($sql)) {
            echo $this->_message_ok("modificó");
        } else {
            echo $this->_message_error("al modificar");
        }
    }



    //******** 3.2 METODO save_consulta() *****************	
    public function save_receta()
    {
        $this->IdReceta = $_POST['id'];
        $this->IdConsulta = $_POST['consulta'];
        $this->IdMedicamento = $_POST['medicamento'];
        $this->Cantidad = $_POST['cantidad'];

        $sql = "INSERT INTO recetas VALUES(
                    NULL, 
                    '$this->IdConsulta',
                    '$this->IdMedicamento',
                    '$this->Cantidad'
                );";

        if ($this->con->query($sql)) {
            echo $this->_message_ok("guardó");
        } else {
            echo $this->_message_error("guardar");
        }
    }

    //******** 3.3 METODO _get_name_File() *****************	
    //sin foto
    // private function _get_name_file($nombre_original, $tamanio)
    // {
    //     $tmp = explode(".", $nombre_original); //Divido el nombre por el punto y guardo en un arreglo
    //     $numElm = count($tmp); //cuento el número de elemetos del arreglo
    //     $ext = $tmp[$numElm - 1]; //Extraer la última posición del arreglo.
    //     $cadena = "";
    //     for ($i = 1; $i <= $tamanio; $i++) {
    //         $c = rand(65, 122);
    //         if (($c >= 91) && ($c <= 96)) {
    //             $c = NULL;
    //             $i--;
    //         } else {
    //             $cadena .= chr($c);
    //         }
    //     }
    //     return $cadena . "." . $ext;
    // }
    //TODO VA IGUAL EN ESTA PARTE

    //******** 3.3 METODO _get_name_File() *****************	

    // private function _get_name_file($nombre_original, $tamanio)
    // {
    //     $tmp = explode(".", $nombre_original); //Divido el nombre por el punto y guardo en un arreglo
    //     $numElm = count($tmp); //cuento el número de elemetos del arreglo
    //     $ext = $tmp[$numElm - 1]; //Extraer la última posición del arreglo.
    //     $cadena = "";
    //     for ($i = 1; $i <= $tamanio; $i++) {
    //         $c = rand(65, 122);
    //         if (($c >= 91) && ($c <= 96)) {
    //             $c = NULL;
    //             $i--;
    //         } else {
    //             $cadena .= chr($c);
    //         }
    //     }
    //     return $cadena . "." . $ext;
    // }
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

    /*Aquí se agregó el parámetro:  $defecto*
	private function _get_radio($arreglo,$nombre,$defecto){
		
		$html = '
		<table border=0 align="left">';
		
		//CODIGO NECESARIO EN CASO QUE EL USUARIO NO SE ESCOJA UNA OPCION
		
		foreach($arreglo as $etiqueta){
			$html .= '
			<tr>
				<td>' . $etiqueta . '</td>
				<td>';
				
				if($defecto == NULL){
					// OPCION PARA GRABAR UN NUEVO consulta (id=0)
					$html .= '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>';
				
				}else{
					// OPCION PARA MODIFICAR UN consulta EXISTENTE
					$html .= ($defecto == $etiqueta)? '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>' : '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '"/></td>';
				}
			
			$html .= '</tr>';
		}
		$html .= '
		</table>';
		return $html;
	}*/


    //************* PARTE II ******************	

    public function get_form($id = NULL)
    {

        if ($id == NULL) {
            $this->IdConsulta = NULL;
            $this->IdMedicamento = NULL;
            $this->Cantidad = NULL;

            $flag = NULL;  //VARIABLES AUXILIARES
            $op = "new";
        } else {

            $sql = "SELECT * FROM recetas WHERE IdReceta=$id;";
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

                $this->IdConsulta = $row['IdConsulta'];
                $this->IdMedicamento = $row['IdMedicamento'];
                $this->Cantidad = $row['Cantidad'];

                $flag = "enabled";
                $op = "update";
            }
        }
        $html = '
        <form name="Form_Consulta" method="POST" action="receta.php" enctype="multipart/form-data">
            <!-- Agrego dos líneas -> hidden oculto -->
            <input type="hidden" name="id" value="' . $id  . '">
            <input type="hidden" name="op" value="' . $op  . '">
            <div class="container mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header" style="background-color: #FFA500; color: white;">
                            DATOS RECETAS
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="IdConsulta" class="col-md-4 col-form-label">Consulta:</label>
                                    <div class="col-md-8">
                                        <input type="text"  class="form-control" size="15" name="consulta" value="' . $this->IdConsulta . '">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="IdMedicamento" class="col-md-4 col-form-label">Medicamento:</label>
                                    <div class="col-md-8">
                                    ' . $this->_get_combo_db("medicamentos","IdMedicamento","Nombre","medicamento",$this->IdMedicamento) . '
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="Cantidad" class="col-md-4 col-form-label">Cantidad:</label>
                                    <div class="col-md-8">
                                        <input type="text"  class="form-control" size="15" name="cantidad" value="' . $this->Cantidad . '">
                                    </div>
                                </div>
                                </div><div class="form-group row">
                                <div class="col-md-8 offset-md-4">
                                    <input type="submit" class="btn btn-primary" name="Guardar" value="GUARDAR">
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
				<th colspan="8" class="text-center">Lista de Medicos</th>
			</tr>
			<tr>
				<th colspan="8" class="text-center align-middle" ><a class="btn btn-outline-success" href="recetas.php?d=' . $d_new_final . '">Nuevo</a></th>
			</tr>
                    <tr>
                        <th>Paciente</th>
                        <th>Medicamento</th>
                        <th>Cantidad</th>
                        <th colspan="5">Acciones</th>
                    </tr>
                </thead>
            <tbody>
        </div>';
        $sql = "SELECT
        r.IdReceta,
        p.Nombre AS Paciente,
        m.Nombre AS Medicamento,
        r.Cantidad
        FROM
            recetas r
        INNER JOIN medicamentos m ON m.IdMedicamento = r.IdMedicamento
        INNER JOIN consultas c ON c.IdConsulta = r.IdConsulta
        LEFT JOIN pacientes p ON c.IdPaciente = p.IdPaciente;";
        $res = $this->con->query($sql);
        while ($row = $res->fetch_assoc()) {
            $d_del = "del/" . $row['IdReceta'];
            $d_del_final = base64_encode($d_del);
            $d_act = "act/" . $row['IdReceta'];
            $d_act_final = base64_encode($d_act);
            $d_det = "det/" . $row['IdReceta'];
            $d_det_final = base64_encode($d_det);
            $html .= '
            <tr>
                <td>' . $row['Paciente'] . '</td>
                <td>' . $row['Medicamento'] . '</td>
                <td>' . $row['Cantidad'] . '</td>
                <td class="text-center"><button class="btn btn-danger"><a href="receta.php?d=' . $d_del_final . '">Borrar</a></button></td>
                <td class="text-center"><button class="btn btn-warning"><a href="receta.php?d=' . $d_act_final . '">Actualizar</a></button></td>
                <td class="text-center"><button class="btn btn-info"><a href="receta.php?d=' . $d_det_final . '">Detalle</a></button></td>
            </tr>';
        }
        $html .= '</table>';
        return $html;
    }

    public function get_detail_receta($id)
    {

        $sql = "SELECT
        p.Nombre AS nombrepaciente,
        m.Nombre AS Medicamento,
        r.Cantidad
        FROM
            recetas r
        INNER JOIN medicamentos m ON m.IdMedicamento = r.IdMedicamento
        INNER JOIN (
            SELECT
                c.IdConsulta AS consulta,
                p.Nombre
            FROM
                consultas c
            LEFT JOIN pacientes p ON c.IdPaciente = p.IdPaciente
        ) p ON p.consulta = r.IdConsulta
        WHERE
            r.IdReceta = $id;";

        // $sql = "SELECT IdConsulta AS nombrepaciente, IdMedicamento, Cantidad
        //     FROM recetas
        //     WHERE IdReceta = $id;";

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
            $html = '<div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header" style="background-color: #FFA500; color: white;">
                            DATOS RECETAS
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <td>Paciente:</td>
                                    <td>' . $row['nombrepaciente'] . '</td>
                                </tr>
                                <tr>
                                    <td>Medicamento:</td>
                                    <td>' . $row['Medicamento'] . '</td>
                                </tr>
                                <tr>
                                    <td>Cantidad:</td>
                                    <td>' . $row['Cantidad'] . '</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <a href="receta.php" class="btn btn-primary">Regresar</a>
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

    public function delete_receta($id)
    {
        $sql = "DELETE FROM recetas WHERE IdReceta=$id;";
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
				<th><a href="receta.php">Regresar</a></th>
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
				<th><a href="receta.php">Regresar</a></th>
			</tr>
		</table>';
        return $html;
    }

    //**************************	

} // FIN SCRPIT