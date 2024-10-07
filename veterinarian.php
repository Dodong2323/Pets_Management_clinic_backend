<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

class VeterinarianOperations {
    function handleCreateVeterinarian($data) {
        include 'db.php';
        $query = "INSERT INTO tbl_veterinarians (user_id, license_number, specialization, years_of_experience, availability) 
                  VALUES (:user_id, :license_number, :specialization, :years_of_experience, :availability)";
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            ':user_id' => $data['user_id'],
            ':license_number' => $data['license_number'],
            ':specialization' => $data['specialization'],
            ':years_of_experience' => $data['years_of_experience'],
            ':availability' => $data['availability']
        ]);
        
        if ($result) {
            return json_encode([
                'success' => true, 
                'message' => 'Veterinarian created successfully', 
                'id' => $pdo->lastInsertId(), 
                'operation' => 'createVeterinarian'
            ]);
        } else {
            return json_encode([
                'success' => false, 
                'message' => 'Failed to create veterinarian', 
                'operation' => 'createVeterinarian'
            ]);
        }
    }

    function handleUpdateVeterinarian($data) {
        include 'db.php';
        $query = "UPDATE tbl_veterinarians 
                  SET license_number = :license_number, 
                      specialization = :specialization, 
                      years_of_experience = :years_of_experience, 
                      availability = :availability 
                  WHERE vet_id = :vet_id";
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            ':vet_id' => $data['vet_id'],
            ':license_number' => $data['license_number'],
            ':specialization' => $data['specialization'],
            ':years_of_experience' => $data['years_of_experience'],
            ':availability' => $data['availability']
        ]);
        
        if ($result) {
            return json_encode([
                'success' => true, 
                'message' => 'Veterinarian updated successfully', 
                'operation' => 'updateVeterinarian'
            ]);
        } else {
            return json_encode([
                'success' => false, 
                'message' => 'Failed to update veterinarian', 
                'operation' => 'updateVeterinarian'
            ]);
        }
    }

    function handleGetVeterinarian($data) {
        include 'db.php';
        $query = "SELECT v.*, u.FirstName, u.LastName, u.Email 
                  FROM tbl_veterinarians v 
                  JOIN users u ON v.user_id = u.UserID 
                  WHERE v.vet_id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $data['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return json_encode([
                'success' => true, 
                'data' => $result, 
                'operation' => 'getVeterinarian'
            ]);
        } else {
            return json_encode([
                'success' => false, 
                'message' => 'Veterinarian not found', 
                'operation' => 'getVeterinarian'
            ]);
        }
    }

    function handleDeleteVeterinarian($data) {
        include 'db.php';
        $query = "DELETE FROM tbl_veterinarians WHERE vet_id = :id";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([':id' => $data['id']]);
        
        if ($result) {
            return json_encode([
                'success' => true, 
                'message' => 'Veterinarian deleted successfully', 
                'operation' => 'deleteVeterinarian'
            ]);
        } else {
            return json_encode([
                'success' => false, 
                'message' => 'Failed to delete veterinarian', 
                'operation' => 'deleteVeterinarian'
            ]);
        }
    }

    function handleListVeterinarians() {
        include 'db.php';
        $query = "SELECT v.*, u.FirstName, u.LastName, u.Email 
                  FROM tbl_veterinarians v 
                  JOIN users u ON v.user_id = u.UserID";
        $stmt = $pdo->query($query);
        $vets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return json_encode([
            'success' => true, 
            'data' => $vets, 
            'operation' => 'listVeterinarians'
        ]);
    }
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$vetOps = new VeterinarianOperations();

switch ($operation) {
    case "createVeterinarian":
        echo $vetOps->handleCreateVeterinarian(json_decode($json, true));
        break;
    case "updateVeterinarian":
        echo $vetOps->handleUpdateVeterinarian(json_decode($json, true));
        break;
    case "getVeterinarian":
        echo $vetOps->handleGetVeterinarian(json_decode($json, true));
        break;
    case "deleteVeterinarian":
        echo $vetOps->handleDeleteVeterinarian(json_decode($json, true));
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