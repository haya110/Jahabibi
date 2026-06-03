<?php
include '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Admin+access+required");
    exit();
}

include '../includes/header.php';

$allowed_tabs = ['menu', 'orders', 'users'];
$tab = isset($_GET['tab']) && in_array($_GET['tab'], $allowed_tabs)
    ? $_GET['tab']
    : 'menu';

function get_status_class($status) {
    switch ($status) {
        case 'completed':  return 'badge-success';
        case 'cancelled':  return 'badge-danger';
        default:           return 'badge-warning';
    }
}

$products = [];
$fetch_error_products = null;
if ($tab === 'menu') {
    try {
        $stmt = $pdo->query("SELECT * FROM products ORDER BY is_active DESC, id DESC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $fetch_error_products = htmlspecialchars($e->getMessage());
    }
}

$orders = [];
$fetch_error_orders = null;
if ($tab === 'orders') {
    try {
        $stmt = $pdo->query("
            SELECT o.id, o.total_amount, o.status, o.created_at,
                   o.phone, o.address, o.payment_method, o.notes,
                   u.full_name, u.email
            FROM orders o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
        ");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $fetch_error_orders = htmlspecialchars($e->getMessage());
    }
}

$customers = [];
$fetch_error_users = null;
if ($tab === 'users') {
    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.full_name, u.email, u.created_at,
                   COUNT(o.id) AS order_count
            FROM users u
            LEFT JOIN orders o ON o.user_id = u.id
            WHERE u.role = 'customer'
            GROUP BY u.id
            ORDER BY u.created_at DESC
        ");
        $stmt->execute();
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $fetch_error_users = htmlspecialchars($e->getMessage());
    }
}

// Summary stats always shown in header cards
$stats = ['products' => 0, 'orders' => 0, 'users' => 0, 'revenue' => 0];
try {
    $stats['products'] = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
    $stats['orders']   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $stats['users']    = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
    $stats['revenue']  = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status = 'completed'")->fetchColumn();
} catch (PDOException $e) {}
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg:         #f18242a9;
    --surface:      #d48e3241;
    --surface2:   #faf9f7;
    --border:     #ff8777;
    --border2:    #d5d1c9;
    --text:       #1a1814;
    --text2:      #1f1f1f;
    --text3:      #030303;
    --primary:    #c84b2f;
    --primary-dk: #a83820;
    --primary-lt: #fdf1ee;
    --success:    #2d7a4f;
    --success-lt: #edf7f2;
    --warning:    #b45309;
    --warning-lt: #fef3e2;
    --danger:     #b91c1c;
    --danger-lt:  #fef2f2;
    --info:       #1e5fa5;
    --info-lt:    #eff6ff;
    --radius:     10px;
    --radius-lg:  14px;
    --shadow:     0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:  0 4px 16px rgba(0,0,0,.1), 0 2px 4px rgba(0,0,0,.06);
}
body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); font-size: 14px; line-height: 1.5; min-height: 100vh; }

/* Layout */
.adm-wrap { display: flex; min-height: 100vh; }

/* Sidebar */
.adm-sidebar { width: 220px; flex-shrink: 0; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; overflow-y: auto; z-index: 100; }
.adm-logo { padding: 20px 20px 16px; border-bottom: 1px solid var(--border); }
.adm-logo-title { font-size: 15px; font-weight: 600; color: var(--primary); letter-spacing: -.2px; }
.adm-logo-sub { font-size: 11px; color: var(--text3); margin-top: 1px; }
.adm-nav { padding: 12px 10px; flex: 1; }
.adm-nav-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: var(--text3); padding: 8px 10px 4px; }
.adm-nav-link { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: var(--radius); text-decoration: none; color: var(--text2); font-size: 13.5px; font-weight: 400; transition: background .15s, color .15s; margin-bottom: 2px; }
.adm-nav-link .material-icons-round { font-size: 18px; }
.adm-nav-link:hover { background: var(--bg); color: var(--text); }
.adm-nav-link.active { background: var(--primary-lt); color: var(--primary); font-weight: 500; }
.adm-nav-link.active .material-icons-round { color: var(--primary); }
.adm-sidebar-footer { padding: 14px 16px; border-top: 1px solid var(--border); font-size: 12px; color: var(--text3); }
.adm-sidebar-footer strong { color: var(--text2); display: block; font-size: 13px; }

