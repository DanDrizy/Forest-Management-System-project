<?php
require_once '../../logs/backend/auth_check.php';
checkUserAuth('Siliculture');

include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin_id = $_POST['pin_id'] ?? null;
    $soil_ph = $_POST['soil_ph'] ?? null;
    $organic = $_POST['organic_matter_percent'] ?? null;
    $n = $_POST['nitrogen_level'] ?? null;
    $p = $_POST['phosphorus_level'] ?? null;
    $k = $_POST['potassium_level'] ?? null;
    $moisture = $_POST['soil_moisture_percent'] ?? null;
    $temp = $_POST['soil_temperature'] ?? null;
    $texture = $_POST['soil_texture'] ?? 'loam';
    $compaction = $_POST['compaction_level'] ?? 'low';
    $erosion = $_POST['erosion_signs'] ?? 'none';
    $date = $_POST['testing_date'] ?? null;
    $method = $_POST['testing_method'] ?? '';
    $tester = $_POST['tested_by'] ?? '';
    $notes = $_POST['notes'] ?? '';

    $lab_file = null;
    if (!empty($_FILES['lab_result_file']['name'])) {
        $dir = '../../uploads/soil/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $filename = uniqid() . '_' . basename($_FILES['lab_result_file']['name']);
        $uploadPath = $dir . $filename;

        if (move_uploaded_file($_FILES['lab_result_file']['tmp_name'], $uploadPath)) {
            $lab_file = 'uploads/soil/' . $filename;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO soil_health_data
        (pin_id, soil_ph, organic_matter_percent, nitrogen_level, phosphorus_level, potassium_level, soil_moisture_percent, soil_temperature, soil_texture, compaction_level, erosion_signs, testing_date, testing_method, lab_results_path, tested_by, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $inserted = $stmt->execute([
        $pin_id, $soil_ph, $organic, $n, $p, $k, $moisture, $temp,
        $texture, $compaction, $erosion, $date, $method, $lab_file, $tester, $notes
    ]);

    echo json_encode(['success' => $inserted]);
    exit;
}

?>