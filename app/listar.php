<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Listado de Facturas</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Listado de facturas</h1>
    <p class="subtitulo">Todos los registros de la tabla ordenados por fecha.</p>
    <hr>

<?php
/*
 * Fichero:     listar.php
 * Descripcion: Muestra todos los registros de la tabla facturas.
 *              Calcula el total de importes y un resumen por estado.
 * Autor:       Juan Carlos Larrocha
 * Fecha:       2025-26
 */

include("conexion.php");
mysqli_select_db($conexion, "gestion_facturas");

$sql = "SELECT id_factura, fecha_factura, num_factura, proveedor, concepto, importe, estado
        FROM facturas
        ORDER BY fecha_factura ASC";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    echo "<p class='error'>Error al consultar la BD: " . mysqli_error($conexion) . "</p>";

} elseif (mysqli_num_rows($resultado) == 0) {
    echo "<p>No hay facturas registradas.</p>";

} else {
    $total      = 0;
    $pendientes = 0;
    $rechazadas = 0;
    $filas      = [];

    while ($fila = mysqli_fetch_row($resultado)) {
        $filas[] = $fila;

        if ($fila[5] != null) {
            $total = $total + $fila[5];
        }

        if ($fila[6] == "Pendiente") {
            $pendientes = $pendientes + 1;
        }

        if ($fila[6] == "Rechazada") {
            $rechazadas = $rechazadas + 1;
        }
    }

    // Tarjetas de resumen
    echo "<div class='resumen'>";

    echo "<div class='resumen-tarjeta' style='background:#eff6ff;border-color:#bfdbfe'>
            <div class='numero' style='color:#1d4ed8'>" . count($filas) . "</div>
            <div class='etiqueta' style='color:#3b82f6'>Total facturas</div>
          </div>";

    echo "<div class='resumen-tarjeta' style='background:#ecfdf5;border-color:#a7f3d0'>
            <div class='numero' style='color:#065f46'>" . number_format($total, 2, ',', '.') . " &euro;</div>
            <div class='etiqueta' style='color:#10b981'>Importe total</div>
          </div>";

    echo "<div class='resumen-tarjeta' style='background:#fef3c7;border-color:#fcd34d'>
            <div class='numero' style='color:#92400e'>" . $pendientes . "</div>
            <div class='etiqueta' style='color:#d97706'>Pendientes</div>
          </div>";

    echo "<div class='resumen-tarjeta' style='background:#fee2e2;border-color:#fca5a5'>
            <div class='numero' style='color:#991b1b'>" . $rechazadas . "</div>
            <div class='etiqueta' style='color:#dc2626'>Rechazadas</div>
          </div>";

    echo "</div>";

    // Tabla de facturas
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

    foreach ($filas as $f) {
        if ($f[5] != null) {
            $importe_txt = number_format($f[5], 2, ',', '.') . " &euro;";
        } else {
            $importe_txt = "&mdash;";
        }

        $estado_css = strtolower($f[6]);

        echo "<tr>";
        echo "<td>" . $f[0] . "</td>";
        echo "<td>" . $f[1] . "</td>";
        echo "<td>" . $f[2] . "</td>";
        echo "<td>" . $f[3] . "</td>";
        echo "<td>" . $f[4] . "</td>";
        echo "<td class='num'>" . $importe_txt . "</td>";
        echo "<td><span class='badge badge-" . $estado_css . "'>" . $f[6] . "</span></td>";
        echo "</tr>";
    }

    echo "<tfoot>";
    echo "<tr>";
    echo "<td colspan='5'><strong>TOTAL CON IMPORTE REGISTRADO</strong></td>";
    echo "<td class='num'><strong>" . number_format($total, 2, ',', '.') . " &euro;</strong></td>";
    echo "<td></td>";
    echo "</tr>";
    echo "</tfoot>";

    echo "</table>";
}

mysqli_close($conexion);
?>

    <br>
    <a href="index.php" class="boton boton-gris">Volver al menu</a>

</div>
</body>
</html>
