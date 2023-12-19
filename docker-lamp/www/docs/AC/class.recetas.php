<?php
class recetas{
	
	
	private $RecetaID;
	private $ConsultaID;
	private $MedicamentoID;
    private $Cantidad;
	private $con;
	
	function __construct($cn){
		$this->con = $cn;
	    echo "EJECUTANDOSE";
	}
	

	//*********************** 3.1 METODO update_paciente() **************************************************	

	public function update_receta(){
		$this->RecetaID = $_POST['RecetaID'];
        $this->ConsultaID = $_POST['Diagnostico'];
	    $this->MedicamentoID = $_POST['Nombre'];
	    $this->Cantidad = $_POST['Cantidad'];

		
		// Paso 1: Actualizar en consultas si es necesario
        $sqlConsulta = "UPDATE consultas SET Diagnostico = '{$this->ConsultaID}' WHERE ConsultaID = (SELECT ConsultaID FROM recetas WHERE RecetaID = {$this->RecetaID})";
        $this->con->query($sqlConsulta);
    
        // Paso 2: Actualizar en medicamentos
        $sqlMedicamento = "UPDATE medicamentos SET Nombre = '{$this->MedicamentoID}' WHERE MedicamentoID = (SELECT MedicamentoID FROM recetas WHERE RecetaID = {$this->RecetaID})";
        $this->con->query($sqlMedicamento);
    
        // Paso 3: Actualizar en recetas
        $sqlReceta = "UPDATE recetas SET Cantidad = {$this->Cantidad} WHERE RecetaID = {$this->RecetaID}";
        
        if($this->con->query($sqlReceta)){
            echo $this->_message_ok("modificó");
        } else {
            echo $this->_message_error("al modificar");
        }
    }
	
//*********************** 3.2 METODO save_paciente() **************************************************	

public function save_receta(){
    $this->RecetaID = $_POST['RecetaID'];
    $this->ConsultaID = $_POST['Diagnostico'];
    $this->MedicamentoID = $_POST['Nombre'];
    $this->Cantidad = $_POST['Cantidad'];

    // Insertar en consultas
    $sqlConsulta = "INSERT INTO consultas (Diagnostico) VALUES ('{$this->ConsultaID}')";
    $this->con->query($sqlConsulta);

    // Obtener el ConsultaID insertado
    $consultaIDResult = $this->con->query("SELECT LAST_INSERT_ID() as ConsultaID");
    $consultaID = ($consultaIDResult->num_rows > 0) ? $consultaIDResult->fetch_assoc()['ConsultaID'] : null;

    // Insertar en medicamentos
    $sqlMedicamento = "INSERT INTO medicamentos (Nombre) VALUES ('{$this->MedicamentoID}')";
    $this->con->query($sqlMedicamento);

    // Obtener el MedicamentoID insertado
    $medicamentoIDResult = $this->con->query("SELECT LAST_INSERT_ID() as MedicamentoID");
    $medicamentoID = ($medicamentoIDResult->num_rows > 0) ? $medicamentoIDResult->fetch_assoc()['MedicamentoID'] : null;

    // Insertar en recetas
    $sqlReceta = "INSERT INTO recetas (RecetaID, ConsultaID, MedicamentoID, Cantidad)
                  VALUES ('{$this->RecetaID}', '{$consultaID}', '{$medicamentoID}', '{$this->Cantidad}')";

    if($this->con->query($sqlReceta)){
        echo $this->_message_ok("guardó");
    } else {
        echo $this->_message_error("guardar: " . $this->con->error);
    }
}




//*********************** 3.3 METODO _get_name_File() **************************************************	
	
private function _get_name_file($nombre_original, $tamanio){
	$tmp = explode(".",$nombre_original); //Divido el nombre por el punto y guardo en un arreglo
	$numElm = count($tmp); //cuento el número de elemetos del arreglo
	$ext = $tmp[$numElm-1]; //Extraer la última posición del arreglo.
	$cadena = "";
		for($i=1;$i<=$tamanio;$i++){
			$c = rand(65,122);
			if(($c >= 91) && ($c <=96)){
				$c = NULL;
				 $i--;
			 }else{
				$cadena .= chr($c);
			}
		}
	return $cadena . "." . $ext;
}


//************************************* PARTE II ****************************************************	

