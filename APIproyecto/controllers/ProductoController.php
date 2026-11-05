<?php
// controllers/ProductoController.php
// Incluimos el Model Producto porque el Controller
// necesita utilizar sus métodos para trabajar con los productos.
require_once __DIR__ .
    "/../models/Producto.php";
// Incluimos la clase Response para poder enviar las respuestas de la API en
//formato JSON.
require_once __DIR__ .
    "/../utils/Response.php";
class ProductoController
{
    // Variable donde se almacena un objeto del Model Producto.
    // A través de esta variable podemos utilizar
    // los métodos definidos en el Model.
    private $modelo;
    // Constructor de la clase.
    // Se ejecuta automáticamente cuando se crea un objeto ProductoController.
    public function __construct()
    {
        // Creamos un objeto de la clase Producto.
        // Producto hereda de Model y por eso puede utilizar
        // la conexión con la base de datos.
        $this->modelo = new Producto();
    }
    // GET /productos
    //
    // Endpoint utilizado para obtener todos los productos.
    // GET /productos o GET /productos?buscar=remera
    public function listar()
    {
        // $_GET trae los parámetros de la URL. Si no vino "buscar", usamos "" (vacío).
        $buscar = $_GET["buscar"] ?? "";
        if ($buscar !== "") {
            Response::json($this->modelo->buscar($buscar), 200); // con filtro
        } else {
            Response::json($this->modelo->getAll(), 200); // sin filtro: todos
        }
    }

    // GET /productos/{id}
    //
    // Endpoint utilizado para obtener un producto específico.
    // Recibe el ID del producto como parámetro.
    public function verUno($id)
    {
        // Le pedimos al Model que busque el producto
        // utilizando el ID recibido.
        $producto = $this->modelo->getById($id);
        // Comprobamos si el producto fue encontrado.
        //
        // Si existe, lo enviamos con código 200.
        // Si no existe, enviamos un mensaje de error
        // con código 404 (recurso no encontrado).
        $producto ? Response::json($producto, 200) : Response::json(["error"
        => "Producto no encontrado"], 404);
    }
    // POST /productos
    //
    // Endpoint utilizado para crear un nuevo producto.
    // Esta operación estará disponible solamente para administradores.
    public function crear($datos)
    {
        // Enviamos al Model los datos necesarios
        // para crear el nuevo producto.
        $this->modelo->create(
            $datos["nombre"],
            $datos["descripcion"],
            $datos["precio"],
            $datos["imagen"]
        );
        // Informamos al cliente que el producto
        // fue creado correctamente.
        //
        // El código 201 significa "creado".
        Response::json(["mensaje"
        => "Producto creado"], 201);
    }
    // PUT /productos/{id}
    //
    // Endpoint utilizado para modificar un producto existente.
    // Recibe el ID del producto y los nuevos datos.
    public function editar($id, $datos)
    {
        // Le pedimos al Model que actualice el producto
        // utilizando el ID y los nuevos datos.
        $this->modelo->update(
            $id,
            $datos
        );
        // Informamos al cliente que el producto
        // fue actualizado correctamente.
        Response::json(["mensaje"
        => "Producto actualizado"], 200);
    }
    // DELETE /productos/{id}
    //
    // Endpoint utilizado para eliminar un producto.
    // Recibe como parámetro el ID del producto.
    public function eliminar($id)
    {
        // Le pedimos al Model que elimine
        // el producto indicado.
        $this->modelo->delete($id);
        // Informamos al cliente que el producto
        // fue eliminado correctamente.
        Response::json(["mensaje"
        => "Producto eliminado"], 200);
    }
    // POST /productos/imagen (solo admin). El archivo llega en el campo "imagen" del formulario.
    public function subirImagen() {
        // 1) $_FILES guarda los archivos subidos. Verificamos que llegó y sin errores.
        if (!isset($_FILES["imagen"]) || $_FILES["imagen"]["error"] !== UPLOAD_ERR_OK) {
            Response::json(["error" => "No se recibió ninguna imagen"], 400);
            return; // corta el método
        }
        // 2) Sacamos la extensión del nombre original (jpg, png...) en minúsculas
        $extension = strtolower(pathinfo(
            $_FILES["imagen"]["name"],
            PATHINFO_EXTENSION
        ));
    // Solo aceptamos formatos de imagen conocidos (evita que suban un .php, por ejemplo)
 $permitidas = ["jpg", "jpeg", "png", "webp"];
 if (!in_array($extension, $permitidas)) {
 Response::json(["error" => "Formato no permitido (usá jpg, png o webp)"],
400);
 return;
 }
 // 3) Nombre único con uniqid(), para que dos imágenes no se pisen. Ej: img_65f2a1.png
 $nombre = uniqid("img_") . "." . $extension;
 // Carpeta destino: api_proyecto/public/uploads/ (tiene que existir)
 $destino = __DIR__ . "/../public/uploads/" . $nombre;
 // 4) PHP guarda el archivo en una carpeta temporal; lo movemos a su lugar definitivo
 if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $destino)) {
 // Devolvemos solo el nombre: Angular lo guardará en el campo imagen del producto
 Response::json(["imagen" => $nombre], 201);
 } else {
 Response::json(["error" => "No se pudo guardar la imagen"], 500);
 }
}

}
?>