/* Main */
.adm-main { flex: 1; min-width: 0; }
.adm-topbar { background: var(--surface); border-bottom: 1px solid var(--border); padding: 0 28px; height: 56px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 50; gap: 12px; }
.adm-topbar-title { font-size: 15px; font-weight: 600; color: var(--text); }
.adm-hamburger { display: none; background: none; border: none; cursor: pointer; padding: 4px; color: var(--text); }
.adm-hamburger .material-icons-round { font-size: 22px; }
.adm-content { padding: 24px 28px; }

/* Stat cards */
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 14px; margin-bottom: 24px; }
.stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 16px 18px; display: flex; align-items: center; gap: 14px; box-shadow: var(--shadow); }
.stat-icon { width: 40px; height: 40px; border-radius: var(--radius); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-icon .material-icons-round { font-size: 20px; }
.stat-icon.red   { background: var(--primary-lt); color: var(--primary); }
.stat-icon.green { background: var(--success-lt); color: var(--success); }
.stat-icon.blue  { background: var(--info-lt);    color: var(--info); }
.stat-icon.amber { background: var(--warning-lt); color: var(--warning); }
.stat-label { font-size: 11px; color: var(--text3); font-weight: 500; text-transform: uppercase; letter-spacing: .05em; }
.stat-value { font-size: 22px; font-weight: 600; color: var(--text); line-height: 1.2; margin-top: 1px; }

/* Section card */
.section-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow); overflow: hidden; margin-bottom: 20px; }
.section-head { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
.section-head h3 { font-size: 14px; font-weight: 600; color: var(--text); }
.section-body { padding: 20px; }

/* Search */
.search-wrap { position: relative; display: flex; align-items: center; }
.search-wrap .material-icons-round { position: absolute; left: 10px; font-size: 16px; color: var(--text3); pointer-events: none; }
.search-input { padding: 7px 12px 7px 34px; border: 1px solid var(--border2); border-radius: 8px; font-size: 13px; font-family: inherit; background: var(--surface2); color: var(--text); outline: none; width: 200px; transition: border-color .15s, width .2s; }
.search-input:focus { border-color: var(--primary); background: var(--surface); width: 240px; }
.filters-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.filter-select { padding: 7px 28px 7px 10px; border: 1px solid var(--border2); border-radius: 8px; font-size: 13px; font-family: inherit; background: var(--surface2); color: var(--text); outline: none; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239e9a94' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 8px center; cursor: pointer; }
.filter-select:focus { border-color: var(--primary); }

/* Table */
.tbl-wrap { overflow-x: auto; }
table.adm-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.adm-table th { text-align: left; padding: 10px 16px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--text3); background: var(--surface2); border-bottom: 1px solid var(--border); white-space: nowrap; }
.adm-table td { padding: 12px 16px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }
.adm-table tbody tr:last-child td { border-bottom: none; }
.adm-table tbody tr:hover td { background: #fdfcfa; }
.prod-img { width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border); display: block; flex-shrink: 0; }
.no-img { width: 44px; height: 44px; border-radius: 8px; background: var(--bg); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.no-img .material-icons-round { font-size: 18px; color: var(--text3); }
.prod-name { font-weight: 500; }
.prod-desc { font-size: 12px; color: var(--text3); margin-top: 2px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Badges */
.badge { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 500; white-space: nowrap; }
.badge-success { background: var(--success-lt); color: var(--success); }
.badge-warning { background: var(--warning-lt); color: var(--warning); }
.badge-danger  { background: var(--danger-lt);  color: var(--danger); }
.badge-info    { background: var(--info-lt);     color: var(--info); }
.badge-neutral { background: var(--bg); color: var(--text2); border: 1px solid var(--border); }

/* Buttons */
.btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 14px; border-radius: 8px; font-size: 13px; font-family: inherit; font-weight: 500; cursor: pointer; border: 1px solid transparent; text-decoration: none; transition: background .15s; white-space: nowrap; }
.btn .material-icons-round { font-size: 16px; }
.btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
.btn-primary:hover { background: var(--primary-dk); border-color: var(--primary-dk); }
.btn-outline { background: var(--surface); color: var(--text2); border-color: var(--border2); }
.btn-outline:hover { background: var(--bg); }
.btn-ghost { background: none; color: var(--text2); border: none; padding: 6px 8px; border-radius: 7px; }
.btn-ghost:hover { background: var(--bg); color: var(--text); }
.btn-ghost.danger:hover { background: var(--danger-lt); color: var(--danger); }
.btn-ghost.warn:hover   { background: var(--warning-lt); color: var(--warning); }
.btn-ghost.go:hover     { background: var(--success-lt); color: var(--success); }
.btn-sm { padding: 5px 10px; font-size: 12px; }
.btn-sm .material-icons-round { font-size: 14px; }
.action-row { display: flex; align-items: center; gap: 2px; }

/* Forms */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-full { grid-column: 1 / -1; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group label { font-size: 12px; font-weight: 500; color: var(--text2); }
.form-control { padding: 9px 12px; border: 1px solid var(--border2); border-radius: 8px; font-size: 13px; font-family: inherit; color: var(--text); background: var(--surface); outline: none; width: 100%; transition: border-color .15s; }
.form-control:focus { border-color: var(--primary); }
textarea.form-control { resize: vertical; min-height: 80px; }
select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239e9a94' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; padding-right: 28px; cursor: pointer; }

/* Alert */
.alert { padding: 10px 14px; border-radius: var(--radius); font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.alert .material-icons-round { font-size: 16px; flex-shrink: 0; }
.alert-error   { background: var(--danger-lt);  color: var(--danger); }
.alert-success { background: var(--success-lt); color: var(--success); }

/* Empty state */
.empty-state { text-align: center; padding: 48px 20px; color: var(--text3); }
.empty-state .material-icons-round { font-size: 40px; display: block; margin: 0 auto 10px; opacity: .5; }
.empty-state p { font-size: 13px; }

/* Status update inline form */
.status-form { display: flex; flex-direction: column; gap: 5px; min-width: 130px; }

/* Avatar */
.avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--primary-lt); color: var(--primary); font-size: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.user-cell { display: flex; align-items: center; gap: 10px; }
.user-name  { font-weight: 500; font-size: 13px; }
.user-email { font-size: 11px; color: var(--text3); }

/* Modal */
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.35); z-index: 200; align-items: center; justify-content: center; padding: 20px; }
.modal-overlay.open { display: flex; }
.modal-box { background: var(--surface); border-radius: var(--radius-lg); width: 100%; max-width: 460px; box-shadow: var(--shadow-md); display: flex; flex-direction: column; max-height: 90vh; }
.modal-head { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.modal-head h3 { font-size: 14px; font-weight: 600; }
.modal-body { padding: 20px; overflow-y: auto; }
.modal-section-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: var(--text3); margin: 14px 0 6px; }
.modal-section-label:first-child { margin-top: 0; }
.modal-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 7px 0; border-bottom: 1px solid var(--border); font-size: 13px; }
.modal-row:last-of-type { border-bottom: none; }
.modal-row-label { color: var(--text3); font-size: 12px; white-space: nowrap; }
.modal-row-val   { color: var(--text); text-align: right; }
.modal-items { list-style: none; padding: 0; }
.modal-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--border); font-size: 13px; gap: 10px; }
.modal-item:last-child { border-bottom: none; }
.modal-total { display: flex; justify-content: space-between; align-items: center; margin-top: 12px; padding-top: 12px; border-top: 2px solid var(--border); }
.modal-total-label { font-size: 13px; color: var(--text2); }
.modal-total-val   { font-size: 16px; font-weight: 600; color: var(--primary); }

