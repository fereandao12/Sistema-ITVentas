<?php

require_once "../modelos/Articulo.php";

$articulo = new Articulo();

$idarticulo=isset($_POST['idarticulo'])? limpiarCadena($_POST['idarticulo']):"";
$idcategoria=isset($_POST['idcategoria'])? limpiarCadena($_POST['idcategoria']):"";
$codigo=isset($_POST['codigo'])? limpiarCadena($_POST['codigo']):"";
$nombre=isset($_POST['nombre'])? limpiarCadena($_POST['nombre']):"";
$stock=isset($_POST['stock'])? limpiarCadena($_POST['stock']):"";
$descripcion=isset($_POST['descripcion'])? limpiarCadena($_POST['descripcion']):"";
$imagen=isset($_POST['imagen'])? limpiarCadena($_POST['imagen']):"";


switch ($_GET['op']) {
	case 'guardaryeditar':

		if(!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])){
			$imagen=_POST["imagenactual"];
		}else{
			$ext = explode(".", $_FILES['imagen']['name']);
			if($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png"){

				$imagen = round(microtime(true)) . '.' . end($ext);
				move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/articulos/" . $imagen);
			}
		}
		if(empty($idarticulo)){
			$rspta=$articulo->insertar($idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen);
			echo $rspta ? "Articulo registrada" : "Articulo no se pudo registrar";
		}else{
			$rspta=$articulo->editar($idarticulo,$idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen);
			echo $rspta ? "Articulo actualizado" : "Articulo no se pudo actualizar";
		}
	break;
	
	case 'desactivar':
			$rspta=$articulo->desactivar($idarticulo);
			echo $rspta ? "Articulo descativado" : "Articulo no se pudo desactivar";
	break;

	case 'activar':
			$rspta=$articulo->activar($idarticulo);
			echo $rspta ? "Articulo activado" : "Articulo no se pudo activar";	
	break;

	case 'mostrar':
			$rspta=$articulo->mostrar($idarticulo);
			//codificar el resultado usando JSON
			echo json_encode($rspta);
	break;

	case 'listar':
		$rspta=$articulo->listar();
		//Declaracion de array
		$data = Array();

		while ($reg=$rspta->fetch_object()) {
				$data[]=array(
					"0"=>($reg->condicion)?'<button class="btn btn-warning"><i class="fa	fa-pencil" onclick="mostrar('.$reg->idarticulo.')"> Editar </i></button>'.
					' <button class="btn btn-danger"><i class="fa	fa-close" onclick="desactivar('.$reg->idarticulo.')"> desactivar </i></button>':
					' <button class="btn btn-warning"><i class="fa	fa-pencil" onclick="mostrar('.$reg->idarticulo.')"> Editar </i></button>'.
					' <button class="btn btn-success"><i class="fa	fa-check" onclick="activar('.$reg->idarticulo.')"> Activar </i></button>',
					"1"=>$reg -> nombre,
					"2"=>$reg -> categoria,
					"3"=>$reg -> codigo,
					"4"=>$reg -> stock,
					"5"=>"<img src='../files/articulos/" . $reg->imagen . "' height='50px' width='50px'>",
					"6"=>($reg -> condicion)?'<span class="label bg-green"> Activado </span>':'<span class="label bg-red"> Desactivado </span>'
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

	case 'selectCategoria':

		require_once "../modelos/Categoria.php";
		$categoria = new Categoria();

		$rspta = $categoria->select();


	    while ($reg = $rspta->fetch_object())
		    {
		     echo '<option value=' . $reg->idcategoria . '>' . $reg->nombre . '</option>';
			}	

	break;

}

?>