<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Resultado - Buscar Factura</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Resultado de la busqueda</h1>
    <hr>

<?php
/*
 * Fichero:     buscar.php
 * Descripcion: Recibe el texto del formulario form_buscar.php por GET.
 *              Busca coincidencias parciales con LIKE en los campos
 *              num_factura, proveedor y concepto.
 * Autor:       Juan Carlos Larrocha
 * Fecha:       2025-26
 */

$texto = $_GET["texto"];

if (empty(trim($texto))) {
    echo "<p class='error'>Debes introducir algun texto para buscar.</p>";
    echo "<a href='form_buscar.php' class='boton'>Volver al formulario</a>";

} else {
    include("conexion.php");
    mysqli_select_db($conexion, "gestion_facturas");

    $texto_esc = mysqli_real_escape_string($conexion, trim($texto));

    $sql = "SELECT id_factura, fecha_factura, num_factura, proveedor, concepto, importe, estado
            FROM facturas
            WHERE num_factura LIKE '%$texto_esc%'
               OR proveedor   LIKE '%$texto_esc%'
               OR concepto    LIKE '%$texto_esc%'
            ORDER BY fecha_factura ASC";

    $resultado = mysqli_query($conexion, $sql);

    if (!$resultado) {
        echo "<p class='error'>Error en la consulta: " . mysqli_error($conexion) . "</p>";

    } elseif (mysqli_num_rows($resultado) == 0) {
        echo "<p class='error'>No se encontro ninguna factura con el texto <strong>\"" . $texto_esc . "\"</strong>.</p>";

    } else {
        $num = mysqli_num_rows($resultado);
        echo "<p class='ok'>Se encontraron <strong>" . $num . "</strong> resultado(s) para \"" . $texto_esc . "\".</p>";

        echo "<table>";
        echo "<tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Num. Factura</th>
                <th>Proveedor</th>
                <th>Concepto</th>
                <th>Importe</th>
                <th>Estado</th>
              </tr>";

        while ($fila = mysqli_fetch_row($resultado)) {
            if ($fila[5] != null) {
                $importe_txt = number_format($fila[5], 2, ',', '.') . " &euro;";
            } else {
                $importe_txt = "&mdash;";
            }

            $estado_css = strtolower($fila[6]);

            echo "<tr>";
            echo "<td>" . $fila[0] . "</td>";
            echo "<td>" . $fila[1] . "</td>";
            echo "<td>" . $fila[2] . "</td>";
            echo "<td>" . $fila[3] . "</td>";
            echo "<td>" . $fila[4] . "</td>";
            echo "<td class='num'>" . $importe_txt . "</td>";
            echo "<td><span class='badge badge-" . $estado_css . "'>" . $fila[6] . "</span></td>";
            echo "</tr>";
        }

        echo "</table>";
    }

    mysqli_close($conexion);
}
?>

    <br>
    <a href="form_buscar.php" class="boton">Nueva busqueda</a>
    <a href="index.php"       class="boton boton-gris">Volver al menu</a>

</div>
</body>
</html>
