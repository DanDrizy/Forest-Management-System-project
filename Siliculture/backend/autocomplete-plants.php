<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

include'../../database/connection.php';

// Include your database connection file
// require_once 'your-database-connection.php';

// Get the search term from the request
$searchTerm = isset($_GET['term']) ? trim($_GET['term']) : '';

// Initialize response array
$response = array();

if (strlen($searchTerm) >= 2) { // Only search if at least 2 characters are entered
    try {
        // Prepare the SQL query to search for plants with g_status = 'sent'
        $sql = "SELECT *
                FROM germination 
                WHERE plant_name LIKE :searchTerm AND del = 0
                ORDER BY plant_name ASC 
                LIMIT 10";
        
        $stmt = $pdo->prepare($sql);
        $searchParam = '%' . $searchTerm . '%';
        $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as $row) {
            $response[] = array(
                'id' => $row['g_id'],
                'label' => $row['plant_name'],
                'value' => $row['plant_name'],
                'seeds' => $row['seeds'],
                'soil' => $row['soil'],
                'note' => $row['g_note']
            );
        }
        
    } catch (PDOException $e) {
        // Handle database error
        $response = array('error' => 'Database error: ' . $e->getMessage());
    }
}

// Return JSON response
echo json_encode($response);
?>