<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

class AppointmentOperations
{
    function handleCreateAppointment($data)
    {
        include 'db.php';
        $query = "INSERT INTO tbl_appointment (pet_id, owner_id, VetID, AppointmentDate, AppointmentTime, ReasonForVisit, Status) 
                  VALUES (:pet_id, :owner_id, :VetID, :AppointmentDate, :AppointmentTime, :ReasonForVisit, :Status)";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':pet_id' => $data['PetID'], // Update to match new column name
            ':owner_id' => $data['OwnerID'], // Update to match new column name
            ':VetID' => $data['VetID'],
            ':AppointmentDate' => $data['AppointmentDate'],
            ':AppointmentTime' => $data['AppointmentTime'],
            ':ReasonForVisit' => $data['ReasonForVisit'],
            ':Status' => $data['Status']
        ]);
        
        if ($stmt->rowCount() > 0) {
            return json_encode(['success' => true, 'message' => 'Appointment created successfully', 'id' => $pdo->lastInsertId(), 'operation' => 'createAppointment']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to create appointment', 'operation' => 'createAppointment']);
        }
    }

    function handleGetAppointment($data)
    {
        include 'db.php';
        $query = "SELECT * FROM tbl_appointment WHERE AppointmentID = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $data['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return json_encode(['success' => true, 'data' => $result, 'operation' => 'getAppointment']);
        } else {
            return json_encode(['success' => false, 'message' => 'Appointment not found', 'operation' => 'getAppointment']);
        }
    }

    function handleUpdateAppointment($data)
    {
        include 'db.php';
        $query = "UPDATE tbl_appointment SET pet_id = :pet_id, owner_id = :owner_id, VetID = :VetID, AppointmentDate = :AppointmentDate, 
                  AppointmentTime = :AppointmentTime, ReasonForVisit = :ReasonForVisit, Status = :Status WHERE AppointmentID = :AppointmentID";
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            ':pet_id' => $data['PetID'], // Update to match new column name
            ':owner_id' => $data['OwnerID'], // Update to match new column name
            ':VetID' => $data['VetID'],
            ':AppointmentDate' => $data['AppointmentDate'],
            ':AppointmentTime' => $data['AppointmentTime'],
            ':ReasonForVisit' => $data['ReasonForVisit'],
            ':Status' => $data['Status'],
            ':AppointmentID' => $data['AppointmentID']
        ]);
        
        if ($result) {
            return json_encode(['success' => true, 'message' => 'Appointment updated successfully', 'operation' => 'updateAppointment']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to update appointment', 'operation' => 'updateAppointment']);
        }
    }

    function handleDeleteAppointment($data)
    {
        include 'db.php';
        $query = "DELETE FROM tbl_appointment WHERE AppointmentID = :id";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([':id' => $data['id']]);
        
        if ($result) {
            return json_encode(['success' => true, 'message' => 'Appointment deleted successfully', 'operation' => 'deleteAppointment']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to delete appointment', 'operation' => 'deleteAppointment']);
        }
    }

    function handleListAppointments($data)
    {
        include 'db.php';
        $query = "SELECT * FROM tbl_appointment WHERE 1=1";
        $params = [];

        if (!empty($data['PetID'])) {
            $query .= " AND pet_id = :pet_id"; // Update to match new column name
            $params[':pet_id'] = $data['PetID']; // Update to match new column name
        }

        if (!empty($data['OwnerID'])) {
            $query .= " AND owner_id = :owner_id"; // Update to match new column name
            $params[':owner_id'] = $data['OwnerID']; // Update to match new column name
        }

        if (!empty($data['VetID'])) {
            $query .= " AND VetID = :VetID";
            $params[':VetID'] = $data['VetID'];
        }

        if (!empty($data['Status'])) {
            $query .= " AND Status = :Status";
            $params[':Status'] = $data['Status'];
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return json_encode(['success' => true, 'data' => $appointments, 'operation' => 'listAppointments']);
    }
}

// ... rest of the file remains unchanged ...

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$appointmentOps = new AppointmentOperations();
switch ($operation) {
    case "createAppointment":
        echo $appointmentOps->handleCreateAppointment(json_decode($json, true));
        break;
    case "getAppointment":
        echo $appointmentOps->handleGetAppointment(json_decode($json, true));
        break;
    case "updateAppointment":
        echo $appointmentOps->handleUpdateAppointment(json_decode($json, true));
        break;
    case "deleteAppointment":
        echo $appointmentOps->handleDeleteAppointment(json_decode($json, true));
        break;
    case "listAppointments":
        echo $appointmentOps->handleListAppointments(json_decode($json, true));
        break;
    default:
        break;
}

?>
