<?php
class consultas{
	private $ConsultaID;
	private $PacienteID;
	private $MedicoID;
	private $FechaConsulta;
	private $Diagnostico;
	private $Genero;
	private $Foto;
	
	private $con;
	
	function __construct($cn){
		$this->con = $cn;
	}
		
		
//******** 3.1 METODO update_consulta() *****************	
	
	public function update_consultas(){
		$this-> ConsultaID = $_POST['id'];
		$this-> Genero = $_POST['Genero'];
		$this->PacienteID = $_POST['paciente'];
		$this->MedicoID = $_POST['medico'];
		$this->FechaConsulta = $_POST['FechaConsulta'];
		$this->Diagnostico = $_POST['diagnostico'];
		$this->Foto = $this->_get_name_file($_FILES['Foto']['name'], 12);

		$path = "../../startbootstrap-agency-gh-pages/assets/img/gente/".$this->Foto;

		//exit;
		if (!move_uploaded_file($_FILES['Foto']['tmp_name'], $path)) {
			$mensaje = "Cargar la imagen";
			echo $this->_message_error($mensaje);
			exit;
		}
		
		
		//exit;
		$sql = "UPDATE consultas SET PacienteID=$this->PacienteID,
									MedicoID='$this->MedicoID',
									FechaConsulta='$this->FechaConsulta',
									Diagnostico='$this->Diagnostico',
									Foto='$this->Foto'

				WHERE ConsultaID=$this->ConsultaID;";
		echo $sql;
		//exit;

		if($this->con->query($sql)){
			echo $this->_message_ok("modificó");
		}else{
			echo $this->_message_error("al modificar");
		}								
										
	}
	

//******** 3.2 METODO save_consulta() *****************	

	public function save_consultas(){
		$this->PacienteID = $_POST['paciente'];
		$this->Genero = $_POST['Genero'];
		$this->MedicoID = $_POST['medico'];
		$this->FechaConsulta = $_POST['FechaConsulta'];
		$this->Diagnostico = $_POST['diagnostico'];	 


		// Obtener el valor del campo de género del formulario
		if (isset($_POST['Genero'])) {
			$genero = $_POST['Genero'];
		} else {
			$genero = '';
		}

		// Verificar la regla de negocio
		if ($genero === 'Femenino') {
			// Si el paciente es femenino, verificar si la especialidad es ginecología
			if ($this->MedicoID !== '4') {
				// Si la especialidad no es ginecología, mostrar un mensaje de error
				echo $this->_message_error("Una paciente femenina solo puede tener una consulta con el ginecólogo.");
				exit;
			}
		} elseif ($genero === 'Masculino') {
			// Si el paciente es masculino, verificar que la especialidad no sea ginecología
			if ($this->MedicoID === '4') {
				// Si la especialidad es ginecología, mostrar un mensaje de error
				echo $this->_message_error("Los pacientes masculinos no pueden tener una consulta con el ginecólogo.");
				exit;
			}
		} else {
			// Género no seleccionado, mostrar un mensaje de error
			echo $this->_message_error("Por favor, seleccione el género del paciente.");
			echo "Género: $this->Genero";
			exit;
		}	


				echo "<br> FILES <br>";    
				echo "<pre>";
					print_r($_FILES);
				echo "</pre>";
		    
		
		
		$this->Foto = $this->_get_name_file($_FILES['Foto']['name'],12);
		
		$path = "../../startbootstrap-agency-gh-pages/assets/img/gente/".$this->Foto;
		
		//exit; SIRVE PARA HACER MANTENIMIENTO 
		if(!move_uploaded_file($_FILES['Foto']['tmp_name'],$path)){
			$mensaje = "Cargar la imagen";
			echo $this->_message_error($mensaje);
			exit;
		}
		
		$sql = "INSERT INTO consultas VALUES(NULL,
											$this->PacienteID,
											$this->MedicoID,
											'$this->FechaConsulta',
											'$this->Diagnostico',
											'$this->Foto');";
											
		
		//echo $sql;
		//exit;
		if($this->con->query($sql)){
			echo $this->_message_ok("guardó");
		}else{
			echo $this->_message_error("guardar");
		}								
										
	}


