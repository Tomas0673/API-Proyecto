<?php

require_once __DIR__ . "/../models/Carrito.php";
require_once __DIR__ . "/../models/Producto.php";
require_once __DIR__ . "/../utils/Response.php";

class CarritoController {

    private $modelo;
    private $productoModelo;

    public function __construct() {
        $this->modelo = new Carrito();
        $this->productoModelo = new Producto();
    }

    public function ver($usuario) {
        $carrito = $this->modelo->obtenerOCrear($usuario["id_usuario"]);
        $detalle = $this->modelo->verDetalle($carrito["id_carrito"]);

        Response::json($detalle, 200);
    }

    public function agregar($usuario, $datos) {
        $carrito = $this->modelo->obtenerOCrear($usuario["id_usuario"]);
        $producto = $this->productoModelo->getById($datos["id_producto"]);

        if (!$producto) {
            Response::json(["error" => "Producto no encontrado"], 404);
            return;
        }

        $this->modelo->agregarProducto(
            $carrito["id_carrito"],
            $datos["id_producto"],
            $datos["cantidad"],
            $producto["precio"]
        );

        Response::json(["mensaje" => "Producto agregado al carrito"], 201);
    }

    public function actualizar($usuario, $idProducto, $datos) {
        $carrito = $this->modelo->obtenerOCrear($usuario["id_usuario"]);

        $this->modelo->actualizarCantidad(
            $carrito["id_carrito"],
            $idProducto,
            $datos["cantidad"]
        );

        Response::json(["mensaje" => "Cantidad actualizada"], 200);
    }

    public function eliminar($usuario, $idProducto) {
        $carrito = $this->modelo->obtenerOCrear($usuario["id_usuario"]);

        $this->modelo->eliminarProducto(
            $carrito["id_carrito"],
            $idProducto
        );

        Response::json(["mensaje" => "Producto eliminado del carrito"], 200);
    }

    public function vaciar($usuario) {
        $carrito = $this->modelo->obtenerOCrear($usuario["id_usuario"]);

        $this->modelo->vaciar($carrito["id_carrito"]);

        Response::json(["mensaje" => "Carrito vaciado"], 200);
    }
}

?>