	public function get_form($id=NULL){
		// Código agregado -- //
	if($id == NULL) {
			$this->RecetaID = NULL;
			$this->ConsultaID = NULL;
			$this->MedicamentoID = NULL;
            $this->Cantidad = NULL;
			
			$flag = NULL;
			$op = "new";
	}else{

			$sql = "SELECT r.RecetaID, r.Cantidad as Cantidad, c.Diagnostico as Diagnostico, m.Nombre as Nombre  
            FROM recetas r
            INNER JOIN consultas c ON r.ConsultaID = c.ConsultaID
            INNER JOIN medicamentos m ON m.MedicamentoID = r.MedicamentoID
            WHERE r.RecetaID = $id
            ;";

			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();
            
            $num = $res->num_rows;
            if($num==0){
                $mensaje = "tratar de actualizar la consulta paciente con id= ".$id;
                echo $this->_message_error($mensaje);
            }else{               
                
             // ***** TUPLA ENCONTRADA *****
				/* echo "<br>REGISTRO A MODIFICAR: <br>";
					echo "<pre>";
						print_r($row);
					echo "</pre>";
			 */
		
             // ATRIBUTOS DE LA CLASE VEHICULO   
                $this->RecetaID = $row['RecetaID'];
                $this->ConsultaID = $row['Diagnostico'];
                $this->MedicamentoID = $row['Nombre'];
                $this->Cantidad = $row['Cantidad'];
				

				$flag = "enabled";
                $op = "update"; 
            }
	}
        
    $Genero = ["Masculino",
				"Femenino",
				"LGBTQ+"
			    ];
    
		

                
		$html = '
		<form name="Form_recetas" method="POST" action="recetas.php" enctype="multipart/form-data">
			<input type="hidden" name="id" value="' . $id  . '">
			<input type="hidden" name="op" value="' . $op  . '">
            <div class="container mt-5">
            <div class="table-responsive">
            <div  class="table table-hover" align="center">
					<div class="col-md-8">
						<table class="table table-bordered">
                            <thead class="text-center" style="background-color: #DFF0D8; color: black;">
                                <tr>
                                    <th colspan="2">DATOS Paciente</th>
                                </tr>
                            </thead>
                    
							<tbody>
                                <tr>
                                    <td>ID:</td>
                                    <td><input type="text" class="form-control" name="RecetaID" value="' . $this->RecetaID . '"></td>
                                </tr>

								<tr>
									<td>Diagnostico:</td>
									<td><input type="text" class="form-control" name="Diagnostico" value="' . $this->ConsultaID. '"></td>
								</tr>
                                <tr>
									<td>Medicamento:</td>
									<td><input type="text" class="form-control" name="Nombre" value="' . $this->MedicamentoID . '"></td>
								</tr>
                                
								<tr>
									<td>Cantidad:</td>
									<td><input type="number" class="form-control" name="Cantidad" value="' . $this->Cantidad . '"></td>
								</tr>
                                
								<tr>
                                    <td colspan="1" class="text-center">
                                        <input type="submit" class="btn btn-primary" name="Guardar" value="GUARDAR">
                                    </td>
                                    <td colspan="1" class="text-center">
                                        <button class="btn btn-danger mt-1">
                                            <a href="recetas.php" style="color: white;" class="btn-link">Regresar</a>
                                        </button>
                                    </td>
                                </tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</form>';

		return $html;
	}
	
	
	
