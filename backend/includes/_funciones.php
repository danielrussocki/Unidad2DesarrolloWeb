<?php
require_once("_db.php");
switch ($_POST["accion"]) {
	case 'login':
		login();
		break;
	
	default:
		# code...
		break;
}
function login(){
	echo "Tu usuario es: ".$_POST["usuario"]." y tu password es: ".$_POST["password"];
	//Conectar con la base de datos
	//Si usuario y contraseña están vacíos que imprima 3
	//Consultar a la base de datos que el usuario exista
	//Si el usuario existe, consultar que el password sea correcto
	//Si el password es correcto, imprimir 1
	//Si el password no es correcto, imprimir 0
	//Si el usuario no existe, imprimir 0
}
?>