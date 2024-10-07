<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

class MedicineOperations {
    function handleCreateMedicine($data) {
        include 'db.php';
        $query = "INSERT INTO tbl_medicines (medicine_name, description, dosage_form, unit_of_measurement, quantity_in_stock, expiration_date) 
                  VALUES (:medicine_name, :description, :dosage_form, :unit_of_measurement, :quantity_in_stock, :expiration_date)";
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            ':medicine_name' => $data['medicine_name'],
            ':description' => $data['description'],
            ':dosage_form' => $data['dosage_form'],
            ':unit_of_measurement' => $data['unit_of_measurement'],
            ':quantity_in_stock' => $data['quantity_in_stock'],
            ':expiration_date' => $data['expiration_date']
        ]);
        
        if ($result) {
            return json_encode([
                'success' => true, 
                'message' => 'Medicine created successfully', 
                'id' => $pdo->lastInsertId(), 
                'operation' => 'createMedicine'
            ]);
        } else {
            return json_encode([
                'success' => false, 
                'message' => 'Failed to create medicine', 
                'operation' => 'createMedicine'
            ]);
        }
    }

    function handleUpdateMedicine($data) {
        include 'db.php';
        $query = "UPDATE tbl_medicines 
                  SET medicine_name = :medicine_name,
                      description = :description,
                      dosage_form = :dosage_form,
                      unit_of_measurement = :unit_of_measurement,
                      quantity_in_stock = :quantity_in_stock,
                      expiration_date = :expiration_date
                  WHERE medicine_id = :medicine_id";
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            ':medicine_id' => $data['medicine_id'],
            ':medicine_name' => $data['medicine_name'],
            ':description' => $data['description'],
            ':dosage_form' => $data['dosage_form'],
            ':unit_of_measurement' => $data['unit_of_measurement'],
            ':quantity_in_stock' => $data['quantity_in_stock'],
            ':expiration_date' => $data['expiration_date']
        ]);
        
        if ($result) {
            return json_encode([
                'success' => true, 
                'message' => 'Medicine updated successfully', 
                'operation' => 'updateMedicine'
            ]);
        } else {
            return json_encode([
                'success' => false, 
                'message' => 'Failed to update medicine', 
                'operation' => 'updateMedicine'
            ]);
        }
    }

    function handleUpdateMedicineStock($data) {
        include 'db.php';
        $query = "UPDATE tbl_medicines 
                  SET quantity_in_stock = quantity_in_stock + :quantity_change 
                  WHERE medicine_id = :medicine_id";
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            ':quantity_change' => $data['quantity_change'],
            ':medicine_id' => $data['medicine_id']
        ]);
        
        if ($result) {
            return json_encode([
                'success' => true, 
                'message' => 'Medicine stock updated successfully', 
                'operation' => 'updateMedicineStock'
            ]);
        } else {
            return json_encode([
                'success' => false, 
                'message' => 'Failed to update medicine stock', 
                'operation' => 'updateMedicineStock'
            ]);
        }
    }

    function handleGetMedicine($data) {
        include 'db.php';
        $query = "SELECT * FROM tbl_medicines WHERE medicine_id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $data['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return json_encode([
                'success' => true, 
                'data' => $result, 
                'operation' => 'getMedicine'
            ]);
        } else {
            return json_encode([
                'success' => false, 
                'message' => 'Medicine not found', 
                'operation' => 'getMedicine'
            ]);
        }
    }

    function handleDeleteMedicine($data) {
        include 'db.php';
        $query = "DELETE FROM tbl_medicines WHERE medicine_id = :id";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([':id' => $data['id']]);
        
        if ($result) {
            return json_encode([
                'success' => true, 
                'message' => 'Medicine deleted successfully', 
                'operation' => 'deleteMedicine'
            ]);
        } else {
            return json_encode([
                'success' => false, 
                'message' => 'Failed to delete medicine', 
                'operation' => 'deleteMedicine'
            ]);
        }
    }

    function handleListMedicines() {
        include 'db.php';
        $query = "SELECT * FROM tbl_medicines";
        $stmt = $pdo->query($query);
        $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return json_encode([
            'success' => true, 
            'data' => $medicines, 
            'operation' => 'listMedicines'
        ]);
    }
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$medOps = new MedicineOperations();

switch ($operation) {
    case "createMedicine":
        echo $medOps->handleCreateMedicine(json_decode($json, true));
        break;
    case "updateMedicine":
        echo $medOps->handleUpdateMedicine(json_decode($json, true));
        break;
    case "updateMedicineStock":
        echo $medOps->handleUpdateMedicineStock(json_decode($json, true));
        break;
    case "getMedicine":
        echo $medOps->handleGetMedicine(json_decode($json, true));
        break;
    case "deleteMedicine":
        echo $medOps->handleDeleteMedicine(json_decode($json, true));
        break;
    case "listMedicines":
        echo $medOps->handleListMedicines();
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