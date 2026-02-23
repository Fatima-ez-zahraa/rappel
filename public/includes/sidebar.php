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
} else {
    $menuItems[] = ['icon' => 'layout-dashboard', 'label' => 'Tableau de bord', 'path' => '/rappel/public/pro/dashboard.php'];
    $menuItems[] = ['icon' => 'credit-card', 'label' => 'Plans & Tarifs', 'path' => '/rappel/public/pro/pricing.php'];
    $menuItems[] = ['icon' => 'settings', 'label' => 'Parametres', 'path' => '/rappel/public/pro/settings.php'];
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
        <div class="admin-nav-separator mx-1 mt-3 mb-2 px-2 py-1.5 rounded-lg flex items-center gap-2">
            <i data-lucide="shield" style="width:13px;height:13px;flex-shrink:0;"></i>
            <span class="text-[10px] font-bold uppercase tracking-widest sidebar-label">Administration</span>
        </div>
        <?php endif; ?>
        <a href="<?= htmlspecialchars($item['path']) ?>"
           class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 group relative overflow-hidden <?= $isAdminItem ? 'admin-nav-item' : '' ?> <?= $isActive ? 'bg-brand-50 text-brand-700 shadow-sm' : 'text-navy-800 hover:bg-neutral-50 hover:text-navy-950' ?>">
            <?php if ($isActive): ?>
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-600 rounded-r-full"></div>
            <?php endif; ?>
            <i data-lucide="<?= htmlspecialchars($item['icon']) ?>"
               class="min-w-[22px] <?= $isActive ? 'text-brand-600' : 'text-neutral-400 group-hover:text-neutral-700' ?>"
               style="width:22px;height:22px;"></i>
            <span class="font-medium whitespace-nowrap sidebar-label"><?= htmlspecialchars($item['label']) ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- User Profile & Footer -->
    <div class="p-4 border-t border-navy-100/50">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-gradient-to-br from-navy-50 to-white border border-navy-100 transition-all sidebar-user-section">
            <div class="relative flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-navy-900 text-white flex items-center justify-center font-bold shadow-md text-sm">
                    <?= strtoupper(substr($userEmail, 0, 1)) ?>
                </div>
            </div>
            <div class="overflow-hidden sidebar-user-info">
                <p class="text-sm font-bold text-navy-950 truncate">
                    <?= htmlspecialchars(explode('@', $userEmail)[0]) ?>
                </p>
                <p class="text-xs text-navy-700 font-semibold truncate lowercase">
                    <?= htmlspecialchars($userRole) ?>
                </p>
            </div>
        </div>

        <a href="/rappel/public/logout.php"
           class="mt-3 w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 transition-colors font-bold sidebar-logout">
            <span class="sidebar-label">Se deconnecter</span>
        </a>
    </div>

    <!-- Toggle Button (desktop only) -->
    <button id="sidebar-toggle"
            onclick="toggleSidebar()"
            class="absolute -right-3 top-24 w-6 h-6 bg-white border border-navy-100 rounded-full shadow-md flex items-center justify-center text-navy-500 hover:text-brand-600 transition-colors z-50 hidden lg:flex">
        <i data-lucide="chevron-left" id="toggle-icon" style="width:14px;height:14px;"></i>
    </button>
</aside>
