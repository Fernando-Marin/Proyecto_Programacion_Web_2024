<?php
class Alumno {
    private $pdo;

    public $numero_de_control;
    public $nombre;
    public $primer_apellido;
    public $segundo_apellido;
    public $semestre;
    public $id_usuario;
    public $id_carrera;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create() {
        $sql = "INSERT INTO alumno (Numero_de_control, Nombre, Primer_Apellido, Segundo_Apellido, Semestre, ID_usuario, ID_carrera)
                VALUES (:numero_de_control, :nombre, :primer_apellido, :segundo_apellido, :semestre, :id_usuario, :id_carrera)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':numero_de_control' => $this->numero_de_control,
            ':nombre' => $this->nombre,
            ':primer_apellido' => $this->primer_apellido,
            ':segundo_apellido' => $this->segundo_apellido,
            ':semestre' => $this->semestre,
            ':id_usuario' => $this->id_usuario,
            ':id_carrera' => $this->id_carrera
        ]);
    }

    public function read($numero_de_control) {
        $sql = "SELECT * FROM alumno WHERE Numero_de_control = :numero_de_control";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':numero_de_control' => $numero_de_control]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $sql = "UPDATE alumno SET Nombre = :nombre, Primer_Apellido = :primer_apellido, Segundo_Apellido = :segundo_apellido, Semestre = :semestre, ID_usuario = :id_usuario, ID_carrera = :id_carrera
                WHERE Numero_de_control = :numero_de_control";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':numero_de_control' => $this->numero_de_control,
            ':nombre' => $this->nombre,
            ':primer_apellido' => $this->primer_apellido,
            ':segundo_apellido' => $this->segundo_apellido,
            ':semestre' => $this->semestre,
            ':id_usuario' => $this->id_usuario,
            ':id_carrera' => $this->id_carrera
        ]);
    }

    public function delete($numero_de_control) {
        $sql = "DELETE FROM alumno WHERE Numero_de_control = :numero_de_control";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':numero_de_control' => $numero_de_control]);
    }
}
?>
