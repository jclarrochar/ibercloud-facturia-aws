<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Juan Carlos Larrocha">
    <title>Resultado - Procesar factura PDF</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">
    <h1>Resultado: procesar factura PDF con IA</h1>
    <hr>

<?php
/*
 * Fichero:     procesar_pdf.php
 * Descripcion: Recibe un PDF subido por el formulario form_subir_pdf.php, extrae
 *              su texto con la utilidad pdftotext, lo envia a OpenAI para que lo
 *              estructure, y guarda el resultado en la tabla facturas.
 * Autor:       Juan Carlos Larrocha
 * Fecha:       2025-26
 */

include("config.php");
include("conexion.php");
mysqli_select_db($conexion, "gestion_facturas");

// =====================================================================
// 1. VALIDACION DEL ARCHIVO SUBIDO
// =====================================================================

$errores = [];

if (!isset($_FILES["archivo_pdf"]) || $_FILES["archivo_pdf"]["error"] != UPLOAD_ERR_OK) {
    $errores[] = "No se ha subido ningun archivo o ha habido un error en la subida.";

} else {
    $archivo    = $_FILES["archivo_pdf"];
    $tipo_mime  = mime_content_type($archivo["tmp_name"]);
    $tamanio_mb = $archivo["size"] / 1024 / 1024;

    if ($tipo_mime != "application/pdf") {
        $errores[] = "El archivo subido no es un PDF valido (tipo detectado: " . $tipo_mime . ").";
    }

    if ($tamanio_mb > 5) {
        $errores[] = "El archivo es demasiado grande (" . round($tamanio_mb, 2) . " MB). Maximo 5 MB.";
    }
}

$estado = isset($_POST["estado"]) ? $_POST["estado"] : "Pendiente";

if (count($errores) > 0) {
    echo "<div class='error'><strong>Errores en la subida del archivo:</strong><ul>";
    foreach ($errores as $err) {
        echo "<li>" . $err . "</li>";
    }
    echo "</ul></div>";
    echo "<a href='form_subir_pdf.php' class='boton'>Volver al formulario</a>";
    echo "</div></body></html>";
    exit;
}

// =====================================================================
// 2. EXTRACCION DE TEXTO DEL PDF CON pdftotext
// =====================================================================

$ruta_pdf = $archivo["tmp_name"];

// Detectar pdftotext: primero buscar en PATH, si no usar ruta absoluta de Windows como fallback.
// En Linux/cloud: usa el comando "pdftotext" del PATH (instalado con poppler-utils).
// En Windows: si Apache no ve el PATH del sistema, usa la ruta absoluta del ejecutable.
$pdftotext_cmd = "pdftotext";

if (stripos(PHP_OS, "WIN") === 0) {
    $ruta_windows = "C:\\xpdf-tools\\bin64\\pdftotext.exe";
    if (file_exists($ruta_windows)) {
        $pdftotext_cmd = '"' . $ruta_windows . '"';
    }
}

// Comprobacion final: ejecutar la version para verificar que el comando funciona
$test = shell_exec($pdftotext_cmd . " -v 2>&1");
if (empty($test) || stripos($test, "pdftotext") === false) {
    echo "<p class='error'>
            La utilidad <strong>pdftotext</strong> no esta disponible para Apache.<br>
            En Linux: <code>sudo apt install poppler-utils</code><br>
            En XAMPP/Windows: verifica que existe el ejecutable en
            <code>C:\\xpdf-tools\\bin64\\pdftotext.exe</code> o ajusta la ruta en este script.
          </p>";
    echo "<a href='form_subir_pdf.php' class='boton'>Volver al formulario</a>";
    echo "</div></body></html>";
    exit;
}

// Ejecutar pdftotext con argumentos escapados para seguridad.
// El flag -enc UTF-8 fuerza la salida en UTF-8 desde el principio.
$ruta_segura = escapeshellarg($ruta_pdf);
$texto_pdf = shell_exec($pdftotext_cmd . " -layout -enc UTF-8 " . $ruta_segura . " - 2>&1");

if (empty(trim($texto_pdf))) {
    echo "<p class='error'>
            No se ha podido extraer texto del PDF. Posibles causas:<br>
            - El PDF contiene solo imagenes escaneadas (no texto seleccionable).<br>
            - El PDF esta protegido o danado.<br>
            En esos casos, se requeriria un modelo de vision por computador (gpt-4o), no incluido en esta version.
          </p>";
    echo "<a href='form_subir_pdf.php' class='boton'>Volver al formulario</a>";
    echo "</div></body></html>";
    exit;
}

