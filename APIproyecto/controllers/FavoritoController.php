<?php
// Indicamos que este archivo contiene el controlador de favoritos.
// Incluimos el modelo Favorito, que permite consultar,
// agregar y eliminar favoritos en la base de datos.
require_once __DIR__ . "/../models/Favorito.php";
// Incluimos la clase Response, que permite enviar
// respuestas al cliente en formato JSON.
require_once __DIR__ . "/../utils/Response.php";
// Declaramos la clase FavoritoController.
// Este controlador coordina las operaciones de favoritos.
class FavoritoController {
 // Variable que guardará un objeto del modelo Favorito.
 private $modelo;
 // Constructor: se ejecuta automáticamente cuando
 // creamos un objeto de la clase FavoritoController.
 public function __construct() {
 // Creamos un objeto del modelo Favorito.
 $this->modelo = new Favorito();
 } // Finaliza el constructor.
 // GET /favoritos
 // Este método permite consultar los favoritos del usuario.
 public function listar($usuario) {
 // Llamamos al método listar() del modelo Favorito.
 // Le pasamos el ID del usuario para obtener sus productos favoritos.
 // Después enviamos el resultado en formato JSON.
 // El código HTTP 200 indica que la consulta se procesó correctamente.
 Response::json($this->modelo->listar($usuario["id_usuario"]), 200);
 } // Finaliza el método listar.
 // POST /favoritos/agregar
 // Este método permite agregar un producto a los favoritos.
 // Recibe los datos del usuario y los datos enviados en la solicitud.
 public function agregar($usuario, $datos) {
 // Llamamos al método agregar() del modelo Favorito.
 // Enviamos el ID del usuario y el ID del producto que quiere guardar.
 // El ID del producto se obtiene del arreglo $datos.
 $this->modelo->agregar($usuario["id_usuario"], $datos["id_evento"]);
 // Enviamos una respuesta JSON indicando que se agregó el favorito.
 // El código HTTP 201 indica que se creó un registro.
 Response::json(["mensaje" => "Agregado a favoritos"], 201);
 } // Finaliza el método agregar.
 // DELETE /favoritos/eliminar/{id}
 // Este método permite quitar un producto de los favoritos.
 // Recibe los datos del usuario y el ID del producto que se eliminará.
 public function eliminar($usuario, $id_evento) {
 // Llamamos al método eliminar() del modelo Favorito.
 // Le enviamos el ID del usuario y el ID del producto.
 // De esta manera, se elimina la relación correspondiente
 // a ese usuario y ese producto.
 $this->modelo->eliminar($usuario["id_usuario"], $id_evento);
 // Enviamos una respuesta JSON confirmando la operación.
 // El código HTTP 200 indica que la solicitud se procesó correctamente.
 Response::json(["mensaje" => "Eliminado de favoritos"], 200);
 } // Finaliza el método eliminar.
} // Finaliza la clase FavoritoController.