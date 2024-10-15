<?php

include 'headers.php';
include 'db.php';

class ReportOperations
{

    function handleCreateReport($json) {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_reports (report_type, report_title, report_description, generated_by, generated_date, report_data)
                VALUES (:report_type, :report_title, :report_description, :generated_by, NOW(), :report_data)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':report_type', $json['report_type']);
        $stmt->bindParam(':report_title', $json['report_title']);
        $stmt->bindParam(':report_description', $json['report_description']);
        $stmt->bindParam(':generated_by', $json['generated_by']);
        $stmt->bindParam(':report_data', $json['report_data']);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }  

    function handleGetReport($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tbl_reports WHERE report_id = :report_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":report_id", $json["report_id"]);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? json_encode($result) : json_encode([]);
    }

    
    function handleUpdateReport($json){
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_reports SET report_type = :report_type, report_title = :report_title,
                report_description = :report_description, report_data = :report_data, file_path = :file_path
                WHERE report_id = :report_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':report_type', $json['report_type']);
        $stmt->bindParam(':report_title', $json['report_title']);
        $stmt->bindParam(':report_description', $json['report_description']);
        $stmt->bindParam(':report_data', $json['report_data']);
        $stmt->bindParam(':file_path', $json['file_path']);
        $stmt->bindParam(':report_id', $json['report_id']);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
   

    function handleDeleteReport($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "DELETE FROM tbl_reports WHERE report_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $json["id"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function handleListReports(){
        include 'db.php';
        $json = json_decode($json, true);
        $query = "SELECT * FROM tbl_reports ORDER BY generated_date DESC";
        $stmt = $pdo->query($query);
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode(['success' => true, 'data' => $reports, 'operation' => 'listReports']);
    }
}

$conn = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$reportOps = new ReportOperations();

switch ($operation) {
    case "createReport":
        echo $reportOps->handleCreateReport($json);
        break;
    case "getReport":
        echo $reportOps->handleGetReport($json);
        break;
    case "updateReport":
        echo $reportOps->handleUpdateReport($json);
        break;
    case "deleteReport":
        echo $reportOps->handleDeleteReport($json);
        break;
    case "listReports":
        echo $reportOps->handleListReports();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid operation', 'operation' => $operation]);
        break;
}

?>
