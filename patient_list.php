<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

$search = trim((string)($_GET['search'] ?? ''));
$message = $_GET['message'] ?? '';
$status = $_GET['status'] ?? '';

if ($search !== '') {
    $searchTerm = '%' . $search . '%';
    $stmt = $pdo->prepare(
        'SELECT *
         FROM patients
         WHERE first_name LIKE :first_name_search
            OR last_name LIKE :last_name_search
            OR amka LIKE :amka_search
            OR CONCAT(first_name, " ", last_name) LIKE :full_name_search
         ORDER BY admission_date DESC, id DESC'
    );
    $stmt->execute([
        ':first_name_search' => $searchTerm,
        ':last_name_search' => $searchTerm,
        ':amka_search' => $searchTerm,
        ':full_name_search' => $searchTerm,
    ]);
} else {
    $stmt = $pdo->query('SELECT * FROM patients ORDER BY admission_date DESC, id DESC');
}

$patients = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Λίστα Ασθενών</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page">
        <header class="topbar">
            <div>
                <p class="eyebrow">Νοσοκομειακό σύστημα</p>
                <h1>Λίστα Ασθενών</h1>
            </div>
            <a class="button secondary" href="index.php">Νέα καταγραφή</a>
        </header>

        <?php if ($message !== ''): ?>
            <div class="alert <?php echo $status === 'success' ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form class="search-form" method="get" action="patient_list.php">
            <label>
                Αναζήτηση με όνομα, επώνυμο ή ΑΜΚΑ
                <input type="search" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            </label>
            <button class="button" type="submit">Αναζήτηση</button>
        </form>

        <section class="table-wrap">
            <?php if (count($patients) === 0): ?>
                <p class="empty">Δεν βρέθηκαν καταχωρημένοι ασθενείς.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Ονοματεπώνυμο</th>
                            <th>ΑΜΚΑ</th>
                            <th>Ύψος</th>
                            <th>Βάρος</th>
                            <th>Γέννηση</th>
                            <th>Εισαγωγή</th>
                            <th>Αιτία</th>
                            <th>Ενέργειες</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $patient['first_name'] . ' ' . $patient['last_name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($patient['amka'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars((string)$patient['height'], ENT_QUOTES, 'UTF-8'); ?> cm</td>
                                <td><?php echo htmlspecialchars((string)$patient['weight'], ENT_QUOTES, 'UTF-8'); ?> kg</td>
                                <td><?php echo htmlspecialchars($patient['birth_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($patient['admission_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($patient['reason'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div class="row-actions">
                                        <a class="button small secondary" href="edit_patient.php?id=<?php echo (int)$patient['id']; ?>">Επεξεργασία</a>
                                        <form action="delete_patient.php" method="post" onsubmit="return confirm('Θέλετε σίγουρα να διαγράψετε τον ασθενή;');">
                                            <input type="hidden" name="id" value="<?php echo (int)$patient['id']; ?>">
                                            <button class="button small danger" type="submit">Διαγραφή</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
