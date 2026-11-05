<?php
// Indicamos que este archivo contiene el modelo Ticket.
// Incluimos la clase Model, que permite acceder
// a la conexión con la base de datos.
require_once __DIR__ . "/../core/Model.php";
// Incluimos la biblioteca FPDF, que permite generar archivos PDF.
require_once __DIR__ . "/../utils/fpdf/fpdf.php";
// Creamos la clase Ticket.
// Al extender Model, puede utilizar la conexión $this->pdo.
class Ticket extends Model {
 // Genera un archivo PDF con los datos de una compra.
 // Recibe el ID de la compra, sus datos generales
 // y el detalle de los productos comprados.
 public function generarPdf($idCompra, $compra, $detalle) {
 // Creamos un nuevo documento PDF.
 $pdf = new FPDF();
 // Agregamos una página al documento.
 $pdf->AddPage();
 // Configuramos la fuente Arial, en negrita y tamaño 16.
 $pdf->SetFont("Arial", "B", 16);
 // Escribimos el título del ticket y lo centramos.
 // 0: ancho disponible.
 // 10: altura de la celda.
 // 0: sin borde.
 // 1: pasa a la siguiente línea.
 // C: centra el texto.
 $pdf->Cell(0, 10, "Ticket de compra #$idCompra", 0, 1, "C");
 // Configuramos Arial, estilo normal y tamaño 12.
 $pdf->SetFont("Arial", "", 12);
 // Escribimos la fecha de la compra.
 // Concatenamos el texto "Fecha: " con la fecha almacenada.
 $pdf->Cell(0, 10, "Fecha: " . $compra["fecha"], 0, 1);
 // Agregamos un espacio vertical de 5 unidades.
 $pdf->Ln(5);
 // Recorremos los productos que forman parte de la compra.
 foreach ($detalle as $item) {
 // Construimos una línea con el nombre del producto,
 // la cantidad comprada y el subtotal.
 // El punto (.) permite unir cadenas de texto.
 $linea = $item["nombre"] . " x" . $item["cantidad"] . " -> $" .
$item["subtotal"];
 // Escribimos la información del producto en el PDF.
 // 0: ancho disponible.
 // 8: altura de la celda.
 // 0: sin borde.
 // 1: pasa a la siguiente línea.
 $pdf->Cell(0, 8, $linea, 0, 1);
 } // Finaliza el recorrido de los productos.
 // Agregamos un espacio vertical de 5 unidades.
 $pdf->Ln(5);
 // Configuramos Arial en negrita y tamaño 12.
 $pdf->SetFont("Arial", "B", 12);
 // Escribimos el total de la compra.
 $pdf->Cell(0, 10, "Total: $" . $compra["total"], 0, 1);
 // Creamos un número identificador para el ticket.
 // "TCK-" es el prefijo.
 // str_pad completa el ID con ceros a la izquierda
 // hasta alcanzar una longitud de 6 caracteres.
 // Por ejemplo, el ID 25 se convierte en TCK-000025.
 $numeroTicket = "TCK-" . str_pad($idCompra, 6, "0", STR_PAD_LEFT);
 // Construimos la ruta donde se guardará el archivo PDF.
 // __DIR__ representa la carpeta donde está este archivo PHP.
 $ruta = __DIR__ . "/../public/uploads/tickets/$numeroTicket.pdf";
 // Guardamos el PDF en un archivo del servidor.
 // La letra "F" indica que se guarda en el disco.
 // No se envía directamente al navegador.
 $pdf->Output("F", $ruta);
 // Preparamos una consulta SQL para guardar los datos del ticket.
 // Se registra su número, el ID de la compra,
 // la ruta del archivo y la fecha actual.
 $sql = "INSERT INTO tickets (numero_ticket, id_compra, pdf_path, fecha)
 VALUES (:numero, :id_compra, :pdf_path, NOW())";
 // Preparamos la consulta utilizando la conexión a la base de datos.
 $stmt = $this->pdo->prepare($sql);
 // Ejecutamos la consulta y reemplazamos los parámetros
 // por los valores correspondientes.
 $stmt->execute([
 // Número identificador del ticket.
 "numero" => $numeroTicket,
 // ID de la compra a la que pertenece el ticket.
 "id_compra" => $idCompra,
 // Ruta relativa del archivo PDF.
 "pdf_path" => "uploads/tickets/$numeroTicket.pdf"
 ]);
 // Devolvemos el número del ticket generado.
 return $numeroTicket;
 } // Finaliza el método generarPdf.
 // Busca el ticket asociado a una compra.
 // Recibe el ID de la compra que queremos consultar.
 public function getByCompra($idCompra) {
 // Preparamos una consulta para buscar el ticket
 // cuyo id_compra coincida con el valor recibido.
 $sql = "SELECT * FROM tickets WHERE id_compra = :id_compra";
 // Preparamos la consulta SQL.
 $stmt = $this->pdo->prepare($sql);
 // Ejecutamos la consulta indicando el ID de la compra.
 $stmt->execute(["id_compra" => $idCompra]);
 // Devolvemos el registro encontrado como arreglo asociativo.
 // Si no existe un ticket para esa compra, devuelve false.
 return $stmt->fetch(PDO::FETCH_ASSOC);
 } // Finaliza el método getByCompra.
} // Finaliza la clase Ticket.