// Sanear el texto: forzar UTF-8 valido y eliminar caracteres de control
// que pueden romper el JSON que se envia a OpenAI.
$texto_pdf = mb_convert_encoding($texto_pdf, "UTF-8", "UTF-8");
$texto_pdf = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $texto_pdf);

echo "<p class='ok'>Texto extraido correctamente del PDF (" . strlen($texto_pdf) . " caracteres).</p>";

// =====================================================================
// 3. LLAMADA A LA API DE OPENAI
// =====================================================================

if (empty($openai_api_key) || $openai_api_key == "PEGA_AQUI_TU_API_KEY_DE_OPENAI") {
    echo "<p class='error'>
            La API key de OpenAI no esta configurada. Edita el fichero <code>config.php</code>
            o define la variable de entorno <code>OPENAI_API_KEY</code>.
          </p>";
    echo "<a href='index.php' class='boton'>Volver al menu</a>";
    echo "</div></body></html>";
    exit;
}

// Construir el cuerpo de la peticion al endpoint /v1/chat/completions
$cuerpo = [
    "model" => $openai_modelo,
    "messages" => [
        [
            "role" => "system",
            "content" => "Eres un experto en extraccion de datos de facturas. Devuelve solo el CSV pedido sin explicaciones ni comentarios."
        ],
        [
            "role" => "user",
            "content" => $openai_prompt . "\n\nEste es el texto a parsear:\n" . $texto_pdf
        ]
    ],
    "temperature" => 0
];

// Generar JSON con manejo robusto de UTF-8 y deteccion de errores
$json_body = json_encode($cuerpo, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
if ($json_body === false) {
    echo "<p class='error'>Error al generar JSON para OpenAI: " . json_last_error_msg() . "</p>";
    echo "<a href='form_subir_pdf.php' class='boton'>Volver al formulario</a>";
    echo "</div></body></html>";
    exit;
}

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json; charset=utf-8",
    "Authorization: Bearer " . $openai_api_key
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

$respuesta_raw = curl_exec($ch);
$http_code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error    = curl_error($ch);
curl_close($ch);

if ($http_code != 200) {
    echo "<p class='error'>
            Error en la llamada a OpenAI (HTTP " . $http_code . "):<br>
            <pre>" . htmlspecialchars($respuesta_raw) . "</pre>
            " . ($curl_error ? "Error cURL: " . $curl_error : "") . "
          </p>";
    echo "<a href='form_subir_pdf.php' class='boton'>Volver al formulario</a>";
    echo "</div></body></html>";
    exit;
}

$respuesta_json = json_decode($respuesta_raw, true);
$csv_devuelto = trim($respuesta_json["choices"][0]["message"]["content"]);

if ($csv_devuelto == "error" || empty($csv_devuelto)) {
    echo "<p class='error'>La IA no ha podido extraer datos validos del PDF.</p>";
    echo "<a href='form_subir_pdf.php' class='boton'>Volver al formulario</a>";
    echo "</div></body></html>";
    exit;
}

echo "<p class='ok'>Respuesta de OpenAI recibida correctamente.</p>";
echo "<p class='subtitulo'>CSV devuelto por la IA:</p>";
echo "<pre style='background:#f5f5f5;padding:10px;border:1px solid #ddd;font-size:12px'>"
     . htmlspecialchars($csv_devuelto) . "</pre>";

// =====================================================================
// 4. PARSEO DEL CSV Y VALIDACION
// =====================================================================

// Limpiar respuesta: a veces la IA envuelve el CSV en markdown (```csv ... ```)
$csv_limpio = preg_replace('/^```[a-z]*\s*/m', '', $csv_devuelto);
$csv_limpio = preg_replace('/```\s*$/m', '', $csv_limpio);
$csv_limpio = trim($csv_limpio);

// Dividir en lineas y filtrar las vacias
$lineas = array_filter(array_map('trim', explode("\n", $csv_limpio)), function($l) {
    return $l !== '';
});
$lineas = array_values($lineas);

if (count($lineas) < 2) {
    echo "<p class='error'>La respuesta de la IA no tiene el formato esperado (faltan lineas).</p>";
    echo "<a href='form_subir_pdf.php' class='boton'>Volver al formulario</a>";
    echo "</div></body></html>";
    exit;
}

// Buscar la cabecera (linea que contiene "fecha_factura")
$indice_cabecera = -1;
foreach ($lineas as $i => $linea) {
    if (stripos($linea, "fecha_factura") !== false) {
        $indice_cabecera = $i;
        break;
    }
}

if ($indice_cabecera === -1 || !isset($lineas[$indice_cabecera + 1])) {
    echo "<p class='error'>No se ha podido localizar la linea de datos en la respuesta de la IA.</p>";
    echo "<a href='form_subir_pdf.php' class='boton'>Volver al formulario</a>";
    echo "</div></body></html>";
    exit;
}

// La linea de datos es la siguiente a la cabecera
$datos = explode(";", trim($lineas[$indice_cabecera + 1]));
if (count($datos) != 5) {
    echo "<p class='error'>La respuesta de la IA no tiene 5 campos (tiene " . count($datos) . ").</p>";
    echo "<a href='form_subir_pdf.php' class='boton'>Volver al formulario</a>";
    echo "</div></body></html>";
    exit;
}

$fecha       = trim($datos[0]);
$num_factura = trim($datos[1]);
$proveedor   = trim($datos[2]);
$concepto    = trim($datos[3]);
$importe     = trim($datos[4]);

// Validacion basica
$errores_datos = [];

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    $errores_datos[] = "La fecha extraida no tiene formato valido (aaaa-mm-dd): " . htmlspecialchars($fecha);
}