/* Sidebar overlay for mobile */
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 90; }

/* Responsive */
@media (max-width: 860px) {
    .adm-sidebar { position: fixed; left: -220px; top: 0; height: 100vh; transition: left .25s ease; z-index: 95; }
    .adm-sidebar.open { left: 0; }
    .sidebar-overlay.open { display: block; }
    .adm-hamburger { display: flex; }
    .adm-content { padding: 16px; }
    .adm-topbar { padding: 0 16px; }
    .form-grid { grid-template-columns: 1fr; }
    .form-full { grid-column: 1; }
}
@media (max-width: 600px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
    .adm-table th:nth-child(3), .adm-table td:nth-child(3) { display: none; }
    .search-input { width: 150px; }
    .search-input:focus { width: 170px; }
}
@media (max-width: 420px) {
    .filters-row { flex-direction: column; align-items: stretch; }
    .search-wrap { width: 100%; }
    .search-input, .search-input:focus { width: 100%; }
    .filter-select { width: 100%; }
}
</style>

<div class="adm-wrap">
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside class="adm-sidebar" id="sidebar">
    <div class="adm-logo">
        <div class="adm-logo-title">Mr Jahabibi Admin</div>
        <div class="adm-logo-sub">Management Panel</div>
    </div>
    <nav class="adm-nav">
        <div class="adm-nav-label">Manage</div>
        <a href="?tab=menu"   class="adm-nav-link <?php echo $tab==='menu'   ?'active':''; ?>"><span class="material-icons-round">restaurant_menu</span> Menu List</a>
        <a href="?tab=orders" class="adm-nav-link <?php echo $tab==='orders' ?'active':''; ?>"><span class="material-icons-round">receipt_long</span> Orders</a>
        <a href="?tab=users"  class="adm-nav-link <?php echo $tab==='users'  ?'active':''; ?>"><span class="material-icons-round">group</span> Customers</a>
        <div class="adm-nav-label" style="margin-top:10px;">Account</div>
        <a href="<?php echo $rootPath; ?>includes/logout.php" class="order-btn"><span class="material-icons-round">logout</span> Sign Out</a>
    </nav>
    <div class="adm-sidebar-footer">
        Signed in as<br>
        <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>
    </div>
