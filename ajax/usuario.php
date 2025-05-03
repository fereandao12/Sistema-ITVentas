<?php

session_start();
require_once "../modelos/Usuario.php";

$usuario = new Usuario();

$idusuario=isset($_POST['idusuario'])? limpiarCadena($_POST['idusuario']):"";
$nombre=isset($_POST['nombre'])? limpiarCadena($_POST['nombre']):"";
$tipo_documento=isset($_POST['tipo_documento'])? limpiarCadena($_POST['tipo_documento']):"";
$num_documento=isset($_POST['num_documento'])? limpiarCadena($_POST['num_documento']):"";
$direccion=isset($_POST['direccion'])? limpiarCadena($_POST['direccion']):"";
$telefono=isset($_POST['telefono'])? limpiarCadena($_POST['telefono']):"";
$email=isset($_POST['email'])? limpiarCadena($_POST['email']):"";
$cargo=isset($_POST['cargo'])? limpiarCadena($_POST['cargo']):"";
$login=isset($_POST['login'])? limpiarCadena($_POST['login']):"";
$clave=isset($_POST['clave'])? limpiarCadena($_POST['clave']):"";
$imagen=isset($_POST['imagen'])? limpiarCadena($_POST['imagen']):"";


switch ($_GET['op']) {
	case 'guardaryeditar':

		if(!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])){
			$imagen=$_POST["imagenactual"];
		}else{
			$ext = explode(".", $_FILES['imagen']['name']);
			if($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png"){

				$imagen = round(microtime(true)) . '.' . end($ext);
				move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/usuarios/" . $imagen);
			}
		}

		//Hash SHA256 en la contraseña
		$clavehash=hash("SHA256", $clave);
		
		if(empty($idusuario)){
			$rspta=$usuario->insertar($nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clavehash, $imagen, $_POST['permiso']);
			echo $rspta ? "Usuario registrado" : "No se pudieron registrar todo los datos del Usuario";
		}else{
			$rspta=$usuario->editar($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clavehash, $imagen, $_POST['permiso']);
			echo $rspta ? "Usuario actualizado" : "Usuario no se pudo actualizar";
		}
	break;
	
	case 'desactivar':
			$rspta=$usuario->desactivar($idusuario);
			echo $rspta ? "Usuario descativado" : "Usuario no se pudo desactivar";
	break;

	case 'activar':
			$rspta=$usuario->activar($idusuario);
			echo $rspta ? "Usuario activado" : "Usuario no se pudo activar";	
	break;

	case 'mostrar':
			$rspta=$usuario->mostrar($idusuario);
			//codificar el resultado usando JSON
			echo json_encode($rspta);
	break;

	case 'listar':
		$rspta=$usuario->listar();
		//Declaracion de array
		$data = Array();

		while ($reg=$rspta->fetch_object()) {
				$data[]=array(
					"0"=>($reg->condicion)?'<button class="btn btn-warning"><i class="fa	fa-pencil" onclick="mostrar('.$reg->idusuario.')"> Editar </i></button>'.
					' <button class="btn btn-danger"><i class="fa	fa-close" onclick="desactivar('.$reg->idusuario.')"> desactivar </i></button>':
					' <button class="btn btn-warning"><i class="fa	fa-pencil" onclick="mostrar('.$reg->idusuario.')"> Editar </i></button>'.
					' <button class="btn btn-success"><i class="fa	fa-check" onclick="activar('.$reg->idusuario.')"> Activar </i></button>',
					"1"=>$reg -> nombre,
					"2"=>$reg -> tipo_documento,
					"3"=>$reg -> num_documento,
					"4"=>$reg -> telefono,
					"5"=>$reg -> email,
					"6"=>$reg -> login,
					"7"=>"<img src='../files/usuarios/" . $reg->imagen . "' height='50px' width='50px'>",
					"8"=>($reg -> condicion)?'<span class="label bg-green"> Activado </span>':'<span class="label bg-red"> Desactivado </span>'
				);
		}
		$results = array(
		"sEcho"=>1, //informacion para dataTables
		"iTotalRecords"=>count($data), //enviamos el total de registros al dataTable
		"iTotalDisplayRecords"=>count($data),//enviamos el total de registros a visualizar
		"aaData"=>$data	
		);	
		echo json_encode($results);
	break;

	case 'permisos':
		//Obtenemos todos los permisos de la tabla permisos
		require_once "../modelos/Permiso.php";
		$permiso = new Permiso();
		$rspta = $permiso->listar();

		//Obtener permisos otorgados al Usuario
		$id = $_GET['id'];
		$marcados = $usuario -> listarmarcados($id);
		//Declaramos el array para almacenar todos los permisos marcados
		$valores = array();

		//Almacenar los permisos asignados a al array
		while($per = $marcados-> fetch_object()){
			array_push($valores, $per -> idpermiso); 
		}

		//mostramos la lista de permisos en la vista y si estan o no marcados
		while ($reg = $rspta->fetch_object()) {

			$sw = in_array($reg->idpermiso,$valores)?'checked':'';
			echo '<li> <input type="checkbox" '.$sw.' name="permiso[]" value=" '.$reg->idpermiso.' "> ' .$reg->nombre. '  </li>';
		}
		
	break;

	case 'verificar':
		$logina=$_POST['logina'];
		$clavea=$_POST['clavea'];

		//Hash SHA256 en la contraseña
		$clavehash = hash("SHA256",$clavea);

		$rspta=$usuario->verificar($logina,$clavehash);

		$fetch = $rspta->fetch_object();

		if(isset($fetch)){
			//Declaramos las variables de sesión
			$_SESSION['idusuario']=$fetch->idusuario;
			$_SESSION['nombre']=$fetch->nombre;
			$_SESSION['imagen']=$fetch->imagen;
			$_SESSION['login']=$fetch->login;

			//obtenemos los permisos del usuario
			$marcados = $usuario->listarmarcados($fetch->idusuario);

			//Declaramos el array para almacenar todos los permisos marcados
			$valores = array();

			//Almacenamos los permisos marcados en el array
			while($per = $marcados->fetch_object()){
				array_push($valores, $per->idpermiso);
			}


			//Determinamos los accesos del usuario 
			in_array(1, $valores)?$_SESSION['Escritorio']=1:$_SESSION['Escritorio']=0;
			in_array(2, $valores)?$_SESSION['Almacen']=1:$_SESSION['Almacen']=0;
			in_array(3, $valores)?$_SESSION['Compras']=1:$_SESSION['Compras']=0;
			in_array(4, $valores)?$_SESSION['Ventas']=1:$_SESSION['Ventas']=0;
			in_array(5, $valores)?$_SESSION['Acceso']=1:$_SESSION['Acceso']=0;
			in_array(6, $valores)?$_SESSION['Consulta Compras']=1:$_SESSION['Consulta Compras']=0;
			in_array(7, $valores)?$_SESSION['Consulta Ventas']=1:$_SESSION['Consulta Ventas']=0;
		}
		echo json_encode($fetch);

	break;

	case 'salir':
		//Limpiamos las variables de sesión
		session_unset();
		//Destruimos la sesión
		session_destroy();
		//Redireccionamos al login
		header("Location: ../index.php");
	break;

}

?>