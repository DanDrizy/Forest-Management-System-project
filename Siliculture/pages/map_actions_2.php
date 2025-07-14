<?php 
require_once '../../logs/backend/auth_check.php';
checkUserAuth('Siliculture');

include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin_id = $_POST['pin_id'] ?? null;
    $species_name = $_POST['species_name'] ?? '';
    $species_type = $_POST['species_type'] ?? '';
    $observation_type = $_POST['observation_type'] ?? '';
    $abundance = $_POST['abundance'] ?? 'common';
    $native_status = $_POST['native_status'] ?? 'unknown';
    $observation_date = $_POST['observation_date'] ?? '';
    $observation_time = $_POST['observation_time'] ?? null;
    $weather = $_POST['weather_conditions'] ?? '';
    $observed_by = $_POST['observed_by'] ?? '';
    $notes = $_POST['notes'] ?? '';

    $photo_path = null;
    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = '../../uploads/biodiversity/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = uniqid() . '_' . basename($_FILES['photo']['name']);
        $uploadFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
            $photo_path = 'uploads/biodiversity/' . $filename;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO biodiversity_observations
        (pin_id, species_name, species_type, observation_type, abundance, native_status, observation_date, observation_time, weather_conditions, photo_path, observed_by, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $inserted = $stmt->execute([
        $pin_id, $species_name, $species_type, $observation_type, $abundance, $native_status,
        $observation_date, $observation_time, $weather, $photo_path, $observed_by, $notes
    ]);

    echo json_encode(['success' => $inserted]);
    exit;
}

?>