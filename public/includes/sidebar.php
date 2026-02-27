<?php
// sidebar.php - Dashboard sidebar
// Requires: auth.php to be included first (for getCurrentUser())
$user = getCurrentUser();
$userEmail = $user['email'] ?? 'utilisateur@example.com';
$userRole = $user['role'] ?? 'provider';
$isAdminUser = $userRole === 'admin';

// Determine current page for active state
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
$currentQuery = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY) ?? '';
parse_str($currentQuery, $queryParams);
$currentAdminView = $queryParams['view'] ?? 'overview';
$isAdminArea = strpos($currentPath, '/rappel/public/admin/') === 0 || strpos($currentPath, '/admin/') !== false;

// Use a stable logo file for admin to avoid visibility/cropping issues
$sidebarLogo = '/rappel/public/assets/img/logo.png';
$sidebarLogoFullClass = $isAdminArea ? 'h-12 w-auto object-contain sidebar-logo-full' : 'h-10 w-auto object-contain sidebar-logo-full';
$sidebarLogoIconClass = $isAdminArea ? 'h-9 w-auto object-contain sidebar-logo-icon hidden' : 'h-8 w-auto object-contain sidebar-logo-icon hidden';

$menuItems = [];

if ($isAdminUser) {
    $menuItems[] = ['icon' => 'shield', 'label' => 'Vue globale', 'path' => '/rappel/public/admin/dashboard.php?view=overview', 'admin_view' => 'overview', 'is_admin' => true];
} elseif ($userRole === 'client') {
    $menuItems[] = ['icon' => 'layout-dashboard', 'label' => 'Mon Espace', 'path' => '/rappel/public/client/dashboard.php'];
    $menuItems[] = ['icon' => 'clock', 'label' => 'Mes Demandes', 'path' => '/rappel/public/client/dashboard.php#requests'];
    $menuItems[] = ['icon' => 'settings', 'label' => 'Paramètres', 'path' => '/rappel/public/client/settings.php'];
} else {
    $menuItems[] = ['icon' => 'layout-dashboard', 'label' => 'Tableau de bord', 'path' => '/rappel/public/pro/dashboard.php'];
    $menuItems[] = ['icon' => 'inbox', 'label' => 'Mes Leads', 'path' => '/rappel/public/pro/leads.php'];
    $menuItems[] = ['icon' => 'users', 'label' => 'Mes Clients', 'path' => '/rappel/public/pro/clients.php'];
    $menuItems[] = ['icon' => 'file-text', 'label' => 'Mes Devis', 'path' => '/rappel/public/pro/quotes.php'];
    $menuItems[] = ['icon' => 'bar-chart-2', 'label' => 'Performance', 'path' => '/rappel/public/pro/performance.php'];
    $menuItems[] = ['icon' => 'credit-card', 'label' => 'Plans & Tarifs', 'path' => '/rappel/public/pro/pricing.php'];
    $menuItems[] = ['icon' => 'settings', 'label' => 'Paramètres', 'path' => '/rappel/public/pro/settings.php'];
}

