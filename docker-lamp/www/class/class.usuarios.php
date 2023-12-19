<?php
class usuarios
{
	private $IdUsuario;
	private $Nombre;
	private $Password;
	private $Rol;
	private $Foto;
	private $con;

	function __construct($cn)
	{
		$this->con = $cn;
	}


	//*********************** 3.1 METODO update_vehiculo() **************************************************	

	public function update_usuarios()
	{
		$this->IdUsuario = $_POST['idusuario'];
		$this->Nombre = $_POST['Nombre'];
		$this->Password = isset($_POST['Password']) ? $_POST['Password'] : $this->get_password_from_database(); // Utiliza la contraseña actual si no se proporciona una nueva
		$this->Rol = $_POST['Rol'];
		$this->Foto = $_FILES['Foto']['name'];
	
		$sql = "UPDATE usuarios SET Nombre='$this->Nombre',
									Password='$this->Password',
									Rol='$this->Rol',
									Foto='$this->Foto'
									WHERE IdUsuario=$this->IdUsuario;";
	
		if ($this->con->query($sql)) {
			echo $this->_message_ok("modificó");
		} else {
			echo $this->_message_error("al modificar");
		}
	}
	
	// Agrega un método para obtener la contraseña actual del usuario desde la base de datos
	private function get_password_from_database()
	{
		$sql = "SELECT Password FROM usuarios WHERE IdUsuario = $this->IdUsuario;";
		$result = $this->con->query($sql);
		
		if ($result && $row = $result->fetch_assoc()) {
			return $row['Password'];
		} else {
			return ''; // Devuelve una cadena vacía si no se puede obtener la contraseña actual
		}
	}


	//*********************** 3.2 METODO save_vehiculo() **************************************************	

