<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Resultado - Modificar Factura</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Resultado: modificar factura</h1>
    <hr>

<?php
/*
 * Fichero:     actualizar.php
 * Descripcion: Recibe los datos de form_actualizar.php por POST.
 *              Construye el UPDATE solo con los campos que se han rellenado.
 *              Muestra la tabla antes y despues para confirmar el cambio.
 * Autor:       Juan Carlos Larrocha
 * Fecha:       2025-26
 */

$id_factura      = $_POST["id_factura"];
$nueva_fecha     = $_POST["nueva_fecha"];
$nuevo_num       = $_POST["nuevo_num"];
$nuevo_proveedor = $_POST["nuevo_proveedor"];
$nuevo_concepto  = $_POST["nuevo_concepto"];
$nuevo_importe   = $_POST["nuevo_importe"];
$nuevo_estado    = $_POST["nuevo_estado"];

if (empty($id_factura) || !is_numeric($id_factura)) {
    echo "<p class='error'>No se ha seleccionado una factura valida.</p>";
    echo "<a href='form_actualizar.php' class='boton'>Volver</a>";
    exit();
}

include("conexion.php");
mysqli_select_db($conexion, "gestion_facturas");

$id = (int)$id_factura;

$campos = [];

if (!empty(trim($nueva_fecha))) {
    $v = mysqli_real_escape_string($conexion, trim($nueva_fecha));
    $campos[] = "fecha_factura = '$v'";
}

if (!empty(trim($nuevo_num))) {
    $v = mysqli_real_escape_string($conexion, trim($nuevo_num));
    $campos[] = "num_factura = '$v'";
}

if (!empty(trim($nuevo_proveedor))) {
    $v = mysqli_real_escape_string($conexion, trim($nuevo_proveedor));
    $campos[] = "proveedor = '$v'";
}

if (!empty(trim($nuevo_concepto))) {
    $v = mysqli_real_escape_string($conexion, trim($nuevo_concepto));
    $campos[] = "concepto = '$v'";
}

if ($nuevo_importe != "" && is_numeric($nuevo_importe) && $nuevo_importe >= 0) {
    $campos[] = "importe = " . (float)$nuevo_importe;
}

if (!empty($nuevo_estado)) {
    $v = mysqli_real_escape_string($conexion, $nuevo_estado);
    $campos[] = "estado = '$v'";
}

if (count($campos) == 0) {
    echo "<p class='error'>No has introducido ningun dato nuevo. No se ha realizado ningun cambio.</p>";
    echo "<a href='form_actualizar.php' class='boton'>Volver</a>";
    mysqli_close($conexion);
    exit();
}

echo "<p class='seccion'>Estado ANTES de la modificacion:</p>";
include("_tabla_facturas.php");

$set = implode(", ", $campos);
$sql = "UPDATE facturas SET $set WHERE id_factura = $id";

if (mysqli_query($conexion, $sql)) {
    echo "<p class='ok'>La factura ha sido modificada correctamente.</p>";
    echo "<p class='seccion'>Estado DESPUES de la modificacion:</p>";
    include("_tabla_facturas.php");
} else {
    echo "<p class='error'>Error al actualizar: " . mysqli_error($conexion) . "</p>";
}

mysqli_close($conexion);
?>

    <br>
    <a href="form_actualizar.php" class="boton boton-verde">Modificar otra factura</a>
    <a href="index.php"           class="boton boton-gris">Volver al menu</a>

</div>
</body>
</html>
