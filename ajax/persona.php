<?php

require_once "../modelos/Persona.php";

$persona = new Persona();

$idpersona=isset($_POST['idpersona'])? limpiarCadena($_POST['idpersona']):"";
$tipo_persona=isset($_POST['tipo_persona'])? limpiarCadena($_POST['tipo_persona']):"";
$nombre=isset($_POST['nombre'])? limpiarCadena($_POST['nombre']):"";
$tipo_documento=isset($_POST['tipo_documento'])? limpiarCadena($_POST['tipo_documento']):"";
$num_documento=isset($_POST['num_documento'])? limpiarCadena($_POST['num_documento']):"";
$direccion=isset($_POST['direccion'])? limpiarCadena($_POST['direccion']):"";
$telefono=isset($_POST['telefono'])? limpiarCadena($_POST['telefono']):"";
$email=isset($_POST['email'])? limpiarCadena($_POST['email']):"";


switch ($_GET['op']) {
	case 'guardaryeditar':
		if(empty($idpersona)){
			$rspta=$persona->insertar($tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email);
			echo $rspta ? "persona registrada" : "persona no se pudo registrar";
		}else{
			$rspta=$persona->editar($idpersona, $tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email);
			echo $rspta ? "persona actualizada" : "persona no se pudo actualizar";
		}
	break;
	
	case 'eliminar':
			$rspta=$persona->desactivar($idpersona);
			echo $rspta ? "persona descativada" : "persona no se pudo desactivar";
	break;


	case 'mostrar':
			$rspta=$persona->mostrar($idpersona);
			//codificar el resultado usando JSON
			echo json_encode($rspta);
	break;

	case 'listarp':
		$rspta=$persona->listarp();
		//Declaracion de array
		$data = Array();

		while ($reg=$rspta->fetch_object()) {
				$data[]=array(
					"0"=>' <button class="btn btn-warning"><i class="fa	fa-pencil" onclick="mostrar('.$reg->idpersona.')"> Editar </i></button>'.
					' <button class="btn btn-danger"><i class="fa	fa-trash" onclick="eliminar('.$reg->idpersona.')"> Eliminar </i></button>',
					"1"=>$reg -> nombre,
					"2"=>$reg -> tipo_documento,
					"3"=>$reg -> num_documento,
					"4"=>$reg -> direccion,
					"5"=>$reg -> telefono,
					"6"=>$reg -> email
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

	case 'listarc':
		$rspta=$persona->listarc();
		//Declaracion de array
		$data = Array();

		while ($reg=$rspta->fetch_object()) {
				$data[]=array(
					"0"=>' <button class="btn btn-warning"><i class="fa	fa-pencil" onclick="mostrar('.$reg->idpersona.')"> Editar </i></button>'.
					' <button class="btn btn-danger"><i class="fa	fa-trash" onclick="eliminar('.$reg->idpersona.')"> Eliminar </i></button>',
					"1"=>$reg -> nombre,
					"2"=>$reg -> tipo_documento,
					"3"=>$reg -> num_documento,
					"4"=>$reg -> direccion,
					"5"=>$reg -> telefono,
					"6"=>$reg -> email
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
}

?>