//******** 3.3 METODO _get_name_File() *****************	
	
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
	
	
//************* PARTE I ********************
	
	    
	 //Aquí se agregó el parámetro:  $defecto/
	private function _get_combo_db($tabla,$valor,$etiqueta,$nombre,$defecto){
		$html = '<select name="' . $nombre . '">';
		$sql = "SELECT $valor,$etiqueta FROM $tabla;";
		$res = $this->con->query($sql);
		while($row = $res->fetch_assoc()){
			//ImpResultQuery($row);
			$html .= ($defecto == $row[$valor])?'<option value="' . $row[$valor] . '" selected>' . $row[$etiqueta] . '</option>' . "\n" : '<option value="' . $row[$valor] . '">' . $row[$etiqueta] . '</option>' . "\n";
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
	
	//Aquí se agregó el parámetro:  $defecto
    
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
	}
	
	
//************* PARTE II ******************	

	public function get_form($id=NULL){
		
		if($id == NULL){
			$this->PacienteID = NULL;
			$this->Genero = NULL;
			$this->MedicoID = NULL;
			$this->FechaConsulta = NULL;
			$this->Diagnostico = NULL;
			$this->Foto = NULL;
			
			$flag = NULL;  //VARIABLES AUXILIARES
			$op = "new";
			
		}else{

			$sql = "SELECT * FROM consultas WHERE ConsultaID=$id;";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();
			
			$num = $res->num_rows;
            if($num==0){
                $mensaje = "tratar de actualizar la consulta con id= ".$id;
                echo $this->_message_error($mensaje);
            }else{   
			
              // ** TUPLA ENCONTRADA **
				echo "<br>TUPLA <br>";
				echo "<pre>";
					print_r($row);
				echo "</pre>";
			
				$this->PacienteID = $row['PacienteID'];
				$this->Genero = $row['PacienteID'];
				$this->MedicoID = $row['MedicoID'];
				$this->FechaConsulta = $row['FechaConsulta'];
				$this->Diagnostico = $row['Diagnostico'];
				$this->Foto = $row['Foto'];
				
				
				$flag = "enable";
				$op = "update";
			}
		}
		
		
		
		$html = '
		<form name="Form_recetas" method="POST" action="index.php" enctype="multipart/form-data">
		<input type="hidden" name="id" value="' . $id  . '">
		<input type="hidden" name="op" value="' . $op  . '">
		<div class="container mt-5">
		<div class="table-responsive">
		<div  class="table table-hover" align="center">
				<div class="col-md-8">
					<table class="table table-bordered">
						<thead class="text-center" style="background-color: #800080; color: white;">
							<tr>
								<th colspan="2">DATOS Consulta</th>
							</tr>
						</thead>
					<td>Paciente:</td>
					<td>' . $this->_get_combo_db("pacientes","PacienteID","Nombre","paciente",$this->PacienteID) . '</td>
				</tr>
				<tr>
				<td>Genero:</td>
					<td>' . $this->_get_combo_db("pacientes","Genero","Genero","Genero",$this->Genero) . '</td>
				</tr>
				<tr>
					<td>Especialidad:</td>
					<td>' . $this->_get_combo_db("medicos","MedicoID","Especialidad","medico",$this->MedicoID) . '</td>
				</tr>
				<tr>
					<td>Fecha Consulta:</td>
					<td><input type="date" name="FechaConsulta" value=" ' . $this->ConsultaID . '" required></td>
				</tr>	
				<tr>
					<td>Diagnostico:</td>
					<td><input type="text" size="15" name="diagnostico" value="' . $this->Diagnostico . '" required></td>
				</tr>
				<tr>
					<td>Foto:</td>
					<td><input type="file" name="Foto"'.$flag.'></td>
				</tr>
				<tr>
				<td colspan="1" class="text-center">
					<input type="submit" class="btn btn-primary" name="Guardar" value="GUARDAR">
				</td>
				<td colspan="1" class="text-center">
					<button class="btn btn-danger mt-1">
						<a href="index.php" style="color: white;" class="btn-link">Regresar</a>
					</button>
				</td>
			</tr>											
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
                <tr class="active" style="background-color: #800080; color: white;">
                    <th colspan="8" class="text-center">Lista de consulta</th>
                </tr>
                <tr style="background-color: #800080; color: white;">
                <th colspan="8" class="text-center align-middle" ><a class="btn btn-outline-success" href="index.php?d=' . $d_new_final . '">Nuevo</a></th>
                </tr>
                <tr class="text-center" style="background-color: #800080; color: white;">
                    <th>Paciente</th>
					<th>Medico</th>				
				<th>Fecha de Consulta</th>
				<th>Diagnostico</th>
				<th colspan="3">Acciones</th>
			</tr>
			</div>
			</div>';
			$sql = "SELECT c.ConsultaID, p.Nombre AS nombrepaciente, p.Genero, m.Especialidad, m.Nombre AS nombremedico, c.FechaConsulta, c.Diagnostico
			FROM consultas c
			JOIN pacientes p ON c.PacienteID = p.PacienteID
			JOIN medicos m ON c.MedicoID = m.MedicoID;";	
		$res = $this->con->query($sql);
		// Sin codificar <td><a href="index.php?op=del&id=' . $row['id'] . '">Borrar</a></td>
		while($row = $res->fetch_assoc()){
			$d_del = "del/" . $row['ConsultaID'];
			$d_del_final = base64_encode($d_del);
			$d_act = "act/" . $row['ConsultaID'];
			$d_act_final = base64_encode($d_act);
			$d_det = "det/" . $row['ConsultaID'];
			$d_det_final = base64_encode($d_det);					
			$html .= '
				<tr>
					<td>' . $row['nombrepaciente'] . '</td>
					<td>' . $row['nombremedico'] . '</td>
					<td>' . $row['FechaConsulta'] . '</td>
					<td>' . $row['Diagnostico'] . '</td>
					<td><button class="btn btn-danger"><a href="index.php?d=' . $d_del_final . '">Borrar</a></button></td>
					<td><button class="btn btn-warning"><a href="index.php?d=' . $d_act_final . '">Actualizar</a></button></td>
					<td><button class="btn btn-info"><a href="index.php?d=' . $d_det_final . '">Detalle</a></button></td>
				</tr>';
		}
		$html .= '  
		</table>';
		
		return $html;
		
	}
	
	
	public function get_detail_consultas($id){
		$sql = "SELECT c.ConsultaID, p.Nombre AS nombrepaciente, p.Genero, m.Especialidad, m.Nombre AS nombremedico, c.FechaConsulta, c.Diagnostico, c.Foto 
        FROM consultas c
        JOIN pacientes p ON c.PacienteID = p.PacienteID
        JOIN medicos m ON c.MedicoID = m.MedicoID
        WHERE c.ConsultaID = $id;";
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		
		$num = $res->num_rows;


        //Si es que no existiese ningun registro debe desplegar un mensaje 
        //$mensaje = "tratar de eliminar el consulta con id= ".$id;
        //echo $this->_message_error($mensaje);
        //y no debe desplegarse la tablas
        
        if($num==0){
            $mensaje = "tratar de editar el consulta con id= ".$id;
            echo $this->_message_error($mensaje);
        }else{ 
				$html = '
				<div class="table-responsive">
				<table class="table table-hover" style="border: 3px solid #800080; margin: auto;">
					<thead>
						<tr>
							<th colspan="2" style="background-color: #800080; color: white; text-align: center;">DATOS DE LA CONSULTA</th>
						</tr>
					</thead>
					<tbody>

					<tr>
						<td>Paciente: </td>
						<td>'. $row['nombrepaciente'] .'</td>
					</tr>
					<tr>
						<td>Genero: </td>
						<td>'. $row['Genero'] .'</td>
					</tr>
					<tr>
						<td>Nombre del medico: </td>
						<td>'. $row['nombremedico'] .'</td>
					</tr>
					<tr>
						<td>Diagnostico: </td>
						<td>'. $row['Diagnostico'] .'</td>
					</tr>
					<tr>
						<td>Especialidad: </td>
						<td>'. $row['Especialidad'] .'</td>
					</tr>
					<tr>
						<td>Fecha Consulta: </td>
						<td>'. $row['FechaConsulta'] .'</td>
					</tr>
					<tr>
						<th colspan="2"><img src="' .PATH .'' . $row['Foto'] . '" width="300px"/></th>
					</tr>		
					<tr>
					<th colspan="2" style="text-align: center;"><a href="index.php" class="btn btn-primary">Regresar</a></th>
					</tr>																					
					</tbody>
					</table>
				</div>';
				
				return $html;
		}
	}
	
	
	public function delete_consultas($id){
		$sql = "DELETE FROM consultas WHERE ConsultaID=$id;";
			if($this->con->query($sql)){
			echo $this->_message_ok("ELIMINÓ");
		}else{
			echo $this->_message_error("eliminar");
		}	
	}
	

	
//*************************	
	
	private function _message_error($tipo){
		$html = '
		<table border="0" align="center">
			<tr>
				<th>Error al ' . $tipo . '. Favor contactar a .................... </th>
			</tr>
			<tr>
				<th><a href="index.php">Regresar</a></th>
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
				<th><a href="index.php">Regresar</a></th>
			</tr>
		</table>';
		return $html;
	}
	
//**************************	
	
} // FIN SCRPIT
?>