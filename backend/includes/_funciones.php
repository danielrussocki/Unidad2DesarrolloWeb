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
	default:
		# code...
		break;
}
function login(){
	//echo "Tu usuario es: ".$_POST["usuario"]." y tu password es: ".$_POST["password"];
	//Conectar con la base de datos
	global $mysqli;
	$usu = $_POST["usuario"];
	$pass = $_POST["password"];
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
			$query = "INSERT INTO usuarios VALUES('','$nombre','$correo','$telefono','$password','1')";
			$data = $mysqli->query($query);
			echo "Usuario agregado correctamente";
		} else{
			echo "correo ya existente";
		}
	}
}
function insertar_works(){
	$work = $_POST['work'];
	$description = $_POST['description'];
	$image = utf8_encode($_POST['image']);
	global $mysqli;
	if ($work!=''&&$description!=''&&$image!='') {

// if (is_uploaded_file($_FILES['image']['tmp_name'])) { 
// //revisar que sea jpg 
// if ($_FILES['image']['type'] == "image/jpeg" || $_FILES['image']['type'] == "image/pjpeg"){ 
// //nuevo nombre para la image 
// $nuevoNombre = time().".jpg"; 
// //mover la image 
// move_uploaded_file($_FILES['image']['tmp_name'], "../../img_productos/webcams/$nuevoNombre"); 
// //obtener la inforamción 
// $data = GetImageSize("../../img_productos/webcams/$nuevoNombre"); 

// // Inserto el nombre dentro de la Base de datos 
// /*=======================================*/ 
// mysql_query("INSERT INTO tu_tabla (id, ruta) VALUES ('1',$nuevoNombre)  "); 
// /*===============================================*/ 
// //mensaje de éxito 
// echo "<img src='../../img_productos/webcams/$nuevoNombre' $data[3]> <br> imagen $nuevoNombre subida con éxito"; 
// }else{ 
// echo "Formato no válido para fichero de imagen"; 
// } 
// } else { 
// echo "Error al cargar imagen: " . $_FILES['image']['name']; 
// } 

		$query = "INSERT INTO works VALUES('','$image','$work','$description')";
		$mysqli->query($query);
	}
}
?>