</aside>

<!-- Main -->
<div class="adm-main">
    <div class="adm-topbar">
        <div style="display:flex;align-items:center;gap:10px;">
            <button class="adm-hamburger" onclick="openSidebar()" aria-label="Open menu">
                <span class="material-icons-round">menu</span>
            </button>
            <span class="adm-topbar-title">
                <?php echo $tab==='menu' ? 'Menu List' : ($tab==='orders' ? 'Orders' : 'Customers'); ?>
            </span>
        </div>
        <div>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error" style="margin:0;padding:6px 12px;">
                    <span class="material-icons-round">error_outline</span><?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php elseif (isset($_GET['success'])): ?>
                <div class="alert alert-success" style="margin:0;padding:6px 12px;">
                    <span class="material-icons-round">check_circle</span><?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="adm-content">

        <!-- Stat cards -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon red"><span class="material-icons-round">restaurant_menu</span></div>
                <div><div class="stat-label">Active Items</div><div class="stat-value"><?php echo $stats['products']; ?></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><span class="material-icons-round">receipt_long</span></div>
                <div><div class="stat-label">Total Orders</div><div class="stat-value"><?php echo $stats['orders']; ?></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><span class="material-icons-round">group</span></div>
                <div><div class="stat-label">Customers</div><div class="stat-value"><?php echo $stats['users']; ?></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><span class="material-icons-round">payments</span></div>
                <div><div class="stat-label">Revenue</div><div class="stat-value">₱<?php echo number_format($stats['revenue'], 0); ?></div></div>
            </div>
        </div>


        <?php if ($tab === 'menu'): ?>
        <!-- ─── MENU TAB ─── -->

        <div class="section-card">
            <div class="section-head"><h3>Add New Item</h3></div>
            <div class="section-body">
                <?php if ($fetch_error_products): ?>
                    <div class="alert alert-error"><span class="material-icons-round">error_outline</span><?php echo $fetch_error_products; ?></div>
                <?php endif; ?>
                <form action="../actions/add_product_action.php" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group form-full">
                            <label>Product Name</label>
                            <input type="text" name="name" class="form-control" required maxlength="100" placeholder="e.g. Chicken Katsu Bento Ni Jorby">
                        </div>
                        <div class="form-group form-full">
                            <label>Description</label>
                            <textarea name="description" class="form-control" required placeholder="Short description..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Price (₱)</label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" class="form-control" required>
                                <option value="chicken_meals">Chicken Meals</option>
                                <option value="burger_meals">Burger Meals</option>
                                <option value="spaghetti_pasta">Spaghetti & Pasta</option>
                                <option value="rice_meals">Rice Meals</option>
                                <option value="breakfast_meals">Breakfast Meals</option>
                                <option value="desserts_drinks">Desserts & Drinks</option>
                                <option value="family_bundles">Family Bundles</option>
                                <option value="sides_snacks">Sides & Snacks</option>
                                <option value="beverages">Beverages</option>    
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group form-full">
                            <label>Product Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                    <div style="margin-top:16px;">
                        <button type="submit" class="btn btn-primary"><span class="material-icons-round">add</span> Add Product</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="section-card">
            <div class="section-head">
                <h3>Menu Items</h3>
                <div class="search-wrap">
                    <span class="material-icons-round">search</span>
                    <input type="text" class="search-input" id="menuSearch" placeholder="Search…" oninput="filterTable('menuSearch','menuTable')">
                </div>
            </div>
            <div class="tbl-wrap">
                <table class="adm-table" id="menuTable">
                    <thead><tr><th>Item</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="5"><div class="empty-state"><span class="material-icons-round">restaurant_menu</span><p>No items yet — add one above.</p></div></td></tr>
                    <?php else: foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <?php if (!empty($p['image_url'])): ?>
                                        <img src="../<?php echo htmlspecialchars($p['image_url']); ?>" alt="" class="prod-img">
                                    <?php else: ?>
                                        <div class="no-img"><span class="material-icons-round">image_not_supported</span></div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="prod-name"><?php echo htmlspecialchars($p['name']); ?></div>
                                        <div class="prod-desc"><?php echo htmlspecialchars($p['description'] ?? ''); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-neutral"><?php echo ucfirst($p['category'] ?? '—'); ?></span></td>
                            <td style="font-weight:500;white-space:nowrap;">₱<?php echo number_format((float)$p['price'], 2); ?></td>
                            <td><span class="badge <?php echo $p['is_active'] ? 'badge-success' : 'badge-neutral'; ?>"><?php echo $p['is_active'] ? 'Active' : 'Archived'; ?></span></td>
                            <td>
                                <div class="action-row">
                                    <a href="edit_product.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-ghost" title="Edit"><span class="material-icons-round">edit</span></a>
                                    <form method="POST" action="../actions/toggle_product_action.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                                        <input type="hidden" name="is_active" value="<?php echo $p['is_active'] ? 0 : 1; ?>">
                                        <button type="submit" class="btn btn-ghost <?php echo $p['is_active'] ? 'warn' : 'go'; ?>" title="<?php echo $p['is_active'] ? 'Archive' : 'Restore'; ?>">
                                            <span class="material-icons-round"><?php echo $p['is_active'] ? 'visibility_off' : 'visibility'; ?></span>
                                        </button>
                                    </form>
                                    <form method="POST" action="../actions/delete_product_action.php" style="display:inline;" onsubmit="return confirm('Permanently delete this product?');">
                                        <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                                        <button type="submit" class="btn btn-ghost danger" title="Delete"><span class="material-icons-round">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <?php elseif ($tab === 'orders'): ?>
        <!-- ─── ORDERS TAB ─── -->

        <div class="section-card">
            <div class="section-head">
                <h3>All Orders</h3>
                <div class="filters-row">
                    <select class="filter-select" id="statusFilter" onchange="filterByStatus()">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <div class="search-wrap">
                        <span class="material-icons-round">search</span>
                        <input type="text" class="search-input" id="orderSearch" placeholder="Search customer…" oninput="filterTable('orderSearch','ordersTable')">
                    </div>
                </div>
            </div>
            <?php if ($fetch_error_orders): ?>
                <div class="section-body"><div class="alert alert-error"><span class="material-icons-round">error_outline</span><?php echo $fetch_error_orders; ?></div></div>
            <?php elseif (empty($orders)): ?>
                <div class="empty-state"><span class="material-icons-round">receipt_long</span><p>No orders yet.</p></div>
            <?php else: ?>
            <div class="tbl-wrap">
                <table class="adm-table" id="ordersTable">
                    <thead><tr><th>#</th><th>Customer</th><th>Email</th><th>Date</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr data-status="<?php echo htmlspecialchars($o['status']); ?>">
                            <td style="color:var(--text3);font-size:12px;white-space:nowrap;">#<?php echo (int)$o['id']; ?></td>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar"><?php echo strtoupper(substr($o['full_name'],0,2)); ?></div>
                                    <div class="user-name"><?php echo htmlspecialchars($o['full_name']); ?></div>
                                </div>
                            </td>
                            <td style="color:var(--text3);font-size:12px;"><?php echo htmlspecialchars($o['email']); ?></td>
                            <td style="white-space:nowrap;color:var(--text2);">
                                <?php echo date('M d, Y', strtotime($o['created_at'])); ?><br>
                                <span style="font-size:11px;color:var(--text3);"><?php echo date('H:i', strtotime($o['created_at'])); ?></span>
                            </td>
                            <td style="font-weight:500;white-space:nowrap;">₱<?php echo number_format((float)$o['total_amount'], 2); ?></td>
                            <td><span class="badge <?php echo get_status_class($o['status']); ?>"><?php echo ucfirst($o['status']); ?></span></td>
                            <td>
                                <div style="display:flex;flex-direction:column;gap:6px;">
                                    <form action="../actions/update_order_status_action.php" method="POST" class="status-form">
                                        <input type="hidden" name="order_id" value="<?php echo (int)$o['id']; ?>">
                                        <select name="new_status" class="form-control" style="font-size:12px;padding:5px 8px;">
                                            <option value="pending"   <?php echo $o['status']==='pending'   ?'selected':''; ?>>Pending</option>
                                            <option value="completed" <?php echo $o['status']==='completed' ?'selected':''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $o['status']==='cancelled' ?'selected':''; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline btn-sm"><span class="material-icons-round">sync</span> Update</button>
                                    </form>
                                    <button class="btn btn-ghost btn-sm" style="justify-content:center;"
                                        onclick="showOrderDetails(
                                            <?php echo (int)$o['id']; ?>,
                                            '<?php echo htmlspecialchars(addslashes($o['full_name'])); ?>',
                                            '<?php echo htmlspecialchars(ucfirst($o['status'])); ?>',
                                            '<?php echo date('M d, Y H:i', strtotime($o['created_at'])); ?>',
                                            '<?php echo number_format((float)$o['total_amount'], 2); ?>',
                                            '<?php echo htmlspecialchars(addslashes($o['address'] ?? 'N/A')); ?>',
                                            '<?php echo htmlspecialchars(addslashes($o['payment_method'] ?? 'N/A')); ?>',
                                            '<?php echo htmlspecialchars(addslashes($o['notes'] ?? 'None')); ?>'
                                        )">
                                        <span class="material-icons-round">open_in_new</span> Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>


        <?php elseif ($tab === 'users'): ?>
        <!-- ─── USERS TAB ─── -->

        <div class="section-card">
            <div class="section-head">
                <h3>Customers</h3>
                <div class="search-wrap">
                    <span class="material-icons-round">search</span>
                    <input type="text" class="search-input" id="userSearch" placeholder="Search…" oninput="filterTable('userSearch','usersTable')">
                </div>
            </div>
            <?php if ($fetch_error_users): ?>
                <div class="section-body"><div class="alert alert-error"><span class="material-icons-round">error_outline</span><?php echo $fetch_error_users; ?></div></div>
            <?php elseif (empty($customers)): ?>
                <div class="empty-state"><span class="material-icons-round">group</span><p>No customers yet.</p></div>
            <?php else: ?>
            <div class="tbl-wrap">
                <table class="adm-table" id="usersTable">
                    <thead><tr><th>#</th><th>Customer</th><th>Email</th><th>Joined</th><th>Orders</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td style="color:var(--text3);font-size:12px;">#<?php echo (int)$c['id']; ?></td>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar"><?php echo strtoupper(substr($c['full_name'],0,2)); ?></div>
                                    <div class="user-name"><?php echo htmlspecialchars($c['full_name']); ?></div>
                                </div>
                            </td>
                            <td style="color:var(--text3);font-size:12px;"><?php echo htmlspecialchars($c['email']); ?></td>
                            <td style="color:var(--text2);"><?php echo date('M d, Y', strtotime($c['created_at'])); ?></td>
                            <td><span class="badge badge-neutral"><?php echo (int)$c['order_count']; ?> orders</span></td>
                            <td>
                                <div class="action-row">
                                    <form method="POST" action="../actions/manage_user_action.php" style="display:inline;" onsubmit="return confirm('Promote this user to admin?');">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$c['id']; ?>">
                                        <input type="hidden" name="action" value="promote_to_admin">
                                        <button type="submit" class="btn btn-ghost" title="Promote to Admin" style="color:var(--info);"><span class="material-icons-round">admin_panel_settings</span></button>
                                    </form>
                                    <form method="POST" action="../actions/manage_user_action.php" style="display:inline;" onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$c['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-ghost danger" title="Delete"><span class="material-icons-round">person_remove</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <?php endif; ?>

    </div><!-- /.adm-content -->
</div><!-- /.adm-main -->
</div><!-- /.adm-wrap -->


<!-- Order Details Modal -->
<div class="modal-overlay" id="orderModal">
    <div class="modal-box">
        <div class="modal-head">
            <h3>Order <span id="mOrderId"></span></h3>
            <button class="btn btn-ghost" onclick="closeModal()"><span class="material-icons-round">close</span></button>
        </div>
        <div class="modal-body">
            <div class="modal-section-label">Summary</div>
            <div class="modal-row"><span class="modal-row-label">Customer</span><span class="modal-row-val" id="mCustomer"></span></div>
            <div class="modal-row"><span class="modal-row-label">Status</span><span class="modal-row-val" id="mStatus"></span></div>
            <div class="modal-row"><span class="modal-row-label">Date</span><span class="modal-row-val" id="mDate"></span></div>
            <div class="modal-row"><span class="modal-row-label">Address</span><span class="modal-row-val" id="mAddress"></span></div>
            <div class="modal-row"><span class="modal-row-label">Payment</span><span class="modal-row-val" id="mPayment"></span></div>
            <div class="modal-row"><span class="modal-row-label">Notes</span><span class="modal-row-val" id="mNotes"></span></div>
            <div class="modal-section-label">Items</div>
            <ul class="modal-items" id="mItems"><li class="modal-item" style="color:var(--text3);">Loading…</li></ul>
            <div class="modal-total">
                <span class="modal-total-label">Total</span>
                <span class="modal-total-val">₱<span id="mTotal"></span></span>
            </div>
        </div>
    </div>
</div>

<script>
function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebarOverlay').classList.add('open'); }
function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('open'); }
function closeModal()   { document.getElementById('orderModal').classList.remove('open'); }

