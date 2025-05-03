var tabla;

//Función que se ejecuta al inicio

function init(){
	mostrarform(false);
	listar();

	$("#formulario").on("submit",function(e){
		guardaryeditar(e);
	})
}

//Función limpiar
function limpiar(){

	$("#idpersona").val("");
	$("#nombre").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");

}

//Función mostrar formulario
function mostrarform(flag){
	limpiar();
	if(flag){
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled",false);
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
}

//Función listar
function listar(){
	tabla = $("#tbllistado").dataTable({
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
			url: '../ajax/persona.php?op=listarp',
			type: "get",
			dataType: "json",
			error: function(e){
				console.log(e.responseText);
			}
		},
		"bDestroy": true,
		"iDisplayLength": 5,//Paginacion cada 5 registros
		"order": [[0,"desc"]]//ordernar (columna,nombre)
	}).DataTable();
}
//Función para guardar o editar

function guardaryeditar(e){
	e.preventDefault(); //No se activara la accion predeterminada del evento
	$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/persona.php?op=guardaryeditar",
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

function mostrar(idpersona) {
	$.post("../ajax/persona.php?op=mostrar",{idpersona : idpersona}, function(data,status){
		data = JSON.parse(data);
		mostrarform(true);

		$("#nombre").val(data.nombre);
		$("#tipo_documento").val(data.tipo_documento);
		$("#num_documento").val(data.num_documento);
		$("#direccion").val(data.direccion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#idpersona").val(data.idpersona);


	});
}

//Funcion para desactivar registros
function eliminar(idpersona){
	bootbox.confirm("¿Esta seguro de eliminar el proveedor?",function(result){
		if(result){
			$.post("../ajax/persona.php?op=eliminar",{idpersona : idpersona}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}



init();