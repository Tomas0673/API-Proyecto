<?php
// Este archivo es el punto de entrada de la API.
// Las solicitudes llegan aquí para que luego se procesen
// las rutas correspondientes.
// Indicamos qué origen tiene permiso para comunicarse con la API.
// En este caso, permitimos las solicitudes desde Angular,
// que se ejecuta en http://localhost:4200.
header("Access-Control-Allow-Origin: http://localhost:4200");
// Indicamos qué métodos HTTP puede utilizar el frontend.
// GET: consultar datos.
// POST: crear datos.
// PUT: modificar datos.
// DELETE: eliminar datos.
// OPTIONS: comprobar si la solicitud está permitida.
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Indicamos qué encabezados HTTP acepta la API.
// Content-Type permite indicar el formato de los datos enviados.
// Authorization permite enviar el token de autenticación.
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Comprobamos si la solicitud utiliza el método OPTIONS.
// El navegador puede enviar esta solicitud de comprobación
// antes de realizar determinadas solicitudes, como PUT o DELETE.
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
 // Respondemos con el código HTTP 200,
 // que indica que la solicitud se procesó correctamente.
 http_response_code(200);
 // Detenemos la ejecución porque no necesitamos
 // continuar con las rutas para esta comprobación.
 exit;
} // Finaliza la condición que procesa las solicitudes OPTIONS.
// Incluimos la clase Response, que permite enviar
// respuestas al cliente en formato JSON.
require_once __DIR__ . "/../utils/Response.php";
// Incluimos el archivo que analiza las solicitudes
// y las dirige hacia el controlador correspondiente.
// Este archivo contiene la lógica de las rutas de la API.
require __DIR__ . "/../routes/Routes.php";
