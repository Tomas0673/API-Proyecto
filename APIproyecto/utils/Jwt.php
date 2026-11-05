<?php
// utils/Jwt.php
// Clase encargada de generar y verificar tokens JWT.
// Estos tokens permiten identificar a un usuario
// y comprobar su autenticación en la API.
class Jwt {
 // Clave secreta utilizada para generar y verificar
 // la firma del token.
 // En un proyecto real, debe guardarse en un archivo
 // de configuración seguro, como .env, y no directamente
 // dentro del código.
 private static $secreto = "clave-secreta-del-proyecto";
 // Convierte los datos a Base64 URL-safe.
 // Esta codificación permite utilizar los datos dentro
 // de una URL sin ciertos caracteres problemáticos.
 private static function base64UrlEncode($datos) {
 // base64_encode() codifica los datos en Base64.
 // strtr() reemplaza los caracteres + y / por - y _.
 // rtrim() elimina los signos = del final.
 return rtrim(strtr(base64_encode($datos), "+/", "-_"), "=");
 }
 // Convierte los datos codificados nuevamente a su
 // representación original.
 private static function base64UrlDecode($datos) {
 // Reemplaza - y _ por los caracteres utilizados
 // en la codificación Base64 tradicional.
 // Luego, base64_decode() decodifica los datos.
 return base64_decode(strtr($datos, "-_", "+/"));
 }
 // Genera un token JWT a partir de los datos del usuario.
 // El parámetro $payload contiene la información que
 // queremos incluir en el token.
 public static function generar($payload) {
 // Crea el encabezado del token.
 // "alg" indica el algoritmo utilizado para la firma.
 // "typ" indica que se trata de un token JWT.
 //
 // json_encode() convierte el arreglo en texto JSON.
 // Luego, base64UrlEncode() codifica ese texto.
 $header = self::base64UrlEncode(
 json_encode(["alg" => "HS256", "typ" => "JWT"])
 );
 // Agrega al payload la fecha y hora de vencimiento.
 // time() devuelve la fecha y hora actual en segundos.
 // 60 * 60 * 24 equivale a 24 horas.
 $payload["exp"] = time() + (60 * 60 * 24);
 // Convierte los datos del usuario y su vencimiento
 // a JSON y luego los codifica.
 $payloadCod = self::base64UrlEncode(json_encode($payload));
 // Genera la firma digital del token.
 // hash_hmac() utiliza el algoritmo SHA-256 y la
 // clave secreta para crear una firma.
 // La firma permite detectar si el token fue modificado.
 $firma = hash_hmac(
 "sha256",
 "$header.$payloadCod",
 self::$secreto,
 true
 );
 // Codifica la firma para incorporarla al token.
 $firmaCod = self::base64UrlEncode($firma);
 // Devuelve las tres partes del JWT separadas por puntos:
 // encabezado.payload.firma
 return "$header.$payloadCod.$firmaCod";
 }
 // Verifica un token recibido.
 // Si es válido y no venció, devuelve los datos del usuario.
 // Si no es válido, devuelve false.
 public static function verificar($token) {
 // Separa el token utilizando el punto como separador.
 // Un JWT debe tener tres partes.
 $partes = explode(".", $token);
 // Si no tiene exactamente tres partes,
 // el formato del token es incorrecto.
 if (count($partes) !== 3) return false;
 // Guarda cada parte del token en una variable:
 // encabezado, datos y firma.
 [$header, $payloadCod, $firmaCod] = $partes;
 // Calcula nuevamente la firma esperada utilizando
 // el encabezado, los datos y la clave secreta.
 $firmaEsperada = self::base64UrlEncode(
 hash_hmac(
 "sha256",
 "$header.$payloadCod",
 self::$secreto,
 true
 )
 );
 // Compara la firma calculada con la recibida.
 // hash_equals() realiza una comparación segura.
 // Si las firmas no coinciden, el token se rechaza.
 if (!hash_equals($firmaEsperada, $firmaCod)) return false;
 // Decodifica los datos del token y los convierte
 // nuevamente en un arreglo asociativo de PHP.
 $payload = json_decode(
 self::base64UrlDecode($payloadCod),
 true
 );
 // Comprueba si el token ya venció.
 // Si la fecha de vencimiento es anterior a la actual,
 // devuelve false.
if (
    !is_array($payload) ||
    !isset($payload["exp"]) ||
    !is_numeric($payload["exp"]) ||
    $payload["exp"] < time()
) {
    return false;
}

 // Si todo es correcto, devuelve los datos del usuario.
 return $payload;
 }
}
?>
