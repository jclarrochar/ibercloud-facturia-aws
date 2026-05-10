# FacturIA - Modulo de procesamiento con IA

Estos son los archivos nuevos que se anaden a la aplicacion UD6-BD original
para incorporar el procesamiento de facturas PDF mediante inteligencia artificial.

## Archivos del paquete

| Archivo | Tipo | Descripcion |
|---------|------|-------------|
| `config.php` | NUEVO | Configuracion de la API key de OpenAI y prompt |
| `form_subir_pdf.php` | NUEVO | Formulario web para subir un PDF |
| `procesar_pdf.php` | NUEVO | Procesa el PDF con OpenAI y guarda en BD |
| `index.php` | MODIFICADO | Nueva fila con el boton "Subir factura PDF" |

## Como integrar estos archivos en la aplicacion existente

### Paso 1 - Copiar los archivos

Copia los 4 archivos a tu carpeta `UD6-BD`. El `index.php` reemplaza al existente
(el cambio es solo anadir una fila nueva al menu).

### Paso 2 - Instalar pdftotext

La aplicacion necesita la utilidad `pdftotext` para extraer texto de los PDFs.

**En XAMPP/Windows (desarrollo local):**

1. Descarga "Xpdf command line tools" de https://www.xpdfreader.com/download.html
2. Descomprime el ZIP en `C:\xpdf-tools`
3. Anade `C:\xpdf-tools\bin64` al PATH de Windows
   (Panel de control -> Sistema -> Configuracion avanzada -> Variables de entorno)
4. Cierra y vuelve a abrir Apache desde el panel XAMPP

**En Linux (cuando lo desplieguemos en AWS):**

```bash
sudo apt install -y poppler-utils
```

Esto se hara automaticamente desde el script user-data de la EC2, no tienes
que hacerlo a mano.

### Paso 3 - Configurar la API key de OpenAI

Edita el archivo `config.php` y sustituye el texto `PEGA_AQUI_TU_API_KEY_DE_OPENAI`
por tu API key real generada en https://platform.openai.com/api-keys

**IMPORTANTE:**
- Esta API key NO debe subirse a GitHub.
- Mas adelante, cuando montemos el repositorio, anadiremos `config.php` al
  archivo `.gitignore`.
- En produccion (AWS), la API key vendra desde AWS Secrets Manager via
  variable de entorno `OPENAI_API_KEY`. El codigo ya esta preparado para
  detectarla automaticamente.

### Paso 4 - Probar en local

1. Arranca XAMPP (Apache + MySQL).
2. Abre http://localhost/UD6-BD/ en el navegador.
3. Si es la primera vez, haz clic en "Crear BD y tabla" para inicializar.
4. Haz clic en "Subir factura PDF (procesamiento con IA)".
5. Selecciona un PDF de factura (puedes usar los de la carpeta `facturas/` del
   FacturIA original) y pulsa "Procesar factura con IA".
6. Si todo va bien, veras la respuesta de la IA y la confirmacion de insercion.
7. Comprueba en "Ver todas las facturas" que la nueva factura aparece en la tabla.

## Flujo tecnico interno (para defensa)

```
Usuario sube PDF
       v
form_subir_pdf.php (formulario HTML)
       v
procesar_pdf.php
       |
       +-- 1. Valida que sea PDF y < 5 MB
       +-- 2. Ejecuta: pdftotext factura.pdf
       +-- 3. Envia el texto a OpenAI gpt-4o-mini con el prompt
       +-- 4. Recibe CSV estructurado con los 5 campos
       +-- 5. Parsea y valida los datos
       +-- 6. INSERT en la tabla 'facturas' de MySQL
       v
Pagina de resultado con los datos extraidos
```

## Solucion de problemas

**"pdftotext no esta instalado"**
- Verifica que esta en el PATH ejecutando en cmd: `pdftotext -v`
- Si no aparece, repite el Paso 2.

**"La API key no esta configurada"**
- Revisa que has editado `config.php` correctamente.
- La API key debe empezar por `sk-` y tener varios caracteres.

**"Error en la llamada a OpenAI (HTTP 401)"**
- La API key es invalida o ha expirado. Genera una nueva.

**"Error en la llamada a OpenAI (HTTP 429)"**
- Has superado el limite de uso de OpenAI. Espera o anade saldo.

**"No se ha podido extraer texto del PDF"**
- El PDF es una imagen escaneada. Esta version solo procesa PDFs con texto.
