<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

class ReportOperations
{
    function handleCreateReport($data)
    {
        include 'db.php';
        $query = "INSERT INTO tbl_reports (report_type, report_title, report_description, generated_by, generated_date, report_data, file_path) 
                  VALUES (:report_type, :report_title, :report_description, :generated_by, NOW(), :report_data, :file_path)";
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            ':report_type' => $data['report_type'],
            ':report_title' => $data['report_title'],
            ':report_description' => $data['report_description'] ?? null,
            ':generated_by' => $data['generated_by'],
            ':report_data' => $data['report_data'] ?? null,
            ':file_path' => $data['file_path'] ?? null
        ]);
        
        if ($result) {
            return json_encode(['success' => true, 'message' => 'Report created successfully', 'id' => $pdo->lastInsertId(), 'operation' => 'createReport']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to create report', 'operation' => 'createReport']);
        }
    }

    function handleGetReport($data)
    {
        include 'db.php';
        $query = "SELECT * FROM tbl_reports WHERE report_id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $data['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return json_encode(['success' => true, 'data' => $result, 'operation' => 'getReport']);
        } else {
            return json_encode(['success' => false, 'message' => 'Report not found', 'operation' => 'getReport']);
        }
    }

    function handleUpdateReport($data)
    {
        include 'db.php';
        $query = "UPDATE tbl_reports SET report_type = :report_type, report_title = :report_title, 
                  report_description = :report_description, report_data = :report_data, file_path = :file_path 
                  WHERE report_id = :report_id";
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            ':report_type' => $data['report_type'],
            ':report_title' => $data['report_title'],
            ':report_description' => $data['report_description'] ?? null,
            ':report_data' => $data['report_data'] ?? null,
            ':file_path' => $data['file_path'] ?? null,
            ':report_id' => $data['report_id']
        ]);
        
        if ($result) {
            return json_encode(['success' => true, 'message' => 'Report updated successfully', 'operation' => 'updateReport']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to update report', 'operation' => 'updateReport']);
        }
    }

    function handleDeleteReport($data)
    {
        include 'db.php';
        $query = "DELETE FROM tbl_reports WHERE report_id = :id";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([':id' => $data['id']]);
        
        if ($result) {
            return json_encode(['success' => true, 'message' => 'Report deleted successfully', 'operation' => 'deleteReport']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to delete report', 'operation' => 'deleteReport']);
        }
    }

    function handleListReports()
    {
        include 'db.php';
        $query = "SELECT * FROM tbl_reports ORDER BY generated_date DESC";
        $stmt = $pdo->query($query);
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return json_encode(['success' => true, 'data' => $reports, 'operation' => 'listReports']);
    }
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$reportOps = new ReportOperations();

switch ($operation) {
    case "createReport":
        echo $reportOps->handleCreateReport(json_decode($json, true));
        break;
    case "getReport":
        echo $reportOps->handleGetReport(json_decode($json, true));
        break;
    case "updateReport":
        echo $reportOps->handleUpdateReport(json_decode($json, true));
        break;
    case "deleteReport":
        echo $reportOps->handleDeleteReport(json_decode($json, true));
        break;
    case "listReports":
        echo $reportOps->handleListReports();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid operation', 'operation' => $operation]);
        break;
}

?>