if (empty($proveedor)) {
    $errores_datos[] = "No se ha podido extraer el proveedor.";
}

if (!is_numeric($importe)) {
    $errores_datos[] = "El importe extraido no es numerico: " . htmlspecialchars($importe);
}

if (count($errores_datos) > 0) {
    echo "<div class='error'><strong>Datos extraidos invalidos:</strong><ul>";
    foreach ($errores_datos as $err) {
        echo "<li>" . $err . "</li>";
    }
    echo "</ul></div>";
    echo "<a href='form_subir_pdf.php' class='boton'>Volver al formulario</a>";
    echo "</div></body></html>";
    exit;
}

// =====================================================================
// 5. INSERCION EN LA BASE DE DATOS
// =====================================================================

$fecha_esc  = mysqli_real_escape_string($conexion, $fecha);
$num_esc    = mysqli_real_escape_string($conexion, $num_factura);
$prov_esc   = mysqli_real_escape_string($conexion, $proveedor);
$conc_esc   = mysqli_real_escape_string($conexion, $concepto);
$estado_esc = mysqli_real_escape_string($conexion, $estado);
$imp_val    = (float)$importe;

$sql = "INSERT INTO facturas (fecha_factura, num_factura, proveedor, concepto, importe, estado)
        VALUES ('$fecha_esc', '$num_esc', '$prov_esc', '$conc_esc', $imp_val, '$estado_esc')";

if (mysqli_query($conexion, $sql)) {
    echo "<p class='ok'>
            Factura procesada y guardada correctamente.<br>
            <strong>" . htmlspecialchars($prov_esc) . "</strong> &mdash; "
            . number_format($imp_val, 2, ',', '.') . " &euro; (" . htmlspecialchars($estado_esc) . ")
          </p>";

    echo "<p class='seccion'>Datos extraidos por la IA:</p>";
    echo "<table>";
    echo "<tr><th>Campo</th><th>Valor</th></tr>";
    echo "<tr><td>Fecha</td><td>" . htmlspecialchars($fecha_esc) . "</td></tr>";
    echo "<tr><td>Num. factura</td><td>" . htmlspecialchars($num_esc) . "</td></tr>";
    echo "<tr><td>Proveedor</td><td>" . htmlspecialchars($prov_esc) . "</td></tr>";
    echo "<tr><td>Concepto</td><td>" . htmlspecialchars($conc_esc) . "</td></tr>";
    echo "<tr><td>Importe</td><td class='num'>" . number_format($imp_val, 2, ',', '.') . " &euro;</td></tr>";
    echo "<tr><td>Estado</td><td><span class='badge badge-" . strtolower($estado_esc) . "'>"
         . htmlspecialchars($estado_esc) . "</span></td></tr>";
    echo "</table>";

} else {
    echo "<p class='error'>Error al insertar en la base de datos: " . mysqli_error($conexion) . "</p>";
}

mysqli_close($conexion);
?>

    <br>
    <a href="form_subir_pdf.php" class="boton boton-verde">Procesar otra factura</a>
    <a href="listar.php"         class="boton boton-gris">Ver listado</a>
    <a href="index.php"          class="boton boton-gris">Volver al menu</a>

</div>
</body>
</html>
