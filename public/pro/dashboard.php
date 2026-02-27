<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Tableau de bord';
$user = getCurrentUser();
$token = getToken();
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
    <div class="animate-fade-in-up">
        <h1 class="text-4xl lg:text-5xl font-display font-black text-navy-950 tracking-tight leading-tight">Tableau de bord</h1>
        <div class="flex items-center gap-3 mt-3">
            <div class="flex -space-x-2">
                <div class="w-8 h-1 bg-brand-500 rounded-full"></div>
                <div class="w-4 h-1 bg-accent-500 rounded-full opacity-50"></div>
            </div>
            <p class="text-navy-400 font-bold uppercase text-[10px] tracking-[0.2em]">Bienvenue, <span class="text-navy-950"><?= htmlspecialchars($user['first_name'] ?? explode('@', $user['email'])[0]) ?></span></p>
        </div>
    </div>
    <div class="flex items-center gap-4 animate-fade-in-up" style="animation-delay: 100ms">
        <button onclick="loadDashboard()" class="h-14 w-14 flex items-center justify-center rounded-2xl bg-white border border-navy-100 text-navy-400 hover:text-navy-900 transition-all hover:shadow-lg active:scale-95 group">
            <i data-lucide="refresh-cw" class="group-hover:rotate-180 transition-transform duration-700" style="width:20px;height:20px;"></i>
        </button>
        <a href="/rappel/public/pro/leads.php" class="h-14 px-8 bg-brand-600 hover:bg-brand-700 text-white font-black rounded-2xl shadow-xl shadow-brand-500/20 transition-all active:scale-95 flex items-center gap-3 group">
            <!-- <i data-lucide="plus-circle" class="group-hover:rotate-90 transition-transform duration-300" style="width:20px;height:20px;"></i> -->
            <span>Gérer mes leads</span>
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div id="stats-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    <?php for ($i = 0; $i < 4; $i++): ?>
    <div class="glass-card p-8 animate-pulse rounded-[2.5rem] border-white/60">
        <div class="w-14 h-14 bg-navy-50 rounded-2xl mb-6"></div>
        <div class="h-10 bg-navy-50 rounded-xl w-1/2 mb-3"></div>
        <div class="h-4 bg-navy-50 rounded-lg w-3/4"></div>
    </div>
    <?php endfor; ?>
</div>

<!-- Main Content Grid -->
<div class="grid lg:grid-cols-3 gap-10">
    <!-- Recent Leads Section -->
    <div class="lg:col-span-2">
        <div class="glass-card rounded-[3rem] p-1 border-white/40 shadow-premium min-h-[600px] flex flex-col overflow-hidden bg-white/30 backdrop-blur-3xl">
            <div id="dashboard-tabs" class="p-8 pb-0 border-b border-white/20 bg-white/10 flex gap-8">
                <button onclick="switchTab('leads')" id="tab-leads" class="pb-4 text-sm font-black uppercase tracking-widest text-brand-600 border-b-4 border-brand-500 transition-all">Leads</button>
                <button onclick="switchTab('quotes')" id="tab-quotes" class="pb-4 text-sm font-black uppercase tracking-widest text-navy-400 hover:text-navy-950 transition-all">Devis</button>
                <button onclick="switchTab('clients')" id="tab-clients" class="pb-4 text-sm font-black uppercase tracking-widest text-navy-400 hover:text-navy-950 transition-all">Clients</button>
            </div>
            
            <div id="tab-content-container" class="flex-1 p-8 lg:p-10">
                <div id="recent-leads" class="space-y-6">
                    <div class="flex flex-col items-center justify-center py-32 opacity-40">
                        <div class="relative w-20 h-20 mb-6">
                            <div class="absolute inset-0 border-4 border-navy-100 rounded-full"></div>
                            <div class="absolute inset-0 border-4 border-brand-500 rounded-full border-t-transparent animate-spin"></div>
                        </div>
                        <p class="font-black text-navy-900 text-sm uppercase tracking-widest">Synchronisation...</p>
                    </div>
                </div>
                <div id="recent-quotes" class="hidden space-y-6"></div>
                <div id="recent-clients" class="hidden space-y-6"></div>
            </div>
        </div>
    </div>

    <!-- Sidebar Section -->
    <div class="space-y-10">
        <!-- Quick Actions -->
        <div class="glass-card rounded-[3rem] p-10 border-white/40 shadow-premium bg-white/40 backdrop-blur-3xl">
            <h2 class="text-xl font-display font-black text-navy-950 mb-8 tracking-tight uppercase text-center">Pilotage</h2>
            <div class="grid grid-cols-1 gap-5">
                <?php
                $actions = [
                    ['url' => '/rappel/public/pro/leads.php', 'icon' => 'users', 'color' => 'bg-brand-100 text-brand-700', 'title' => 'Mes Leads', 'desc' => 'Gérer les contacts'],
                    ['url' => '/rappel/public/pro/clients.php', 'icon' => 'shield-check', 'color' => 'bg-emerald-100 text-emerald-700', 'title' => 'Mes Clients', 'desc' => 'Base de données'],
                    ['url' => '/rappel/public/pro/quotes.php', 'icon' => 'file-text', 'color' => 'bg-accent-100 text-accent-700', 'title' => 'Mes Devis', 'desc' => 'Suivre vos offres'],
                    ['url' => '/rappel/public/pro/performance.php', 'icon' => 'activity', 'color' => 'bg-indigo-100 text-indigo-700', 'title' => 'Analytique', 'desc' => 'Vos performances'],
                    ['url' => '/rappel/public/pro/settings.php', 'icon' => 'settings', 'color' => 'bg-navy-100 text-navy-700', 'title' => 'Compte', 'desc' => 'Configuration']
                ];
                foreach ($actions as $act):
                ?>
                <a href="<?= $act['url'] ?>" class="flex items-center gap-5 p-5 rounded-[2rem] bg-white/30 border border-white hover:bg-white hover:shadow-xl hover:shadow-navy-900/5 transition-all group overflow-hidden relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-14 h-14 <?= $act['color'] ?> rounded-2xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform relative z-10">
                        <i data-lucide="<?= $act['icon'] ?>" style="width:24px;height:24px;"></i>
                    </div>
                    <div class="flex-1 min-w-0 relative z-10">
                        <p class="font-black text-navy-950 text-sm tracking-tight mb-0.5"><?= $act['title'] ?></p>
                        <p class="text-[10px] text-navy-400 font-bold uppercase tracking-widest"><?= $act['desc'] ?></p>
                    </div>
                    <i data-lucide="chevron-right" class="text-navy-100 group-hover:text-navy-400 group-hover:translate-x-1 transition-all relative z-10" style="width:20px;height:20px;"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
