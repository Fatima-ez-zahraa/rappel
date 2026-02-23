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
<body class="<?= $isAdminLayout ? 'admin-theme ' : '' ?>bg-[#D3D3D3] font-sans antialiased overflow-x-hidden">

<?php include __DIR__ . '/sidebar.php'; ?>

<!-- Main Content -->
<main id="dashboard-main" class="min-h-screen transition-all duration-300 ease-in-out"
      style="padding-left:260px;">

    <!-- Mobile Header -->
    <div id="mobile-header" class="sticky top-0 z-20 <?= $isAdminLayout ? 'h-20 bg-zinc-950/90 border-b border-zinc-900 text-zinc-100' : 'h-16 bg-white/70 border-b border-white/50' ?>
                                    backdrop-blur-md flex items-center justify-between px-4 shadow-sm lg:hidden">
        <div class="flex items-center gap-3">
            <button onclick="openSidebar()" class="p-2 -ml-2 <?= $isAdminLayout ? 'text-zinc-200 hover:bg-zinc-900' : 'text-navy-600 hover:bg-navy-50' ?> rounded-lg transition-colors">
                <i data-lucide="menu" style="width:24px;height:24px;"></i>
            </button>
            <a href="/rappel/public/">
                <img src="<?= htmlspecialchars($headerLogo) ?>" alt="Rappelez-moi" class="<?= htmlspecialchars($headerLogoClass) ?>">
            </a>
        </div>
        <div class="flex items-center gap-2">
            <?php if ($isAdminLayout): ?>
            <button onclick="toggleAdminTheme()" class="p-2 rounded-lg hover:bg-white/10 transition-colors" title="Changer de thème">
                <i id="theme-icon-mob" data-lucide="sun" style="width:20px;height:20px;"></i>
            </button>
            <?php endif; ?>
            <div class="w-8 h-8 rounded-full <?= $isAdminLayout ? 'bg-gradient-to-r from-slate-600 to-slate-800' : 'bg-gradient-to-r from-brand-500 to-indigo-600' ?>"></div>
        </div>
    </div>

    <!-- Desktop Theme Toggle for Admin -->
    <?php if ($isAdminLayout): ?>
    <div class="hidden lg:flex fixed top-6 right-8 z-50">
        <button onclick="toggleAdminTheme()" 
                class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 hover:border-white/20 transition-all shadow-xl group"
                title="Changer de thème">
            <i id="theme-icon-desk" data-lucide="sun" class="text-white group-hover:scale-110 transition-transform" style="width:20px;height:20px;"></i>
        </button>
    </div>
    <?php endif; ?>

    <!-- Page Content -->
    <div class="p-4 lg:p-8 max-w-[1600px] mx-auto animate-fade-in">

    <?php if ($isAdminLayout): ?>
    <script>
    (function() {
        const theme = localStorage.getItem('admin-theme') || 'dark';
        document.body.classList.remove('admin-theme-dark', 'admin-theme-light');
        document.body.classList.add('admin-theme-' + theme);
        
        window.toggleAdminTheme = function() {
            const current = document.body.classList.contains('admin-theme-dark') ? 'dark' : 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            
            document.body.classList.remove('admin-theme-' + current);
            document.body.classList.add('admin-theme-' + next);
            localStorage.setItem('admin-theme', next);
            
            updateThemeIcons(next);
        };
        
        function updateThemeIcons(theme) {
            const icons = ['theme-icon-mob', 'theme-icon-desk'];
            icons.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.setAttribute('data-lucide', theme === 'dark' ? 'sun' : 'moon');
                }
            });
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
        
        document.addEventListener('DOMContentLoaded', () => updateThemeIcons(theme));
    })();
    </script>
    <?php endif; ?>
