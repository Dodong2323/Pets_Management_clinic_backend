<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

class AdoptionOperations
{
    function handleCreateAdoption($data)
    {
        include 'db.php';
        $query = "INSERT INTO tbl_adoptions (PetID, AdopterID, AdoptionDate, Status, Notes) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iisss", $data['PetID'], $data['AdopterID'], $data['AdoptionDate'], 
                          $data['Status'], $data['Notes']);
        
        if ($stmt->execute()) {
            return json_encode(['success' => true, 'message' => 'Adoption created successfully', 'id' => $stmt->insert_id, 'operation' => 'createAdoption']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to create adoption', 'operation' => 'createAdoption']);
        }
    }

    function handleGetAdoption($data)
    {
        include 'db.php';
        $query = "SELECT * FROM tbl_adoptions WHERE AdoptionID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $data['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return json_encode(['success' => true, 'data' => $result->fetch_assoc(), 'operation' => 'getAdoption']);
        } else {
            return json_encode(['success' => false, 'message' => 'Adoption not found', 'operation' => 'getAdoption']);
        }
    }

    function handleUpdateAdoption($data)
    {
        include 'db.php';
        $query = "UPDATE tbl_adoptions SET PetID = ?, AdopterID = ?, AdoptionDate = ?, Status = ?, Notes = ? 
                  WHERE AdoptionID = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iisssi", $data['PetID'], $data['AdopterID'], $data['AdoptionDate'], 
                          $data['Status'], $data['Notes'], $data['AdoptionID']);
        
        if ($stmt->execute()) {
            return json_encode(['success' => true, 'message' => 'Adoption updated successfully', 'operation' => 'updateAdoption']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to update adoption', 'operation' => 'updateAdoption']);
        }
    }

    function handleDeleteAdoption($data)
    {
        include 'db.php';
        $query = "DELETE FROM tbl_adoptions WHERE AdoptionID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $data['id']);
        
        if ($stmt->execute()) {
            return json_encode(['success' => true, 'message' => 'Adoption deleted successfully', 'operation' => 'deleteAdoption']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to delete adoption', 'operation' => 'deleteAdoption']);
        }
    }

    function handleListAdoptions($data)
    {
        include 'db.php';
        $query = "SELECT * FROM tbl_adoptions WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($data['PetID'])) {
            $query .= " AND PetID = ?";
            $params[] = $data['PetID'];
            $types .= "i";
        }

        if (!empty($data['AdopterID'])) {
            $query .= " AND AdopterID = ?";
            $params[] = $data['AdopterID'];
            $types .= "i";
        }

        if (!empty($data['Status'])) {
            $query .= " AND Status = ?";
            $params[] = $data['Status'];
            $types .= "s";
        }

        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $adoptions = [];
        while ($row = $result->fetch_assoc()) {
            $adoptions[] = $row;
        }
        
        return json_encode(['success' => true, 'data' => $adoptions, 'operation' => 'listAdoptions']);
    }
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$adoptionOps = new AdoptionOperations();
switch ($operation) {
    case "createAdoption":
        echo $adoptionOps->handleCreateAdoption(json_decode($json, true));
        break;
    case "getAdoption":
        echo $adoptionOps->handleGetAdoption(json_decode($json, true));
        break;
    case "updateAdoption":
        echo $adoptionOps->handleUpdateAdoption(json_decode($json, true));
        break;
    case "deleteAdoption":
        echo $adoptionOps->handleDeleteAdoption(json_decode($json, true));
        break;
    case "listAdoptions":
        echo $adoptionOps->handleListAdoptions(json_decode($json, true));
        break;
    default:
        break;
}

?>
