<?php

require_once __DIR__ . "/../models/Compra.php";
require_once __DIR__ . "/../models/Carrito.php";
require_once __DIR__ . "/../utils/Response.php";

class CompraController {

    private $modelo;
    private $carritoModelo;

    public function __construct() {
        $this->modelo = new Compra();
        $this->carritoModelo = new Carrito();
    }

    public function finalizar($usuario) {
        $carrito = $this->carritoModelo->obtenerOCrear($usuario["id_usuario"]);
        $productos = $this->carritoModelo->verDetalle($carrito["id_carrito"]);

        if (empty($productos)) {
            Response::json(["error" => "El carrito está vacío"], 400);
            return;
        }

        $idCompra = $this->modelo->finalizar(
            $usuario["id_usuario"],
            $carrito["id_carrito"],
            $productos
        );

        Response::json([
            "mensaje" => "Compra realizada",
            "id_compra" => $idCompra
        ], 201);
    }

    public function historial($usuario) {
        Response::json($this->modelo->historial($usuario["id_usuario"]), 200);
    }
    // GET /compras/todas (solo admin; el control de rol lo hace Routes.php)
public function todas() {
 Response::json($this->modelo->todas(), 200);
}

}

?>