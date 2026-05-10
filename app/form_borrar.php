<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Borrar Factura</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Borrar factura</h1>
    <p class="subtitulo">Esta accion eliminara el registro de forma permanente.</p>
    <hr>

    <form name="form_borrar" method="post" action="borrar.php" class="form-centrado">

        <div class="campo">
            <label for="id_factura">Selecciona la factura a eliminar</label>

            <?php
            include("conexion.php");
            mysqli_select_db($conexion, "gestion_facturas");

            $facturas = mysqli_query($conexion,
                "SELECT id_factura, num_factura, proveedor FROM facturas ORDER BY fecha_factura ASC");

            if (!$facturas || mysqli_num_rows($facturas) == 0) {
                echo "<p class='error'>No hay facturas en la base de datos.</p>";
                echo "<a href='index.php' class='boton boton-gris'>Volver</a>";
                mysqli_close($conexion);
                exit();
            }

            echo "<select name='id_factura' id='id_factura'>";
            while ($f = mysqli_fetch_row($facturas)) {
                echo "<option value='" . $f[0] . "'>" . $f[1] . " - " . $f[2] . "</option>";
            }
            echo "</select>";

            mysqli_close($conexion);
            ?>

            <small>Se mostrara la tabla antes y despues para confirmar el borrado.</small>
        </div>

        <div class="form-acciones">
            <input type="submit" value="Eliminar factura" class="boton boton-rojo"
                   onclick="return confirm('Seguro que quieres eliminar esta factura? Esta accion no se puede deshacer.')">
            <a href="index.php" class="boton boton-gris">Cancelar</a>
        </div>

    </form>

</div>
</body>
</html>
