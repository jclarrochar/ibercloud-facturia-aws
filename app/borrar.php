<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Resultado - Borrar Factura</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Resultado: borrar factura</h1>
    <hr>

<?php
/*
 * Fichero:     borrar.php
 * Descripcion: Recibe el id de la factura elegida en form_borrar.php por POST.
 *              Comprueba que existe antes de borrarla.
 *              Muestra la tabla ANTES y DESPUES del borrado para confirmar.
 * Autor:       Juan Carlos Larrocha
 * Fecha:       2025-26
 */

$id_factura = $_POST["id_factura"];

if (empty($id_factura) || !is_numeric($id_factura)) {
    echo "<p class='error'>No se ha recibido un identificador valido.</p>";
    echo "<a href='form_borrar.php' class='boton boton-rojo'>Volver</a>";
    exit();
}

include("conexion.php");
mysqli_select_db($conexion, "gestion_facturas");

$id = (int)$id_factura;

$buscar = mysqli_query($conexion, "SELECT num_factura, proveedor FROM facturas WHERE id_factura = $id");

if (!$buscar || mysqli_num_rows($buscar) == 0) {
    echo "<p class='error'>No se encontro ninguna factura con el ID <strong>" . $id . "</strong>.</p>";
    mysqli_close($conexion);
    echo "<a href='form_borrar.php' class='boton boton-rojo'>Volver</a>";
    exit();
}

$datos    = mysqli_fetch_row($buscar);
$num_txt  = $datos[0];
$prov_txt = $datos[1];

echo "<p class='seccion'>Estado ANTES del borrado:</p>";
include("_tabla_facturas.php");

$sql = "DELETE FROM facturas WHERE id_factura = $id";

if (mysqli_query($conexion, $sql)) {
    echo "<p class='ok'>La factura <strong>" . $num_txt . "</strong> de <strong>" . $prov_txt . "</strong> ha sido eliminada correctamente.</p>";
    echo "<p class='seccion'>Estado DESPUES del borrado:</p>";
    include("_tabla_facturas.php");
} else {
    echo "<p class='error'>Error al borrar: " . mysqli_error($conexion) . "</p>";
}

mysqli_close($conexion);
?>

    <br>
    <a href="form_borrar.php" class="boton boton-rojo">Borrar otra factura</a>
    <a href="index.php"       class="boton boton-gris">Volver al menu</a>

</div>
</body>
</html>
