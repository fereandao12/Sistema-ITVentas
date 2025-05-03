	var tabla;

	//Función que se ejecuta al inicio

	function init(){
		mostrarform(false);
		listar();

		$("#formulario").on("submit",function(e){
			guardaryeditar(e);
		})

		$("#imagenmuestra").hide();
		//Mostramos los permisos
		$.post("../ajax/usuario.php?op=permisos&id=",function(r){
			$("#permisos").html(r);
		});
	}

	//Función limpiar
	function limpiar(){

		$("#nombre").val("");
		$("#tipo_documento").val("");
		$("#num_documento").val("");
		$("#direccion").val("");
		$("#telefono").val("");
		$("#email").val("");	
		$("#cargo").val("");
		$("#login").val("");
		$("#clave").val("");
		$("#imagenmuestra").attr("src","");
		$("#imagenactual").val("");
		$("#idusuario").val("");
		$("#permisos").val("");
	}

	//Función mostrar formulario
	function mostrarform(flag){
		limpiar();
		if(flag){
			$("#listadoregistros").hide();
			$("#formularioregistros").show();
			$("#btnGuardar").prop("disabled",false);
			$("#btnagregar").hide();
		}
		else{
			$("#listadoregistros").show();
			$("#formularioregistros").hide();
		}
	}

	//Función cancelarform
	function cancelarform(){
		limpiar();
		mostrarform(false);
		$("#btnagregar").show();
	}

	//Función listar
	function listar(){
		tabla = $('#tbllistado').dataTable({
		"aProcessing":true, //Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: 'Bfrtip', //Definimos los elementos del contro de la tabla
		buttons: [
			'copyHtml5',
			'excelHtml5',
			'csvHtml5',
			'pdf'
		],
		"ajax":{
			url: '../ajax/usuario.php?op=listar',
			type: "get",
			dataType: "json",
			error: function(e){
				console.log(e.responseText);
			}
		},
		"bDestroy": true,
		"iDisplayLength": 6,//Paginacion cada 5 registros
		"order": [[0,"desc"]]//ordernar (columna,nombre)
		}).DataTable();
	}
	//Función para guardar o editar

	function guardaryeditar(e){
		e.preventDefault(); //No se activara la accion predeterminada del evento
		$("#btnGuardar").prop("disabled",true);
		var formData = new FormData($("#formulario")[0]);

		$.ajax({
			url: "../ajax/usuario.php?op=guardaryeditar",
			type: "POST",
			data: formData,
			contentType: false,
			processData: false,

			success: function(datos){
				bootbox.alert(datos);
				mostrarform(false);
				tabla.ajax.reload();
			}
		});
		limpiar();
	}

	//Funcion para mostrar registros

	function mostrar(idusuario) {
		$.post("../ajax/usuario.php?op=mostrar", {idusuario : idusuario}, function(data,status){
			data = JSON.parse(data);
			mostrarform(true);
			
			$("#nombre").val(data.nombre);
			$("#tipo_documento").val(data.tipo_documento);
			$("#num_documento").val(data.num_documento);
			$("#direccion").val(data.direccion);
			$("#telefono").val(data.telefono);
			$("#email").val(data.email);	
			$("#cargo").val(data.cargo)
			$("#login").val(data.login);
			$("#clave").val(data.clave);
			$("#imagenmuestra").show();
			$("#imagenmuestra").attr("src","../files/usuarios/"+data.imagen);
			$("#imagenactual").val(data.imagen);
			$("#idusuario").val(data.idusuario);

		});
		$.post("../ajax/usuario.php?op=permisos&id="+idusuario,function(r){
			$("#permisos").html(r);
		});
	}

	//Funcion para desactivar registros
	function desactivar(idusuario){
		bootbox.confirm("¿Esta seguro de desactivar el usuario?",function(result){
			if(result){
				$.post("../ajax/usuario.php?op=desactivar",{idusuario : idusuario}, function(e){
					bootbox.alert(e);
					tabla.ajax.reload();
				});
			}
		})
	}

	function activar(idusuario){
		bootbox.confirm("¿Esta seguro de activar el usuario?",function(result){
			if(result){
				$.post("../ajax/usuario.php?op=activar",{idusuario : idusuario}, function(e){
					bootbox.alert(e);
					tabla.ajax.reload();
				});
			}
		})
	}


  init();