<?php
//incluimos la conexion a la BDD
require "../config/Conexion.php";



Class Articulo{

	//Constructor vacio
	public function __construct(){	

	}

	//Metodo para insertar registros
	public function insertar($idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen){
		$sql = "INSERT INTO articulo (idcategoria,codigo,nombre,stock,descripcion,imagen,condicion)
		VALUES ('$idcategoria','$codigo','$nombre','$stock','$descripcion','$imagen','1')";

		return ejecutarConsulta($sql);
	}

	//Metodo para editar registros
	public function editar($idarticulo,$idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen){
		$sql = "UPDATE articulo SET idcategoria ='$idcategoria',codigo ='$codigo', nombre = '$nombre', stock = '$stock', descripcion = '$descripcion', imagen = '$imagen'
		WHERE idarticulo='$idarticulo'";

		return ejecutarConsulta($sql);

	}

	//Metodo para desactivar registros
	public function desactivar($idarticulo){
		$sql="UPDATE articulo SET condicion ='0' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}

	//Metodo para activar registros
	public function activar($idarticulo){
		$sql="UPDATE articulo SET condicion ='1' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}

	//Metodo para mostrar los datos de un registro a modificar
	public function mostrar($idarticulo){
		$sql="SELECT * FROM articulo WHERE idarticulo = '$idarticulo'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Metodo para listar los registros
	public function listar(){
		$sql="SELECT a.idarticulo, a.idcategoria, c.nombre AS categoria, a.codigo, a.nombre, a.stock, a.descripcion, a.imagen, a.condicion FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria";
		return ejecutarConsulta($sql);	
	}

	//Metodo para listar los registros activos
	public function listarActivos(){
		$sql="SELECT a.idarticulo, a.idcategoria, c.nombre AS categoria, a.codigo, a.nombre, a.stock, a.descripcion, a.imagen, a.condicion FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.condicion = '1'";
		return ejecutarConsulta($sql);	
	}	

	//IMPLEMENTAR UN MÉTODO PARA LISTAR LOS REGISTROS ACTIVOS, SU ULTIMO PRECIO Y EL STOCK (VAMOS A UNIR CON EL ULTIMO REGISTRO DE LA TABLA DETALLE_INGRESO)
	public function listarActivosVenta(){
		$sql="SELECT a.idarticulo, a.idcategoria, c.nombre as categoria, a.codigo, a.nombre, a.stock, (SELECT precio_venta FROM detalle_ingreso WHERE idarticulo=a.idarticulo ORDER BY iddetalle_ingreso DESC LIMIT 0,1) AS precio_venta, a.descripcion, a.imagen, a.condicion FROM articulo a INNER JOIN categoria c ON a.idcategoria = c.idcategoria WHERE a.condicion = '1'";
		return ejecutarConsulta($sql);
	}
}


?>