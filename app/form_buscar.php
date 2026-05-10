<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Buscar Factura</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Buscar factura</h1>
    <p class="subtitulo">Introduce el texto que quieres buscar. Se buscara en el numero de factura, el proveedor y el concepto.</p>
    <hr>

    <form name="form_buscar" method="get" action="buscar.php" class="form-centrado">

        <div class="campo">
            <label for="texto">Texto a buscar *</label>
            <input type="text" name="texto" id="texto"
                   placeholder="ej: TechSolutions, cloud, auditoria..." required>
        </div>

        <div class="form-acciones">
            <input type="submit" value="Buscar" class="boton">
            <a href="index.php" class="boton boton-gris">Volver</a>
        </div>

    </form>

</div>
</body>
</html>
