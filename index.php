<?php
declare(strict_types=1);

$message = $_GET['message'] ?? '';
$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Καταγραφή Ασθενών Νοσοκομείου</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page">
        <header class="topbar">
            <div>
                <p class="eyebrow">Νοσοκομειακό σύστημα</p>
                <h1>Καταγραφή Ασθενή</h1>
            </div>
            <a class="button secondary" href="patient_list.php">Λίστα ασθενών</a>
        </header>

        <?php if ($message !== ''): ?>
            <div class="alert <?php echo $status === 'success' ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form class="patient-form" action="save_patient.php" method="post">
            <section class="form-section">
                <h2>Προσωπικά στοιχεία</h2>
                <div class="grid">
                    <label>
                        Όνομα
                        <input type="text" name="first_name" maxlength="50" required>
                    </label>

                    <label>
                        Επώνυμο
                        <input type="text" name="last_name" maxlength="50" required>
                    </label>

                    <label>
                        ΑΜΚΑ
                        <input type="text" name="amka" maxlength="11" pattern="[0-9]{11}" required>
                    </label>

                    <label>
                        Ημερομηνία γέννησης
                        <input type="date" name="birth_date" required>
                    </label>
                </div>
            </section>

            <section class="form-section">
                <h2>Ιατρικά στοιχεία εισαγωγής</h2>
                <div class="grid">
                    <label>
                        Ύψος σε cm
                        <input type="number" name="height" min="30" max="260" step="0.01" required>
                    </label>

                    <label>
                        Βάρος σε kg
                        <input type="number" name="weight" min="1" max="500" step="0.01" required>
                    </label>

                    <label>
                        Ημερομηνία εισαγωγής
                        <input type="date" name="admission_date" required>
                    </label>
                </div>

                <label>
                    Αιτία εισαγωγής
                    <textarea name="reason" rows="5" required></textarea>
                </label>
            </section>

            <div class="actions">
                <button class="button" type="submit">Αποθήκευση ασθενή</button>
                <button class="button secondary" type="reset">Καθαρισμός</button>
            </div>
        </form>
    </main>
</body>
</html>
