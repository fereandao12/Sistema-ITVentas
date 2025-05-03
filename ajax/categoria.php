<?php

require_once "../modelos/Categoria.php";

$categoria = new Categoria();

$idcategoria=isset($_POST['idcategoria'])? limpiarCadena($_POST['idcategoria']):"";
$nombre=isset($_POST['nombre'])? limpiarCadena($_POST['nombre']):"";
$descripcion=isset($_POST['descripcion'])? limpiarCadena($_POST['descripcion']):"";


switch ($_GET['op']) {
	case 'guardaryeditar':
		if(empty($idcategoria)){
			$rspta=$categoria->insertar($nombre,$descripcion);
			echo $rspta ? "Categoria registrada" : "Categoria no se pudo registrar";
		}else{
			$rspta=$categoria->editar($idcategoria,$nombre,$descripcion);
			echo $rspta ? "Categoria actualizada" : "Categoria no se pudo actualizar";
		}
	break;
	
	case 'desactivar':
			$rspta=$categoria->desactivar($idcategoria);
			echo $rspta ? "Categoria descativada" : "Categoria no se pudo desactivar";
	break;

	case 'activar':
			$rspta=$categoria->activar($idcategoria);
			echo $rspta ? "Categoria activada" : "Categoria no se pudo activar";	
	break;

	case 'mostrar':
			$rspta=$categoria->mostrar($idcategoria);
			//codificar el resultado usando JSON
			echo json_encode($rspta);
	break;

	case 'listar':
		$rspta=$categoria->listar();
		//Declaracion de array
		$data = Array();

		while ($reg=$rspta->fetch_object()) {
				$data[]=array(
					"0"=>($reg->condicion)?'<button class="btn btn-warning"><i class="fa	fa-pencil" onclick="mostrar('.$reg->idcategoria.')"> Editar </i></button>'.
					' <button class="btn btn-danger"><i class="fa	fa-close" onclick="desactivar('.$reg->idcategoria.')"> desactivar </i></button>':
					' <button class="btn btn-warning"><i class="fa	fa-pencil" onclick="mostrar('.$reg->idcategoria.')"> Editar </i></button>'.
					' <button class="btn btn-success"><i class="fa	fa-check" onclick="activar('.$reg->idcategoria.')"> Activar </i></button>',
					"1"=>$reg -> nombre,
					"2"=>$reg -> descripcion,
					"3"=>($reg -> condicion)?'<span class="label bg-green"> Activado </span>':'<span class="label bg-red"> Desactivado </span>'
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