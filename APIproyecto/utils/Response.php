<?php
// utils/Response.php
// Clase encargada de preparar y enviar las respuestas de la API al cliente.
class Response {
 // Método estático que recibe los datos que queremos enviar y el código de
//respuesta HTTP.
 //
 // El código 200 se utiliza por defecto cuando la solicitud se realizó
//correctamente.
 public static function json($datos, $codigo = 200) {
 // Establecemos el código de respuesta HTTP.
 // Por ejemplo:
 // 200 = solicitud exitosa.
 // 404 = recurso no encontrado.
 // 400 = solicitud incorrecta.
 // 500 = error del servidor.
 http_response_code($codigo);
 // Indicamos que la respuesta que vamos a enviar tiene formato JSON.
 header("Content-Type: application/json");
 // Convertimos los datos de PHP a formato JSON y los enviamos al
//cliente.
 echo json_encode($datos);
 }
 }
?>
