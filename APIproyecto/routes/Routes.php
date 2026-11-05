<?php
// El router decide qué controlador y qué método ejecutar según la URL y el método HTTP.
// Cargamos todos los controladores y la clase Jwt (para validar tokens)
require_once __DIR__ . "/../controllers/ProductoController.php";
require_once __DIR__ . "/../controllers/UsuarioController.php";
require_once __DIR__ . "/../controllers/CarritoController.php";
require_once __DIR__ . "/../controllers/CompraController.php";
require_once __DIR__ . "/../controllers/TicketController.php";
require_once __DIR__ . "/../controllers/FavoritoController.php";
require_once __DIR__ . "/../controllers/EventoController.php";
require_once __DIR__ . "/../utils/Jwt.php";
// ¿Quién hace el pedido? Lee el token del header Authorization y lo valida.
// Si es válido devuelve los datos del usuario; si no, corta con error 401.
function usuarioAutenticado() {
 $headers = getallheaders(); // todos los headers del pedido
 $auth = $headers["Authorization"] ?? ""; // "Bearer eyJ..." (o vacío si no vino)
 if (!str_starts_with($auth, "Bearer ")) { // tiene que empezar con "Bearer "
 Response::json(["error" => "Falta el token"], 401);
 exit; // detiene todo el script
 }
 $token = substr($auth, 7); // saca las 7 letras de "Bearer "
 $payload = Jwt::verificar($token); // comprueba firma y vencimiento
 if (!$payload) {
 Response::json(["error" => "Token inválido o vencido"], 401);
 exit;
 }
 // Normalizamos el nombre de la clave para que el resto de la API
 // use siempre el mismo identificador que existe en la base de datos.
 $idUsuario = $payload["id_usuario"] ?? $payload["id"] ?? null;
 if (!is_numeric($idUsuario)) {
 Response::json(["error" => "Token sin identificador de usuario válido"], 401);
 exit;
 }
 $payload["id_usuario"] = (int) $idUsuario;
 unset($payload["id"]);
 return $payload;
}
// ¿Puede hacerlo? Solo deja pasar a los usuarios con rol admin (si no, error 403).
function requiereAdmin($usuario) {
 if ($usuario["rol"] !== "admin") {
 Response::json(["error" => "No autorizado, se requiere rol admin"], 403);
 exit;
 }
}
$metodo = $_SERVER["REQUEST_METHOD"]; // GET, POST, PUT o DELETE
// parse_url(..., PHP_URL_PATH) se queda solo con la ruta, SIN el "?buscar=..."
$ruta = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
$partes = explode("/", $ruta); // separa la ruta en pedazos por cada "/"
// Descartamos todo lo que viene antes de "public" (api_proyecto/public/...)
$pos = array_search("public", $partes);
if ($pos !== false) {
 $partes = array_slice($partes, $pos + 1);
}
// Ejemplo: /carrito/actualizar/3 -> recurso="carrito", accion="actualizar",param="3"
$recurso = $partes[0] ?? null; // productos, usuarios, carrito...
$accion = $partes[1] ?? null; // registro, agregar, o un id
$param = $partes[2] ?? null; // el id del final (si hay)
// Lee el cuerpo JSON del pedido y lo convierte en array ([] si no vino nada)
$datos = json_decode(file_get_contents("php://input"), true) ?? [];
switch ($recurso) {
 case "productos":
 $c = new ProductoController();
 if ($metodo === "GET" && $accion === null) { $c->listar(); } //GET /productos
 elseif ($metodo === "GET") { $c->verUno($accion); } //GET /productos/3
 elseif ($metodo === "POST" && $accion === "imagen") { //POST /productos/imagen
 requiereAdmin(usuarioAutenticado()); //solo admin 
 $c->subirImagen();
 }
 elseif ($metodo === "POST") { requiereAdmin(usuarioAutenticado()); $c->crear($datos); }
 elseif ($metodo === "PUT") { requiereAdmin(usuarioAutenticado()); $c->editar($accion, $datos); }
 elseif ($metodo === "DELETE") { requiereAdmin(usuarioAutenticado()); $c->eliminar($accion); }
 break;
 case "usuarios":
 $c = new UsuarioController();
 if ($metodo === "POST" && $accion === "registro") { $c->registro($datos); }
// público
 elseif ($metodo === "POST" && $accion === "login") { $c->login($datos); }
// público
 elseif ($metodo === "GET" && $accion === "todos") {
// lista completa
 requiereAdmin(usuarioAutenticado());
// solo admin
 $c->todos();
 }
 elseif ($metodo === "GET") { $c->perfil(usuarioAutenticado()); }
// mi perfil
 break;
 case "carrito":
 $c = new CarritoController();
 $u = usuarioAutenticado(); // todo el carrito exige estar logueado
 if ($metodo === "GET" && $accion === null) { $c->ver($u); }
 elseif ($metodo === "POST" && $accion === "agregar") { $c->agregar($u,
$datos); }
 elseif ($metodo === "PUT" && $accion === "actualizar") { $c->actualizar($u,
$param, $datos); }
 elseif ($metodo === "DELETE" && $accion === "eliminar") { $c->eliminar($u,
$param); }
 elseif ($metodo === "DELETE" && $accion === "vaciar") { $c->vaciar($u); }
 break;
 case "compras":
 $c = new CompraController();
 $u = usuarioAutenticado();
 if ($metodo === "POST" && $accion === "finalizar") { $c->finalizar($u); }
 elseif ($metodo === "GET" && $accion === "todas") { requiereAdmin($u); $c->todas(); } // todas las ventas
 elseif ($metodo === "GET") { $c->historial($u); }
// mis compras
 break;
 case "tickets":
 $c = new TicketController();
 usuarioAutenticado(); // hay que estar logueado
 if ($metodo === "POST" && $accion === "generar") { $c->generar($param); }
// crea el PDF
 elseif ($metodo === "GET") { $c->verTicket($accion); }
// datos del ticket
 break;
 case "favoritos":
 $c = new FavoritoController();
 $u = usuarioAutenticado();
 if ($metodo === "GET" && $accion === null) { $c->listar($u); }
 elseif ($metodo === "POST" && $accion === "agregar") { $c->agregar($u,
$datos); }
 elseif ($metodo === "DELETE" && $accion === "eliminar") { $c->eliminar($u,
$param); }
 break;
 default:
 // Ninguna ruta coincidió
 Response::json(["error" => "Ruta no válida"], 404);
}
