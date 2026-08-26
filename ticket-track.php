<?php
require 'config/database.php';
header('Content-Type: application/json');

// Disable display errors in production
if (php_sapi_name() !== 'cli') {
    ini_set('display_errors', 0);
}
error_reporting(E_ALL);

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
        exit;
    }

    // Validate parameter
    if (!isset($_GET['number']) || empty($_GET['number'])) {
        echo json_encode(['success' => false, 'message' => 'Nomor tiket diperlukan']);
        exit;
    }

    $searchNumber = trim($conn->real_escape_string($_GET['number']));

    // Validate database connection
    if (!$conn) {
        throw new Exception('Koneksi database gagal');
    }

    // Check if tickets table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'tickets'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Sistem tiket belum siap']);
        exit;
    }

    // Query with multiple matching strategies
    $sql = "SELECT * FROM tickets WHERE 
            CONCAT(id, '-', ticket_number) = ? 
            OR ticket_number = ?
            OR id = ?
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("sss", $searchNumber, $searchNumber, $searchNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $ticket = $result->fetch_assoc();
        
        // Calculate SLA (10 working days)
        try {
            $created = new DateTime($ticket['created_at']);
            $now = new DateTime();
            $workingDays = 0;
            $current = clone $created;
            
            while ($current < $now && $workingDays < 10) {
                $current->modify('+1 day');
                if ($current->format('N') < 6) { // Monday-Friday
                    $workingDays++;
                }
            }
            
            $sisaHari = max(0, 10 - $workingDays);
        } catch (Exception $e) {
            $sisaHari = 10; // fallback
        }
        
        // Get history if table exists
        $historyData = [];
        $historyTableCheck = $conn->query("SHOW TABLES LIKE 'ticket_history'");
        if ($historyTableCheck && $historyTableCheck->num_rows > 0) {
            $historySql = "SELECT * FROM ticket_history WHERE ticket_id = ? ORDER BY created_at DESC";
            $historyStmt = $conn->prepare($historySql);
            if ($historyStmt) {
                $historyStmt->bind_param("i", $ticket['id']);
                $historyStmt->execute();
                $historyResult = $historyStmt->get_result();
                while ($h = $historyResult->fetch_assoc()) {
                    $historyData[] = $h;
                }
            }
        }
        
        // Ensure all fields exist
        $ticket['display_number'] = $ticket['id'] . '-' . $ticket['ticket_number'];
        
        echo json_encode([
            'success' => true,
            'ticket' => $ticket,
            'display_number' => $ticket['display_number'],
            'sla_remaining' => $sisaHari,
            'history' => $historyData
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tiket tidak ditemukan']);
    }
    
} catch (Throwable $e) {
    error_log("Ticket tracking error: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
}
exit;
?>