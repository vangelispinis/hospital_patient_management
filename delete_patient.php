<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: patient_list.php?status=error&message=' . urlencode('Δεν βρέθηκε ο ασθενής.'));
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM patients WHERE id = :id');
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 0) {
        header('Location: patient_list.php?status=error&message=' . urlencode('Δεν βρέθηκε ο ασθενής.'));
        exit;
    }

    header('Location: patient_list.php?status=success&message=' . urlencode('Ο ασθενής διαγράφηκε.'));
    exit;
} catch (PDOException $e) {
    header('Location: patient_list.php?status=error&message=' . urlencode('Παρουσιάστηκε σφάλμα κατά τη διαγραφή.'));
    exit;
}
