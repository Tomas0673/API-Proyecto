<?php
require_once __DIR__ . "/../core/Model.php";

// Modelo encargado de consultar y modificar los eventos almacenados en la base de datos.
class Evento extends Model
{
    // Recupera todos los eventos, mostrando primero los que se crearon más recientemente.
    public function getAll()
    {
        $sql = "SELECT * FROM evento ORDER BY creado DESC";
        $resultado = $this->pdo->query($sql);

        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca un evento por su identificador; devuelve una fila o false si no existe.
    public function getById($id)
    {
        $sql = "SELECT * FROM evento WHERE id_evento = :id_evento";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["id_evento" => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Inserta un evento con los datos recibidos y la fecha actual.
    // Devuelve el ID generado cuando la inserción funciona, o false si falla.
    public function create($datos)
    {
        $sql = "INSERT INTO evento (
                    titulo, anio, imagen_1, imagen_2, imagen_3,
                    descripcion_corta, descripcion_larga, creado
                ) VALUES (
                    :titulo, :anio, :imagen_1, :imagen_2, :imagen_3,
                    :descripcion_corta, :descripcion_larga, NOW()
                )";
        $stmt = $this->pdo->prepare($sql);
        $ejecutado = $stmt->execute([
            "titulo" => $datos["titulo"],
            "anio" => $datos["anio"],
            "imagen_1" => $datos["imagen_1"],
            "imagen_2" => $datos["imagen_2"],
            "imagen_3" => $datos["imagen_3"],
            "descripcion_corta" => $datos["descripcion_corta"],
            "descripcion_larga" => $datos["descripcion_larga"]
        ]);

        return $ejecutado ? $this->pdo->lastInsertId() : false;
    }

    // Actualiza los campos de un evento existente y devuelve si la consulta se ejecutó correctamente.
    public function update($id, $datos)
    {
        $sql = "UPDATE evento
                SET titulo = :titulo,
                    anio = :anio,
                    imagen_1 = :imagen_1,
                    imagen_2 = :imagen_2,
                    imagen_3 = :imagen_3,
                    descripcion_corta = :descripcion_corta,
                    descripcion_larga = :descripcion_larga
                WHERE id_evento = :id_evento";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "titulo" => $datos["titulo"],
            "anio" => $datos["anio"],
            "imagen_1" => $datos["imagen_1"],
            "imagen_2" => $datos["imagen_2"],
            "imagen_3" => $datos["imagen_3"],
            "descripcion_corta" => $datos["descripcion_corta"],
            "descripcion_larga" => $datos["descripcion_larga"],
            "id_evento" => $id
        ]);
    }

    // Elimina el evento indicado y devuelve si la consulta se ejecutó correctamente.
    public function delete($id)
    {
        $sql = "DELETE FROM evento WHERE id_evento = :id_evento";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute(["id_evento" => $id]);
    }

    // Busca coincidencias parciales en el título, el año y ambas descripciones.
    // Los parámetros preparados mantienen el texto de búsqueda separado del SQL.
    public function buscar($texto)
    {
        $sql = "SELECT * FROM evento
                WHERE titulo LIKE :titulo
                   OR CAST(anio AS CHAR) LIKE :anio
                   OR descripcion_corta LIKE :descripcion_corta
                   OR descripcion_larga LIKE :descripcion_larga
                ORDER BY creado DESC";
        $stmt = $this->pdo->prepare($sql);
        $busqueda = "%" . $texto . "%";
        $stmt->execute([
            "titulo" => $busqueda,
            "anio" => $busqueda,
            "descripcion_corta" => $busqueda,
            "descripcion_larga" => $busqueda
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}