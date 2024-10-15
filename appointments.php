<?php
include 'db.php';
include 'headers.php';


class AppointmentOperations
{

    function createAppointment($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_appointment (pet_id, owner_id, vet_id, ServiceID, AppointmentDate, AppointmentTime, ReasonForVisit, Status) 
                VALUES (:pet_id, :owner_id, :vet_id, :ServiceID, :AppointmentDate, :AppointmentTime, :ReasonForVisit, :Status)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":pet_id", $json["pet_id"]);
        $stmt->bindParam(":owner_id", $json["owner_id"]);
        $stmt->bindParam(":vet_id", $json["vet_id"]);
        $stmt->bindParam(":ServiceID", $json["ServiceID"]);
        $stmt->bindParam(":AppointmentDate", $json["AppointmentDate"]);
        $stmt->bindParam(":AppointmentTime", $json["AppointmentTime"]);
        $stmt->bindParam(":ReasonForVisit", $json["ReasonForVisit"]);
        $stmt->bindParam(":Status", $json["Status"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    

    function getAppointment($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tbl_appointment WHERE AppointmentID = :AppointmentID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":AppointmentID", $json["AppointmentID"]);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? json_encode($result) : json_encode([]);
    }


    function updateAppointment($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_appointment SET pet_id = :pet_id, owner_id = :owner_id, vet_id = :vet_id, ServiceID = :ServiceID, AppointmentDate = :AppointmentDate,
                AppointmentTime = :AppointmentTime, ReasonForVisit = :ReasonForVisit, Status = :Status, UpdatedAt = NOW() 
                WHERE AppointmentID = :AppointmentID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":AppointmentID", $json["AppointmentID"]);
        $stmt->bindParam(":pet_id", $json["pet_id"]);
        $stmt->bindParam(":owner_id", $json["owner_id"]);
        $stmt->bindParam(":VetID", $json["VetID"]);
        $stmt->bindParam(":AppointmentDate", $json["AppointmentDate"]);
        $stmt->bindParam(":AppointmentTime", $json["AppointmentTime"]);
        $stmt->bindParam(":ReasonForVisit", $json["ReasonForVisit"]);
        $stmt->bindParam(":Status", $json["Status"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function deleteAppointment($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "DELETE FROM tbl_appointment WHERE AppointmentID = :AppointmentID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":AppointmentID", $json["AppointmentID"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function listAppointments(){
        include 'db.php';
        $sql = "SELECT pet_name, First_name, Middle_name, Last_name, user_firstname, user_lastname, ServiceName, AppointmentDate, AppointmentTime, ReasonForVisit, Status, b.CreatedAt, b.UpdatedAt
                FROM tbl_pets AS a 
                INNER JOIN tbl_appointment AS b ON b.pet_id = a.pet_id
                INNER JOIN tbl_owners AS c ON c.owner_id = b.owner_id
                INNER JOIN tbl_veterinarians AS d ON d.vet_id = b.vet_id
                INNER JOIN users AS e ON e.UserID = d.user_id
                INNER JOIN tbl_services AS f ON f.ServiceID = b.ServiceID";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : 0;
    }
}

// ... rest of the file remains unchanged ...

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$appointmentOps = new AppointmentOperations();
switch ($operation) {
    case "createAppointment":
        echo $appointmentOps->createAppointment($json);
        break;
    case "getAppointment":
        echo $appointmentOps->getAppointment($json);
        break;
    case "updateAppointment":
        echo $appointmentOps->updateAppointment($json);
        break;
    case "deleteAppointment":
        echo $appointmentOps->deleteAppointment($json);
        break;
    case "listAppointments":
        echo $appointmentOps->listAppointments($json);
        break;
    default:
        break;
}

?>
