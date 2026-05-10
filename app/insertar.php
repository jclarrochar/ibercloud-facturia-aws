<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Resultado - Anadir Factura</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Resultado: anadir factura</h1>
    <hr>

<?php
/*
 * Fichero:     insertar.php
 * Descripcion: Recibe los datos del formulario form_insertar.php por POST.
 *              Valida los datos antes de insertar en la BD.
 *              Si son correctos ejecuta INSERT y muestra la tabla actualizada.
 * Autor:       Juan Carlos Larrocha
 * Fecha:       2025-26
 */

$fecha       = $_POST["fecha_factura"];
$num_factura = $_POST["num_factura"];
$proveedor   = $_POST["proveedor"];
$concepto    = $_POST["concepto"];
$importe     = $_POST["importe"];
$estado      = $_POST["estado"];

// Validacion de los datos de entrada
$errores = [];

if (empty(trim($fecha))) {
    $errores[] = "La fecha es obligatoria.";
}

if (empty(trim($num_factura))) {
    $errores[] = "El numero de factura es obligatorio.";
}

if (empty(trim($proveedor))) {
    $errores[] = "El nombre del proveedor es obligatorio.";
}

if (!empty($importe) && !is_numeric($importe)) {
    $errores[] = "El importe debe ser un numero.";
}

if (!empty($importe) && $importe < 0) {
    $errores[] = "El importe no puede ser negativo.";
}

if (count($errores) > 0) {
    echo "<div class='error'><strong>Corrige los siguientes errores:</strong><ul>";
    foreach ($errores as $err) {
        echo "<li>" . $err . "</li>";
    }
    echo "</ul></div>";
    echo "<a href='form_insertar.php' class='boton'>Volver al formulario</a>";

} else {
    include("conexion.php");
    mysqli_select_db($conexion, "gestion_facturas");

    $fecha_esc  = mysqli_real_escape_string($conexion, trim($fecha));
    $num_esc    = mysqli_real_escape_string($conexion, trim($num_factura));
    $prov_esc   = mysqli_real_escape_string($conexion, trim($proveedor));
    $conc_esc   = mysqli_real_escape_string($conexion, trim($concepto));
    $estado_esc = mysqli_real_escape_string($conexion, $estado);

    if (empty($importe)) {
        $imp_val = "NULL";
    } else {
        $imp_val = (float)$importe;
    }

    $sql = "INSERT INTO facturas (fecha_factura, num_factura, proveedor, concepto, importe, estado)
            VALUES ('$fecha_esc', '$num_esc', '$prov_esc', '$conc_esc', $imp_val, '$estado_esc')";

    if (mysqli_query($conexion, $sql)) {
        echo "<p class='ok'>La factura de <strong>" . $prov_esc . "</strong> ha sido anadida correctamente.</p>";
        echo "<p class='seccion'>Estado actual de la tabla:</p>";
        include("_tabla_facturas.php");
    } else {
        echo "<p class='error'>Error al insertar: " . mysqli_error($conexion) . "</p>";
    }

    mysqli_close($conexion);
}
?>

    <br>
    <a href="form_insertar.php" class="boton boton-verde">Anadir otra factura</a>
    <a href="index.php"         class="boton boton-gris">Volver al menu</a>

</div>
</body>
</html>
