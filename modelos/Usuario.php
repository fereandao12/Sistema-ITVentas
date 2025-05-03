<?php
//incluimos la conexion a la BDD
require "../config/Conexion.php";



Class Usuario{

	//Constructor vacio
	public function __construct(){	

	}

	//Metodo para insertar registros
	public function insertar($nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $permisos){
		$sql = "INSERT INTO usuario (nombre, tipo_documento, num_documento, direccion, telefono, email, cargo, login, clave, imagen ,condicion)
		VALUES ('$nombre', '$tipo_documento', '$num_documento', '$direccion', '$telefono', '$email', '$cargo', '$login', '$clave', '$imagen','1')";

		//return ejecutarConsulta($sql);
		$idususarionew = ejecutarConsulta_retornarID($sql);
		$num_elementos=0;

		$sw=true;

		while ($num_elementos < count($permisos)) {

			$sql_detalle = "INSERT INTO usuario_permiso(idusuario, idpermiso) VALUES('$idususarionew','$permisos[$num_elementos]')";

			ejecutarConsulta($sql_detalle) or $sw = false;

			$num_elementos = $num_elementos + 1;
		}

		return $sw;

	}

	//Metodo para editar registros
	public function editar($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $permisos){
		$sql = "UPDATE usuario SET nombre ='$nombre', tipo_documento = '$tipo_documento', num_documento = '$num_documento', direccion = '$direccion', email = '$email', cargo = '$cargo', login = '$login', clave = '$clave', imagen = '$imagen'
		WHERE idusuario='$idusuario'";

		ejecutarConsulta($sql);

		//Eliminamos todos los permisos asignados para volvernos a registrar
		$sqldel = "DELETE FROM usuario_permiso WHERE idusuario='$idusuario'";
		ejecutarConsulta($sqldel);

		$num_elementos=0;

		$sw=true;

		while ($num_elementos < count($permisos)) {

		$sql_detalle = "INSERT INTO usuario_permiso(idusuario, idpermiso) VALUES('$idusuario','$permisos[$num_elementos]')";

			ejecutarConsulta($sql_detalle) or $sw = false;

			$num = $num_elementos++;
		}

		return $sw;

	}

	//Metodo para desactivar registros
	public function desactivar($idusuario){
		$sql="UPDATE usuario SET condicion ='0' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Metodo para activar registros
	public function activar($idusuario){
		$sql="UPDATE usuario SET condicion ='1' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Metodo para mostrar los datos de un registro a modificar
	public function mostrar($idusuario){
		$sql="SELECT * FROM usuario WHERE idusuario = '$idusuario'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Metodo para listar los registros
	public function listar(){
		$sql="SELECT * FROM usuario";
		return ejecutarConsulta($sql);	
	}	

	//Implementar un método para listar los permisos marcados
	public function listarmarcados($idusuario){
		$sql="SELECT * FROM usuario_permiso WHERE idusuario = '$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Funcion para verificar el acceso al sistema
	public function verificar($login, $clave){
		$sql = "SELECT idusuario, nombre, tipo_documento, num_documento, telefono, email, cargo, imagen, login FROM usuario WHERE login='$login' AND clave='$clave' AND condicion='1'";

		return ejecutarConsulta($sql);
	}
	
}


?>