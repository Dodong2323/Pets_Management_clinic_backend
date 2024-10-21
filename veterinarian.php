<?php
include 'headers.php';
include 'db.php';

class VeterinarianOperations {
    function handleCreateVeterinarian($json) {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_veterinarians (user_id, license_number, specialization, years_of_experience, availability) 
                  VALUES (:user_id, :license_number, :specialization, :years_of_experience, :availability)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":user_id", $json["user_id"]);
        $stmt->bindParam(":license_number", $json["license_number"]);
        $stmt->bindParam(":specialization", $json["specialization"]);
        $stmt->bindParam(":years_of_experience", $json["years_of_experience"]);
        $stmt->bindParam(":availability", $json["availability"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }


    function handleUpdateVeterinarian($json) {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_veterinarians SET user_id = :user_id, license_number = :license_number, specialization = :specialization, years_of_experience = :years_of_experience, availability = :availability WHERE vet_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":user_id", $json["user_id"]);
        $stmt->bindParam(":license_number", $json["license_number"]);
        $stmt->bindParam(":specialization", $json["specialization"]);
        $stmt->bindParam(":years_of_experience", $json["years_of_experience"]);
        $stmt->bindParam(":availability", $json["availability"]);
        $stmt->bindParam(":id", $json['id']);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }


    function handleGetVeterinarian($json) {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tbl_veterinarians WHERE vet_id = :vet_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":vet_id", $json["vet_id"]);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? json_encode($result) : json_encode([]);
    }

    function handleDeleteVeterinarian($json) {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "DELETE FROM tbl_veterinarians WHERE vet_id = :vet_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":vet_id", $json["vet_id"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

     
    function handleListVeterinarians() {
        include 'db.php';
        $sql = "SELECT * FROM tbl_veterinarians";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $stmt->rowCount() > 0 ? json_encode($result) : json_encode([]);
    }
        
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$vetOps = new VeterinarianOperations();

switch ($operation) {
    case "createVeterinarian":
        echo $vetOps->handleCreateVeterinarian($json);
        break;
    case "updateVeterinarian":
        echo $vetOps->handleUpdateVeterinarian($json);
        break;
    case "getVeterinarian":
        echo $vetOps->handleGetVeterinarian($json);
        break;
    case "deleteVeterinarian":
        echo $vetOps->handleDeleteVeterinarian($json);
        break;
    case "listVeterinarians":
        echo $vetOps->handleListVeterinarians();
        break;
    default:
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid operation', 
            'operation' => $operation
        ]);
        break;
}
?>