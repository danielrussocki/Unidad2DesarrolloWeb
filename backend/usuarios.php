<?php
session_start();
error_reporting(0);
if (isset($_SESSION['access'])) {
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard Template · Bootstrap</title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Custom styles for this template -->
    <link href="css/estilos.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow" id="navh">
  
</nav>

<div class="container-fluid">
  <div class="row">
    <nav class="col-md-2 d-none d-md-block bg-light sidebar" id="navv">

    </nav>
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4" id="main">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group mr-2">
            <button type="button" class="btn btn-sm btn-outline-danger cancelar">Cancelar</button>
            <button type="button" class="btn btn-sm btn-outline-success" id="nuevo_registro">Nuevo</button>
          </div>
        </div>
      </div>

      <h2>Usuarios</h2>
      <div class="table-responsive view" id="show-data">
        <table class="table table-striped table-sm" id="list_usuarios">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            
          </tbody>
        </table>
      </div>
      <div id="insert_data" class="view">
        <form id="form_data" method="post" enctype="multipart/form-data">
            <div class="row">
              <div class="col">
          <div class="form-group">
            <label for="nombre">Nombre: </label>
            <input type="text" id="nombre" name="nombre" class="form-control">
          </div>
          <div class="form-group">
            <label for="correo">Correo: </label>
          <input type="email" id="correo" name="correo" class="form-control">
          </div>
            <div class="form-group">
            <input type="file" id="foto" name="foto">
            <input type="text" name="ruta" id="ruta" readonly="readonly">
            </div>
            <div class="preview" id="preview"></div>
              </div>
              <div class="col">
          <div class="form-group">
            <label for="telefono">Teléfono: </label>
          <input type="tel" id="telefono" name="telefono" class="form-control">
          </div>
          <div class="form-group">
            <label for="password">Password: </label>
          <input type="password" id="password" name="password" class="form-control">
          </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <button class="btn btn-success" type="button" id="guardar_datos">
                  Guardar
                </button>
              </div>
            </div>
        </form>
      </div>
    </main>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
  //Todas las vistas se ocultan
  //Pregunto qué vista se está mostrando
  //Si la vista que está no es la que quiero...
    //Pregunto cuál es la vista que se va a mostrar
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
      "accion":"consultar_usuarios"
    };
  $.post("includes/_funciones.php",obj,function(respuesta){
    let template = '';
    $.each(respuesta, function(i,e){
            template += `
            <tr>
              <td>${e.nombre_usr}</td>
              <td>${e.telefono_usr}</td>
              <td>
                <a href="#" data-id="${e.id_usr}" class="editar_registro">Editar</a>
                <a href="#" data-id="${e.id_usr}" class="eliminar_registro">Eliminar</a>
              </td>
            </tr>
            `;
      });
    $("#list_usuarios tbody").html(template);
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
  $('#guardar_datos').click(function(e){
    e.preventDefault();
    let obj = {
      "accion":"insertar_usuarios"
    };
    $('#form_data').find("input").each(function(){
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
      obj["tabla"] = "usuarios";
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
      $('#list_usuarios').on("click",".eliminar_registro",function(e){
        e.preventDefault();
        let confirmacion = confirm("Desea eliminar este registro?");
        if (confirmacion) {
          let id = $(this).data('id'),
          obj = {
            "accion":"eliminar_registro",
            "registro":id,
            "tabla":"usuarios"
          };
          $.post("includes/_funciones.php",obj,function(respuesta){
            alert(respuesta);
            consultar();
          });
        }else{
          alert("El registro no se ha eliminado");
        }
      });
      $('#list_usuarios').on("click",".editar_registro",function(e){
        e.preventDefault();
        $('#form_data')[0].reset();
        change_view('insert_data');
        let id = $(this).data('id');
        $("#guardar_datos").text("Editar").data("editar",1).data("registro",id);
        console.log(id);
        obj = {
          "accion":"consultar_registro",
          "registro":id,
          "tabla":"usuarios"
          // "nombre":nombre,
          // "correo":correo,
          // "telefono":telefono,
          // "password":password
        };
        $.post("includes/_funciones.php",obj,function(r){
          console.log(obj);
          console.log(r);
          $('#nombre').val(r.nombre_usr);
          $('#correo').val(r.correo_usr);
          $('#telefono').val(r.telefono_usr);
          $('#password').val(r.pswd_usr);
        },"JSON");
      });
  $('#main').find('.cancelar').click(function(){
    change_view();
    $('#form_data')[0].reset();
  });
  $(".nav-item").find("#works_link").click(function(){
    $("#main").html("");
  });
  $("#navh").on("click","#signout",function(){
    let obj = {
      "accion":"session_kill"
    };
    $.post("includes/_funciones.php",obj,function(xd){
      window.location.href = xd;
    });
  });
  $("#foto").on("change",function(e){
    let formDatos = new FormData($("#form_data")[0]);
    formDatos.append("accion","carga_foto");
    formDatos.append("carpeta","usuarios");
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
      },
      success: function(datos){
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