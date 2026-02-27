<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Performances';
$token = getToken();
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12 animate-fade-in-up">
    <div>
        <h1 class="text-4xl lg:text-5xl font-display font-black text-navy-950 tracking-tight leading-tight">Analytique</h1>
        <div class="flex items-center gap-3 mt-3">
            <div class="w-12 h-1 bg-indigo-500 rounded-full"></div>
            <p class="text-navy-400 font-bold uppercase text-[10px] tracking-[0.2em]">Mesurez votre impact et votre croissance</p>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <div class="h-14 px-6 bg-white/50 backdrop-blur-xl border border-white/60 rounded-2xl flex items-center gap-4 shadow-sm">
            <i data-lucide="calendar" class="text-navy-400" style="width:18px;height:18px;"></i>
            <span class="text-xs font-black text-navy-900 uppercase tracking-widest">Derniers 30 jours</span>
        </div>
    </div>
</div>

<!-- Stats Overview -->
<div id="perf-stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 px-1">
    <?php for ($i = 0; $i < 4; $i++): ?>
    <div class="glass-card p-8 animate-pulse rounded-[2.5rem] border-white/60 bg-white/40">
        <div class="w-14 h-14 bg-navy-50 rounded-2xl mb-6"></div>
        <div class="h-10 bg-navy-50 rounded-xl w-1/2 mb-3"></div>
        <div class="h-4 bg-navy-50 rounded-lg w-3/4"></div>
    </div>
    <?php endfor; ?>
</div>

<!-- Charts Row -->
<div class="grid lg:grid-cols-2 gap-10 mb-10 px-1">
    <!-- Leads by Status -->
    <div class="glass-card rounded-[3rem] p-10 border-white/60 shadow-premium bg-white/30 backdrop-blur-3xl animate-fade-in-up" style="animation-delay: 200ms">
        <div class="flex items-center gap-4 mb-10">
            <div class="w-12 h-12 rounded-xl bg-accent-100 text-accent-700 flex items-center justify-center shadow-sm">
                <i data-lucide="pie-chart" style="width:24px;height:24px;"></i>
            </div>
            <div>
                <h2 class="text-xl font-display font-black text-navy-950 uppercase tracking-tight">Répartition</h2>
                <p class="text-navy-400 text-[10px] font-black uppercase tracking-widest">État de votre pipeline</p>
            </div>
        </div>
        <div id="status-chart" class="space-y-8"></div>
    </div>

    <!-- Monthly Activity -->
    <div class="glass-card rounded-[3rem] p-10 border-white/60 shadow-premium bg-white/30 backdrop-blur-3xl animate-fade-in-up" style="animation-delay: 300ms">
        <div class="flex items-center gap-4 mb-10">
            <div class="w-12 h-12 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center shadow-sm">
                <i data-lucide="bar-chart-3" style="width:24px;height:24px;"></i>
            </div>
            <div>
                <h2 class="text-xl font-display font-black text-navy-950 uppercase tracking-tight">Activité</h2>
                <p class="text-navy-400 text-[10px] font-black uppercase tracking-widest">Évolution mensuelle</p>
            </div>
        </div>
        <div id="monthly-chart" class="space-y-6"></div>
    </div>
</div>

<!-- Sector Breakdown -->
<!-- <div class="glass-card rounded-[3rem] p-10 border-white/60 shadow-premium bg-white/30 backdrop-blur-3xl animate-fade-in-up" style="animation-delay: 400ms">
    <div class="flex items-center gap-4 mb-10">
        <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shadow-sm">
            <i data-lucide="layers" style="width:24px;height:24px;"></i>
        </div>
        <div>
            <h2 class="text-xl font-display font-black text-navy-950 uppercase tracking-tight">Impact Sectoriel</h2>
            <p class="text-navy-400 text-[10px] font-black uppercase tracking-widest">Performance par catégorie</p>
        </div>
    </div>
    <div id="sector-breakdown" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
</div> -->

<?php
$safeToken = addslashes($token ?? '');
$extraScript = <<<'JS'
<script>
const PHP_TOKEN = 'RAPPEL_PHP_TOKEN_PLACEHOLDER';

async function loadPerformance() {
    if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);
    try {
        const stats = await apiFetch('/stats');
        renderPerfStats(stats);
        renderStatusChart(stats);
        if (stats.analytics) {
            renderMonthlyChart(stats.analytics.monthlyActivity);
            renderSectorBreakdown(stats.analytics.sectorImpact);
        }
    } catch (err) {
        console.error(err);
    }
}

