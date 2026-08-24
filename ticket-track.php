<?php
require 'config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['number'])) {
    $searchNumber = $conn->real_escape_string($_GET['number']);
    
    // Cari berdasarkan kombinasi id-ticket_number atau ticket_number saja
    $sql = "SELECT * FROM tickets WHERE 
            CONCAT(id, '-', ticket_number) = '$searchNumber' 
            OR ticket_number = '$searchNumber'
            OR id = '$searchNumber'";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $ticket = $result->fetch_assoc();
        
        // Hitung SLA (10 hari kerja)
        $created = new DateTime($ticket['created_at']);
        $now = new DateTime();
        $workingDays = 0;
        $current = clone $created;
        
        while ($current < $now && $workingDays < 10) {
            $current->modify('+1 day');
            $dayOfWeek = $current->format('N');
            if ($dayOfWeek < 6) { // Senin-Jumat
                $workingDays++;
            }
        }
        
        $sisaHari = max(0, 10 - $workingDays);
        
        // Ambil history
        $historySql = "SELECT * FROM ticket_history WHERE ticket_id = {$ticket['id']} ORDER BY created_at DESC";
        $history = $conn->query($historySql);
        $historyData = [];
        while ($h = $history->fetch_assoc()) {
            $historyData[] = $h;
        }
        
        echo json_encode([
            'success' => true,
            'ticket' => $ticket,
            'display_number' => $ticket['id'] . '-' . $ticket['ticket_number'],
            'sla_remaining' => $sisaHari,
            'history' => $historyData
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tiket tidak ditemukan']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak valid']);
}
?>