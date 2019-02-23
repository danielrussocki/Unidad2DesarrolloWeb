<?php
require_once '_db.php';
switch ($_POST["accion"]) {
	case "login":
		login();
		break;
	case "consultar_usuarios":
		consultar_usuarios();
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
?>