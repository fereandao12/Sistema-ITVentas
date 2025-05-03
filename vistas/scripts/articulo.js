	var tabla;

	//Función que se ejecuta al inicio

	function init(){
		mostrarform(false);
		listar();

		$("#formulario").on("submit",function(e){
			guardaryeditar(e);
		})

		//Cargamos los items al select categoria
		$.post("../ajax/articulo.php?op=selectCategoria", function(r){
			$("#idcategoria").html(r);
		});
		$("#imagenmuestra").hide();
	}

	//Función limpiar
	function limpiar(){
		$("#codigo").val("");
		$("#nombre").val("");
		$("#descripcion").val("");
		$("#stock").val("");
		$("#imagenmuestra").attr("src","");
		$("#imagenactual").val("");	
		$("#print").hide();
		$("#idarticulo").val("");
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
			url: '../ajax/articulo.php?op=listar',
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
			url: "../ajax/articulo.php?op=guardaryeditar",
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

	function mostrar(idarticulo) {
		$.post("../ajax/articulo.php?op=mostrar", {idarticulo : idarticulo}, function(data,status){
			data = JSON.parse(data);
			mostrarform(true);
			
			$("#idcategoria").val(data.idcategoria);
			$("#codigo").val(data.codigo);
			$("#nombre").val(data.nombre);
			$("#stock").val(data.stock);
			$("#descripcion").val(data.descripcion);
			$("#imagenmuestra").show();	
			$("#imagenmuestra").attr("src","../files/articulos/"+data.imagen);
			$("#imagenactual").val(data.imagen);
			$("#idarticulo").val(data.idarticulo);
			generarbarcode();
		});
	}

	//Funcion para desactivar registros
	function desactivar(idarticulo){
		bootbox.confirm("¿Esta seguro de desactivar el Articulo?",function(result){
			if(result){
				$.post("../ajax/articulo.php?op=desactivar",{idarticulo : idarticulo}, function(e){
					bootbox.alert(e);
					tabla.ajax.reload();
				});
			}
		})
	}

	function activar(idarticulo){
		bootbox.confirm("¿Esta seguro de activar el Articulo?",function(result){
			if(result){
				$.post("../ajax/articulo.php?op=activar",{idarticulo : idarticulo}, function(e){
					bootbox.alert(e);
					tabla.ajax.reload();
				});
			}
		})
	}

	function generarbarcode() {
		codigo = $("#codigo").val();
		JsBarcode("#barcode", codigo);
		$("#print").show();
	}


	function imprimir()
	{
		$("#print").printArea();
	}


  init();