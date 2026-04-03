<?php
// pages/my_appointments.php — Student 4
define('BASE_URL', '/lab4/');
$pageTitle = 'My Appointments — BarberCo';
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

$user = currentUser();

// Cancel action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    $cid = (int)$_POST['cancel_id'];
    $upd = $conn->prepare("UPDATE appointments SET status='cancelled' WHERE id=? AND user_id=? AND status='pending'");
    $upd->bind_param('ii', $cid, $user['id']);
    $upd->execute();
    header('Location: my_appointments.php?msg=cancelled');
    exit;
}

$msg = $_GET['msg'] ?? '';

// Fetch user's appointments
$stmt = $conn->prepare(
    "SELECT a.*, s.name AS service_name, s.price, s.duration_minutes, c.icon, st.name AS staff_name
     FROM appointments a
     JOIN services s ON a.service_id = s.id
     JOIN categories c ON s.category_id = c.id
     LEFT JOIN staff st ON a.staff_id = st.id
     WHERE a.user_id = ?
     ORDER BY a.appointment_date DESC, a.appointment_time DESC"
);
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$appointments = $stmt->get_result();

require_once '../includes/header.php';
?>

<main class="page-wrap">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:.5rem;">
        <div>
            <h1 class="page-title">My Appointments</h1>
            <p class="page-subtitle">Hello, <?= htmlspecialchars($user['full_name']) ?>! Here are all your bookings.</p>
        </div>
    </div>

    <?php if ($msg === 'cancelled'): ?>
    <div class="alert alert-info">Your appointment has been cancelled.</div>
    <?php endif; ?>

    <?php if ($appointments->num_rows === 0): ?>
    <div class="empty-state">
        <div class="icon">📅</div>
        <h3>No appointments yet</h3>
        <p style="margin:.5rem 0 1.5rem;">Ready for a fresh look? Book your first appointment today.</p>
        <a href="book.php" class="btn btn-gold">Book Now</a>
    </div>

    <?php else: ?>
    <div style="overflow-x:auto;">
        <table class="appt-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Stylist</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php $rowNum = 1; while ($appt = $appointments->fetch_assoc()): ?>
            <tr>
                <td><?= $rowNum++ ?></td>
                <td>
                    <?= $appt['icon'] ?> <?= htmlspecialchars($appt['service_name']) ?><br>
                    <small style="color:var(--muted);">⏱ <?= $appt['duration_minutes'] ?> min</small>
                </td>
                <td><?= date('M j, Y', strtotime($appt['appointment_date'])) ?></td>
                <td><?= date('g:i A', strtotime($appt['appointment_time'])) ?></td>
                <td><?= $appt['staff_name'] ? htmlspecialchars($appt['staff_name']) : '—' ?></td>
                <td>$<?= number_format($appt['price'], 2) ?></td>
                <td><span class="status-badge status-<?= $appt['status'] ?>"><?= ucfirst($appt['status']) ?></span></td>
                <td>
                    <?php if ($appt['status'] === 'pending'): ?>
                    <form method="POST" onsubmit="return confirm('Cancel this appointment?');" style="display:inline;">
                        <input type="hidden" name="cancel_id" value="<?= $appt['id'] ?>">
                        <button type="submit" class="btn btn-dark" style="padding:.3rem .8rem;font-size:.8rem;">Cancel</button>
                    </form>
                    <?php else: ?>
                    <span style="color:var(--muted);font-size:.82rem;">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>
