<?php

include 'headers.php';
include 'db.php';

class ServicesAPI
{
        // Example JSON: {"ServiceName":"Check-up","Description":"Routine pet check-up","Price":50.00,"Duration":30, "UpdatedAt":"2024-10-19 14:23:55"}
    function addService($json)    {        
        include 'db.php';
        $json = json_decode($json, true);
        $createdAt = date("Y-m-d H:i:s");
        $updatedAt = isset($json["UpdatedAt"]) ? $json["UpdatedAt"] : date("Y-m-d H:i:s");
        $sql = "INSERT INTO tbl_services (ServiceName, Description, Price, Duration, CreatedAt, UpdatedAt) 
                VALUES (:ServiceName, :Description, :Price, :Duration, :CreatedAt, :UpdatedAt)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ServiceName", $json["ServiceName"]);
        $stmt->bindParam(":Description", $json["Description"]);
        $stmt->bindParam(":Price", $json["Price"]);
        $stmt->bindParam(":Duration", $json["Duration"]);
        $stmt->bindParam(":CreatedAt", $createdAt);
        $stmt->bindParam(":UpdatedAt", $updatedAt);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    
    function updateService($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_services 
                SET ServiceName = :ServiceName, Description = :Description, 
                    Price = :Price, Duration = :Duration 
                WHERE ServiceID = :ServiceID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ServiceName", $json["ServiceName"]);
        $stmt->bindParam(":Description", $json["Description"]);
        $stmt->bindParam(":Price", $json["Price"]);
        $stmt->bindParam(":Duration", $json["Duration"]);
        $stmt->bindParam(":ServiceID", $json["ServiceID"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function deleteService($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "DELETE FROM tbl_services WHERE ServiceID = :ServiceID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ServiceID", $json["ServiceID"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function getServiceDetails()
  {
      include 'db.php';
      $sql = "SELECT * FROM tbl_services";
      $stmt = $conn->prepare($sql);
      $stmt->execute();
      return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : 0;
  }

    function getServiceById($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tbl_services WHERE ServiceID = :ServiceID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ServiceID", $json["ServiceID"]);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? json_encode($result) : json_encode([]);
    }
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";
$servicesAPI = new ServicesAPI();

switch ($operation) {
    case "addService":
        echo $servicesAPI->addService($json);
        break;
    case "updateService":
        echo $servicesAPI->updateService($json);
        break;
    case "deleteService":
        echo $servicesAPI->deleteService($json);
        break;
    case "getServiceDetails":
        echo $servicesAPI->getServiceDetails();
        break;
    case "getServiceById":
        echo $servicesAPI->getServiceById($json);
        break;
    default:
        echo json_encode(["error" => "Invalid operation"]);
        break;
}