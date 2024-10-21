<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

class PetAdoption {
    
    function addPetAdoption($json) {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_petadoption (petName, species_id, breed_id, age, gender, 
                colour, description, CreatedAt, UpdatedAt) 
                VALUES (:petName, :species_id, :breed_id, :age, :gender, :colour, 
                :description)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":petName", $json["petName"]);
        $stmt->bindParam(":species_id", $json["species_id"]);
        $stmt->bindParam(":breed_id", $json["breed_id"]);
        $stmt->bindParam(":age", $json["age"]);
        $stmt->bindParam(":gender", $json["gender"]);
        $stmt->bindParam(":colour", $json["colour"]);
        $stmt->bindParam(":description", $json["description"]);
        $stmt->bindParam(":CreatedAt", date("Y-m-d H:i:s"));
        $stmt->bindParam(":UpdatedAt", date("Y-m-d H:i:s"));
        $stmt->execute();
        
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    
    function updatePetAdoption($json) {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_petadoption 
                SET petName = :petName, 
                    species_id = :species_id,
                    breed_id = :breed_id,
                    age = :age,
                    gender = :gender,
                    colour = :colour,
                    description = :description,
                    UserID = :UserID
                WHERE petId = :petId";
                
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":petId", $json["petId"]);
        $stmt->bindParam(":petName", $json["petName"]);
        $stmt->bindParam(":species_id", $json["species_id"]);
        $stmt->bindParam(":breed_id", $json["breed_id"]);
        $stmt->bindParam(":age", $json["age"]);
        $stmt->bindParam(":gender", $json["gender"]);
        $stmt->bindParam(":colour", $json["colour"]);
        $stmt->bindParam(":description", $json["description"]);
        $stmt->bindParam(":UserID", $json["UserID"]);
        $stmt->execute();
        
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    
    function deletePetAdoption($json) {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "DELETE FROM tbl_petadoption WHERE petId = :petId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":petId", $json["petId"]);
        $stmt->execute();
        
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    
    function getPetAdoptionById($json) {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "SELECT pa.*, s.species_name, b.breed_name, u.Username
                FROM tbl_petadoption pa
                LEFT JOIN tbl_species s ON s.species_id = pa.species_id
                LEFT JOIN tbl_breeds b ON b.breed_id = pa.breed_id
                LEFT JOIN users u ON u.UserID = pa.UserID
                WHERE pa.petId = :petId";
                
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":petId", $json["petId"]);
        $stmt->execute();
        
        return $stmt->rowCount() > 0 ? json_encode($stmt->fetch(PDO::FETCH_ASSOC)) : json_encode([]);
    }
    
    function getAllPetAdoptions() {
        include 'db.php';
        $sql = "SELECT pa.*, s.species_name, b.breed_name, u.Username
                FROM tbl_petadoption pa
                LEFT JOIN tbl_species s ON s.species_id = pa.species_id
                LEFT JOIN tbl_breeds b ON b.breed_id = pa.breed_id
                LEFT JOIN users u ON u.UserID = pa.UserID";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : json_encode([]);
    }
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";
$petAdoption = new PetAdoption();

switch ($operation) {
    case "addPetAdoption":
        echo $petAdoption->addPetAdoption($json);
        break;
    case "updatePetAdoption":
        echo $petAdoption->updatePetAdoption($json);
        break;
    case "deletePetAdoption":
        echo $petAdoption->deletePetAdoption($json);
        break;
    case "getPetAdoptionById":
        echo $petAdoption->getPetAdoptionById($json);
        break;
    case "getAllPetAdoptions":
        echo $petAdoption->getAllPetAdoptions();
        break;
    default:
        echo "Operation '" . $operation . "' not found";
        break;
}