function filterTable(inputId, tableId) {
    const q = document.getElementById(inputId).value.toLowerCase();
    document.querySelectorAll('#' + tableId + ' tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

function filterByStatus() {
    const s = document.getElementById('statusFilter').value.toLowerCase();
    document.querySelectorAll('#ordersTable tbody tr').forEach(r => {
        r.style.display = (!s || (r.dataset.status||'') === s) ? '' : 'none';
    });
}

function showOrderDetails(id, customer, status, date, total, address, payment, notes) {
    document.getElementById('mOrderId').textContent  = '#' + id;
    document.getElementById('mCustomer').textContent = customer;
    document.getElementById('mDate').textContent     = date;
    document.getElementById('mAddress').textContent  = address;
    document.getElementById('mPayment').textContent  = payment;
    document.getElementById('mNotes').textContent    = notes;
    document.getElementById('mTotal').textContent    = total;

    const cls = status.toLowerCase() === 'completed' ? 'badge-success'
              : status.toLowerCase() === 'cancelled'  ? 'badge-danger' : 'badge-warning';
    document.getElementById('mStatus').innerHTML = '<span class="badge ' + cls + '">' + status + '</span>';

    const list = document.getElementById('mItems');
    list.innerHTML = '<li class="modal-item" style="color:var(--text3);">Loading…</li>';

    fetch('../actions/get_order_items_action.php?order_id=' + id)
        .then(r => r.json())
        .then(data => {
            list.innerHTML = '';
            if (!data.length) { list.innerHTML = '<li class="modal-item" style="color:var(--text3);">No items found.</li>'; return; }
            data.forEach(item => {
                const li = document.createElement('li');
                li.className = 'modal-item';
                li.innerHTML = '<span style="color:var(--text2);">' + esc(item.name) + ' &times; ' + item.quantity + '</span>'
                             + '<span style="font-weight:500;">₱' + parseFloat(item.subtotal).toFixed(2) + '</span>';
                list.appendChild(li);
            });
        })
        .catch(() => { list.innerHTML = '<li class="modal-item" style="color:var(--danger);">Could not load items.</li>'; });

    document.getElementById('orderModal').classList.add('open');
}

function esc(str) { const d = document.createElement('div'); d.textContent = str; return d.innerHTML; }

window.addEventListener('click',   e => { if (e.target === document.getElementById('orderModal')) closeModal(); });
window.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>

</body>
</html>