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

    function requestAdoption($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_adoptions (petId, UserID, Status, Reason ) 
        VALUES (:petId, :UserID, :Status, :Reason)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":UserID", $json["UserID"]);
        $stmt->bindParam(":Status", $json["Status"]);
        $stmt->bindParam(":Reason", $json["Reason"]);
        $stmt->bindParam(":petId", $json["petId"]);

        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
     
   
    }

    function updateToAproved($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_adoptions SET Status = :Status WHERE AdoptionID = :AdoptionID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":AdoptionID", $json["AdoptionID"]);
        $stmt->bindParam(":Status", $json["Status"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function updateToReview($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_adoptions SET Status = :Status WHERE AdoptionID = :AdoptionID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":AdoptionID", $json["AdoptionID"]);
        $stmt->bindParam(":Status", $json["Status"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function PendingAdoptionRequests()
    {
        include 'db.php';
        $sql = "SELECT * FROM tbl_adoptions WHERE Status = 'Pending'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : json_encode([]);    
    }


}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$adoptionOps = new AdoptionOperations();
switch ($operation) {
    case "createAdoption":
        echo $adoptionOps->createAdoption($json);
        break;
    case "requestAdoption":
        echo $adoptionOps->requestAdoption($json);
        break;
    case "updateToAproved":
        echo $adoptionOps->updateToAproved($json);
        break;
    case "updateToReview":
        echo $adoptionOps->updateToReview($json);
        break;
    case "PendingAdoptionRequests":
        echo $adoptionOps->PendingAdoptionRequests();
        break;
    default:
        break;
}

?>