function renderPerfStats(s) {
    const cards = [
        { label:'Volume de Leads', value: s.totalLeads ?? 0, icon:'layers', bg:'bg-brand-50', color:'text-brand-600', trend: 'Global' },
        { label:'Conversion', value: (s.conversionRate ?? 0) + '%', icon:'target', bg:'bg-accent-50', color:'text-accent-600', trend: 'Objectif' },
        { label:'Leads Traités', value: ((s.totalLeads ?? 0) - (s.pendingLeads ?? 0)), icon:'check-circle-2', bg:'bg-emerald-50', color:'text-emerald-600', trend: 'Ventes' },
        { label:'En attente', value: s.pendingLeads ?? 0, icon:'clock', bg:'bg-amber-50', color:'text-amber-600', trend: 'Urgence' },
    ];
    document.getElementById('perf-stats').innerHTML = cards.map((c, idx) => `
        <div class="glass-card p-8 rounded-[2.5rem] hover:shadow-2xl transition-all duration-700 group border-white/60 animate-fade-in-up bg-white/40" style="animation-delay: ${idx * 100}ms">
            <div class="flex items-center justify-between mb-8">
                <div class="w-14 h-14 ${c.bg} rounded-2xl flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform duration-500">
                    <i data-lucide="${c.icon}" class="${c.color}" style="width:28px;height:28px;"></i>
                </div>
                <div class="px-3 py-1.5 bg-navy-50 text-[10px] font-black text-navy-400 uppercase tracking-widest rounded-xl">
                    ${c.trend}
                </div>
            </div>
            <p class="text-5xl font-display font-black text-navy-950 mb-2 tracking-tighter">${c.value}</p>
            <p class="text-[10px] font-black text-navy-400 uppercase tracking-[0.2em] flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full ${c.color} opacity-50"></span>
                ${c.label}
            </p>
        </div>
    `).join('');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function renderStatusChart(s) {
    const total = (s.totalLeads ?? 0) || 1;
    const items = [
        { label:'Convertis / Traités', value: ((s.totalLeads ?? 0) - (s.pendingLeads ?? 0)), color:'bg-accent-500', bg:'bg-accent-50/50' },
        { label:'En attente de traitement', value: s.pendingLeads ?? 0, color:'bg-amber-400', bg:'bg-amber-50/50' },
    ];
    document.getElementById('status-chart').innerHTML = items.map(i => {
        const pct = Math.round(i.value/total*100);
        return `
        <div class="group">
            <div class="flex justify-between items-end mb-3">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-navy-400 uppercase tracking-widest">${i.label}</p>
                    <p class="text-xl font-display font-black text-navy-950 tracking-tight">${i.value} Leads</p>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-display font-black text-navy-950">${pct}%</span>
                </div>
            </div>
            <div class="h-4 ${i.bg} rounded-full overflow-hidden p-1 shadow-inner border border-white/50">
                <div class="h-full ${i.color} rounded-full transition-all duration-1000 shadow-sm" style="width:${pct}%"></div>
            </div>
        </div>
    `}).join('');
}

function renderMonthlyChart(data) {
    const container = document.getElementById('monthly-chart');
    if (!data || !data.length) {
        container.innerHTML = `<div class="flex flex-col items-center justify-center py-16 opacity-40">
            <i data-lucide="bar-chart" class="mb-4" style="width:32px;height:32px;"></i>
            <p class="text-[10px] font-black uppercase tracking-widest">Aucune donnée historique</p>
        </div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    const max = Math.max(...data.map(d => parseInt(d.count)), 1);
    
    container.innerHTML = data.map(d => {
        const pct = Math.round(parseInt(d.count)/max*100);
        // Format month key (YYYY-MM) to something readable (e.g. Fév 2028)
        const date = new Date(d.month_key + '-01');
        const monthLabel = date.toLocaleDateString('fr-FR', { month:'short', year:'numeric' });
        
        return `
        <div class="flex items-center gap-6 group">
            <span class="text-[10px] font-black text-navy-400 uppercase tracking-widest w-24 text-right transform group-hover:-translate-x-1 transition-transform">${monthLabel}</span>
            <div class="flex-1 h-10 bg-white/30 rounded-2xl overflow-hidden p-1.5 border border-white/50 shadow-inner group-hover:bg-white/50 transition-all">
                <div class="h-full bg-brand-600 rounded-xl transition-all duration-1000 shadow-lg shadow-brand-500/20" style="width:${pct}%"></div>
            </div>
            <div class="w-12">
                <span class="text-sm font-black text-navy-950">${d.count}</span>
            </div>
        </div>
    `}).join('');
}

function renderSectorBreakdown(data) {
    const el = document.getElementById('sector-breakdown');
    if (!data || !data.length) {
        el.innerHTML = '<div class="col-span-full py-20 text-center opacity-40"><p class="text-[10px] font-black uppercase tracking-widest">En attente de données sectorielles</p></div>';
        return;
    }
    
    el.innerHTML = data.map((d, idx) => {
        const total = parseInt(d.total);
        const processed = parseInt(d.processed);
        const pct = total ? Math.round(processed/total*100) : 0;
        const sectorName = d.sector || 'Général';
        
        return `
        <div class="p-8 bg-white/40 rounded-[2.5rem] border border-white hover:bg-white hover:shadow-xl hover:shadow-navy-900/5 transition-all duration-500 group animate-fade-in-up" style="animation-delay: ${idx * 100}ms">
            <div class="flex items-center justify-between mb-8">
                <p class="font-display font-black text-navy-950 uppercase tracking-tight text-lg">${escapeHtml(sectorName)}</p>
                <div class="h-8 w-8 flex items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <i data-lucide="trending-up" style="width:16px;height:16px;"></i>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-navy-400">
                    <span>Performance</span>
                    <span class="text-navy-950 font-black">${pct}%</span>
                </div>
                <div class="h-3 bg-navy-50 rounded-full overflow-hidden p-0.5 border border-white/50">
                    <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000" style="width:${pct}%"></div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 pt-4 mt-4 border-t border-navy-50/50">
                    <div>
                        <p class="text-[9px] font-black text-navy-400 uppercase tracking-widest mb-1">Volume</p>
                        <p class="text-xl font-display font-black text-navy-950">${total}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-navy-400 uppercase tracking-widest mb-1">Impact</p>
                        <p class="text-xl font-display font-black text-accent-600">${processed}</p>
                    </div>
                </div>
            </div>
        </div>
    `}).join('');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', loadPerformance);
</script>
JS;
$extraScript = str_replace('RAPPEL_PHP_TOKEN_PLACEHOLDER', $safeToken, $extraScript);
?>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>
