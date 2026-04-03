<?php
/*================================================================
TEAM 5 — BarberCo Studio | Barbershop Booking Platform
Lab Group Activity | Academic Year 2025–26
================================================================

TEAM MEMBERS & ROLES
----------------------------------------------------------------

Student 1 — Muntean Alexandru-Ioan
Role: Home Page, General Layout & Authentication
Files:
  - index.php                  (Home page with hero, categories, featured services, staff)
  - includes/header.php        (Navigation bar, HTML head, session-aware menu)
  - includes/footer.php        (Footer with contact info and links)
  - includes/auth.php          (Session helpers: isLoggedIn, requireLogin, logout)
  - includes/db.php            (MySQL database connection)
  - pages/register.php         (User registration form with validation)
  - pages/login.php            (User login form with session handling)
  - pages/logout.php           (Session destroy and redirect)

----------------------------------------------------------------

Student 2 — Tetoianu Kevin Stefan
Role: Services Page, Categories & Service Details
Files:
  - pages/services.php         (All services listing with category filter bar)
  - pages/service_detail.php   (Single service detail page with related services)

----------------------------------------------------------------

Student 3 — Andrei Alexandru-Farcas
Role: Appointment Booking System
Files:
  - pages/book.php             (Booking form: service, stylist, date, time, notes)
  - sql/barbershop.sql         (Full database schema and seed data)

----------------------------------------------------------------

Student 4 — Craciun Andrei
Role: My Appointments Page, Confirmation Page & Final Testing
Files:
  - pages/my_appointments.php  (View and cancel user appointments)
  - pages/confirmation.php     (Booking confirmation with full appointment summary)

----------------------------------------------------------------

PLATFORM OVERVIEW
----------------------------------------------------------------
Platform:     Barbershop / Beauty Salon Booking System
Tech Stack:   PHP, MySQL, HTML, CSS (no frameworks)
Server:       XAMPP (Apache + MySQL)
Database:     barbershop_db

Pages included:
  - Home page with salon presentation and service overview
  - Register, Login, and Logout
  - Services page with category filtering
  - Single service details page
  - Appointment booking form with stylist selection
  - Booking confirmation page
  - My Appointments page with cancel functionality

Database tables:
  - users         (registered accounts)
  - staff         (barber/stylist profiles)
  - categories    (service categories)
  - services      (individual services with price and duration)
  - appointments  (bookings linking users, services and staff)

================================================================*/
// index.php — Home Page | Muntean Alexandru-Ioan
define('BASE_URL', '/lab4/');
$pageTitle = 'BarberCo Studio — Premium Barbershop & Beauty Salon';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Fetch a few featured services
$featured = $conn->query("SELECT s.*, c.name AS cat_name, c.icon FROM services s JOIN categories c ON s.category_id = c.id LIMIT 3");

// Fetch categories
$categories = $conn->query("SELECT * FROM categories");

// Fetch staff
$staff = $conn->query("SELECT * FROM staff WHERE available=1 LIMIT 3");
?>

<!-- HERO -->
<section class="hero">
    <div class="hero-tag">Est. 2010 · Premium Grooming</div>
    <h1>Where Style Meets <em>Craft</em></h1>
    <p>Expert cuts, precision fades, and luxury treatments tailored to you — book your appointment in minutes.</p>
    <div class="hero-actions">
        <a href="<?= BASE_URL ?>pages/book.php" class="btn btn-gold">Book an Appointment</a>
        <a href="<?= BASE_URL ?>pages/services.php" class="btn btn-outline">Explore Services</a>
    </div>
</section>

<!-- INFO BAR -->
<div class="info-bar">
    <div class="inner">
        <div class="info-item">
            <div class="icon">📍</div>
            <h4>Location</h4>
            <p>12 Grand Avenue, Studio 3</p>
        </div>
        <div class="info-item">
            <div class="icon">🕐</div>
            <h4>Hours</h4>
            <p>Mon–Sat: 9am – 8pm<br>Sun: 10am – 6pm</p>
        </div>
        <div class="info-item">
            <div class="icon">📞</div>
            <h4>Phone</h4>
            <p>+1 555-0192</p>
        </div>
        <div class="info-item">
            <div class="icon">✦</div>
            <h4>Experience</h4>
            <p>14+ years of excellence</p>
        </div>
    </div>
</div>

<!-- CATEGORIES -->
<section class="section section-light">
    <div class="max-w">
        <div class="section-header">
            <div class="tag">What We Offer</div>
            <h2>Our Service Categories</h2>
            <p>Everything from a quick trim to a full transformation.</p>
        </div>
        <div class="grid-4">
            <?php while ($cat = $categories->fetch_assoc()): ?>
            <a href="<?= BASE_URL ?>pages/services.php?cat=<?= $cat['id'] ?>" class="cat-card" style="text-decoration:none;color:inherit;">
                <div class="icon"><?= $cat['icon'] ?></div>
                <h3><?= htmlspecialchars($cat['name']) ?></h3>
                <p><?= htmlspecialchars($cat['description']) ?></p>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- FEATURED SERVICES -->
<section class="section">
    <div class="max-w">
        <div class="section-header">
            <div class="tag">Popular Picks</div>
            <h2>Featured Services</h2>
        </div>
        <div class="grid-3">
            <?php while ($svc = $featured->fetch_assoc()): ?>
            <div class="card">
                <div style="background:var(--dark);padding:2rem;text-align:center;font-size:3rem;">
                    <?= $svc['icon'] ?>
                </div>
                <div class="card-body">
                    <p style="font-size:.75rem;color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.3rem;"><?= htmlspecialchars($svc['cat_name']) ?></p>
                    <h3><?= htmlspecialchars($svc['name']) ?></h3>
                    <p><?= htmlspecialchars($svc['description']) ?></p>
                    <div class="card-meta">
                        <span class="price">$<?= number_format($svc['price'], 2) ?></span>
                        <span class="duration">⏱ <?= $svc['duration_minutes'] ?> min</span>
                    </div>
                    <a href="<?= BASE_URL ?>pages/service_detail.php?id=<?= $svc['id'] ?>" class="btn btn-dark btn-full mt-2">View &amp; Book</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>pages/services.php" class="btn btn-gold">View All Services</a>
        </div>
    </div>
</section>

<!-- STAFF -->
<section class="section section-light">
    <div class="max-w">
        <div class="section-header">
            <div class="tag">The Team</div>
            <h2>Meet Our Stylists</h2>
            <p>Skilled professionals dedicated to making you look your best.</p>
        </div>
        <div class="grid-3">
            <?php while ($s = $staff->fetch_assoc()):
                $initials = implode('', array_map(fn($w) => $w[0], explode(' ', $s['name'])));
            ?>
            <div class="staff-card">
                <div class="staff-avatar"><?= $initials ?></div>
                <h3><?= htmlspecialchars($s['name']) ?></h3>
                <div class="role"><?= htmlspecialchars($s['role']) ?></div>
                <p><?= htmlspecialchars($s['bio']) ?></p>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section section-dark" style="text-align:center;">
    <div class="hero-tag" style="margin-bottom:1.4rem;">Ready for a fresh look?</div>
    <h2 style="font-size:clamp(1.8rem,4vw,3rem);color:var(--white);margin-bottom:1rem;">
        Book Your Appointment Today
    </h2>
    <p style="color:rgba(245,240,232,.65);max-width:420px;margin:0 auto 2rem;">
        Choose your service, pick your stylist, and select a time that works for you.
    </p>
    <a href="<?= BASE_URL ?>pages/book.php" class="btn btn-gold">Get Started</a>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
