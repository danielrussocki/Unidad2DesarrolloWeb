<?php
require_once '_db.php';
switch ($_POST["accion"]) {
	case "login":
		login();
		break;
	case "consultar_usuarios":
		consultar_usuarios();
		break;
	case "insertar_usuarios":
		insertar_usuarios();
		break;
	case "insertar_works":
		insertar_works();
		break;
	case "consultar_works":
		consultar_works();
		break;
	case 'editar_registro':
		editar_registro($_POST["registro"],$_POST["tabla"]);
		break;
	case "eliminar_registro":
		eliminar_registro($_POST["registro"],$_POST["tabla"]);
		break;
	case "consultar_registro":
		consultar_registro($_POST["registro"],$_POST["tabla"]);
		break;
	case "session_kill":
		kill();
		break;
	case "carga_foto":
		carga_foto($_POST["carpeta"]);
		break;
	default:
		# code...
		break;
}
function carga_foto($carpeta){
	if (isset($_FILES["foto"])) {
		$file = $_FILES["foto"];
		$nombre = $_FILES["foto"]["name"];
		$temporal = $_FILES["foto"]["tmp_name"];
		$tipo = $_FILES["foto"]["type"];
		$tam = $_FILES["foto"]["size"];
		$ruta = "../../img/".$carpeta."/";
		$respuesta = [
			"archivo" => "../img/".$carpeta."/logotipo.png",
			"status" => 0
		];
		if (move_uploaded_file($_FILES["foto"]["tmp_name"],$ruta.$nombre)) {
				$respuesta["archivo"] = "../img/".$carpeta."/".$nombre;
				$respuesta["status"] = 1;
			}
		echo json_encode($respuesta);
	}
}
function kill(){
	session_start();
	error_reporting(0);
	session_destroy();
	echo "index.html";
}
function login(){
	//echo "Tu usuario es: ".$_POST["usuario"]." y tu password es: ".$_POST["password"];
	//Conectar con la base de datos
	global $mysqli;
	$usu = $_POST["usuario"];
	$usu = mysqli_real_escape_string($mysqli,$usu);
	$pass = $_POST["password"];
	$pass = mysqli_real_escape_string($mysqli,$pass);
	$num = 0;
	//Si usuario y contraseña están vacíos que imprima 3
	if ($usu==''||$pass=='') {
		$num = 3;
	} else {
		$query = "SELECT * FROM usuarios WHERE correo_usr = '$usu'";
		$result = $mysqli->query($query);
		if ($result->num_rows == 0) {
			$num = 2;
		} else {
			$query2 = "SELECT * FROM usuarios WHERE correo_usr = '$usu' AND pswd_usr = '$pass'";
			$result2 = $mysqli->query($query2);
			if ($result2->num_rows > 0) {
				$num = 1;
				session_start();
				error_reporting(0);
				$_SESSION['access'] = $usu;
			} elseif ($result2->num_rows == 0) {
				$num = 0;
			}
		}
	}
	imprimir($num);
	//Consultar a la base de datos que el usuario exista
	//Si el usuario existe, consultar que el password sea correcto
	//Si el password es correcto, imprimir 1
	//Si el password no es correcto, imprimir 0
	//Si el usuario no existe, imprimir 2
}
function imprimir($n){
	switch ($n) {
		case 0:
			echo "Contraseña incorrecta - 0";
			break;
		case 1:
			echo "Acceso permitido - 1";
			break;
		case 2:
			echo "El usuario no existe - 2";
			break;
		case 3:
			echo "Favor de llenar los campos - 3";
			break;
		default:
			# code...
			break;
	}
}
function consultar_usuarios(){
	// extract($_POST); **PARA EXTRAER POST AUTOMÁTICAMENTE
	global $mysqli;
	$query = "SELECT * FROM usuarios";
	$resultado = mysqli_query($mysqli, $query);
	$arreglo = [];
	while($fila = mysqli_fetch_array($resultado)){
		array_push($arreglo, $fila);
	}
	echo json_encode($arreglo);//Imprime el Json encodeado
	//$result = $mysqli->query($query);
	//print_r($fila);
}
function editar_registro($id,$tabla){
	global $mysqli;
	extract($_POST);
	switch ($tabla) {
		case 'usuarios':
			$query = "UPDATE usuarios SET nombre_usr = '$nombre', correo_usr = '$correo', telefono_usr = '$telefono', pswd_usr = '$password' WHERE id_usr = $id";
			break;
		case 'works':
			$foto = $_POST['foto'];
			$ruta = "img/works/";
			$info = pathinfo($foto);
			$nombre_archivo = $ruta.$info['basename'];
			$query = "UPDATE works SET works_file = '$nombre_archivo', works_title = '$work', works_subtitle = '$description' WHERE works_id = $id";
			break;
		default:
			echo "error";
			break;
	}
	$resultado = mysqli_query($mysqli,$query);
	if ($resultado) {
		echo "1";
	} else {
		echo "error";
	}
}
function eliminar_registro($id,$tabla){
	global $mysqli;
	switch ($tabla) {
		case 'usuarios':
			$query = "DELETE FROM usuarios WHERE id_usr = $id";
			break;
		case 'works':
			$query = "DELETE FROM works WHERE works_id = $id";
			break;
		default:
			echo "error";
			break;
	}
	$resultado = mysqli_query($mysqli, $query);
	if ($resultado) {
		echo "Se eliminó correctamente";
	} else {
		echo "Se generó un error, intenta nuevamente";
	}
}
function consultar_registro($id,$tabla){
	global $mysqli;
	switch ($tabla) {
		case 'usuarios':
			$query_c = "SELECT * FROM usuarios WHERE id_usr = $id LIMIT 1";
			break;
		case 'works':
			$query_c = "SELECT * FROM works WHERE works_id = $id LIMIT 1";
			break;
		default:
			echo "error";
			break;
	}
	$resultado = mysqli_query($mysqli,$query_c);
	$fila = mysqli_fetch_array($resultado);
	echo json_encode($fila);
}
function consultar_works(){
	global $mysqli;
	$query = "SELECT * FROM works";
	$resultado = mysqli_query($mysqli, $query);
	$arreglo = [];
	while ($fila = mysqli_fetch_array($resultado)) {
		array_push($arreglo, $fila);
	}
	echo json_encode($arreglo);
}
function insertar_usuarios(){
	$nombre = $_POST['nombre'];
	$correo = $_POST['correo'];
	$telefono = $_POST['telefono'];
	$password = $_POST['password'];
	global $mysqli;
	if ($nombre!=''&&$correo!=''&&$telefono!=''&&$password!='') {
		$verif = "SELECT * FROM usuarios WHERE correo_usr = '$correo'";
		$resultado = $mysqli->query($verif);
		if ($resultado->num_rows == 0) {
			$query = "INSERT INTO usuarios VALUES('','$nombre','$correo','$password','$telefono','1')";
			$data = $mysqli->query($query);
			echo "1";
		} else{
			echo "0";
		}
	}
}
function insertar_works(){
	$work = $_POST['work'];
	$description = $_POST['description'];
	$foto = $_POST['foto'];
	$ruta = "img/works/";
	$info = pathinfo($foto);
	$nombre_archivo = $ruta.$info['basename'];
	global $mysqli;
	if ($work!=''&&$description!=''&&$foto!='') {
		$query = "INSERT INTO works VALUES('','$nombre_archivo','$work','$description')";
		$mysqli->query($query);
		echo "1";
	} else {
		echo "0";
	}
}
?>