<?php

include 'db.php';
include 'headers.php';

class AdoptionOperations
{

    function createAdoption($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_adoptions (pet_id, AdopterID, AdoptionDate, Status, Notes) VALUES (:pet_id, :AdopterID, :AdoptionDate, :Status, :Notes)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":AdopterID", $json["AdopterID"]);
        $stmt->bindParam(":AdoptionDate", $json["AdoptionDate"]);
        $stmt->bindParam(":Status", $json["Status"]);
        $stmt->bindParam(":Notes", $json["Notes"]);
        $stmt->bindParam(":pet_id", $json["pet_id"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;

    }

    function getAdoption($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tbl_adoptions WHERE AdoptionID = :AdoptionID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":AdoptionID", $json["AdoptionID"]);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? json_encode($result) : json_encode([]);
    }


    function updateAdoption($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_adoptions SET pet_id = :pet_id, AdopterID = :AdopterID, AdoptionDate = :AdoptionDate, Status = :Status, Notes = :Notes WHERE AdoptionID = :AdoptionID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":AdoptionID", $json["AdoptionID"]);
        $stmt->bindParam(":AdopterID", $json["AdopterID"]);
        $stmt->bindParam(":AdoptionDate", $json["AdoptionDate"]);
        $stmt->bindParam(":Status", $json["Status"]);
        $stmt->bindParam(":Notes", $json["Notes"]);
        $stmt->bindParam(":pet_id", $json["pet_id"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function deleteAdoption($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "DELETE FROM tbl_adoptions WHERE AdoptionID = :AdoptionID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":AdoptionID", $json["AdoptionID"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function listAdoptions() {
        include 'db.php';
        $sql = "SELECT a.pet_name, c.First_name, c.Middle_name, c.Last_name, b.AdoptionDate, b.Status, b.Notes, b.CreatedAt, b.UpdatedAt
                FROM tbl_pets AS a
                INNER JOIN tbl_adoptions AS b ON b.pet_id = a.pet_id
                INNER JOIN tbl_owners AS c ON c.owner_id = b.AdopterID";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : 0;
    }
    
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$adoptionOps = new AdoptionOperations();
switch ($operation) {
    case "createAdoption":
        echo $adoptionOps->createAdoption($json);
        break;
    case "getAdoption":
        echo $adoptionOps->getAdoption($json);
        break;
    case "updateAdoption":
        echo $adoptionOps->updateAdoption($json);
        break;
    case "deleteAdoption":
        echo $adoptionOps->deleteAdoption($json);
        break;
    case "listAdoptions":
        echo $adoptionOps->listAdoptions();
        break;
    default:
        break;
}

?>