	public function save_usuarios()
	{

		$this->IdUsuario = $_POST["idusuario"];
		$this->Nombre = $_POST['Nombre'];
		$this->Password = isset($_POST['Password']) ? $_POST['Password'] : '';
		$this->Rol = $_POST['Rol'];
		$this->Foto = $_FILES['Foto']['name'];
		/*
				echo "<br> FILES <br>";
				echo "<pre>";
					print_r($_FILES);
				echo "</pre>";
		    */

			$this->foto = $this->_get_name_file($_FILES['Foto']['name'], 12);
			$path = "../../../images/Usuarios/".$this->Foto;
			

		//exit;
		if (!move_uploaded_file($_FILES['Foto']['tmp_name'], $path)) {
			$mensaje = "Cargar la imagen";
			echo $this->_message_error($mensaje);
			exit;
		}

		$sql = "INSERT INTO usuarios VALUES(NULL,
											'$this->Nombre',
											'123',
											'$this->Rol',
											'$this->Foto');";
		//echo $sql;
		//exit;
		if ($this->con->query($sql)) {
			echo $this->_message_ok("guardó");
		} else {
			echo $this->_message_error("guardar");
		}
	}


	//*********************** 3.3 METODO _get_name_File() **************************************************	

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


	//*************************************** PARTE I ************************************************************


	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_combo_db($tabla, $valor, $etiqueta, $nombre, $defecto)
	{
		$html = '<select name="' . $nombre . '">';
		$sql = "SELECT $valor,$etiqueta FROM $tabla;";
		$res = $this->con->query($sql);
		while ($row = $res->fetch_assoc()) {
			//ImpResultQuery($row);
			$html .= ($defecto == $row[$valor]) ? '<option value="' . $row[$valor] . '" selected>' . $row[$etiqueta] . '</option>' . "\n" : '<option value="' . $row[$valor] . '">' . $row[$etiqueta] . '</option>' . "\n";
		}
		$html .= '</select>';
		return $html;
	}

	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_combo_anio($nombre, $anio_inicial, $defecto)
	{
		$html = '<select name="' . $nombre . '">';
		$anio_actual = date('Y');
		for ($i = $anio_inicial; $i <= $anio_actual; $i++) {
			$html .= ($i == $defecto) ? '<option value="' . $i . '" selected>' . $i . '</option>' . "\n" : '<option value="' . $i . '">' . $i . '</option>' . "\n";
		}
		$html .= '</select>';
		return $html;
	}

	/*Aquí se agregó el parámetro:  $defecto*/
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
				// OPCION PARA GRABAR UN NUEVO VEHICULO (idusuario=0)
				$html .= '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>';
			} else {
				// OPCION PARA MODIFICAR UN VEHICULO EXISTENTE
				$html .= ($defecto == $etiqueta) ? '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>' : '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '"/></td>';
			}

			$html .= '</tr>';
		}
		$html .= '
		</table>';
		return $html;
	}


	//************************************* PARTE II ****************************************************	

	public function get_form($idusuario = NULL)
	{

		if ($idusuario == NULL) {
			$this->Nombre = NULL;
			$this->Password = NULL;
			$this->Rol = NULL;
			$this->Foto = NULL;

			//$flag = NULL;
			$flag = "enabled"; //-> ese para el formulario nuevo 
			$op = "new";
		} else {

			$sql = "SELECT * FROM usuarios WHERE IdUsuario=$idusuario;";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();

			$num = $res->num_rows;
			if ($num == 0) {
				$mensaje = "tratar de actualizar el vehiculo con idusuario= " . $idusuario;
				echo $this->_message_error($mensaje);
			} else {

				// ***** TUPLA ENCONTRADA *****
				echo "<br>TUPLA <br>";
				echo "<pre>";
				print_r($row);
				echo "</pre>";

				$this->Nombre = $row['Nombre'];
				$this->Password = $row['Password'];
				$this->Rol = $row['Rol'];
				$this->Foto = $row['Foto'];

				$flag = "enabled"; //->para actualizar no poder modificar la foto
				$op = "update";
			}
		}

		$html = '
		<form name="Form_vehiculo" method="POST" action="usuarios.php" enctype="multipart/form-data">
			<!-- Agrego dos líneas -> hidden oculto -->
			<input type="hidden" name="idusuario" value="' . $idusuario . '">
			<input type="hidden" name="op" value="' . $op . '">
					 
			<div class="container mt-4">
				<div class="row justify-content-center">
					<div class="col-md-8">
						<div class="card">
						<div class="card-header" style="background-color: #FFA500; color: white;">
						DATOS USUARIO
					</div>
							<div class="card-body">
								<div class="form-group row">
									<label for="nombre" class="col-md-4 col-form-label">Nombre de Usuario:</label>
									<div class="col-md-8">
										<input type="text" class="form-control" name="Nombre" value="' . $this->Nombre . '">
									</div>
								</div>
								<div class="form-group row">
									<label for="marca" class="col-md-4 col-form-label">Rol:</label>
									<div class="col-md-8">
										' . $this->_get_combo_db("roles", "IdRol", "Nombre", "Rol", $this->Rol) . '
									</div>
								</div>
								<div class="form-group row">
									<label for="foto" class="col-md-4 col-form-label">Foto:</label>
									<div class="col-md-8">
										<input type="file" name="Foto" ' . $flag . ' required>
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
				<th colspan="8" class="text-center">Lista de Usuarios</th>
			</tr>
			<tr>
				<th colspan="8" class="text-center align-middle" ><a class="btn btn-outline-success" href="usuarios.php?d=' . $d_new_final . '">Nuevo</a></th>
			</tr>
					
				<tr>
					<th>Nombre</th>
					<th>Rol</th>
					<th colspan="5">Acciones</th>
				</tr>
			</thead>
		<tbody>';
		$sql = "SELECT
		u.IdUsuario,
		u.Nombre,
		u.Password,
		r.Nombre AS Rol
		FROM
			usuarios u
		JOIN
			roles r ON u.Rol = r.IdRol;	";
		$res = $this->con->query($sql);
		// Sin codificar <td><a href="index.php?op=del&idusuario=' . $row['idusuario'] . '">Borrar</a></td>
		while ($row = $res->fetch_assoc()) {
			$d_del = "del/" . $row['IdUsuario'];
			$d_del_final = base64_encode($d_del);
			$d_act = "act/" . $row['IdUsuario'];
			$d_act_final = base64_encode($d_act);
			$d_det = "det/" . $row['IdUsuario'];
			$d_det_final = base64_encode($d_det);
			$html .= '
				<tr>
					<td>' . $row['Nombre'] . '</td>
					<td>' . $row['Rol'] . '</td>
					<td class="text-center"><button class="btn btn-danger"><a href="usuarios.php?d=' . $d_del_final . '">Borrar</a></button></td>
					<td class="text-center"><button class="btn btn-warning"><a href="usuarios.php?d=' . $d_act_final . '">Actualizar</a></button></td>
					<td class="text-center"><button class="btn btn-info"><a href="usuarios.php?d=' . $d_det_final . '">Detalle</a></button></td>
				</tr>';
		}
		$html .= '  
		</table>';

		return $html;
	}

	public function get_image_path($image_name) {
		$paths = [
			"../assets/imagenes/Usuarios/" . $image_name,
			"../assets/imagenes/Sellos/" . $image_name,
			"../assets/imagenes/paises/" . $image_name
		];
	
		foreach ($paths as $path) {
			if (file_exists($path)) {
				return $path;
			}
		}
	
		return ""; // Retorna un string vacío si no se encuentra la imagen
	}

	public function get_detail_usuarios($idusuario)
	{
		$sql = "SELECT
		u.IdUsuario,
		u.Nombre,
		u.Password,
		r.Nombre AS Rol,
		u.Foto
		FROM
			usuarios u
		JOIN
			roles r ON u.Rol = r.IdRol
		WHERE IdUsuario = $idusuario;	";

		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();

		$num = $res->num_rows;


		//Si es que no existiese ningun registro debe desplegar un mensaje 
		//$mensaje = "tratar de eliminar el vehiculo con idusuario= ".$idusuario;
		//echo $this->_message_error($mensaje);
		//y no debe desplegarse la tablas

		if ($num == 0) {
			$mensaje = "tratar de editar el usuario con idusuario= " . $idusuario;
			echo $this->_message_error($mensaje);
		} else {
			$image_path = $this->get_image_path($row['Foto']);
			$html = '
			<div class="container mt-4">
			<div class="row justify-content-center">
				<div class="col-md-8">
					<div class="card">
					<div class="card-header" style="background-color: #FFA500; color: white;">
					Detalles USUARIO
				</div>
						<div class="card-body">
							<table class="table">
								<tr>
									<td>Nombre de Usuario:</td>
									<td>' . $row['Nombre'] . '</td>
								</tr>
								<tr>
									<td>Rol:</td>
									<td>' . $row['Rol'] . '</td>
								</tr>
								<tr>
                                    <td colspan="2">
									<img src="' . $image_path . '" class="img-fluid" alt="Foto del Usuario"/>
                                    </td>
                                </tr>
								
								<tr>
									<td colspan="2">
										<a href="./usuarios.php" class="btn btn-primary">Regresar</a>
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


	public function delete_usuarios($idusuario)
	{
		$sql = "DELETE FROM usuarios WHERE IdUsuario=$idusuario;";
		if ($this->con->query($sql)) {
			echo $this->_message_ok("ELIMINÓ");
		} else {
			echo $this->_message_error("eliminar");
		}
	}

	private function _message_error($tipo)
	{
		$html = '
		<table border="0" align="center">
			<tr>
				<th>Error al ' . $tipo . '. Favor contactar a .................... </th>
			</tr>
			<tr>
				<th><a href="./usuarios.php">Regresar</a></th>
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
				<th><a href="./usuarios.php">Regresar</a></th>
			</tr>
		</table>';
		return $html;
	}

	//****************************************************************************	

} // FIN SCRPIT
