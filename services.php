<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

class ServicesAPI
{

    function addService($json)
    {
        //addService {"ServiceName":"Check-up","Description":"Routine pet check-up","Price":50.00,"Duration":30}
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_services (ServiceName, Description, Price, Duration) 
                VALUES (:ServiceName, :Description, :Price, :Duration)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ServiceName", $json["ServiceName"]);
        $stmt->bindParam(":Description", $json["Description"]);
        $stmt->bindParam(":Price", $json["Price"]);
        $stmt->bindParam(":Duration", $json["Duration"]);
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
      return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
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