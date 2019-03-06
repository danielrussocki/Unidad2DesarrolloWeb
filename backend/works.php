<?php
session_start();
error_reporting(0);
if (isset($_SESSION['access'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Works</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Custom styles for this template -->
    <link href="css/estilos.css" rel="stylesheet">
</head>
<body>
	    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow" id="navh"></nav>
	<div class="container-fluid view">
		<nav class="col-md-2 d-none d-md-block bg-light sidebar" id="navv"></nav>
		<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 mt-4" id="main">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group mr-2">
            <button type="button" class="btn btn-sm btn-outline-danger cancelar">Cancelar</button>
            <button type="button" class="btn btn-sm btn-outline-success" id="nuevo_registro">Nuevo</button>
          </div>
        </div>
      </div>
      <h2>Works</h2>
      <div class="table-responsive view" id="show-data">
        <table class="table table-striped table-sm" id="list_works">
          <thead>
            <tr>
              <th>Imagen</th>
              <th>Título</th>
              <th>Subtítulo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            
          </tbody>
        </table>
      </div>
      <div id="insert_data" class="view">
		<form id="form_works" method="post" enctype="multipart/form-data">
            <div class="row">
            	<div class="col">
		        	<div class="form-group">
		            	<label for="work">Trabajo: </label>
		            	<input type="text" id="work" name="work" class="form-control">
		        	</div>
		        </div>
		        <div class="col">
		        	<div class="form-group">
		            <label for="description">Descripción: </label>
		        	<input type="text" id="description" name="description" class="form-control">
		        	</div>
            	</div>
            </div>
            <div class="row">
            	<div class="col">
	            		<div class="form-group">
	            			<div class="custom-file">
							<input type="file" class="custom-file-input" name="foto" id="foto">
							<label class="custom-file-label" for="foto">Adjuntar archivo</label>
              <input type="text" name="ruta" id="ruta" readonly="readonly">
						</div>
            		</div>
                <div id="preview" class="preview"></div>
            	</div>
            </div>
            <div class="row">
              <div class="col">
                <button class="btn btn-success" type="button" id="enviar">
                  Guardar
                </button>
              </div>
            </div>
        </form>
        </div>
    </main>
	</div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
  function change_view(vista = 'show-data'){
    $('#main').find('.view').each(function(){
      $(this).slideUp(100);
      //$(this).addClass('d-none');
      let id=$(this).attr("id");
      console.log(id);
      if (vista == id) {
        $(this).slideDown(100);
        //$(this).removeClass('d-none');
      }
    });
  }
  function consultar(){
    let obj = {
      "accion":"consultar_works"
    };
  $.post("includes/_funciones.php",obj,function(respuesta){
    let template = '';
    $.each(respuesta, function(i,e){
            template += `
            <tr>
              <td>${e.works_file}</td>
              <td>${e.works_title}</td>
              <td>${e.works_subtitle}</td>
              <td>
                <a href="#" data-id="${e.works_id}" class="editar_registro">Editar</a>
                <a href="#" data-id="${e.works_id}" class="eliminar_registro">Eliminar</a>
              </td>
            </tr>
            `;
      });
    $("#list_works tbody").html(template);
    },"JSON");
  }
		$(document).ready(function(){
		$("#navv").load("./sidebar.html");
    	$("#navh").load("./navbar.html");
      consultar();
      change_view();
		});
    $('#nuevo_registro').click(function(){
    change_view('insert_data');
    });
    $('#main').find('.cancelar').click(function(){
    change_view();
    $('#form_works')[0].reset();
    });
    $('#enviar').click(function(e){
    e.preventDefault();
    let obj = {
      "accion":"insertar_works"
    };
    $('#form_works').find("input").each(function(){
      $(this).removeClass('is-invalid');
      if ($(this).val() != '') {
        obj[$(this).prop('name')] = $(this).val();
      } else {
        $(this).addClass('is-invalid').focus();
        return false;
      }
    });
    if ($(this).data('editar') == 1) {
      console.log(obj);
      obj["accion"] = "editar_registro";
      obj["registro"] = $(this).data("registro");
      obj["tabla"] = "works";
      console.log(obj);
      $(this).text("Guardar").removeData("editar").removeData("registro");
    }
    $.post("includes/_funciones.php",obj,function(d){
        if (d=="1") {
          change_view();
          consultar();
        } else {
          alert("error, try again");
        }
      });
  });
    $('#list_works').on("click",".eliminar_registro",function(e){
        e.preventDefault();
        let confirmacion = confirm("Desea eliminar este registro?");
        if (confirmacion) {
          let id = $(this).data('id'),
          obj = {
            "accion":"eliminar_registro",
            "registro":id,
            "tabla":"works"
          };
          $.post("includes/_funciones.php",obj,function(respuesta){
            alert(respuesta);
            consultar();
          });
        }else{
          alert("El registro no se ha eliminado");
        }
      });
      $('#list_works').on("click",".editar_registro",function(e){
        e.preventDefault();
        $('#form_works')[0].reset();
        change_view('insert_data');
        let id = $(this).data('id');
        $("#enviar").text("Editar").data("editar",1).data("registro",id);
        console.log(id);
        obj = {
          "accion":"consultar_registro",
          "registro":id,
          "tabla":"works"
          // "nombre":nombre,
          // "correo":correo,
          // "telefono":telefono,
          // "password":password
        };
        $.post("includes/_funciones.php",obj,function(r){
          console.log(obj);
          console.log(r);
          $("#work").val(r.works_title);
          $("#description").val(r.works_subtitle);
          $("foto").val(r.works_file);
          $("#ruta").val(r.works_file);
        },"JSON");
      });
    $("#navh").on("click","#signout",function(){
    let obj = {
      "accion":"session_kill"
    }
    $.post("includes/_funciones.php",obj,function(xd){
      window.location.href = xd;
    });
  });
    $("#foto").on("change",function(e){
    let formDatos = new FormData($("#form_works")[0]);
    formDatos.append("accion","carga_foto");
    formDatos.append("carpeta","works");
    console.log(e);
    console.log(formDatos);
    $.ajax({
      url:"includes/_funciones.php",
      type:"POST",
      data: formDatos,
      contentType: false,
      processData: false,
      beforeSend: function(){
        let template = `<span>Subiendo, por favor espere...</span>`;
        $("#preview").html(template);
        console.log(formDatos);
      },
      success: function(datos){
        console.log(datos);
        let respuesta = JSON.parse(datos);
        console.log(JSON.parse(datos));
        console.log($.parseJSON(datos));
        if (respuesta.status == 0) {
          alert("No se cargó la imagen");
        } else {
          let template = `
            <img src="${respuesta.archivo}" class="img-fluid img-thumbnail" alt=""/>
          `;
          $("#ruta").val(respuesta.archivo);
          $("#preview").html(template);
        }
      },
      error: function(){
        let template = `<span>Error inesperado, intente nuevamente.</span>`;
        $("#preview").html(template);
      }
    });
  });
</script>
</body>
</html>
<?php
} else {
  header("location:index.html");
}
?>