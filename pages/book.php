<?php
// pages/book.php — Farcas Andrei-Alexandru
define('BASE_URL', '/lab4/');
$pageTitle = 'Book Appointment — BarberCo';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$user    = currentUser();
$error   = '';
$success = false;
$apptId  = null;

// Load services and staff for dropdowns
$services = $conn->query("SELECT s.id, s.name, s.price, s.duration_minutes, c.name AS cat_name FROM services s JOIN categories c ON s.category_id=c.id ORDER BY c.id, s.name");
$staffRes = $conn->query("SELECT id, name, role FROM staff WHERE available=1");

$preService = isset($_GET['service']) ? (int)$_GET['service'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = (int)($_POST['service_id'] ?? 0);
    $staff_id   = !empty($_POST['staff_id']) ? (int)$_POST['staff_id'] : null;
    $appt_date  = trim($_POST['appointment_date'] ?? '');
    $appt_time  = trim($_POST['appointment_time'] ?? '');
    $notes      = trim($_POST['notes'] ?? '');

    // Validate
    if (!$service_id || !$appt_date || !$appt_time) {
        $error = 'Please fill in all required fields.';
    } elseif (strtotime($appt_date) < strtotime('today')) {
        $error = 'Please choose a date in the future.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO appointments (user_id, service_id, staff_id, appointment_date, appointment_time, notes, status)
             VALUES (?, ?, ?, ?, ?, ?, 'pending')"
        );
        $stmt->bind_param('iiisss', $user['id'], $service_id, $staff_id, $appt_date, $appt_time, $notes);
        if ($stmt->execute()) {
            $apptId  = $stmt->insert_id;
            $success = true;
        } else {
            $error = 'Booking failed. Please try again.';
        }
        $stmt->close();
    }
}

if ($success && $apptId) {
    header('Location: confirmation.php?id=' . $apptId);
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="page-wrap-sm" style="max-width:640px;flex:1;">
    <h1 class="page-title">Book an Appointment</h1>
    <p class="page-subtitle">Fill in the details below and we'll confirm your slot.</p>

    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

    <div class="form-card">
        <form method="POST" novalidate>
            <div class="form-group">
                <label for="service_id">Service *</label>
                <select id="service_id" name="service_id" required>
                    <option value="">— Select a service —</option>
                    <?php
                    $lastCat = '';
                    while ($svc = $services->fetch_assoc()):
                        if ($lastCat !== $svc['cat_name']) {
                            if ($lastCat) echo '</optgroup>';
                            echo '<optgroup label="' . htmlspecialchars($svc['cat_name']) . '">';
                            $lastCat = $svc['cat_name'];
                        }
                        $sel = (($_POST['service_id'] ?? $preService) == $svc['id']) ? 'selected' : '';
                    ?>
                    <option value="<?= $svc['id'] ?>" <?= $sel ?>>
                        <?= htmlspecialchars($svc['name']) ?> — $<?= number_format($svc['price'],2) ?> (<?= $svc['duration_minutes'] ?>min)
                    </option>
                    <?php endwhile; if ($lastCat) echo '</optgroup>'; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="staff_id">Preferred Stylist <small style="font-weight:400;">(optional)</small></label>
                <select id="staff_id" name="staff_id">
                    <option value="">No preference</option>
                    <?php while ($st = $staffRes->fetch_assoc()):
                        $sel = (($_POST['staff_id'] ?? '') == $st['id']) ? 'selected' : '';
                    ?>
                    <option value="<?= $st['id'] ?>" <?= $sel ?>><?= htmlspecialchars($st['name']) ?> — <?= htmlspecialchars($st['role']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="appointment_date">Date *</label>
                    <input type="date" id="appointment_date" name="appointment_date" required
                           min="<?= date('Y-m-d') ?>"
                           value="<?= htmlspecialchars($_POST['appointment_date'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="appointment_time">Time *</label>
                    <select id="appointment_time" name="appointment_time" required>
                        <option value="">— Select —</option>
                        <?php
                        $times = ['09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30',
                                  '13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30',
                                  '17:00','17:30','18:00','18:30','19:00','19:30'];
                        foreach ($times as $t):
                            $sel = (($_POST['appointment_time'] ?? '') === $t) ? 'selected' : '';
                        ?>
                        <option value="<?= $t ?>" <?= $sel ?>><?= date('g:i A', strtotime($t)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Additional Notes</label>
                <textarea id="notes" name="notes" placeholder="Any special requests or preferences..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-gold btn-full">Confirm Booking</button>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
