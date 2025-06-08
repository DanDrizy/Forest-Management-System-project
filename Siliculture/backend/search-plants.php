<?php 

if (isset($_POST['ajax_search']) && $_POST['ajax_search'] == '1') {
    header('Content-Type: application/json');
    
    $query = isset($_POST['query']) ? trim($_POST['query']) : '';
    $plants = [];
    
    if (!empty($query)) {
        try {
            // Include database connection
            require_once '../../database/connection.php';
            
            // Prepare SQL for SQLite
            $sql = "SELECT DISTINCT * FROM germination 
                    WHERE plant_name LIKE :query 
                    ORDER BY plant_name ASC 
                    LIMIT 10";
            
            $stmt = $pdo->prepare($sql);
            $searchTerm = '%' . $query . '%';
            $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
            $stmt->execute();
            $data = "1";
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $plants[] = $row['plant_name'];
            }
            
        } catch (Exception $e) {
            error_log("Plant search error: " . $e->getMessage());
            $plants = [];
        }
    }
    
    echo json_encode($plants);
    exit;
}



?>