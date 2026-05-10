<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Anadir Factura</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Anadir nueva factura</h1>
    <p class="subtitulo">Rellena los datos de la factura. Los campos con * son obligatorios.</p>
    <hr>

    <form name="form_insertar" method="post" action="insertar.php" class="form-centrado">

        <div class="campo">
            <label for="fecha_factura">Fecha de la factura *</label>
            <input type="date" name="fecha_factura" id="fecha_factura" required>
        </div>

        <div class="campo">
            <label for="num_factura">Numero de factura *</label>
            <input type="text" name="num_factura" id="num_factura"
                   maxlength="20" placeholder="ej: FAC-2024-011" required>
        </div>

        <div class="campo">
            <label for="proveedor">Proveedor *</label>
            <input type="text" name="proveedor" id="proveedor"
                   maxlength="100" placeholder="Nombre de la empresa" required>
        </div>

        <div class="campo">
            <label for="concepto">Concepto</label>
            <input type="text" name="concepto" id="concepto"
                   maxlength="200" placeholder="Descripcion del servicio o producto">
        </div>

        <div class="campo">
            <label for="importe">Importe (&euro;)</label>
            <input type="number" name="importe" id="importe" min="0" step="0.01" placeholder="0.00">
            <small>Dejar en blanco si el importe aun no se conoce.</small>
        </div>

        <div class="campo">
            <label for="estado">Estado</label>
            <select name="estado" id="estado">
                <option value="Pendiente">Pendiente</option>
                <option value="Pagada">Pagada</option>
                <option value="Rechazada">Rechazada</option>
            </select>
        </div>

        <div class="form-acciones">
            <input type="submit" value="Guardar factura" class="boton boton-verde">
            <a href="index.php" class="boton boton-gris">Cancelar</a>
        </div>

    </form>

</div>
</body>
</html>
