# Generador de CFDI

Este script convierte el xml de un cfdi a PDF

## Configuracion

Este script esta listo para funcionar y solo añadir tu logo, solo agrega tu `logo.png` al path de origen

** Nota **
Por el momento la extraccion desde archivos xml solo funciona con

[x] CFDI de Ingreso

Con la API de SAT.WS puedes generar

[x] CFDI de Ingreso
[X] CFDI de Pago
[X] CFDI de Egreso

## Uso

#### Extraer XML
Para extraer desde un xml simplemente añade un archivo `.xml` y remplaza el nombre en la linea #27 del archivo `cfdi.php`

Despues ve a

`http://localhost/cfdi.php`

Tambien puedes subir un archivo mediante `upload.php`

Solo crea una carpeta llamada `cfdi_xml`

Despues ve a 

`http://localhost/upload.php`

Agrega el archivo y espera a que se descargue a tu ordenador


#### API SAT.WS
Este script funciona con la API de [SatWS](https://sat.ws/)

** OJO ***
No hace ningun tipo de conexion con la API, simplemente se ajusta al formato extraido de la API.
Puedes cambiar la URL de la obtencion de la informacion en formato JSON en la linea #36 del archivo `cfdi.php`

Para obtener el PDF utilizando la API necesitas ingresar una ID en la url
(La ID y el UUID son dos valores diferentes)

[Documentacion sobre SAT.WS](https://sat.ws/docs/api/#operation/GetInvoiceCFDI)

Ejemplo

`http://localhost/cfdi.php?id=:id`

