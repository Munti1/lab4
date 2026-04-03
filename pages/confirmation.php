<?php
// pages/confirmation.php — Craiun Ioan-Andrei
define('BASE_URL', '/lab4/');
$pageTitle = 'Booking Confirmed — BarberCo';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$user = currentUser();
$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) { header('Location: ' . BASE_URL); exit; }

$stmt = $conn->prepare(
    "SELECT a.*, s.name AS service_name, s.price, s.duration_minutes, c.icon,
            st.name AS staff_name
     FROM appointments a
     JOIN services s ON a.service_id = s.id
     JOIN categories c ON s.category_id = c.id
     LEFT JOIN staff st ON a.staff_id = st.id
     WHERE a.id = ? AND a.user_id = ?"
);
$stmt->bind_param('ii', $id, $user['id']);
$stmt->execute();
$appt = $stmt->get_result()->fetch_assoc();

if (!$appt) { header('Location: ' . BASE_URL); exit; }

require_once __DIR__ . '/../includes/header.php';
?>

<main class="page-wrap" style="flex:1;">
    <div class="confirm-box">
        <div class="check">✓</div>
        <h1>Booking Confirmed!</h1>
        <p>Your appointment has been received. We'll see you soon!</p>

        <div class="confirm-details">
            <dl>
                <dt>Service</dt>
                <dd><?= $appt['icon'] ?> <?= htmlspecialchars($appt['service_name']) ?></dd>

                <dt>Date</dt>
                <dd><?= date('l, F j, Y', strtotime($appt['appointment_date'])) ?></dd>

                <dt>Time</dt>
                <dd><?= date('g:i A', strtotime($appt['appointment_time'])) ?></dd>

                <dt>Duration</dt>
                <dd><?= $appt['duration_minutes'] ?> minutes</dd>

                <dt>Stylist</dt>
                <dd><?= $appt['staff_name'] ? htmlspecialchars($appt['staff_name']) : 'No preference' ?></dd>

                <dt>Price</dt>
                <dd>$<?= number_format($appt['price'], 2) ?></dd>

                <dt>Status</dt>
                <dd><span class="status-badge status-<?= $appt['status'] ?>"><?= ucfirst($appt['status']) ?></span></dd>

                <?php if ($appt['notes']): ?>
                <dt>Notes</dt>
                <dd><?= htmlspecialchars($appt['notes']) ?></dd>
                <?php endif; ?>
            </dl>
        </div>

        <div style="display:flex;gap:1rem;flex-wrap:wrap;justify-content:center;">
            <a href="my_appointments.php" class="btn btn-dark">My Appointments</a>
            <a href="book.php" class="btn btn-gold">Book Another</a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
