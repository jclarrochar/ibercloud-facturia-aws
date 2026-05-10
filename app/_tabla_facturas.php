<?php
/*
 * Fichero:     _tabla_facturas.php
 * Descripcion: Fragmento reutilizable que muestra la tabla de facturas.
 *              Se incluye desde insertar.php, actualizar.php y borrar.php
 *              para mostrar el estado antes y despues de cada operacion.
 *              Requiere $conexion activo y BD gestion_facturas seleccionada.
 * Autor:       Juan Carlos Larrocha
 * Fecha:       2025-26
 */

$sql_lista = "SELECT id_factura, fecha_factura, num_factura, proveedor, concepto, importe, estado
              FROM facturas
              ORDER BY fecha_factura ASC";

$res = mysqli_query($conexion, $sql_lista);

if (!$res || mysqli_num_rows($res) == 0) {
    echo "<p class='nota'>La tabla no contiene registros.</p>";
} else {
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

    while ($f = mysqli_fetch_row($res)) {
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

    echo "</table>";
}
?>
