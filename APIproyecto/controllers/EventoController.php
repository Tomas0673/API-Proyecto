<?php
require_once __DIR__ . "/../models/Evento.php";
require_once __DIR__ . "/../utils/Response.php";

class EventoController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Evento();
    }

    // GET /eventos o GET /eventos?buscar=texto
    public function listar()
    {
        $buscar = $_GET["buscar"] ?? "";
        $eventos = $buscar !== ""
            ? $this->modelo->buscar($buscar)
            : $this->modelo->getAll();

        Response::json($eventos, 200);
    }

    // GET /eventos/{id_evento}
    public function verUno($id)
    {
        $evento = $this->modelo->getById($id);

        if (!$evento) {
            Response::json(["error" => "Evento no encontrado"], 404);
            return;
        }

        Response::json($evento, 200);
    }

    // POST /eventos
    public function crear($datos)
    {
        if (!$this->datosValidos($datos)) {
            Response::json([
                "error" => "Se requieren titulo, anio, imagen_1, imagen_2, imagen_3, descripcion_corta y descripcion_larga"
            ], 400);
            return;
        }

        $idEvento = $this->modelo->create($datos);
        if ($idEvento === false) {
            Response::json(["error" => "No se pudo crear el evento"], 500);
            return;
        }

        Response::json([
            "mensaje" => "Evento creado",
            "id_evento" => $idEvento
        ], 201);
    }

    // PUT /eventos/{id_evento}
    public function editar($id, $datos)
    {
        if (!$this->datosValidos($datos)) {
            Response::json([
                "error" => "Se requieren titulo, anio, imagen_1, imagen_2, imagen_3, descripcion_corta y descripcion_larga"
            ], 400);
            return;
        }

        if (!$this->modelo->getById($id)) {
            Response::json(["error" => "Evento no encontrado"], 404);
            return;
        }

        if (!$this->modelo->update($id, $datos)) {
            Response::json(["error" => "No se pudo actualizar el evento"], 500);
            return;
        }

        Response::json(["mensaje" => "Evento actualizado"], 200);
    }

    // DELETE /eventos/{id_evento}
    public function eliminar($id)
    {
        if (!$this->modelo->getById($id)) {
            Response::json(["error" => "Evento no encontrado"], 404);
            return;
        }

        if (!$this->modelo->delete($id)) {
            Response::json(["error" => "No se pudo eliminar el evento"], 500);
            return;
        }

        Response::json(["mensaje" => "Evento eliminado"], 200);
    }

    // POST /eventos/imagen con "campo" e "imagen" en multipart/form-data
    public function subirImagen($campo)
    {
        $camposPermitidos = ["imagen_1", "imagen_2", "imagen_3"];
        if (!in_array($campo, $camposPermitidos, true)) {
            Response::json(["error" => "Campo de imagen no válido"], 400);
            return;
        }

        if (!isset($_FILES["imagen"]) || $_FILES["imagen"]["error"] !== UPLOAD_ERR_OK) {
            Response::json(["error" => "No se recibió ninguna imagen"], 400);
            return;
        }

        $informacionImagen = getimagesize($_FILES["imagen"]["tmp_name"]);
        if ($informacionImagen === false) {
            Response::json(["error" => "El archivo no es una imagen válida"], 400);
            return;
        }

        $extensionesPermitidas = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];
        $tipo = $informacionImagen["mime"];
        if (!isset($extensionesPermitidas[$tipo])) {
            Response::json(["error" => "Formato no permitido (jpg, png o webp)"], 400);
            return;
        }

        $nombre = uniqid("evento_", true) . "." . $extensionesPermitidas[$tipo];
        $destino = __DIR__ . "/../public/uploads/" . $nombre;
        if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $destino)) {
            Response::json(["error" => "No se pudo guardar la imagen"], 500);
            return;
        }

        Response::json([$campo => $nombre], 201);
    }

    private function datosValidos($datos)
    {
        $campos = [
            "titulo",
            "anio",
            "imagen_1",
            "imagen_2",
            "imagen_3",
            "descripcion_corta",
            "descripcion_larga"
        ];

        foreach ($campos as $campo) {
            if (!array_key_exists($campo, $datos)) {
                return false;
            }
        }

        return true;
    }
}
