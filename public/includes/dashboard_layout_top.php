<?php

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
$isAdminLayout = isset($isAdminTheme)
    ? (bool)$isAdminTheme
    : (strpos($currentPath, '/admin/') !== false);

$headerLogo = '/rappel/public/assets/img/logo.png';
$headerLogoClass = $isAdminLayout ? 'h-12 w-auto object-contain' : 'h-9 w-auto object-contain';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/head.php'; ?>
</head>
<body class="<?= $isAdminLayout ? 'admin-theme-dark ' : '' ?>bg-[#D3D3D3] font-sans antialiased overflow-x-hidden">

<?php include __DIR__ . '/sidebar.php'; ?>

<!-- Main Content -->
<main id="dashboard-main" class="min-h-screen transition-all duration-300 ease-in-out"
      style="padding-left:260px;">

    <!-- Mobile Header -->
    <div id="mobile-header" class="sticky top-0 z-20 <?= $isAdminLayout ? 'h-20 bg-slate-900/90 border-b border-slate-800 text-slate-100' : 'h-16 bg-white/70 border-b border-white/50' ?>
                                    backdrop-blur-md flex items-center justify-between px-4 shadow-sm lg:hidden">
        <div class="flex items-center gap-3">
            <button onclick="openSidebar()" class="p-2 -ml-2 <?= $isAdminLayout ? 'text-slate-200 hover:bg-slate-800' : 'text-navy-600 hover:bg-navy-50' ?> rounded-lg transition-colors">
                <i data-lucide="menu" style="width:24px;height:24px;"></i>
            </button>
            <a href="/rappel/public/">
                <img src="<?= htmlspecialchars($headerLogo) ?>" alt="Rappelez-moi" class="<?= htmlspecialchars($headerLogoClass) ?>">
            </a>
        </div>
        <div class="w-8 h-8 rounded-full <?= $isAdminLayout ? 'bg-gradient-to-r from-slate-600 to-slate-800' : 'bg-gradient-to-r from-brand-500 to-indigo-600' ?>"></div>
    </div>

    <!-- Page Content -->
    <div class="p-4 lg:p-8 max-w-[1600px] mx-auto animate-fade-in">

    <?php if ($isAdminLayout): ?>
    <div class="admin-banner">
        <i data-lucide="shield-check" style="width:15px;height:15px;"></i>
        <span>Mode Administrateur</span>
        <span style="margin-left:auto;font-weight:500;text-transform:none;letter-spacing:0;font-size:0.78rem;opacity:0.9;">Acces restreint</span>
    </div>
    <?php endif; ?>
