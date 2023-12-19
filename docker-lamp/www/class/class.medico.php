<?php
class medico{
	
	
	private $IdMedico;
	private $Nombre;
	private $Especialidad;
	private $IdUsuario;
	private $con;
	
	function __construct($cn){
		$this->con = $cn;
	    echo "EJECUTANDOSE";
	}
	

	//*********************** 3.1 METODO update_vehiculo() **************************************************	
	
	public function update_medico(){
		// Obtener valores del formulario
		$this->IdMedico = $_POST['IdMedico'];
		$this->Nombre = $_POST['Nombre'];
		$this->Especialidad = $_POST['Especialidad'];
		$this->IdUsuario = $_POST['IdUsuario'];
	
		// Obtener el IdEsp de la especialidad (ya existente o recién insertada)
		$resultEsp = $this->con->query("SELECT IdEsp FROM especialidades WHERE Descripcion = '{$this->Especialidad}'");
		$rowEsp = $resultEsp->fetch_assoc();
		$idEsp = $rowEsp['IdEsp'];
	
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
	
		// Actualizar en medicos
		$sqlMedico = "UPDATE medicos 
					  SET Nombre = '{$this->Nombre}', 
						  Especialidad = '{$idEsp}', 
						  IdUsuario = '{$idUsuario}'
					  WHERE IdMedico = '{$this->IdMedico}'";
	
		if($this->con->query($sqlMedico)) {
			echo $this->_message_ok("modificó");
		} else {
			echo $this->_message_error("al modificar");
		}							
										
	}
	
//*********************** 3.2 METODO save_vehiculo() **************************************************	

public function save_medico(){
	// Obtener valores del formulario
    $this->IdMedico = $_POST['IdMedico'];
    $this->Nombre = $_POST['Nombre'];
    $this->Especialidad = $_POST['Especialidad'];
    $this->IdUsuario = $_POST['IdUsuario'];


    // Obtener el IdEsp de la especialidad (ya existente o recién insertada)
    $resultEsp = $this->con->query("SELECT IdEsp FROM especialidades WHERE Descripcion = '{$this->Especialidad}'");
    $rowEsp = $resultEsp->fetch_assoc();
    $idEsp = $rowEsp['IdEsp'];

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

    // Insertar o actualizar en medicos
    $sqlMedico = "INSERT INTO medicos (IdMedico, Nombre, Especialidad, IdUsuario) VALUES
	 ('{$this->IdMedico}', '{$this->Nombre}', '{$idEsp}', '{$idUsuario}')";

    if($this->con->query($sqlMedico)) {
        echo $this->_message_ok("guardó");
    } else {
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
	if(($id == NULL) || ($id == 0) ) {
			$this->IdMedico = NULL;
			$this->Nombre = NULL;
			$this->Especialidad = NULL;
			$this->IdUsuario = NULL;
			
			$flag = NULL;
			$op = "new";
	}else{
			
			$sql = "SELECT m.IdMedico, m.nombre as Nombre, 
							e.Descripcion as Especialidad, 
							u.Nombre  as NombreU
					FROM medicos m
					INNER JOIN usuarios u ON m.IdUsuario = u.IdUsuario
					INNER JOIN especialidades e ON m.Especialidad = e.IdEsp
					WHERE m.IdMedico=$id";

			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();
            $num = $res->num_rows;
            $bandera = ($num==0) ? 0 : 1;
            
            if(!($bandera)){
                $mensaje = "tratar de actualizar el medico con id= ".$id . "<br>";
                echo $this->_message_error($mensaje);
				
            }else{                
                
             // ***** TUPLA ENCONTRADA *****
				/* echo "<br>REGISTRO A MODIFICAR: <br>";
					echo "<pre>";
						print_r($row);
					echo "</pre>"; */
			
		
             // ATRIBUTOS DE LA CLASE VEHICULO   
                $this->IdMedico = $row['IdMedico'];
                $this->Nombre = $row['Nombre'];
                $this->Especialidad = $row['Especialidad'];
				$this->IdUsuario = $row['NombreU'];
				

				$flag = "enabled";
                $op = "update"; 
            }
	}
        
    
		
		$html = '
		<form name="Form_recetas" method="POST" action="medico.php" enctype="multipart/form-data">
		<input type="hidden" name="id" value="' . $id  . '">
		<input type="hidden" name="op" value="' . $op  . '">
		<div class="container mt-5">
		<div class="table-responsive">
		<div  class="table table-hover" align="center">
				<div class="col-md-8">
					<table class="table table-bordered">
						<thead class="text-center" style="background-color: #FFA500; color: white;">
							<tr>
								<th colspan="2">DATOS MEDICO</th>
							</tr>
						</thead>
							<tbody>
                                <tr>
                                    <td>IdMedico:</td>
                                    <td><input type="text" class="form-control" name="IdMedico" value="' . $this->IdMedico . '"></td>
                                </tr>

								<tr>
									<td>Nombre:</td>
									<td><input type="text" class="form-control" name="Nombre" value="' . $this->Nombre . '"></td>
								</tr>
								<tr>
								<tr>
									<td>Especialidad:</td>
								<td>' . $this->_get_combo_db("especialidades","Descripcion","Descripcion","Especialidad",$this->Especialidad) . '</td>
								</tr>
								
								<tr>
									<td>Usuario:</td>
								<td><input type="text" class="form-control" name="IdUsuario" value="' . $this->IdUsuario . '"></td>
								</tr>
									<tr>
                                    <td colspan="1" class="text-center">
                                        <input type="submit" class="btn btn-primary" name="Guardar" value="GUARDAR">
                                    </td>
                                    <td colspan="1" class="text-center">
                                        <button class="btn btn-danger mt-1">
                                            <a href="medico.php" style="color: white;" class="btn-link">Regresar</a>
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
		$d_new = "new/0";                           //Línea agregada
        $d_new_final = base64_encode($d_new);       //Línea agregada
				
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
				<th colspan="8" class="text-center align-middle" ><a class="btn btn-outline-success" href="medico.php?d=' . $d_new_final . '">Nuevo</a></th>
			</tr>
            <tr class="text-center" style="background-color: #FFA500; color: white;">
                <th >Nombre</th>
                <th>Especialidad</th>
                <th colspan="3" class="text-center">Acciones</th>
            </tr>
        </thead>
			<tbody>';
		$sql = "SELECT m.IdMedico, m.nombre as Nombre, 
					   e.Descripcion as Especialidad
                FROM medicos m
				INNER JOIN especialidades e ON m.Especialidad = e.IdEsp
				;";	
		$res = $this->con->query($sql);
		
		
		
		// VERIFICA si existe TUPLAS EN EJECUCION DEL Query
		$num = $res->num_rows;
        if($num != 0){
		
		    while($row = $res->fetch_assoc()){	    		
				// URL PARA BORRAR
				$d_del = "del/" . $row['IdMedico'];
				$d_del_final = base64_encode($d_del);
				
				// URL PARA ACTUALIZAR
				$d_act = "act/" . $row['IdMedico'];
				$d_act_final = base64_encode($d_act);
				
				// URL PARA EL DETALLE
				$d_det = "det/" . $row['IdMedico'];
				$d_det_final = base64_encode($d_det);	
				
				$html .= '
					<tr>
						<td>' . $row['Nombre'] . '</td>
						<td>' . $row['Especialidad'] . '</td>
						<td class="text-center"><button class="btn btn-danger"><a href="medico.php?d=' . $d_del_final . '">Borrar</a></button></td>
						<td class="text-center"><button class="btn btn-warning"><a href="medico.php?d=' . $d_act_final . '">Actualizar</a></button></td>
						<td class="text-center"><button class="btn btn-info"><a href="medico.php?d=' . $d_det_final . '">Detalle</a></button></td>
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

public function get_detail_medico($id){

		$sql = "SELECT m.IdMedico, m.nombre as Nombre, 
		e.Descripcion as Especialidad, 
		u.Nombre  as NombreU
		FROM medicos m
		INNER JOIN usuarios u ON m.IdUsuario = u.IdUsuario
		INNER JOIN especialidades e ON m.Especialidad = e.IdEsp
		WHERE m.IdMedico=$id";

		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		
		// VERIFICA SI EXISTE id
		$num = $res->num_rows;
        
	if($num == 0){
        $mensaje = "desplegar el detalle del consulta con id= ".$id . "<br>";
        echo $this->_message_error($mensaje);
				
    }else{ 
	
	    /* echo "<br>TUPLA<br>";
	    echo "<pre>";
				print_r($row);
		echo "</pre>"; */
	
		$html = '
		<div class="table-responsive">
		<table class="table table-hover" style="border: 3px solid #FFA500; margin: auto;">
			<thead>
				<tr>
					<th colspan="2" style="background-color: #FFA500; color: white; text-align: center;">DATOS DEL MEDICO</th>
				</tr>
			</thead>
			<tbody>
			<tr>
				<td>Nombre: </td>
				<td>'. $row['Nombre'] .'</td>
			</tr>
			<tr>
				<td>Especialidad: </td>
				<td>'. $row['Especialidad'] .'</td>
			</tr>	
			
			<tr>
				<td>Usuario: </td>
				<td>'. $row['NombreU'] .'</td>
			</tr>

			<tr>
			<th colspan="2" style="text-align: center;"><a href="medico.php" class="btn btn-primary">Regresar</a></th>
		</tr>
	</tbody>
</table>
</div>';
		
		return $html;
	}	
	
}


	public function delete_medico($id){
			   
		$sql = "DELETE FROM medicos WHERE IdMedico=$id;";
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
				<th><a href="medico.php">Regresar</a></th>
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
				<th><a href="medico.php">Regresar</a></th>
			</tr>
		</table>';
		return $html;
	}
//************************************************************************************************************************************************

 
}
?>