$safeToken = addslashes($token ?? '');
$extraScript = <<<'JS'
<script>
const PHP_TOKEN = '__PHP_TOKEN__';


async function loadDashboard() {
    try {
        if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);
        const [stats, leads, quotes] = await Promise.all([
            apiFetch('/stats'),
            apiFetch('/leads'),
            apiFetch('/quotes'),
        ]);

        renderStats(stats || {});
        renderRecentLeads(Array.isArray(leads) ? leads : []);
        renderRecentQuotes(Array.isArray(quotes) ? quotes : []);
        renderRecentClients(Array.isArray(quotes) ? quotes.filter(q => q.status === 'accepted' || q.status === 'signe') : []);
    } catch (err) {
        console.error('Dashboard load error:', err);
        renderStats({});
        renderRecentLeads([]);
    }
}

function switchTab(tab) {
    ['leads', 'quotes', 'clients'].forEach(t => {
        document.getElementById(`tab-${t}`).className = t === tab 
            ? 'pb-4 text-sm font-black uppercase tracking-widest text-brand-600 border-b-4 border-brand-500 transition-all'
            : 'pb-4 text-sm font-black uppercase tracking-widest text-navy-400 hover:text-navy-950 transition-all';
        
        document.getElementById(`recent-${t}`).classList.toggle('hidden', t !== tab);
    });
}

