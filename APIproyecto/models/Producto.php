<?php
// models/Producto.php
// Incluimos el archivo Model.php porque la clase Producto
// va a heredar sus características y métodos.
require_once __DIR__ .
    "/../core/Model.php";
// La clase Producto hereda de Model. Al heredar, también puede utilizar la
//conexión $pdo que fue creada en la clase Model.
class Producto extends Model
{ // hereda $pdo de Model
    // Obtiene todos los productos de la base de datos.
    public function getAll()
    {
        // Consulta SQL para seleccionar todos los registros de la tabla
        //productos.
        $sql = "SELECT * FROM productos";
        // Ejecutamos la consulta utilizando la conexión almacenada
        //en $this->pdo.
        $resultado = $this->pdo->query($sql);
        // Convertimos todos los resultados en un arreglo asociativo
        // y lo devolvemos para que pueda ser utilizado por el Controller.
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }
    // Busca un producto utilizando su ID.
    public function getById($id)
    {
        // Consulta SQL para buscar un producto específico.
        // :id es un parámetro que será reemplazado posteriormente.
        $sql = "SELECT * FROM productos WHERE id = :id";
        // Preparamos la consulta antes de ejecutarla.
        // Esto permite trabajar con parámetros de forma segura.
        $stmt = $this->pdo->prepare($sql);
        // Enviamos el valor de $id al parámetro :id y ejecutamos la consulta.
        $stmt->execute(["id" => $id]);
        // Obtenemos un solo producto y lo devolvemo como un arreglo
        //asociativo.
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Crea un nuevo producto en la base de datos.
    // Recibe los datos necesarios para crear el producto.
    public function create($nombre, $descripcion, $precio, $imagen)
    {
        // Consulta SQL para insertar un nuevo registro
        // en la tabla productos.
        $sql = "INSERT INTO productos (nombre, descripcion, precio, imagen)
        VALUES (:nombre, :descripcion, :precio, :imagen)";
        // Preparamos la consulta antes de ejecutarla.
        $stmt = $this->pdo->prepare($sql);
        // Ejecutamos la consulta enviando los valores que corresponden a cada
        //parámetro.
        return $stmt->execute([
            "nombre" => $nombre,
            "descripcion" => $descripcion,
            "precio" =>$precio,
            "imagen" => $imagen
        ]);
    }
    // Modifica los datos de un producto existente.
    // Recibe el ID del producto y un arreglo con los nuevos datos.
    public function update($id, $datos)
    {
        // Consulta SQL para actualizar los datos del producto.
        // El WHERE permite indicar qué producto se debe modificar.
        $sql = "UPDATE productos SET nombre = :nombre, descripcion =
        :descripcion, precio = :precio, imagen = :imagen WHERE id = :id";
        // Preparamos la consulta.
        $stmt = $this->pdo->prepare($sql);
        // Ejecutamos la consulta enviando los nuevos valores.
        // Cada parámetro recibe el dato correspondiente
        // del arreglo $datos.
        return $stmt->execute([
            "nombre" => $datos["nombre"],
            "descripcion" => $datos["descripcion"],
            "precio" => $datos["precio"],
            "imagen" => $datos["imagen"],
            "id" =>
                $id
        ]);
    }
    // Elimina un producto de la base de datos.
    // Recibe como parámetro el ID del producto que se quiere eliminar.
    public function delete($id)
    {
        // Consulta SQL para eliminar el producto indicado.
        $sql = "DELETE FROM productos WHERE id = :id";
        // Preparamos la consulta.
        $stmt = $this->pdo->prepare($sql);
        // Enviamos el ID del producto y ejecutamos la consulta.
        return $stmt->execute(["id" => $id]);
    }
    public function buscar($texto)
    {
        // LIKE busca coincidencias parciales. Los % del valor significan "cualquier cosa antes/después".
        // Busca el texto tanto en el nombre como en la descripción y ordena alfabéticamente.
        $sql = "SELECT * FROM productos
        WHERE nombre LIKE :texto OR descripcion LIKE :texto
        ORDER BY nombre";
        $stmt = $this->pdo->prepare($sql);
        // Los % se agregan acá, en PHP, y el valor viaja protegido por prepare()
        $stmt->execute(["texto" => "%" . $texto . "%"]);
        // fetchAll devuelve TODAS las filas; FETCH_ASSOC las entrega como arrays con nombres de columna
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

