<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Modificar Factura</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Modificar factura</h1>
    <p class="subtitulo">Elige la factura a modificar y rellena solo los campos que quieras cambiar.</p>
    <hr>

    <form name="form_actualizar" method="post" action="actualizar.php" class="form-centrado">

        <div class="campo">
            <label for="id_factura">Factura a modificar *</label>

            <?php
            include("conexion.php");
            mysqli_select_db($conexion, "gestion_facturas");

            $facturas = mysqli_query($conexion,
                "SELECT id_factura, num_factura, proveedor FROM facturas ORDER BY fecha_factura ASC");

            if (!$facturas || mysqli_num_rows($facturas) == 0) {
                echo "<p class='error'>No hay facturas en la BD para modificar.</p>";
                echo "<a href='index.php' class='boton boton-gris'>Volver</a>";
                mysqli_close($conexion);
                exit();
            }

            echo "<select name='id_factura' id='id_factura'>";
            while ($f = mysqli_fetch_row($facturas)) {
                echo "<option value='" . $f[0] . "'>" . $f[1] . " - " . $f[2] . "</option>";
            }
            echo "</select>";
            ?>

            <small>Selecciona la factura que quieres modificar.</small>
        </div>

        <div class="campo">
            <label for="nueva_fecha">Nueva fecha</label>
            <input type="date" name="nueva_fecha" id="nueva_fecha">
        </div>

        <div class="campo">
            <label for="nuevo_num">Nuevo numero de factura</label>
            <input type="text" name="nuevo_num" id="nuevo_num" maxlength="20">
        </div>

        <div class="campo">
            <label for="nuevo_proveedor">Nuevo proveedor</label>
            <input type="text" name="nuevo_proveedor" id="nuevo_proveedor" maxlength="100">
        </div>

        <div class="campo">
            <label for="nuevo_concepto">Nuevo concepto</label>
            <input type="text" name="nuevo_concepto" id="nuevo_concepto" maxlength="200">
        </div>

        <div class="campo">
            <label for="nuevo_importe">Nuevo importe (&euro;)</label>
            <input type="number" name="nuevo_importe" id="nuevo_importe" min="0" step="0.01">
        </div>

        <div class="campo">
            <label for="nuevo_estado">Nuevo estado</label>
            <select name="nuevo_estado" id="nuevo_estado">
                <option value="">-- Sin cambiar --</option>
                <?php
                echo "<option value='Pendiente'>Pendiente</option>";
                echo "<option value='Pagada'>Pagada</option>";
                echo "<option value='Rechazada'>Rechazada</option>";
                mysqli_close($conexion);
                ?>
            </select>
        </div>

        <p class="nota">Solo se actualizaran los campos que hayas rellenado.</p>

        <input type="submit" value="Guardar cambios" class="boton boton-verde">
        <a href="index.php" class="boton boton-gris">Cancelar</a>

    </form>

</div>
</body>
</html>
