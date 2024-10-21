<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
include 'db.php';

class UserOperations
{
    function handleCreateUser($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "INSERT INTO users (Username, Password, Email, user_firstName, user_lastName, user_level) 
                  VALUES (:Username, :Password, :Email, :FirstName, :LastName, :Role)";
        $role = "3"; // Assuming 3 is for "owner"
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":Username", $json["Username"]);
        $stmt->bindParam(":Password", $json["Password"]); // Plain text password
        $stmt->bindParam(":Email", $json["Email"]);
        $stmt->bindParam(":FirstName", $json["FirstName"]);
        $stmt->bindParam(":LastName", $json["LastName"]);
        $stmt->bindParam(":Role", $role);
        
        if ($stmt->execute()) {
            return json_encode(['success' => true, 'message' => 'User registered successfully']);
        } else {
            return json_encode(['success' => false, 'message' => 'Registration failed']);
        }
    }

    function handleGetUser($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "SELECT UserID, Username, Email, FirstName, LastName, Role FROM tbl_users WHERE UserID = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $json["id"]);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode($result);
    }
        
    function handleUpdateUser($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_users SET Username = :Username, Email = :Email, FirstName = :FirstName, 
                  LastName = :LastName, Role = :Role WHERE UserID = :UserID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":Username", $json["Username"]);
        $stmt->bindParam(":Email", $json["Email"]);
        $stmt->bindParam(":FirstName", $json["FirstName"]);
        $stmt->bindParam(":LastName", $json["LastName"]);
        $stmt->bindParam(":Role", $json["Role"]);
        $stmt->bindParam(":UserID", $json["UserID"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function handleDeleteUser($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "DELETE FROM tbl_users WHERE UserID = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $json["id"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function handleListUsers()
    {
        include 'db.php';
        $sql = "SELECT UserID, Username, Email, FirstName, LastName, Role FROM tbl_users";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
    }

    function handleLogin($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "SELECT * FROM users WHERE Username = :Username AND Password = :Password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":Username", $json["Username"]);
        $stmt->bindParam(":Password", $json["Password"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? json_encode($stmt->fetch(PDO::FETCH_ASSOC)): 0 ;
    }
    function handleListUserPets($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "SELECT p.pet_id, p.pet_name, p.date_of_birth, p.pet_status, s.species_name, b.breed_name
                FROM tbl_pets p
                INNER JOIN tbl_species s ON p.species_id = s.species_id
                INNER JOIN tbl_breeds b ON p.breed_id = b.breed_id
                INNER JOIN tbl_owners o ON p.owner_id = o.owner_id
                WHERE o.user_id = :UserID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":UserID", $json["UserID"]);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
    }

}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$userOps = new UserOperations();

switch ($operation) {
    case "handleLogin":
        echo $userOps->handleLogin($json);
        break;
    case "register":
        echo $userOps->handleCreateUser($json);
        break;
    case "getUser":
        echo $userOps->handleGetUser($json);
        break;
    case "updateUser":
        echo $userOps->handleGetUser($json);
        break;
    case "deleteUser":
        echo $userOps->handleGetUser($json);
        break;
    case "listUsers":
        echo $userOps->handleListUsers();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid operation', 'operation' => $operation]);
        break;
}

?>