if ($isAdminUser) {
    $menuItems[] = ['icon' => 'credit-card', 'label' => 'Forfaits', 'path' => '/rappel/public/admin/dashboard.php?view=plans', 'admin_view' => 'plans', 'is_admin' => true];
    $menuItems[] = ['icon' => 'briefcase-business', 'label' => 'Prestataires', 'path' => '/rappel/public/admin/dashboard.php?view=providers', 'admin_view' => 'providers', 'is_admin' => true];
    $menuItems[] = ['icon' => 'inbox', 'label' => 'Leads', 'path' => '/rappel/public/admin/dashboard.php?view=leads', 'admin_view' => 'leads', 'is_admin' => true];
    $menuItems[] = ['icon' => 'shuffle', 'label' => 'Dispatch', 'path' => '/rappel/public/admin/dashboard.php?view=dispatch', 'admin_view' => 'dispatch', 'is_admin' => true];
    $menuItems[] = ['icon' => 'bar-chart-3', 'label' => 'Analytics', 'path' => '/rappel/public/admin/dashboard.php?view=analytics', 'admin_view' => 'analytics', 'is_admin' => true];
    $menuItems[] = ['icon' => 'settings', 'label' => 'Parametres', 'path' => '/rappel/public/admin/dashboard.php?view=settings', 'admin_view' => 'settings', 'is_admin' => true];
}
?>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-navy-900/20 backdrop-blur-sm z-30 lg:hidden hidden" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar"
       class="fixed top-0 left-0 h-screen z-40 flex flex-col bg-white/70 backdrop-blur-xl border-r border-white/50 shadow-glass transition-all duration-300 ease-in-out"
       style="width:260px;">

    <!-- Logo Section -->
    <div class="h-20 flex items-center px-6 border-b border-navy-100/50">
        <a href="/rappel/public/" class="flex items-center hover:opacity-80 transition-opacity">
            <img src="<?= htmlspecialchars($sidebarLogo) ?>" alt="Rappelez-moi" class="<?= htmlspecialchars($sidebarLogoFullClass) ?>">
            <img src="<?= htmlspecialchars($sidebarLogo) ?>" alt="R" class="<?= htmlspecialchars($sidebarLogoIconClass) ?>" style="<?= $isAdminArea ? 'max-width:54px;' : 'max-width:40px;' ?>">
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-6 px-3 space-y-1 overflow-y-auto">
        <div class="px-3 mb-6">
            <p class="text-[11px] font-black text-navy-400 uppercase tracking-[0.2em] mb-4 sidebar-label">Menu Principal</p>
            <?php
            $adminSeparatorShown = false;
            foreach ($menuItems as $item):
                $itemPath = parse_url($item['path'], PHP_URL_PATH);
                $itemQuery = parse_url($item['path'], PHP_URL_QUERY) ?? '';
                parse_str($itemQuery, $itemQueryParams);
                $isAdminItem = !empty($item['is_admin']);

                if (strpos($itemPath, '/rappel/public/admin/dashboard.php') === 0) {
                    $itemView = $item['admin_view'] ?? ($itemQueryParams['view'] ?? 'overview');
                    $isActive = (strpos($currentPath, '/rappel/public/admin/') === 0) && ($currentAdminView === $itemView);
                } else {
                    $isActive = $currentPath === $itemPath;
                }

                if ($isAdminItem && !$adminSeparatorShown && $isAdminUser):
                    $adminSeparatorShown = true;
            ?>
            <div class="admin-nav-separator mx-1 mt-8 mb-3 px-3 py-2 rounded-lg flex items-center gap-2 bg-navy-950 text-white shadow-lg">
                <i data-lucide="shield" style="width:14px;height:14px;flex-shrink:0;"></i>
                <span class="text-xs font-bold uppercase tracking-widest sidebar-label">Administration</span>
            </div>
            <?php endif; ?>
            <a href="<?= htmlspecialchars($item['path']) ?>"
               class="flex items-center gap-3.5 px-4 py-3.5 rounded-2xl transition-all duration-300 group relative overflow-hidden <?= $isAdminItem ? 'admin-nav-item' : '' ?> <?= $isActive ? 'bg-brand-600 text-white shadow-premium' : 'text-navy-800 hover:bg-white hover:shadow-sm hover:text-brand-600' ?>">
                <i data-lucide="<?= htmlspecialchars($item['icon']) ?>"
                   class="min-w-[22px] transition-transform group-hover:scale-110 <?= $isActive ? 'text-white' : 'text-navy-400 group-hover:text-brand-500' ?>"
                   style="width:22px;height:22px;"></i>
                <span class="font-black text-sm uppercase tracking-wide flex-1 sidebar-label"><?= htmlspecialchars($item['label']) ?></span>
                <?php if ($isActive): ?>
                    <div class="absolute right-0 top-3 bottom-3 w-1 bg-white/30 rounded-l-full"></div>
                <?php endif; ?>
                <?php if ($item['label'] === 'Leads' && !$isAdminUser): ?>
                    <span class="bg-accent-500 text-navy-950 text-[10px] font-black px-2 py-0.5 rounded-md sidebar-label shadow-sm">NEW</span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>


    </nav>

    <!-- User Profile Section v3 -->
    <div class="p-4 bg-white/40 backdrop-blur-md border-t border-navy-100/30 sidebar-user-container">
        <?php 
        $settingsLink = ($userRole === 'client') ? '/rappel/public/client/settings.php' : '/rappel/public/pro/settings.php';
        ?>
        <div class="flex items-center gap-3 p-3 rounded-[1.5rem] bg-white border border-navy-100/50 shadow-sm hover:shadow-md transition-all group/user cursor-pointer sidebar-user-section" onclick="window.location='<?= $settingsLink ?>'">

            <div class="relative flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-navy-950 text-white flex items-center justify-center font-black text-sm group-hover/user:scale-105 transition-transform overflow-hidden">
                    <?= strtoupper(substr($userEmail, 0, 1)) ?>
                </div>
                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-accent-500 border-2 border-white rounded-full shadow-sm animate-pulse-slow"></div>
            </div>
            <div class="overflow-hidden sidebar-user-info">
                <p class="text-xs font-black text-navy-950 truncate uppercase tracking-tight">
                    <?= htmlspecialchars(explode('@', $userEmail)[0]) ?>
                </p>
                <p class="text-[11px] text-navy-400 font-bold truncate uppercase tracking-widest">
                    <?= htmlspecialchars($userRole) ?>
                </p>
            </div>
        </div>

        <a href="/rappel/public/logout.php"
           class="mt-4 w-full inline-flex items-center justify-center gap-3 p-4 text-navy-400 hover:text-red-500 transition-colors font-black uppercase tracking-[0.2em] text-[11px] group sidebar-logout-btn">
            <i data-lucide="power" class="group-hover:rotate-90 transition-transform duration-500" style="width:16px;height:16px;"></i>
            <span class="sidebar-label sidebar-logout-text">Déconnexion</span>
        </a>
    </div>

    <!-- Toggle Button (desktop only) -->
    <button id="sidebar-toggle"
            onclick="toggleSidebar()"
            class="absolute -right-3 top-24 w-6 h-6 bg-white border border-navy-100 rounded-full shadow-md flex items-center justify-center text-navy-500 hover:text-brand-600 transition-colors z-50 hidden lg:flex">
        <i data-lucide="chevron-left" id="toggle-icon" style="width:14px;height:14px;"></i>
    </button>
</aside>
