<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Subir Factura PDF (IA) - Gestion de Facturas</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Subir factura en PDF (procesamiento con IA)</h1>
    <p class="subtitulo">
        Selecciona un PDF de una factura y la inteligencia artificial extraera
        automaticamente sus datos: fecha, numero, proveedor, concepto e importe.
    </p>
    <hr>

    <p class='ok'>
        El procesamiento utiliza el modelo <strong>gpt-4o-mini</strong> de OpenAI sobre
        el texto extraido del PDF. Solo funciona con facturas que contengan texto
        seleccionable (no imagenes escaneadas).
    </p>

    <form name="form_subir_pdf" method="post" action="procesar_pdf.php" enctype="multipart/form-data">

        <div class="campo">
            <label for="archivo_pdf">Archivo PDF de la factura *</label>
            <input type="file" name="archivo_pdf" id="archivo_pdf" accept="application/pdf" required>
            <small>Tamano maximo: 5 MB. Solo se admiten archivos PDF.</small>
        </div>

        <div class="campo">
            <label for="estado">Estado inicial</label>
            <select name="estado" id="estado">
                <option value="Pendiente">Pendiente</option>
                <option value="Pagada">Pagada</option>
                <option value="Rechazada">Rechazada</option>
            </select>
            <small>Estado que se asignara a la factura una vez procesada.</small>
        </div>

        <input type="submit" value="Procesar factura con IA" class="boton boton-verde">
        <a href="index.php" class="boton boton-gris">Cancelar</a>

    </form>

</div>
</body>
</html>
