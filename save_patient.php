<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function redirectWithMessage(string $message, string $status = 'error'): void
{
    header('Location: index.php?status=' . urlencode($status) . '&message=' . urlencode($message));
    exit;
}

function postValue(string $key): string
{
    return trim((string)($_POST[$key] ?? ''));
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
    redirectWithMessage('Συμπληρώστε όλα τα υποχρεωτικά πεδία.');
}

if (!preg_match('/^\d{11}$/', $amka)) {
    redirectWithMessage('Το ΑΜΚΑ πρέπει να αποτελείται από 11 ψηφία.');
}

if (!is_numeric($height) || (float)$height <= 0) {
    redirectWithMessage('Το ύψος πρέπει να είναι θετικός αριθμός.');
}

if (!is_numeric($weight) || (float)$weight <= 0) {
    redirectWithMessage('Το βάρος πρέπει να είναι θετικός αριθμός.');
}

$birthDateTime = DateTime::createFromFormat('Y-m-d', $birthDate);
$admissionDateTime = DateTime::createFromFormat('Y-m-d', $admissionDate);

if (!$birthDateTime || !$admissionDateTime) {
    redirectWithMessage('Οι ημερομηνίες δεν έχουν σωστή μορφή.');
}

if ($admissionDateTime < $birthDateTime) {
    redirectWithMessage('Η ημερομηνία εισαγωγής δεν μπορεί να είναι πριν από την ημερομηνία γέννησης.');
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO patients
            (first_name, last_name, amka, height, weight, birth_date, admission_date, reason)
         VALUES
            (:first_name, :last_name, :amka, :height, :weight, :birth_date, :admission_date, :reason)'
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
    ]);

    header('Location: patient_list.php?status=success&message=' . urlencode('Ο ασθενής αποθηκεύτηκε με επιτυχία.'));
    exit;
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        redirectWithMessage('Υπάρχει ήδη ασθενής με αυτό το ΑΜΚΑ.');
    }

    redirectWithMessage('Παρουσιάστηκε σφάλμα κατά την αποθήκευση.');
}
