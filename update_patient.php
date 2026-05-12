<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function redirectToEdit(int $id, string $message, string $status = 'error'): void
{
    header('Location: edit_patient.php?id=' . $id . '&status=' . urlencode($status) . '&message=' . urlencode($message));
    exit;
}

function postValue(string $key): string
{
    return trim((string)($_POST[$key] ?? ''));
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: patient_list.php?status=error&message=' . urlencode('Δεν βρέθηκε ο ασθενής.'));
    exit;
}

$firstName = postValue('first_name');
$lastName = postValue('last_name');
$amka = postValue('amka');
$height = postValue('height');
$weight = postValue('weight');
$birthDate = postValue('birth_date');
$admissionDate = postValue('admission_date');
$reason = postValue('reason');

if (
    $firstName === '' ||
    $lastName === '' ||
    $amka === '' ||
    $height === '' ||
    $weight === '' ||
    $birthDate === '' ||
    $admissionDate === '' ||
    $reason === ''
) {
    redirectToEdit($id, 'Συμπληρώστε όλα τα υποχρεωτικά πεδία.');
}

if (!preg_match('/^\d{11}$/', $amka)) {
    redirectToEdit($id, 'Το ΑΜΚΑ πρέπει να αποτελείται από 11 ψηφία.');
}

if (!is_numeric($height) || (float)$height <= 0) {
    redirectToEdit($id, 'Το ύψος πρέπει να είναι θετικός αριθμός.');
}

if (!is_numeric($weight) || (float)$weight <= 0) {
    redirectToEdit($id, 'Το βάρος πρέπει να είναι θετικός αριθμός.');
}

$birthDateTime = DateTime::createFromFormat('Y-m-d', $birthDate);
$admissionDateTime = DateTime::createFromFormat('Y-m-d', $admissionDate);

if (!$birthDateTime || !$admissionDateTime) {
    redirectToEdit($id, 'Οι ημερομηνίες δεν έχουν σωστή μορφή.');
}

if ($admissionDateTime < $birthDateTime) {
    redirectToEdit($id, 'Η ημερομηνία εισαγωγής δεν μπορεί να είναι πριν από την ημερομηνία γέννησης.');
}

try {
    $stmt = $pdo->prepare(
        'UPDATE patients
         SET first_name = :first_name,
             last_name = :last_name,
             amka = :amka,
             height = :height,
             weight = :weight,
             birth_date = :birth_date,
             admission_date = :admission_date,
             reason = :reason
         WHERE id = :id'
    );

    $stmt->execute([
        ':first_name' => $firstName,
        ':last_name' => $lastName,
        ':amka' => $amka,
        ':height' => $height,
        ':weight' => $weight,
        ':birth_date' => $birthDate,
        ':admission_date' => $admissionDate,
        ':reason' => $reason,
        ':id' => $id,
    ]);

    header('Location: patient_list.php?status=success&message=' . urlencode('Τα στοιχεία του ασθενή ενημερώθηκαν.'));
    exit;
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        redirectToEdit($id, 'Υπάρχει ήδη ασθενής με αυτό το ΑΜΚΑ.');
    }

    redirectToEdit($id, 'Παρουσιάστηκε σφάλμα κατά την ενημέρωση.');
}
