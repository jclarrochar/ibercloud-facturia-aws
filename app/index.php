<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Gestion de Facturas</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Menu principal</h1>
    <p class="subtitulo">Selecciona la operacion que deseas realizar.</p>
    <hr>

    <table class="menu-tabla">
        <tr>
            <td class="destacado">
                <a href="form_subir_pdf.php">Subir factura PDF (IA)</a>
                <small>Sube un PDF y la IA extrae los datos automaticamente</small>
            </td>
            <td>
                <a href="listar.php">Ver todas las facturas</a>
                <small>Muestra el listado completo con totales</small>
            </td>
            <td>
                <a href="form_insertar.php">Anadir factura</a>
                <small>Registra una nueva factura manualmente</small>
            </td>
        </tr>
        <tr>
            <td>
                <a href="form_buscar.php">Buscar factura</a>
                <small>Busca por numero, proveedor o concepto</small>
            </td>
            <td>
                <a href="form_actualizar.php">Modificar factura</a>
                <small>Actualiza los datos de una factura existente</small>
            </td>
            <td class="peligro">
                <a href="form_borrar.php">Borrar factura</a>
                <small>Elimina una factura de forma permanente</small>
            </td>
        </tr>
    </table>

</div>
</body>
</html>
