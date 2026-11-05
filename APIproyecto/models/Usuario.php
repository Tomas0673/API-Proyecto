```php
<?php

require_once __DIR__ . "/../core/Model.php";

class Usuario extends Model
{

    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "email" => $email
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT id_usuario, nombre, email, rol, creado
                FROM usuarios
                WHERE id_usuario = :id_usuario";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "id_usuario" => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $email, $contraseñaHash)
    {
        $sql = "INSERT INTO usuarios
                (nombre, email, contraseña, rol, creado)
                VALUES (:nombre, :email, :password, 'user', NOW())";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "nombre" => $nombre,
            "email" => $email,
            "password" => $contraseñaHash
        ]);
    }
    public function todos()
    {
        // Elegimos las columnas a mano: NUNCA devolvemos la contraseña ni el token
        $sql = "SELECT id_usuario, nombre, email, rol, creado FROM usuarios ORDER BY id_usuario";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>