<?php
/*
 * Fichero:     config.php
 * Descripcion: Configuracion de la aplicacion. Lee la API key de OpenAI desde
 *              una variable de entorno (recomendado para cloud) o desde el
 *              propio fichero como fallback (solo en local con XAMPP).
 *
 *              IMPORTANTE: este fichero NO debe subirse a GitHub si contiene
 *              la API key real. En el repositorio se sube config.example.php
 *              como plantilla.
 *
 * Autor:       Juan Carlos Larrocha
 * Fecha:       2025-26
 */

// 1. Intentar leer la API key desde variable de entorno (entorno cloud).
//    En AWS, el script de arranque (user-data) la inyectara desde Secrets Manager.
$openai_api_key = getenv("OPENAI_API_KEY");

// 2. Si no hay variable de entorno, usar la siguiente linea (solo desarrollo local).
//    Sustituye el texto por tu API key real solo en tu PC. NO subas este valor a GitHub.
if (empty($openai_api_key)) {
    $openai_api_key = getenv("OPENAI_API_KEY") ?: "PENDIENTE_DE_CONFIGURAR";
}

// 3. Modelo de OpenAI a utilizar
$openai_modelo = "gpt-4o-mini";

// 4. Prompt para estructurar las facturas (adaptado del FacturIA original en Python)
$openai_prompt = <<<PROMPT
Eres un asistente especializado en estructurar informacion de facturas. Te proporcionare texto sin formato extraido de una factura, y tu tarea es transformarlo en un CSV con punto y coma (;) como separador de campos.

Requerimientos de extraccion y formato:
1) fecha_factura: Extrae la fecha de emision de la factura y conviertela al formato aaaa-mm-dd. Si hay varias fechas elige la que sea fecha de emision o fecha de pedido.
2) num_factura: Extrae el numero de la factura. Si no aparece, devuelve "SIN-NUMERO".
3) proveedor: Extrae el nombre de la empresa emisora de la factura, en minusculas y sin signos de puntuacion (puede contener letras y numeros).
4) concepto: Extrae la descripcion del producto o servicio facturado. Si hay varias descripciones, elige la mas representativa (maximo 200 caracteres).
5) importe: Extrae el monto total de la factura como numero con punto decimal (ejemplo: 1234.56). Si la moneda es dolares, conviertelo a euros multiplicando por 0.9243.

Formato de salida obligatorio:
- Siempre incluye la siguiente cabecera como primera linea (sin excepcion):
fecha_factura;num_factura;proveedor;concepto;importe
- En la segunda linea, los valores extraidos en ese mismo orden, separados por punto y coma.
- No incluyas explicaciones, comentarios ni lineas adicionales.
- No uses comillas ni delimitadores extras.

Ejemplo de salida esperada:
fecha_factura;num_factura;proveedor;concepto;importe
2024-03-15;FAC-2024-001;techsolutions sl;mantenimiento servidores q1 2024;1250.00

Si no puedes extraer datos validos, responde exactamente con la palabra: error
PROMPT;

?>
