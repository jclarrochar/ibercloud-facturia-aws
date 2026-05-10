<?php
/*
 * Fichero:     conexion.php
 * Descripcion: Parametros de conexion a MySQL. Se incluye en todos
 *              los ficheros con include() para no repetir el codigo.
 * Autor:       Juan Carlos Larrocha
 * Fecha:       2025-26
 */

$host     = "localhost:3307";
$usuario  = "root";
$password = "";

$conexion = mysqli_connect($host, $usuario, $password)
    or die("<p class='error'>Error al conectar con MySQL: " . mysqli_connect_error() . "</p>");
?>
