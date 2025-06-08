<?php
// ===== FILE: ../../backend/update_stockin.php =====

header('Content-Type: application/json');
require_once '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $in_id = $input['in_id'] ?? null;
    $new_amount = $input['amount'] ?? null;
    $new_price = $input['price'] ?? null;
    
    if (!$in_id || !$new_amount || !$new_price) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Get current stock data
        $current_stock = $pdo->prepare("SELECT s_amount, t_id FROM stockin WHERE in_id = :in_id");
        $current_stock->bindParam(':in_id', $in_id);
        $current_stock->execute();
        $current_data = $current_stock->fetch(PDO::FETCH_ASSOC);
        
        if (!$current_data) {
            throw new Exception('Stock record not found');
        }
        
        $old_amount = $current_data['s_amount'];
        $t_id = $current_data['t_id'];
        $amount_difference = $new_amount - $old_amount;
        
        // Get current timber amount
        $timber_query = $pdo->prepare("SELECT t_amount FROM timber WHERE t_id = :t_id");
        $timber_query->bindParam(':t_id', $t_id);
        $timber_query->execute();
        $timber_data = $timber_query->fetch(PDO::FETCH_ASSOC);
        
        if (!$timber_data) {
            throw new Exception('Timber record not found');
        }
        
        $current_timber_amount = $timber_data['t_amount'];
        
        // Check if we have enough timber for the increase
        if ($amount_difference > 0 && $amount_difference > $current_timber_amount) {
            throw new Exception('Not enough timber available. Available: ' . $current_timber_amount);
        }
        
        // Update stock in
        $update_stockin = $pdo->prepare("UPDATE stockin SET s_amount = :amount, price = :price WHERE in_id = :in_id");
        $update_stockin->bindParam(':amount', $new_amount);
        $update_stockin->bindParam(':price', $new_price);
        $update_stockin->bindParam(':in_id', $in_id);
        $update_stockin->execute();
        
        // Update timber amount (subtract the difference)
        $new_timber_amount = $current_timber_amount - $amount_difference;
        $update_timber = $pdo->prepare("UPDATE timber SET t_amount = :amount WHERE t_id = :t_id");
        $update_timber->bindParam(':amount', $new_timber_amount);
        $update_timber->bindParam(':t_id', $t_id);
        $update_timber->execute();
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

<?php
// ===== FILE: ../../backend/update_stockout.php =====

header('Content-Type: application/json');
require_once '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $out_id = $input['out_id'] ?? null;
    $new_amount = $input['amount'] ?? null;
    $new_price = $input['price'] ?? null;
    
    if (!$out_id || !$new_amount || !$new_price) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Get current stock out data
        $current_stockout = $pdo->prepare("SELECT out_amount, in_id FROM stockout WHERE out_id = :out_id");
        $current_stockout->bindParam(':out_id', $out_id);
        $current_stockout->execute();
        $current_out_data = $current_stockout->fetch(PDO::FETCH_ASSOC);
        
        if (!$current_out_data) {
            throw new Exception('Stock out record not found');
        }
        
        $old_out_amount = $current_out_data['out_amount'];
        $in_id = $current_out_data['in_id'];
        $amount_difference = $new_amount - $old_out_amount;
        
        // Get current stock in data
        $current_stockin = $pdo->prepare("SELECT s_amount FROM stockin WHERE in_id = :in_id");
        $current_stockin->bindParam(':in_id', $in_id);
        $current_stockin->execute();
        $current_in_data = $current_stockin->fetch(PDO::FETCH_ASSOC);
        
        if (!$current_in_data) {
            throw new Exception('Related stock in record not found');
        }
        
        $current_in_amount = $current_in_data['s_amount'];
        
        // Check if we have enough stock for the increase
        if ($amount_difference > 0 && $amount_difference > $current_in_amount) {
            throw new Exception('Not enough stock available. Available: ' . $current_in_amount);
        }
        
        // Update stock out
        $update_stockout = $pdo->prepare("UPDATE stockout SET out_amount = :amount, out_price = :price WHERE out_id = :out_id");
        $update_stockout->bindParam(':amount', $new_amount);
        $update_stockout->bindParam(':price', $new_price);
        $update_stockout->bindParam(':out_id', $out_id);
        $update_stockout->execute();
        
        // Update stock in amount (subtract the difference)
        $new_in_amount = $current_in_amount - $amount_difference;
        $update_stockin = $pdo->prepare("UPDATE stockin SET s_amount = :amount WHERE in_id = :in_id");
        $update_stockin->bindParam(':amount', $new_in_amount);
        $update_stockin->bindParam(':in_id', $in_id);
        $update_stockin->execute();
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Stock out updated successfully']);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>