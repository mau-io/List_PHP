<?php
//conexion a la base de datos
include("conexion.php");

/**
*Funcion para cambiar el status del contenido
*		@param int 1 enable, 0 disable
*/
function content_status($status){
	$pid= $_GET['pid'];
	mysql_query("UPDATE registro . parrafos SET status = $status WHERE pid = $pid") or die(mysql_error());
	header("Location: ../home.php");
}

function content_delete($status){
	$pid= $_GET['pid'];
	mysql_query("DELETE FROM registro . parrafos  WHERE  parrafos . pid  = $pid") or die(mysql_error());
	header("Location: ../home.php");
}

function set_message_error($error){

	echo"<script>
		  alert(" . "'" .$error ."'". ");
		  window.location.href=\"../login.html\"
	 </script>";

}

/**
*Funcion para consultar todo el contenido
*		@param string $departamento opcional
*		@return string
*/
function get_content($uid){
	$query = "SELECT texto,pid,fecha FROM parrafos WHERE uid = $uid ORDER BY pid DESC";
	return format_html($query) == "" ? "Publica un evento." : format_html($query);
}

/**
*Funcion que consulta en la base de datos los campos, fecha y texto
*		@param $uid int, $search string
*		@return string, el resultado de la busqueda formateado con html
*/
function get_search($uid,$search){
	$query = "SELECT texto,uid,pid,fecha FROM parrafos WHERE ( texto LIKE '%".$search."%' OR fecha LIKE '%".$search."%' ) AND uid = $uid ORDER BY pid DESC";
	return format_html($query) == "" ? "No tines eventos" : format_html($query);
}

/**
*Funcion para consultar y formatear el resultado
*		@param query a la base de datos
*		@return atring, devuelve la consulta de los eventos con html
*/
function format_html($query){

	$resultado= @mysql_query($query) or die(mysql_error());
	$output = "";

	while ($datos = @mysql_fetch_assoc($resultado) ){
		$pid 		= $datos['pid'];
		 $output .=  "<p>". $datos['texto'] . "</p>" .
						 "<p>". $datos['fecha'] . "</p><br>
						 <a href='core/delete.php?pid=$pid'>	Eliminar 	</a>
						 <a href='core/disable.php?pid=$pid'>	Desabilitar </a>
						 <a href='core/enable.php?pid=$pid'>	Habilitar 	</a>";
	}
	return $output;
}

function get_image($uid){

	$query = "SELECT imagen FROM imagenes WHERE uid = $uid";
	$resultado = @mysql_query($query) or die(mysql_error());
	$datos = mysql_fetch_assoc($resultado);
	$ruta = "images/" . $datos['imagen'];
	return $ruta;
}

function upload_image($uid){
	//comprobamos si ha ocurrido un error.
	if ($_FILES["imagen"]["error"] > 0){
	  echo "ha ocurrido un error";
	} else {
	  //ahora vamos a verificar si el tipo de archivo es un tipo de imagen permitido.
	  //y que el tamano del archivo no exceda los 100kb
	  $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png");
	  $limite_kb = 100;

	  if (in_array($_FILES['imagen']['type'], $permitidos) && $_FILES['imagen']['size'] <= $limite_kb * 1024){
		 //esta es la ruta donde copiaremos la imagen
		 //recuerden que deben crear un directorio con este mismo nombre
		 //en el mismo lugar donde se encuentra el archivo subir.php
		 $ruta = "images/" . $_FILES['imagen']['name'];
		 //comprobamos si este archivo existe para no volverlo a copiar.
		 //pero si quieren pueden obviar esto si no es necesario.
		 //o pueden darle otro nombre para que no sobreescriba el actual.
		 if (!file_exists($ruta)){
			//aqui movemos el archivo desde la ruta temporal a nuestra ruta
			//usamos la variable $resultado para almacenar el resultado del proceso de mover el archivo
			//almacenara true o false
			$resultado = @move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);
			if ($resultado){
			  $nombre = $_FILES['imagen']['name'];
			  @mysql_query("INSERT INTO imagenes (imagen) VALUES ('$nombre')") ;
			  echo "el archivo ha sido movido exitosamente";
			} else {
			  echo "ocurrio un error al mover el archivo.";
			}
		 } else {
			echo $_FILES['imagen']['name'] . ", este archivo existe";
		 }
	  } else {
		 echo "archivo no permitido, es tipo de archivo prohibido o excede el tamano de $limite_kb Kilobytes";
	  }
	}
}
?>