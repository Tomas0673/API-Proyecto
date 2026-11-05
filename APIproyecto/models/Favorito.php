<?php
// Indicamos que este archivo contiene el modelo Favorito.
// Incluimos la clase Model, que contiene la conexión
// a la base de datos.
require_once __DIR__ . "/../core/Model.php";
// Creamos la clase Favorito.
// Al extender Model, puede utilizar la conexión $this->pdo.
class Favorito extends Model {
 // Obtiene la lista de productos favoritos de un usuario.
 // Recibe el ID del usuario cuyos favoritos queremos consultar.
 public function listar($idUsuario) {
 // Preparamos una consulta SQL para obtener los productos favoritos.
 // f.id obtiene el ID del registro en la tabla favoritos.
 // p.id AS id_producto obtiene el ID del producto
 // y le asigna el nombre id_producto al resultado.
 // También obtenemos el nombre, el precio y la imagen del producto.
 // JOIN relaciona las tablas favoritos y productos.
 // WHERE filtra los resultados por el ID del usuario.
 $sql = "SELECT f.id, e.id AS id_evento, e.titulo, e.anio, e.imagen_1
 FROM favoritos f
 JOIN evento e ON f.id_evento = e.id
 WHERE f.id_usuario = :id_usuario";
 // Preparamos la consulta utilizando la conexión a la base de datos.
 $stmt = $this->pdo->prepare($sql);
 // Ejecutamos la consulta indicando el ID del usuario.
 $stmt->execute(["id_usuario" => $idUsuario]);
 // Devolvemos todos los productos encontrados
 // como un arreglo de arreglos asociativos.
 return $stmt->fetchAll(PDO::FETCH_ASSOC);
 } // Finaliza el método listar.
 // Agrega un producto a la lista de favoritos de un usuario.
 // Recibe el ID del usuario y el ID del producto.
public function agregar($idUsuario, $id_evento) {
 // Primero buscamos si ese usuario ya tiene ese producto como favorito
 $existe = $this->pdo->prepare(
 "SELECT id FROM favoritos WHERE id_usuario = :u AND id_evento = :e"
 );
 $existe->execute(["u" => $idUsuario, "e" => $id_evento]);
 // fetch() devuelve una fila si existe, o false si no hay ninguna
 if ($existe->fetch()) {
 return true; // ya estaba: no hacemos nada, así no se duplica
 }
 // Si no existía, lo insertamos normalmente
 $sql = "INSERT INTO favoritos (id_usuario, id_evento) VALUES (:id_usuario,
:id_evento)";
 $stmt = $this->pdo->prepare($sql);
 return $stmt->execute(["id_usuario" => $idUsuario, "id_evento" => $id_evento]);
}
 // Elimina un producto de la lista de favoritos de un usuario.
 // Recibe el ID del usuario y el ID del producto que quiere quitar.
 public function eliminar($idUsuario, $id_evento) {
 // Preparamos una consulta SQL para eliminar un registro de favoritos.
 // Se deben cumplir las dos condiciones:
 // que el registro pertenezca al usuario indicado
 // y que corresponda al producto indicado.
 $sql = "DELETE FROM favoritos WHERE id_usuario = :id_usuario AND
id_evento = :id_evento";
 // Preparamos la consulta SQL.
 $stmt = $this->pdo->prepare($sql);
 // Ejecutamos la consulta con los IDs recibidos.
 // Devuelve true si la ejecución se realiza correctamente
 // o false si ocurre un error.
 return $stmt->execute(["id_usuario" => $idUsuario, "id_evento" => $id_evento]);
 } // Finaliza el método eliminar.
} // Finaliza la clase Favorito.