<?php
// api/get_markers.php
header('Content-Type: application/json');
include '../includes/db.php';

try {
    // Select users who have active skills and a location set
    // We group by user to send one marker per provider
    $sql = "
        SELECT 
            p.user_id, 
            p.full_name, 
            p.latitude, 
            p.longitude,
            p.address,
            p.profile_picture,
            GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') as skills,
            GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as categories
        FROM profiles p
        JOIN user_skills us ON p.user_id = us.user_id
        JOIN skills s ON us.skill_id = s.id
        JOIN skill_categories c ON s.category_id = c.id
        WHERE p.latitude IS NOT NULL 
          AND p.longitude IS NOT NULL
          AND us.availability_status = 'Available'
        GROUP BY p.user_id
    ";

    $stmt = $pdo->query($sql);
    $providers = $stmt->fetchAll();

    echo json_encode($providers);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
