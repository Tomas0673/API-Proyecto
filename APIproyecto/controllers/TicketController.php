<?php
// Indicamos que este archivo contiene el controlador TicketController.
// Incluimos el modelo Ticket, que permite generar el PDF
// y consultar los tickets guardados en la base de datos.
require_once __DIR__ . "/../models/Ticket.php";
// Incluimos el modelo Compra, que permite consultar
// los datos generales y los productos de una compra.
require_once __DIR__ . "/../models/Compra.php";
// Incluimos la clase Response, que permite enviar
// respuestas al cliente en formato JSON.
require_once __DIR__ . "/../utils/Response.php";
// Declaramos la clase TicketController.
// Se encarga de coordinar las acciones relacionadas con los tickets.
class TicketController {
 // Variable que guardará un objeto del modelo Ticket.
 private $modelo;
 // Variable que guardará un objeto del modelo Compra.
 private $compraModelo;
 // Constructor: se ejecuta automáticamente al crear
 // un objeto de la clase TicketController.
 public function __construct() {
 // Creamos un objeto del modelo Ticket.
 $this->modelo = new Ticket();
 // Creamos un objeto del modelo Compra.
 $this->compraModelo = new Compra();
 } // Finaliza el constructor.
 // POST /tickets/generar/{id_compra}
 // Este método permite generar el ticket de una compra.
 // Recibe el ID de la compra que se quiere procesar.
 public function generar($idCompra) {
 // Buscamos los datos generales de la compra.
 // Por ejemplo: fecha, total e ID.
 $compra = $this->compraModelo->getById($idCompra);
 // Buscamos los productos que forman parte de esa compra.
 // El resultado incluye los datos de cada producto comprado.
 $detalle = $this->compraModelo->detalle($idCompra);
 // Llamamos al modelo Ticket para generar el PDF.
 // Le enviamos el ID de la compra, sus datos generales
 // y el detalle de los productos.
 // El método devuelve el número identificador del ticket.
 $numero = $this->modelo->generarPdf($idCompra, $compra, $detalle);
 // Enviamos una respuesta JSON indicando que se generó el ticket.
 // También devolvemos su número identificador.
 // El código HTTP 201 indica que se creó un nuevo recurso.
 Response::json(["mensaje" => "Ticket generado", "numero_ticket" =>
$numero], 201);
 } // Finaliza el método generar.
 // GET /tickets/{id_compra}
 // Este método permite consultar el ticket asociado a una compra.
 public function verTicket($idCompra) {
 // Buscamos en la base de datos el ticket correspondiente
 // al ID de compra recibido.
 $ticket = $this->modelo->getByCompra($idCompra);
 // Evaluamos si se encontró un ticket.
 // Si $ticket contiene un registro, se envía una respuesta 200.
 // Si no existe, se envía un mensaje de error con código 404.
 $ticket
 ? Response::json($ticket, 200)
 : Response::json(["error" => "Todavía no se generó el ticket"],
404);
 } // Finaliza el método verTicket.
} // Finaliza la clase TicketController.