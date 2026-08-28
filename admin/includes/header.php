<?php

// ============================================
// ONPLAY ADMIN HEADER
// ============================================

require_once __DIR__ . '/../../src/bootstrap.php';

if (function_exists('require_login')) {
    require_login();
}

$u = function_exists('current_user') ? current_user() : [];

if (!is_array($u)) {
    $u = [];
}

$page = isset($page) ? $page : 'لوحة التحكم';

$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$type = $_GET['type'] ?? '';

$username = $u['username'] ?? 'Admin';
$role = $u['role'] ?? 'admin';

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$firstLetter = strtoupper(substr((string)$username, 0, 1));

if ($firstLetter === '') {
    $firstLetter = 'A';
}

function onplay_is_owner($user) {
    return is_array($user)
        && isset($user['role'])
        && $user['role'] === 'owner';
}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover"
    >

    <meta name="theme-color" content="#09090b">

    <title><?= e($page) ?> - ONPLAY</title>

    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;500;600;700&display=swap"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="assets/admin.css?v=10"
    >

</head>

<body>

<!-- الخلفية عند فتح القائمة -->
<div id="sidebarOverlay"></div>

<div class="layout">

    <!-- =========================
         SIDEBAR
    ========================== -->

    <aside id="side" class="sidebar">

        <div class="sidebar-header">

            <div class="brand">

                <div class="brand-icon">
                    <i class="fa-solid fa-play"></i>
                </div>

                <span>ONPLAY</span>

            </div>

            <!-- زر الإغلاق -->
            <button
                type="button"
                class="close-menu"
                id="closeMenu"
                aria-label="إغلاق القائمة"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        <!-- القائمة -->
        <nav class="sidebar-nav">

            <a
                href="index.php"
                class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>"
            >
                <span class="nav-icon">
                    <i class="fa-solid fa-house"></i>
                </span>

                <span class="nav-text">
                    الرئيسية
                </span>
            </a>


            <a
                href="content.php?type=banners"
                class="nav-link <?= $type === 'banners' ? 'active' : '' ?>"
            >
                <span class="nav-icon">
                    <i class="fa-solid fa-images"></i>
                </span>

                <span class="nav-text">
                    البنرات
                </span>
            </a>


            <a
                href="content.php?type=categories"
                class="nav-link <?= $type === 'categories' ? 'active' : '' ?>"
            >
                <span class="nav-icon">
                    <i class="fa-solid fa-table-cells-large"></i>
                </span>

                <span class="nav-text">
                    التصنيفات
                </span>
            </a>


            <a
                href="content.php?type=channels"
                class="nav-link <?= $type === 'channels' ? 'active' : '' ?>"
            >
                <span class="nav-icon">
                    <i class="fa-solid fa-tv"></i>
                </span>

                <span class="nav-text">
                    القنوات
                </span>
            </a>


            <a
                href="content.php?type=matches"
                class="nav-link <?= $type === 'matches' ? 'active' : '' ?>"
            >
                <span class="nav-icon">
                    <i class="fa-solid fa-futbol"></i>
                </span>

                <span class="nav-text">
                    المباريات
                </span>
            </a>


            <a
                href="content.php?type=movies"
                class="nav-link <?= $type === 'movies' ? 'active' : '' ?>"
            >
                <span class="nav-icon">
                    <i class="fa-solid fa-film"></i>
                </span>

                <span class="nav-text">
                    الأفلام
                </span>
            </a>


            <a
                href="content.php?type=series"
                class="nav-link <?= $type === 'series' ? 'active' : '' ?>"
            >
                <span class="nav-icon">
                    <i class="fa-solid fa-clapperboard"></i>
                </span>

                <span class="nav-text">
                    المسلسلات
                </span>
            </a>


            <a
                href="content.php?type=notifications"
                class="nav-link <?= $type === 'notifications' ? 'active' : '' ?>"
            >
                <span class="nav-icon">
                    <i class="fa-solid fa-bell"></i>
                </span>

                <span class="nav-text">
                    الإشعارات
                </span>
            </a>


            <div class="nav-separator"></div>


            <a
                href="settings.php"
                class="nav-link <?= $currentPage === 'settings.php' ? 'active' : '' ?>"
            >
                <span class="nav-icon">
                    <i class="fa-solid fa-gear"></i>
                </span>

                <span class="nav-text">
                    الإعدادات
                </span>
            </a>


            <?php if (onplay_is_owner($u)): ?>

                <a
                    href="users.php"
                    class="nav-link <?= $currentPage === 'users.php' ? 'active' : '' ?>"
                >
                    <span class="nav-icon">
                        <i class="fa-solid fa-users-gear"></i>
                    </span>

                    <span class="nav-text">
                        حسابات الإدارة
                    </span>
                </a>

            <?php endif; ?>


            <div class="sidebar-bottom">

                <a href="logout.php" class="nav-link logout">

                    <span class="nav-icon">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </span>

                    <span class="nav-text">
                        تسجيل الخروج
                    </span>

                </a>

            </div>

        </nav>

    </aside>


    <!-- =========================
         MAIN CONTENT
    ========================== -->

    <main id="mainContent">

        <!-- =========================
             TOPBAR
        ========================== -->

        <header class="topbar">

            <!-- زر فتح القائمة -->
            <button
                type="button"
                class="menu-button"
                id="openMenu"
                aria-label="فتح القائمة"
            >
                <i class="fa-solid fa-bars"></i>
            </button>


            <!-- عنوان الصفحة -->
            <div class="topbar-title">
                <?= e($page) ?>
            </div>


            <!-- المستخدم -->
            <div class="user-menu">

                <div class="user-avatar">
                    <?= e($firstLetter) ?>
                </div>

                <div class="user-details">

                    <strong>
                        <?= e($username) ?>
                    </strong>

                    <small>
                        <?= e($role) ?>
                    </small>

                </div>

            </div>

        </header>


        <!-- محتوى الصفحة -->
        <div class="content">