	public function get_list(){
		$d_new = "new/0";
		$d_new_final = base64_encode($d_new);
		$html = '
		<div class="container mt-5">
    <div class="table-responsive">
    <button onclick="window.location.href=\'../index.html\'" class="btn btn-primary">Regresar</button>
        <table class="table table-hover table-bordered table-striped text-center" align="middle">
            <thead>
                <tr class="active" style="background-color: #DFF0D8; color: black;">
                    <th colspan="8" class="text-center">Lista de recetas</th>
                </tr>
                <tr style="background-color: #DFF0D8; color: black;">
                <th colspan="8" class="text-center align-middle" ><a class="btn btn-outline-success" href="recetas.php?d=' . $d_new_final . '">Nuevo</a></th>
                </tr>
                <tr class="text-center" style="background-color: #DFF0D8; color: black;">
                    <th>Consulta</th>
                    <th>Medicamento a tomar</th>
                    <th>Cantidad</th>
                    <th colspan="3" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>';
		$sql = "SELECT r.RecetaID, r.Cantidad as Cantidad, c.Diagnostico as Diagnostico, m.Nombre as Nombre  
                FROM recetas r
                INNER JOIN consultas c ON r.ConsultaID = c.ConsultaID
                INNER JOIN medicamentos m ON m.MedicamentoID = r.MedicamentoID
                ;";	
		$res = $this->con->query($sql);
		
		
		
		// VERIFICA si existe TUPLAS EN EJECUCION DEL Query
		$num = $res->num_rows;
        if($num != 0){
		
		    while($row = $res->fetch_assoc()){	    		
				// URL PARA BORRAR
				$d_del = "del/" . $row['RecetaID'];
				$d_del_final = base64_encode($d_del);
				
				// URL PARA ACTUALIZAR
				$d_act = "act/" . $row['RecetaID'];
				$d_act_final = base64_encode($d_act);
				
				// URL PARA EL DETALLE
				$d_det = "det/" . $row['RecetaID'];
				$d_det_final = base64_encode($d_det);	
				
				$html .= '
					<tr>
						<td>' . $row['Diagnostico'] . '</td>
						<td>' . $row['Nombre'] . '</td>
                        <td>' . $row['Cantidad'] . '</td>
						<td class="text-center"><button class="btn btn-danger"><a href="recetas.php?d=' . $d_del_final . '">Borrar</a></button></td>
						<td class="text-center"><button class="btn btn-warning"><a href="recetas.php?d=' . $d_act_final . '">Actualizar</a></button></td>
						<td class="text-center"><button class="btn btn-info"><a href="recetas.php?d=' . $d_det_final . '">Detalle</a></button></td>
					</tr>';
			 
		    }
		}else{
			$mensaje = "Tabla" . "<br>";
            echo $this->_message_BD_Vacia($mensaje);
			echo "<br><br><br>";
		}
		$html .= '</table>';
		return $html;
		
	}
	
	
//********************************************************************************************************
	/*
	 $tabla es la tabla de la base de datos
	 $valor es el nombre del campo que utilizaremos como valor del option
	 $etiqueta es nombre del campo que utilizaremos como etiqueta del option
	 $nombre es el nombre del campo tipo combo box (select)
	 * $defecto es el valor para que cargue el combo por defecto
	 */ 
	 
	 // _get_combo_db("marca","id","descripcion","marca",$this->marca)
	 // _get_combo_db("color","id","descripcion","color", $this->color)
	 
	 /*Aquí se agregó el parámetro:  $defecto*/
	private function _get_combo_db($tabla,$valor,$etiqueta,$nombre,$defecto=NULL){
		$html = '<select name="' . $nombre . '">';
		$sql = "SELECT $valor,$etiqueta FROM $tabla;";
		$res = $this->con->query($sql);
		//$num = $res->num_rows;
		
			
		while($row = $res->fetch_assoc()){
		
		/*
			echo "<br>VARIABLE ROW <br>";
					echo "<pre>";
						print_r($row);
					echo "</pre>";
		*/	
			$html .= ($defecto == $row[$valor])?'<option value="' . $row[$valor] . '" selected>' . $row[$etiqueta] . '</option>' . "\n" : '<option value="' . $row[$valor] . '">' . $row[$etiqueta] . '</option>' . "\n";
		}
		$html .= '</select>';
		return $html;
	}
	
