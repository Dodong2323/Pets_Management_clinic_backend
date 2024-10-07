<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // Specify allowed methods
header('Access-Control-Allow-Headers: Content-Type'); // Allow specific headers

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No content response
    exit;
}

include 'db.php'; // Include your database connection file

class OwnerOperations {
    private $pdo;

    // Constructor to initialize PDO
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Method to create an owner
    public function handleCreateOwner($data) {
        try {
            $query = "INSERT INTO tbl_owners (First_name, Middle_name, Last_name, Age, Contact_number, Email_address, owner_address) VALUES (:first_name, :middle_name, :last_name, :age, :contact_number, :email, :owner_address)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':first_name' => $data['first_name'],
                ':middle_name' => $data['middle_name'],
                ':last_name' => $data['last_name'],
                ':age' => $data['age'],
                ':contact_number' => $data['contact_number'],
                ':email' => $data['email'],
                ':owner_address' => $data['owner_address']
            ]);
            return json_encode(['success' => true, 'message' => 'Owner created']);
        } catch (PDOException $e) {
            return json_encode(['success' => false, 'message' => 'Error creating owner: ' . $e->getMessage()]);
        }
    }

    // Method to update an owner
    public function handleUpdateOwner($data) {
        $query = "UPDATE tbl_owners SET First_name = :first_name, Middle_name = :middle_name, Last_name = :last_name, Age = :age, Contact_number = :contact_number, Email_address = :email, owner_address = :owner_address WHERE owner_id = :owner_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':first_name' => $data['first_name'],
            ':middle_name' => $data['middle_name'],
            ':last_name' => $data['last_name'],
            ':age' => $data['age'],
            ':contact_number' => $data['contact_number'],
            ':email' => $data['email'],
            ':owner_address' => $data['owner_address'],
            ':owner_id' => $data['owner_id']
        ]);
        return json_encode(['success' => true, 'message' => 'Owner updated']);
    }

    // Method to get an owner
    public function handleGetOwner($data) {
        $query = "SELECT * FROM tbl_owners WHERE owner_id = :owner_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':owner_id' => $data['owner_id']]);
        $owner = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($owner) {
            return json_encode(['success' => true, 'data' => $owner]);
        } else {
            return json_encode(['success' => false, 'message' => 'Owner not found']);
        }
    }

    // Method to delete an owner
    public function handleDeleteOwner($data) {
        $query = "DELETE FROM tbl_owners WHERE owner_id = :owner_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':owner_id' => $data['owner_id']]);
        return json_encode(['success' => true, 'message' => 'Owner deleted']);
    }

    // Method to list owners
    public function handleListOwners() {
        $query = "SELECT * FROM tbl_owners";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $owners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode(['success' => true, 'data' => $owners]);
    }

    // Method to list pets by owner
    public function handleListPetsByOwner($data) {
        $query = "SELECT pet_id, pet_name FROM tbl_pets WHERE owner_id = :owner_id"; // Adjusted for correct column name
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':owner_id' => $data['owner_id']]);
        $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return json_encode([
            'success' => true,
            'data' => $pets,
            'operation' => 'listPetsByOwner'
        ]);
    }
}

// Initialize the OwnerOperations class with the PDO connection
$ownerOps = new OwnerOperations($pdo);

// Get the JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$operation = $data['operation'] ?? null;

switch ($operation) {
    case "createOwner":
        echo $ownerOps->handleCreateOwner($data);
        break;
    case "updateOwner":
        echo $ownerOps->handleUpdateOwner($data);
        break;
    case "getOwner":
        echo $ownerOps->handleGetOwner($data);
        break;
    case "deleteOwner":
        echo $ownerOps->handleDeleteOwner($data);
        break;
    case "listOwners":
        echo $ownerOps->handleListOwners();
        break;
    case "listPetsByOwner":
        echo $ownerOps->handleListPetsByOwner($data);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid operation', 'operation' => $operation]);
        break;
}
?>