function renderStats(s) {
    const grid = document.getElementById('stats-grid');
    if (!grid) return;

    const totalLeads = Number(s.totalLeads ?? 0);
    const pendingLeads = Number(s.pendingLeads ?? 0);
    const closedLeads = Number(s.closedLeads ?? 0);
    const conversionRate = Number(s.conversionRate ?? 0);

    const cards = [
        { label: 'Pipeline Global', value: totalLeads, icon: 'layers', iconColor: 'text-brand-600', trend: `+${s.revenueGrowth ?? 12}%`, bg: 'bg-brand-50' },
        { label: 'À traiter', value: pendingLeads, icon: 'clock', iconColor: 'text-amber-600', trend: 'Urgence', bg: 'bg-amber-50' },
        { label: 'Prospects traités', value: closedLeads, icon: 'check-circle-2', iconColor: 'text-accent-600', trend: 'Succès', bg: 'bg-accent-50' },
        { label: 'Efficacité', value: `${conversionRate}%`, icon: 'target', iconColor: 'text-indigo-600', trend: 'Score', bg: 'bg-indigo-50' },
    ];

    grid.innerHTML = cards.map((c, idx) => `
        <div class="glass-card p-8 rounded-[2.5rem] hover:shadow-2xl transition-all duration-700 group border-white/60 animate-fade-in-up" style="animation-delay: ${idx * 100}ms">
            <div class="flex items-center justify-between mb-8">
                <div class="w-14 h-14 ${c.bg} rounded-2xl flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform duration-500">
                    <i data-lucide="${c.icon}" class="${c.iconColor}" style="width:28px;height:28px;"></i>
                </div>
                <div class="px-3 py-1.5 bg-navy-50 text-[10px] font-black text-navy-400 uppercase tracking-widest rounded-xl transition-colors group-hover:bg-navy-950 group-hover:text-white">
                    ${c.trend}
                </div>
            </div>
            <p class="text-5xl font-display font-black text-navy-950 mb-2 tracking-tighter">${c.value}</p>
            <p class="text-[10px] font-black text-navy-400 uppercase tracking-[0.2em] flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full ${c.iconColor} opacity-50"></span>
                ${c.label}
            </p>
        </div>
    `).join('');

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function renderRecentLeads(leads) {
    const el = document.getElementById('recent-leads');
    if (!el) return;

    if (!leads.length) {
        el.innerHTML = `<div class="flex flex-col items-center justify-center py-24 opacity-60 animate-fade-in">
            <div class="w-24 h-24 bg-navy-50 rounded-[2.5rem] flex items-center justify-center mb-8 border border-navy-100/50 shadow-sm">
                <i data-lucide="inbox" class="text-navy-200" style="width:40px;height:40px;"></i>
            </div>
            <p class="text-2xl font-display font-black text-navy-950 tracking-tight uppercase">Aucun lead trouvé</p>
            <p class="text-navy-400 text-xs font-black uppercase tracking-widest mt-2">Votre pipeline est vide pour le moment</p>
        </div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    el.innerHTML = `<div class="grid grid-cols-1 gap-5 animate-fade-in max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">${leads.map((lead, idx) => {
        const name = lead.name || [lead.first_name, lead.last_name].filter(Boolean).join(' ') || 'Prospection';
        const phone = lead.phone || '-';
        const sector = lead.service_type || lead.sector || 'Général';
        const status = String(lead.status || '').toLowerCase();
        const isPending = status === 'pending' || status === 'attente_client';

        return `
        <div class="group flex items-center gap-6 p-6 rounded-[2rem] bg-white/40 hover:bg-white hover:shadow-xl hover:shadow-navy-950/5 border border-white transition-all duration-500 cursor-pointer animate-fade-in-up" 
             style="animation-delay: ${idx * 100}ms"
             onclick="window.location='/rappel/public/pro/leads.php'">
            
            <div class="relative flex-shrink-0">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-50 to-brand-100 flex items-center justify-center text-brand-700 font-black text-xl shadow-sm group-hover:scale-110 transition-transform duration-500">
                    ${escapeHtml(name.charAt(0).toUpperCase())}
                </div>
                ${isPending ? '<span class="absolute -top-1 -right-1 w-5 h-5 bg-amber-400 border-[6px] border-white rounded-full shadow-lg"></span>' : ''}
            </div>
            
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-1.5">
                    <p class="font-display font-black text-navy-950 text-lg lg:text-xl truncate tracking-tight uppercase leading-none">${escapeHtml(name)}</p>
                    <div class="h-6 px-3 flex items-center bg-brand-50 text-brand-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-brand-100 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                        ${escapeHtml(sector)}
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="phone" class="text-navy-300" style="width:14px;height:14px;"></i>
                        <p class="text-xs text-navy-500 font-bold tracking-tight">${escapeHtml(phone)}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar" class="text-navy-300" style="width:14px;height:14px;"></i>
                        <p class="text-[10px] text-navy-400 font-black uppercase tracking-widest">${formatRelativeTime(lead.created_at)}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-end gap-3 lg:border-l border-navy-50 lg:pl-8">
                <span class="badge py-2 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm ring-1 ring-inset ${isPending ? 'bg-amber-50 text-amber-600 ring-amber-500/10' : 'bg-accent-50 text-accent-600 ring-accent-500/10'}">
                    ${isPending ? 'Priorité maximale' : 'Clôturé'}
                </span>
                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-navy-50/50 text-navy-300 group-hover:bg-brand-600 group-hover:text-white transition-all transform group-hover:rotate-12">
                    <i data-lucide="arrow-up-right" style="width:20px;height:20px; stroke-width:3"></i>
                </div>
            </div>
        </div>`;
    }).join('')}</div>`;

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function renderRecentQuotes(quotes) {
    const el = document.getElementById('recent-quotes');
    if (!el) return;

    if (!quotes.length) {
        el.innerHTML = `
        <div class="flex flex-col items-center justify-center py-24 opacity-60 animate-fade-in">
            <div class="w-24 h-24 bg-navy-50 rounded-[2.5rem] flex items-center justify-center mb-8 border border-navy-100/50 shadow-sm">
                <i data-lucide="file-text" class="text-navy-200" style="width:40px;height:40px;"></i>
            </div>
            <p class="text-2xl font-display font-black text-navy-950 tracking-tight uppercase">Aucun devis</p>
            <p class="text-navy-400 text-xs font-black uppercase tracking-widest mt-2">Vous n'avez pas encore créé de devis</p>
        </div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    el.innerHTML = `<div class="grid grid-cols-1 gap-5 animate-fade-in max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">${quotes.map((q, idx) => `
        <div class="group flex items-center gap-6 p-6 rounded-[2rem] bg-white/40 hover:bg-white hover:shadow-xl hover:shadow-navy-950/5 border border-white transition-all duration-500 cursor-pointer animate-fade-in-up" 
             style="animation-delay: ${idx * 100}ms"
             onclick="window.location='/rappel/public/pro/quotes.php'">
            <div class="w-16 h-16 rounded-2xl bg-accent-50 text-accent-600 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-500">
                <i data-lucide="file-text" style="width:28px;height:28px;"></i>
            </div>
            <div class="flex-1">
                <p class="font-display font-black text-navy-950 text-lg uppercase leading-none mb-1.5">${escapeHtml(q.client_name || q.client || 'Client')}</p>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-navy-50 text-navy-400 rounded text-[9px] font-black uppercase tracking-widest">${escapeHtml(q.status)}</span>
                    <p class="text-[10px] text-navy-400 font-bold uppercase tracking-widest truncate max-w-[200px]">${escapeHtml(q.project_name || q.description || 'Projet sans titre')}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="font-display font-black text-navy-950 text-xl">${parseFloat(q.amount || 0).toLocaleString()} €</p>
                <p class="text-[9px] text-navy-400 font-bold uppercase tracking-widest mt-1">Total TTC</p>
            </div>
        </div>
    `).join('')}</div>`;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function renderRecentClients(clients) {
    const el = document.getElementById('recent-clients');
    if (!el) return;

    if (!clients.length) {
        el.innerHTML = `
        <div class="flex flex-col items-center justify-center py-24 opacity-60 animate-fade-in">
            <div class="w-24 h-24 bg-navy-50 rounded-[2.5rem] flex items-center justify-center mb-8 border border-navy-100/50 shadow-sm">
                <i data-lucide="users" class="text-navy-200" style="width:40px;height:40px;"></i>
            </div>
            <p class="text-2xl font-display font-black text-navy-950 tracking-tight uppercase">Aucun client</p>
            <p class="text-navy-400 text-xs font-black uppercase tracking-widest mt-2">Acceptez des devis pour voir vos clients ici</p>
        </div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    el.innerHTML = `<div class="grid grid-cols-1 gap-5 animate-fade-in max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">${clients.map((c, idx) => `
        <div class="group flex items-center gap-6 p-6 rounded-[2rem] bg-brand-50/30 border border-brand-100 hover:bg-white hover:shadow-xl hover:shadow-brand-500/5 hover:border-brand-300 transition-all duration-500 animate-fade-in-up" 
             style="animation-delay: ${idx * 100}ms">
            <div class="w-16 h-16 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-brand-500/20 group-hover:scale-110 transition-transform duration-500">
                ${escapeHtml((c.client_name || c.client || 'C').charAt(0).toUpperCase())}
            </div>
            <div class="flex-1">
                <p class="font-display font-black text-navy-950 text-lg uppercase leading-none mb-1.5">${escapeHtml(c.client_name || c.client || 'Client')}</p>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-brand-100 text-brand-700 rounded text-[9px] font-black uppercase tracking-widest">Client</span>
                    <p class="text-[10px] text-navy-400 font-bold uppercase tracking-widest">Contrat signé</p>
                </div>
            </div>
            <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-accent-50 text-accent-500 scale-90 group-hover:scale-110 transition-transform">
                <i data-lucide="shield-check" style="width:24px;height:24px;"></i>
            </div>
        </div>
    `).join('')}</div>`;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', loadDashboard);
</script>
JS;
$extraScript = str_replace('__PHP_TOKEN__', $safeToken, $extraScript);
?>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>

