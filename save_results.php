<?php
header("Content-Type: application/json");
require_once 'db_connect.php';

// Get the JSON data sent from JavaScript
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data) {
    try {
        $sql = "INSERT INTO assessment_results 
                (employee_name, department, total_questions, correct_answers, score_percentage, time_taken) 
                VALUES (:name, :dept, :total, :correct, :score, :time)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'    => $data['name'],
            ':dept'    => $data['dept'],
            ':total'   => $data['total'],
            ':correct' => $data['correct'],
            ':score'   => $data['score'],
            ':time'    => $data['time']
        ]);

        echo json_encode(["status" => "success", "message" => "Results saved!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>