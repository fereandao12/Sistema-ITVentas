<?php

if(strlen(session_id()) < 1)
	session_start();

require_once "../modelos/Ingreso.php";



$ingreso = new Ingreso();
$idusuario=$_SESSION["idusuario"];
$idingreso=isset($_POST['idingreso'])? limpiarCadena($_POST['idingreso']):"";
$idproveedor=isset($_POST['idproveedor'])? limpiarCadena($_POST['idproveedor']):"";

$tipo_comprobante=isset($_POST['tipo_comprobante'])? limpiarCadena($_POST['tipo_comprobante']):"";
$serie_comprobante=isset($_POST['serie_comprobante'])? limpiarCadena($_POST['serie_comprobante']):"";
$num_comprobante=isset($_POST['num_comprobante'])? limpiarCadena($_POST['num_comprobante']):"";
$fecha_hora=isset($_POST['fecha_hora'])? limpiarCadena($_POST['fecha_hora']):"";
$impuesto=isset($_POST['impuesto'])? limpiarCadena($_POST['impuesto']):"";
$total_compra=isset($_POST['total_compra'])? limpiarCadena($_POST['total_compra']):"";

switch ($_GET['op']) {
	case 'guardaryeditar':
		if(empty($idingreso)){
			$rspta=$ingreso->insertar($idproveedor, $idusuario, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto, $total_compra,$_POST['idarticulo'],$_POST['cantidad'],$_POST['precio_compra'],$_POST['precio_venta']);
			echo $rspta ? "Ingreso registrado" : "Ingreso no se pudo registrar";
		}else{
			
		}
	break;
	
	case 'anular':
			$rspta=$ingreso->anular($idingreso);
			echo $rspta ? "Ingreso anulado" : "Ingreso no se pudo anular";
	break;

	case 'mostrar':
			$rspta=$ingreso->mostrar($idingreso);
			//codificar el resultado usando JSON
			echo json_encode($rspta);
	break;

	case 'listarDetalle':
		//Recibimos el idingreso
		$id=$_GET['id'];

		$rspta = $ingreso->listarDetalle($id);
		$total = 0;
		echo ' <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                              <thead style="background-color: #A9D0F5;">
                                <th>Opciones</th> 
                                <th>Articulo</th> 
                                <th>Cantidad</th> 
                                <th>Precio Compra</th> 
                                <th>Precio Venta</th> 
                                <th>Subtotal</th> 
                              </thead>';
		while($reg = $rspta->fetch_object()){
			echo '<tr class="filas"><td></td><td>'.$reg->nombre.'</td><td>'.$reg->cantidad.'</td><td>'.$reg->precio_compra.'</td><td>'.$reg->precio_venta.'</td><td>'.$reg->precio_compra*$reg->cantidad.'</td></tr>';
			$total = $total+($reg->precio_compra*$reg->cantidad);
		}
		echo '<tfoot>
                                <th>TOTAL</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th><h4 id="total"> S/.'.$total.'</h4><input type="hidden" name="total_compra" id="total_compra"></th>
                              </tfoot>';

	break;

	case 'listar':
		$rspta=$ingreso->listar();
		//Declaracion de array
		$data = Array();

		while ($reg=$rspta->fetch_object()) {
				$data[]=array(
					"0"=>($reg->estado == 'Aceptado')?'<button class="btn btn-warning"><i class="fa	fa-eye" onclick="mostrar('.$reg->idingreso.')"> </i></button>'.
					' <button class="btn btn-danger"><i class="fa	fa-close" onclick="anular('.$reg->idingreso.')"></i></button>':
					' <button class="btn btn-warning"><i class="fa	fa-eye" onclick="mostrar('.$reg->idingreso.')"></i></button>',
					"1"=>$reg -> fecha,
					"2"=>$reg -> proveedor,
					"3"=>$reg -> usuario,
					"4"=>$reg -> tipo_comprobante,
					"5"=>$reg -> serie_comprobante. '-' .$reg->num_comprobante,
					"6"=>$reg -> total_compra,
					"7"=>($reg -> estado == 'Aceptado')?'<span class="label bg-green"> Activado </span>':'<span class="label bg-red"> Anulado </span>'
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

	case 'selectProveedor':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona -> listarP();

		while($reg = $rspta->fetch_object()){
			echo '<option value='. $reg->idpersona . '>' . $reg->nombre . '</option>';
		}

	break;

	case 'listarArticulos':

		require_once "../modelos/Articulo.php";

		$articulo = new Articulo();

		$rspta=$articulo->listarActivos();
		//Declaracion de array
		$data = Array();

		while ($reg=$rspta->fetch_object()) {
				$data[]=array(
					"0"=>'<button class="btn btn-warning fa fa-plus" onclick="agregarDetalle('.$reg->idarticulo.',\''.$reg->nombre.'\')"><span class fa fa-plus></span></button>',
					"1"=>$reg -> nombre,
					"2"=>$reg -> categoria,
					"3"=>$reg -> codigo,
					"4"=>$reg -> stock,
					"5"=>"<img src='../files/articulos/" . $reg->imagen . "' height='50px' width='50px'>"
				);
		}
		$results = array(
		"sEcho"=>1, //informacion para dataTables
		"iTotalRecords"=>count($data), //enviamos el total de registros al dataTable
		"iTotalDisplayRecords"=>count($data),//enviamos el total de registros a visualizar
		"aaData"=>$data	
		);	
		echo json_encode($results);	break;
}

?>