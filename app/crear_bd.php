<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Crear BD - Gestion de Facturas</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Inicializacion de la Base de Datos</h1>
    <p class="subtitulo">Crea la BD, la tabla y carga los datos de ejemplo.</p>
    <hr>

<?php
/*
 * Fichero:     crear_bd.php
 * Descripcion: Crea la base de datos gestion_facturas y la tabla facturas.
 *              Inserta 10 registros de ejemplo si la tabla esta vacia.
 *              Solo es necesario ejecutarlo una vez al inicio.
 * Autor:       Juan Carlos Larrocha
 * Fecha:       2025-26
 */

include("conexion.php");

// 1. Crear la base de datos si no existe
$sql_bd = "CREATE DATABASE IF NOT EXISTS gestion_facturas CHARACTER SET utf8mb4";

if (mysqli_query($conexion, $sql_bd)) {
    echo "<p class='ok'>Base de datos <strong>gestion_facturas</strong> creada correctamente.</p>";
} else {
    echo "<p class='error'>Error al crear la BD: " . mysqli_error($conexion) . "</p>";
}

// Seleccionamos la BD antes de crear la tabla
mysqli_select_db($conexion, "gestion_facturas");

// 2. Crear la tabla facturas
$sql_tabla = "CREATE TABLE IF NOT EXISTS facturas (
    id_factura    INT           AUTO_INCREMENT PRIMARY KEY,
    fecha_factura DATE          NOT NULL,
    num_factura   VARCHAR(20)   NOT NULL,
    proveedor     VARCHAR(100)  NOT NULL,
    concepto      VARCHAR(200),
    importe       DECIMAL(10,2),
    estado        VARCHAR(20)   DEFAULT 'Pendiente'
)";

if (mysqli_query($conexion, $sql_tabla)) {
    echo "<p class='ok'>Tabla <strong>facturas</strong> creada correctamente.</p>";
} else {
    echo "<p class='error'>Error al crear la tabla: " . mysqli_error($conexion) . "</p>";
}

// 3. Comprobar si la tabla esta vacia antes de insertar datos
$comprueba = mysqli_query($conexion, "SELECT COUNT(*) FROM facturas");
$fila = mysqli_fetch_row($comprueba);

if ($fila[0] == 0) {
    $sql_datos = "INSERT INTO facturas (fecha_factura, num_factura, proveedor, concepto, importe, estado) VALUES
        ('2024-03-15','FAC-2024-001','TechSolutions SL',               'Mantenimiento servidores Q1 2024',      1250.00, 'Pagada'),
        ('2024-04-22','FAC-2024-002','DataCloud Consulting SL',        'Arquitectura cloud AWS',                8349.00, 'Pagada'),
        ('2024-05-10','FAC-2024-003','EcoTech Soluciones Verdes SL',   'Auditoria energetica instalaciones',    7441.50, 'Pagada'),
        ('2024-06-30','FAC-2024-004','LuxTech Digital SL',             'Diseno UI/UX aplicacion movil',        19360.00, 'Pendiente'),
        ('2024-07-05','FAC-2024-005','Lopez y Asociados Contables',    'Contabilidad mensual julio 2024',         350.00, 'Pagada'),
        ('2024-08-18','FAC-2024-006','MediaPro Studio SL',             'Produccion video corporativo',         10206.35, 'Pagada'),
        ('2024-09-25','FAC-2024-007','DigitalBoost Agency',            'Campana Social Media septiembre',       3200.00, 'Pendiente'),
        ('2024-10-12','FAC-2024-008','Consulting Partners SL',         'Consultoria estrategica direccion',     5500.00, 'Rechazada'),
        ('2024-11-08','FAC-2024-009','Nexus Tech Solutions SL',        'Desarrollo API REST (Node.js)',          4500.00, 'Pagada'),
        ('2024-11-20','FAC-2024-010','Iberia Enterprise Solutions SA', 'Licencias software empresarial anual', 12800.00, 'Pendiente')";

    if (mysqli_query($conexion, $sql_datos)) {
        echo "<p class='ok'>10 registros de ejemplo insertados correctamente.</p>";
    } else {
        echo "<p class='error'>Error al insertar datos: " . mysqli_error($conexion) . "</p>";
    }
} else {
    echo "<p class='ok'>La tabla ya contenia " . $fila[0] . " registros. No se han anadido duplicados.</p>";
}

mysqli_close($conexion);
?>

    <br>
    <a href="listar.php" class="boton boton-verde">Ver las facturas</a>
    <a href="index.php"  class="boton boton-gris">Volver al menu</a>

</div>
</body>
</html>
