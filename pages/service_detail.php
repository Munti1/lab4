<?php
// pages/service_detail.php — Student 2
define('BASE_URL', '/lab4/');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: services.php'); exit; }

$stmt = $conn->prepare("SELECT s.*, c.name AS cat_name, c.icon FROM services s JOIN categories c ON s.category_id=c.id WHERE s.id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$svc = $stmt->get_result()->fetch_assoc();

if (!$svc) { header('Location: services.php'); exit; }

$pageTitle = htmlspecialchars($svc['name']) . ' — BarberCo';

// Related services (same category, not same)
$rel = $conn->prepare("SELECT s.*, c.icon FROM services s JOIN categories c ON s.category_id=c.id WHERE s.category_id=? AND s.id!=? LIMIT 3");
$rel->bind_param('ii', $svc['category_id'], $id);
$rel->execute();
$related = $rel->get_result();

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Detail hero -->
<div class="detail-hero">
    <div class="inner">
        <div class="service-icon-big"><?= $svc['icon'] ?></div>
        <div class="detail-hero-text">
            <p style="font-size:.75rem;color:var(--gold);font-weight:600;letter-spacing:.15em;text-transform:uppercase;margin-bottom:.5rem;">
                <?= htmlspecialchars($svc['cat_name']) ?>
            </p>
            <h1><?= htmlspecialchars($svc['name']) ?></h1>
            <div class="price-large">$<?= number_format($svc['price'], 2) ?></div>
            <p><?= htmlspecialchars($svc['description']) ?></p>
        </div>
    </div>
</div>

<div class="detail-body">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>index.php">Home</a><span>›</span>
        <a href="services.php">Services</a><span>›</span>
        <?= htmlspecialchars($svc['name']) ?>
    </div>

    <!-- Meta -->
    <div class="detail-meta">
        <div class="meta-item">
            <strong>⏱ <?= $svc['duration_minutes'] ?> min</strong>
            Duration
        </div>
        <div class="meta-item">
            <strong>📂 <?= htmlspecialchars($svc['cat_name']) ?></strong>
            Category
        </div>
        <div class="meta-item">
            <strong>💰 $<?= number_format($svc['price'], 2) ?></strong>
            Price
        </div>
    </div>

    <h2 style="font-size:1.4rem;margin-bottom:.8rem;">About This Service</h2>
    <p style="color:var(--muted);line-height:1.8;"><?= htmlspecialchars($svc['description']) ?> Our skilled team ensures every client leaves feeling their best. We use only premium products suited to your hair type and style goals.</p>

    <div class="mt-3">
        <a href="book.php?service=<?= $svc['id'] ?>" class="btn btn-gold">Book This Service</a>
        &nbsp;
        <a href="services.php" class="btn btn-dark">← All Services</a>
    </div>

    <!-- Related -->
    <?php if ($related->num_rows > 0): ?>
    <h2 style="font-size:1.4rem;margin:3rem 0 1.2rem;">Related Services</h2>
    <div class="grid-3">
        <?php while ($r = $related->fetch_assoc()): ?>
        <div class="card">
            <div style="background:var(--dark);padding:1.5rem;text-align:center;font-size:2.4rem;"><?= $r['icon'] ?></div>
            <div class="card-body">
                <h3><?= htmlspecialchars($r['name']) ?></h3>
                <p><?= htmlspecialchars($r['description']) ?></p>
                <div class="card-meta">
                    <span class="price">$<?= number_format($r['price'],2) ?></span>
                    <span class="duration">⏱ <?= $r['duration_minutes'] ?> min</span>
                </div>
                <a href="service_detail.php?id=<?= $r['id'] ?>" class="btn btn-dark btn-full mt-2">View</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