	//_get_combo_anio("anio",1950,$this->anio)
	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_combo_anio($nombre,$anio_inicial,$defecto=NULL){
		$html = '<select name="' . $nombre . '">';
		$anio_actual = date('Y');
		for($i=$anio_inicial;$i<=$anio_actual;$i++){
			$html .= ($defecto == $i)? '<option value="' . $i . '" selected>' . $i . '</option>' . "\n":'<option value="' . $i . '">' . $i . '</option>' . "\n";
		}
		$html .= '</select>';
		return $html;
	}
	
	
	//_get_radio($combustibles, "combustible",$this->combustible) 
	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_radio($arreglo,$nombre,$defecto=NULL){
		$html = '
		<table border=0 align="left">';
		foreach($arreglo as $etiqueta){
			$html .= '
			<tr>
				<td>' . $etiqueta . '</td>
				<td>';
				$html .= ($defecto == $etiqueta)? '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>':'<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '"/></td>';
			
			$html .= '</tr>';
		}
		$html .= '</table>';
		return $html;
	}
	
	
//****************************************** NUEVO CODIGO *****************************************

public function get_detail_receta($id){
		$sql = "SELECT r.RecetaID, r.Cantidad as Cantidad, c.Diagnostico as Diagnostico, m.Nombre as Nombre  
        FROM recetas r
        INNER JOIN consultas c ON r.ConsultaID = c.ConsultaID
        INNER JOIN medicamentos m ON m.MedicamentoID = r.MedicamentoID
        WHERE r.RecetaID = $id;";
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		
		// VERIFICA SI EXISTE id
		$num = $res->num_rows;
        
	if($num == 0){
        $mensaje = "desplegar el detalle del paciente con id= ".$id . "<br>";
        echo $this->_message_error($mensaje);
				
    }else{ 
	
	    /* echo "<br>TUPLA<br>";
	    echo "<pre>";
				print_r($row);
		echo "</pre>"; */
	
		$html = '
        <div class="table-responsive">
    <table class="table table-hover" style="border: 3px solid #DFF0D8; margin: auto;">
        <thead>
            <tr>
                <th colspan="2" style="background-color: #DFF0D8; color: black; text-align: center;">DATOS DEL Paciente</th>
            </tr>
        </thead>
        <tbody>
        <   tr>
                <td style="font-weight: bold;">Id: </td>
                <td>'.$row['RecetaID'].'</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Diagnostico: </td>
                <td>'.$row['Diagnostico'].'</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Medicamento: </td>
                <td>'.$row['Nombre'] .'</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Cantidad: </td>
                <td>'. $row['Cantidad'] .'</td>
            </tr>
            <tr>
                <th colspan="2" style="text-align: center;"><a href="recetas.php" class="btn btn-primary">Regresar</a></th>
            </tr>
        </tbody>
    </table>
</div>';
		
		return $html;
	}	
	
}


	public function delete_recetas($id){
			   
		$sql = "DELETE FROM recetas WHERE RecetaID=$id;";
		if($this->con->query($sql)){
			echo $this->_message_ok("eliminó");
		}else{
			echo $this->_message_error("eliminar<br>");
		}
	}


	
//***************************************************************************************	
	
	private function _calculo_matricula($avaluo){
		return number_format(($avaluo * 0.10),2);
	}
	
//***************************************************************************************************************************
	
	private function _message_error($tipo){
		$html = '
		<table border="0" align="center">
			<tr>
				<th>Error al ' . $tipo . 'Favor contactar a .................... </th>
			</tr>
			<tr>
				<th><a href="recetas.php">Regresar</a></th>
			</tr>
		</table>';
		return $html;
	}
	
	
	private function _message_BD_Vacia($tipo){
	   $html = '
		<table border="0" align="center">
			<tr>
				<th> NO existen registros en la ' . $tipo . 'Favor contactar a .................... </th>
			</tr>
	
		</table>';
		return $html;
	
	
	}
	
	private function _message_ok($tipo){
		$html = '
		<table border="0" align="center">
			<tr>
				<th>El registro se  ' . $tipo . ' correctamente</th>
			</tr>
			<tr>
				<th><a href="recetas.php">Regresar</a></th>
			</tr>
		</table>';
		return $html;
	}
//************************************************************************************************************************************************

 
}
?>

