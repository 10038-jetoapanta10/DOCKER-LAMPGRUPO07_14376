<?php
class paciente{
	
	
	private $PacienteID;
	private $Nombre;
	private $Edad;
    private $Genero;
	private $con;
	
	function __construct($cn){
		$this->con = $cn;
	    echo "EJECUTANDOSE";
	}
	

	//*********************** 3.1 METODO update_paciente() **************************************************	
	
	public function update_paciente(){
		$this->PacienteID = $_POST['PacienteID'];
	    $this->Nombre = $_POST['Nombre'];
	    $this->Edad = $_POST['Edad'];
        $this->Genero = $_POST['Genero'];
		
		$sql = "UPDATE pacientes SET PacienteID='$this->PacienteID',
									Nombre='$this->Nombre',
									Edad='$this->Edad',
                                    Genero='$this->Genero'
				WHERE PacienteID=$this->PacienteID;";
		//echo $sql;
		//exit;
		if($this->con->query($sql)){
			echo $this->_message_ok("modificó");
		}else{
			echo $this->_message_error("al modificar");
		}								
										
	}
	
//*********************** 3.2 METODO save_paciente() **************************************************	

public function save_paciente(){
	$this->PacienteID = $_POST['PacienteID'];
	$this->Nombre = $_POST['Nombre'];
	$this->Edad = $_POST['Edad'];
    $this->Genero = $_POST['Genero'];
	
	 //files ver los datos que ingresarte el id
			/* echo "<br> FILES <br>";
			echo "<pre>";
				print_r($_FILES);
			echo "</pre>"; */
		  
	
	$sql = "INSERT INTO pacientes VALUES(	'$this->PacienteID',
											'$this->Nombre',
											'$this->Edad',
                                            '$this->Genero');";
		//echo $sql;
		//exit;
		if($this->con->query($sql)){
			echo $this->_message_ok("guardó");
		}else{
			echo $this->_message_error("guardar");
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
			$this->PacienteID = NULL;
			$this->Nombre = NULL;
			$this->Edad = NULL;
            $this->Genero = NULL;
			
			$flag = NULL;
			$op = "new";
	}else{

			$sql = "SELECT * FROM pacientes
             WHERE PacienteID=$id;";
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
                $this->PacienteID = $row['PacienteID'];
                $this->Nombre = $row['Nombre'];
                $this->Edad = $row['Edad'];
                $this->Genero = $row['Genero'];
				

				$flag = "enabled";
                $op = "update"; 
            }
	}
        
    $Genero = ["Masculino",
				"Femenino",
				"LGBTQ+"
			    ];
    
		

                
		$html = '
		<form name="Form_paciente" method="POST" action="paciente.php" enctype="multipart/form-data">
			<input type="hidden" name="id" value="' . $id  . '">
			<input type="hidden" name="op" value="' . $op  . '">
            <div class="container mt-5">
            <div class="table-responsive">
            <div  class="table table-hover" align="center">
					<div class="col-md-8">
						<table class="table table-bordered">
                            <thead class="text-center" style="background-color: #4B3621; color: white;">
                                <tr>
                                    <th colspan="2">DATOS Paciente</th>
                                </tr>
                            </thead>
                    
							<tbody>
                                <tr>
                                    <td>PacienteID:</td>
                                    <td><input type="text" class="form-control" name="PacienteID" value="' . $this->PacienteID . '"></td>
                                </tr>

								<tr>
									<td>Nombre:</td>
									<td><input type="text" class="form-control" name="Nombre" value="' . $this->Nombre . '"></td>
								</tr>
								<tr>
									<td>Edad:</td>
									<td><input type="number" class="form-control" name="Edad" value="' . $this->Edad . '"></td>
								</tr>
                                <tr>
									<td>Genero:</td>
									<td>' . $this->_get_radio($Genero, "Genero",$this->Genero) . '</td>
								</tr>
								<tr>
                                    <td colspan="1" class="text-center">
                                        <input type="submit" class="btn btn-primary" name="Guardar" value="GUARDAR">
                                    </td>
                                    <td colspan="1" class="text-center">
                                        <button class="btn btn-danger mt-1">
                                            <a href="paciente.php" style="color: white;" class="btn-link">Regresar</a>
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
                <tr class="active" style="background-color: #4B3621; color: white;">
                    <th colspan="8" class="text-center">Lista de Vehículos</th>
                </tr>
                <tr style="background-color: #4B3621; color: white;">
                <th colspan="8" class="text-center align-middle" ><a class="btn btn-outline-success" href="paciente.php?d=' . $d_new_final . '">Nuevo</a></th>
                </tr>
                <tr class="text-center" style="background-color: #4B3621; color: white;">
                    <th>Nombre</th>
                    <th>Edad</th>
                    <th>Género</th>
                    <th colspan="3" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>';
		$sql = "SELECT p.PacienteID, p.Nombre as Nombre, p.Edad as Edad, p.Genero as Genero  
                FROM pacientes p;";	
		$res = $this->con->query($sql);
		
		
		
		// VERIFICA si existe TUPLAS EN EJECUCION DEL Query
		$num = $res->num_rows;
        if($num != 0){
		
		    while($row = $res->fetch_assoc()){	    		
				// URL PARA BORRAR
				$d_del = "del/" . $row['PacienteID'];
				$d_del_final = base64_encode($d_del);
				
				// URL PARA ACTUALIZAR
				$d_act = "act/" . $row['PacienteID'];
				$d_act_final = base64_encode($d_act);
				
				// URL PARA EL DETALLE
				$d_det = "det/" . $row['PacienteID'];
				$d_det_final = base64_encode($d_det);	
				
				$html .= '
					<tr>
						<td>' . $row['Nombre'] . '</td>
						<td>' . $row['Edad'] . '</td>
                        <td>' . $row['Genero'] . '</td>
						<td class="text-center"><button class="btn btn-danger"><a href="paciente.php?d=' . $d_del_final . '">Borrar</a></button></td>
						<td class="text-center"><button class="btn btn-warning"><a href="paciente.php?d=' . $d_act_final . '">Actualizar</a></button></td>
						<td class="text-center"><button class="btn btn-info"><a href="paciente.php?d=' . $d_det_final . '">Detalle</a></button></td>
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

public function get_detail_paciente($id){
		$sql = "SELECT p.PacienteID, p.Nombre as Nombre, p.Edad as Edad, p.Genero as Genero  
        FROM pacientes p
        WHERE p.PacienteID=$id;";
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
    <table class="table table-hover" style="border: 3px solid #4B3621; margin: auto;">
        <thead>
            <tr>
                <th colspan="2" style="background-color: #4B3621; color: white; text-align: center;">DATOS DEL Paciente</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold;">Nombre: </td>
                <td>'.$row['Nombre'].'</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Edad: </td>
                <td>'.$row['Edad'] .'</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Género: </td>
                <td>'. $row['Genero'] .'</td>
            </tr>
            <tr>
                <th colspan="2" style="text-align: center;"><a href="paciente.php" class="btn btn-primary">Regresar</a></th>
            </tr>
        </tbody>
    </table>
</div>';
		
		return $html;
	}	
	
}


	public function delete_paciente($id){
			   
		$sql = "DELETE FROM pacientes WHERE PacienteID=$id;";
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
				<th><a href="paciente.php">Regresar</a></th>
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
				<th><a href="paciente.php">Regresar</a></th>
			</tr>
		</table>';
		return $html;
	}
//************************************************************************************************************************************************

 
}
?>

