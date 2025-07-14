<?php
require_once '../../database/connection.php';

header('Content-Type: application/json');

$g_id = isset($_GET['g_id']) ? (int)$_GET['g_id'] : 0;

if (!$g_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid germination ID']);
    exit;
}

// Fetch pin ID from germination_pins table
$stmt = $pdo->prepare("SELECT id, tree_name FROM germination_pins WHERE g_id = ? LIMIT 1");
$stmt->execute([$g_id]);
$pin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pin) {
    echo json_encode(['success' => false, 'message' => 'No matching pin found.']);
    exit;
}

$pin_id = $pin['id'];
$tree_name = $pin['tree_name'];

// Fetch biodiversity observations
$bio_stmt = $pdo->prepare("SELECT * FROM biodiversity_observations WHERE pin_id = ? ORDER BY observation_date DESC");
$bio_stmt->execute([$pin_id]);
$bio_data = $bio_stmt->fetchAll(PDO::FETCH_ASSOC);

$bio_html = '';
foreach ($bio_data as $obs) {
    $bio_html .= "<div style='margin-bottom: 10px;'>
        <strong>Species:</strong> {$obs['species_name']} ({$obs['species_type']})<br>
        <strong>Observed:</strong> {$obs['observation_type']} | <strong>Status:</strong> {$obs['native_status']} | <strong>Abundance:</strong> {$obs['abundance']}<br>
        <strong>Date:</strong> {$obs['observation_date']}<br>
        <strong>Weather:</strong> {$obs['weather_conditions']}<br>
        <strong>Observer:</strong> {$obs['observed_by']}<br>
        <em>{$obs['notes']}</em>
    </div>";
}

// Fetch soil health data
$soil_stmt = $pdo->prepare("SELECT * FROM soil_health_data WHERE pin_id = ? ORDER BY testing_date DESC");
$soil_stmt->execute([$pin_id]);
$soil_data = $soil_stmt->fetchAll(PDO::FETCH_ASSOC);

$soil_html = '';
foreach ($soil_data as $soil) {
    $soil_html .= "<div style='margin-bottom: 10px;'>
        <strong>pH:</strong> {$soil['soil_ph']}, <strong>Organic Matter:</strong> {$soil['organic_matter_percent']}%, <strong>N:</strong> {$soil['nitrogen_level']} mg/kg, <strong>P:</strong> {$soil['phosphorus_level']} mg/kg, <strong>K:</strong> {$soil['potassium_level']} mg/kg<br>
        <strong>Moisture:</strong> {$soil['soil_moisture_percent']}%, <strong>Temperature:</strong> {$soil['soil_temperature']}°C, <strong>Texture:</strong> {$soil['soil_texture']}<br>
        <strong>Compaction:</strong> {$soil['compaction_level']}, <strong>Erosion:</strong> {$soil['erosion_signs']}<br>
        <strong>Tested by:</strong> {$soil['tested_by']} on {$soil['testing_date']}<br>
        <em>{$soil['notes']}</em>
    </div>";
}

echo json_encode([
    'success' => true,
    'tree_name' => $tree_name,
    'biodiversity_html' => $bio_html,
    'soil_html' => $soil_html
]);
exit;
