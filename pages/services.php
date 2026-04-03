<?php
// Services Page - Tetoianu Kevin
define('BASE_URL', '/lab4/');
$pageTitle = 'Services — BarberCo';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

// Categories for filter bar
$cats = $conn->query("SELECT * FROM categories ORDER BY id");
$catList = [];
while ($c = $cats->fetch_assoc()) $catList[] = $c;

// Filter by category
$catFilter = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

if ($catFilter > 0) {
    $stmt = $conn->prepare("SELECT s.*, c.name AS cat_name, c.icon FROM services s JOIN categories c ON s.category_id=c.id WHERE s.category_id=? ORDER BY s.price");
    $stmt->bind_param('i', $catFilter);
    $stmt->execute();
    $services = $stmt->get_result();
} else {
    $services = $conn->query("SELECT s.*, c.name AS cat_name, c.icon FROM services s JOIN categories c ON s.category_id=c.id ORDER BY c.id, s.price");
}
?>

<main class="page-wrap">
    <h1 class="page-title">Our Services</h1>
    <p class="page-subtitle">Choose from our full range of grooming and beauty treatments.</p>

    <!-- Filter bar -->
    <div class="filter-bar">
        <a href="services.php" class="filter-btn <?= $catFilter===0?'active':'' ?>">All</a>
        <?php foreach ($catList as $c): ?>
        <a href="services.php?cat=<?= $c['id'] ?>" class="filter-btn <?= $catFilter===$c['id']?'active':'' ?>">
            <?= $c['icon'] ?> <?= htmlspecialchars($c['name']) ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Services grid -->
    <?php $count = 0; ?>
    <div class="grid-3">
        <?php while ($svc = $services->fetch_assoc()):
            $count++;
        ?>
        <div class="card">
            <div style="background:linear-gradient(135deg,var(--dark),var(--mid));padding:2rem;text-align:center;font-size:2.8rem;">
                <?= $svc['icon'] ?>
            </div>
            <div class="card-body">
                <p style="font-size:.72rem;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.3rem;"><?= htmlspecialchars($svc['cat_name']) ?></p>
                <h3><?= htmlspecialchars($svc['name']) ?></h3>
                <p><?= htmlspecialchars($svc['description']) ?></p>
                <div class="card-meta">
                    <span class="price">$<?= number_format($svc['price'], 2) ?></span>
                    <span class="duration">⏱ <?= $svc['duration_minutes'] ?> min</span>
                </div>
                <a href="service_detail.php?id=<?= $svc['id'] ?>" class="btn btn-dark btn-full mt-2">View Details</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <?php if ($count === 0): ?>
    <div class="empty-state">
        <div class="icon">✂️</div>
        <h3>No services found</h3>
        <p>Try selecting a different category.</p>
    </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
