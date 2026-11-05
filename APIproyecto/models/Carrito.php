<?php
// models/Carrito.php
// Incluye la clase Model, que proporciona la conexión
// a la base de datos mediante PDO.
require_once __DIR__ . "/../core/Model.php";
// La clase Carrito hereda de Model.
// Por eso puede utilizar la conexión a la base de datos
// a través de $this->pdo.
class Carrito extends Model {
 // Busca el carrito abierto de un usuario.
 // Si no existe, crea uno nuevo y lo devuelve.
 public function obtenerOCrear($idUsuario) {
 // Consulta si el usuario tiene un carrito cuyo estado
 // sea "abierto".
 // :id_usuario es un parámetro que se completará después.
 $sql = "SELECT * FROM carrito WHERE id_usuario = :id_usuario AND estado
= 'abierto'";
 // Prepara la consulta SQL.
 $stmt = $this->pdo->prepare($sql);
 // Ejecuta la consulta enviando el ID del usuario.
 $stmt->execute(["id_usuario" => $idUsuario]);
 // Obtiene el carrito encontrado como un arreglo asociativo.
 // Si no existe, devuelve false.
 $carrito = $stmt->fetch(PDO::FETCH_ASSOC);
 // Comprueba si no se encontró un carrito abierto.
 if (!$carrito) {
 // Prepara una consulta para crear un carrito nuevo.
 // Se guarda el ID del usuario y se establece
 // su estado inicial como "abierto".
 $sql = "INSERT INTO carrito (id_usuario, estado) VALUES
(:id_usuario, 'abierto')";
 // Prepara la consulta SQL.
 $stmt = $this->pdo->prepare($sql);
 // Ejecuta la consulta con el ID del usuario.
 $stmt->execute(["id_usuario" => $idUsuario]);
 // Obtiene el ID que la base de datos acaba de generar
 // para el nuevo carrito.
 $idCarrito = $this->pdo->lastInsertId();
 // Devuelve un arreglo con los datos del carrito creado.
 // Estos datos se preparan en PHP y se devuelven
 // sin realizar otra consulta a la base de datos.
 return [
 "id_carrito" => $idCarrito,
 "id_usuario" => $idUsuario,
 "estado" => "abierto"
 ];
 }
 // Si ya existía un carrito abierto, devuelve sus datos.
 return $carrito;
 }
 // Obtiene el detalle de un carrito.
 // Devuelve información de los productos que contiene,
 // como nombre, imagen, cantidad y precio unitario.
 public function verDetalle($idCarrito) {
 // Consulta los detalles del carrito y los relaciona
 // con la tabla productos para obtener sus nombres e imágenes.
 //
 // dc es un alias de detalles_carrito.
 // p es un alias de productos.
 //
 // JOIN relaciona ambas tablas mediante el ID del producto.
 $sql = "SELECT dc.id_producto, p.nombre, p.imagen, dc.cantidad,
dc.precio_unitario
 FROM detalles_carrito dc
 JOIN productos p ON dc.id_producto = p.id
 WHERE dc.id_carrito = :id_carrito";
 // Prepara la consulta SQL.
 $stmt = $this->pdo->prepare($sql);
 // Ejecuta la consulta para el carrito indicado.
 $stmt->execute(["id_carrito" => $idCarrito]);
 // Devuelve todos los productos encontrados como
 // un arreglo de arreglos asociativos.
 return $stmt->fetchAll(PDO::FETCH_ASSOC);
 }
 // Agrega un producto al carrito.
 // Si ese producto ya está en el carrito, suma la cantidad.
 // Si todavía no está, crea un nuevo detalle.
 public function agregarProducto($idCarrito, $idProducto, $cantidad,
$precioUnitario) {
 // Busca si el producto ya está agregado al carrito.
 // Se comprueban tanto el ID del carrito como el del producto.
 $sql = "SELECT * FROM detalles_carrito
 WHERE id_carrito = :id_carrito AND id_producto = :id_producto";
 // Prepara la consulta SQL.
 $stmt = $this->pdo->prepare($sql);
 // Ejecuta la consulta con los dos identificadores.
 $stmt->execute([
 "id_carrito" => $idCarrito,
 "id_producto" => $idProducto
 ]);
 // Guarda el detalle encontrado.
 // Si el producto no está en el carrito, devuelve false.
 $existente = $stmt->fetch(PDO::FETCH_ASSOC);
 // Si el producto ya existe en el carrito,
 // se actualiza su cantidad en lugar de duplicar el detalle.
 if ($existente) {
 // Suma la cantidad actual y la nueva cantidad solicitada.
 // Luego llama al método que guarda la cantidad actualizada.
 return $this->actualizarCantidad(
 $idCarrito,
 $idProducto,
 $existente["cantidad"] + $cantidad
 );
 }
 // Si el producto todavía no está en el carrito,
 // prepara una consulta para insertar un detalle nuevo.
 $sql = "INSERT INTO detalles_carrito (id_carrito, id_producto,
cantidad, precio_unitario)
 VALUES (:id_carrito, :id_producto, :cantidad,
:precio_unitario)";
 // Prepara la consulta SQL.
 $stmt = $this->pdo->prepare($sql);
 // Ejecuta la consulta enviando los datos del producto.
 // Devuelve true si la ejecución se realiza correctamente
 // o false si falla.
 return $stmt->execute([
 "id_carrito" => $idCarrito,
 "id_producto" => $idProducto,
 "cantidad" => $cantidad,
 "precio_unitario" => $precioUnitario
 ]);
 }
 // Modifica la cantidad de un producto que ya está
 // incluido en el carrito.
 public function actualizarCantidad($idCarrito, $idProducto, $cantidad) {
 // Prepara una consulta para cambiar la cantidad
 // del producto indicado dentro del carrito indicado.
 $sql = "UPDATE detalles_carrito SET cantidad = :cantidad
 WHERE id_carrito = :id_carrito AND id_producto = :id_producto";
 // Prepara la consulta SQL.
 $stmt = $this->pdo->prepare($sql);
 // Ejecuta la consulta con la nueva cantidad
 // y los identificadores del carrito y del producto.
 return $stmt->execute([
 "cantidad" => $cantidad,
 "id_carrito" => $idCarrito,
 "id_producto" => $idProducto
 ]);
 }
 // Elimina un producto específico de un carrito.
 public function eliminarProducto($idCarrito, $idProducto) {
 // Prepara una consulta DELETE para borrar el detalle
 // correspondiente al carrito y al producto indicados.
 $sql = "DELETE FROM detalles_carrito
 WHERE id_carrito = :id_carrito AND id_producto = :id_producto";
 // Prepara la consulta SQL.
 $stmt = $this->pdo->prepare($sql);
 // Ejecuta la consulta con los identificadores recibidos.
 // Devuelve true si la ejecución se realiza correctamente
 // o false si falla.
 return $stmt->execute([
 "id_carrito" => $idCarrito,
 "id_producto" => $idProducto
 ]);
 }
 // Elimina todos los productos de un carrito,
 // pero no elimina el carrito en sí.
 public function vaciar($idCarrito) {
 // Prepara una consulta para borrar todos los detalles
 // asociados al carrito indicado.
 $sql = "DELETE FROM detalles_carrito WHERE id_carrito = :id_carrito";
 // Prepara la consulta SQL.
 $stmt = $this->pdo->prepare($sql);
 // Ejecuta la consulta para eliminar los productos del carrito.
 return $stmt->execute(["id_carrito" => $idCarrito]);
 }
}
?>