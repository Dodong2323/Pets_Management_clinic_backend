<?php
include 'headers.php';
include 'db.php';

class OwnerOperations {

    function createOwner($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_owners (First_name, Middle_name, Last_name, Age, Contact_number, Email_address, owner_address) 
                VALUES (:first_name, :middle_name, :last_name, :age, :contact_number, :email, :owner_address)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":first_name", $json["first_name"]);
        $stmt->bindParam(":middle_name", $json["middle_name"]);
        $stmt->bindParam(":last_name", $json["last_name"]);
        $stmt->bindParam(":age", $json["age"]);
        $stmt->bindParam(":contact_number", $json["contact_number"]);
        $stmt->bindParam(":email", $json["email"]);
        $stmt->bindParam(":owner_address", $json["owner_address"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function updateOwner($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_owners SET First_name = :first_name, Middle_name = :middle_name, Last_name = :last_name, Age = :age, 
                Contact_number = :contact_number, Email_address = :email, owner_address = :owner_address, UpdatedAt = NOW() 
                WHERE OwnerID = :OwnerID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":OwnerID", $json["OwnerID"]);
        $stmt->bindParam(":first_name", $json["first_name"]);
        $stmt->bindParam(":middle_name", $json["middle_name"]);
        $stmt->bindParam(":last_name", $json["last_name"]);
        $stmt->bindParam(":age", $json["age"]);
        $stmt->bindParam(":contact_number", $json["contact_number"]);
        $stmt->bindParam(":email", $json["email"]);
        $stmt->bindParam(":owner_address", $json["owner_address"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function deleteOwner($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "DELETE FROM tbl_owners WHERE OwnerID = :OwnerID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":OwnerID", $json["OwnerID"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function listOwners(){
        include 'db.php';
        $sql = "SELECT * FROM tbl_owners";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : 0;
    }
    function getOwnerDetails()
    {
        include 'db.php';
        $returnValue = [];
        $returnValue["listOwners"] = $this->listOwners();
        return json_encode($returnValue);

    }
    public function ListPetsByOwner($json) {
        $json = json_decode($json, true);
        include 'db.php';
        $sql = "SELECT * FROM tbl_pets WHERE owner_id = :owner_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":owner_id", $data["owner_id"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : 0;
    }
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$ownerOps = new OwnerOperations();
switch ($operation) {
    case "createOwner":
        echo $ownerOps->createOwner($json);
        break;
    case "updateOwner":
        echo $ownerOps->updateOwner($json);
        break;
    case "deleteOwner":
        echo $ownerOps->deleteOwner($json);
        break;
    case "ListPetsByOwner":
        echo $ownerOps->ListPetsByOwner($json);
        break;
    case "listOwners":
        echo $ownerOps->listOwners();
        break;
    
    // case "listOwners":
    //     echo $ownerOps->listOwners();
    //     break;
    default:
        break;
}