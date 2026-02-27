<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Mes Leads';
$token = getToken();
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div class="animate-fade-in-up">
        <h1 class="text-4xl font-display font-extrabold text-navy-950 tracking-tight">Mes Leads</h1>
        <div class="flex items-center gap-2 mt-2">
            <span class="w-8 h-1 bg-accent-500 rounded-full"></span>
            <p class="text-navy-500 font-semibold uppercase text-xs tracking-widest text-[10px]">Gestion des opportunités</p>
        </div>
    </div>
    <button onclick="openAddModal()" class="btn btn-primary rounded-2xl px-8 py-4 shadow-xl hover:shadow-brand-500/20 transition-all active:scale-95 group animate-fade-in-up">
        <i data-lucide="user-plus" class="group-hover:scale-110 transition-transform" style="width:20px;height:20px;"></i>
        Demander un lead
    </button>
</div>

<!-- Filters & Search -->
<div class="glass-card p-6 mb-10 flex flex-col lg:flex-row gap-6 items-center justify-between rounded-[2rem] border-white/50 animate-fade-in">
    <div class="relative w-full lg:w-96 group">
        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-navy-300 group-focus-within:text-accent-500 transition-colors">
            <i data-lucide="search" style="width:20px;height:20px;"></i>
        </div>
        <input type="text" id="search-input" placeholder="Rechercher par nom ou email..."
               class="w-full pl-12 pr-4 h-14 bg-white/50 border border-navy-100/50 rounded-2xl text-sm focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold transition-all shadow-inner"
               oninput="filterLeads()">
    </div>
    <div class="flex p-1.5 bg-navy-50/50 rounded-2xl w-full lg:w-auto overflow-x-auto shadow-inner">
        <button onclick="setFilter('recommended')" id="filter-recommended" class="flex-1 lg:flex-none px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-navy-400 hover:text-navy-600">Recommandés</button>
        <button onclick="setFilter('all')" id="filter-all" class="flex-1 lg:flex-none px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all bg-white shadow-sm text-navy-950">Tous</button>
        <button onclick="setFilter('pending')" id="filter-pending" class="flex-1 lg:flex-none px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-navy-400 hover:text-navy-600">En attente</button>
        <button onclick="setFilter('processed')" id="filter-processed" class="flex-1 lg:flex-none px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-navy-400 hover:text-navy-600">Traités</button>
    </div>
</div>

<!-- Leads List Container -->
<div id="leads-container" class="min-h-[400px]">
    <div class="flex flex-col items-center justify-center py-32 opacity-30">
        <div class="spinner spinner-dark mb-6 scale-150"></div>
        <p class="text-navy-900 font-display font-bold text-lg">Synchronisation de vos leads...</p>
    </div>
</div>

<!-- Add Lead Modal -->
<div id="add-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-950/40 backdrop-blur-md hidden transition-all duration-300">
    <div class="glass-card bg-white/95 rounded-[2.5rem] shadow-[0_32px_80px_rgba(0,0,0,0.15)] w-full max-w-lg overflow-hidden border-white animate-scale-in">
        <div class="p-8 border-b border-navy-50 flex justify-between items-center bg-navy-50/10">
            <div>
                <h2 class="text-2xl font-display font-extrabold text-navy-950 tracking-tight">Demander un Lead</h2>
                <p class="text-navy-400 text-xs font-bold uppercase tracking-widest mt-1">Saisie manuelle</p>
            </div>
            <button onclick="closeAddModal()" class="w-10 h-10 flex items-center justify-center text-navy-300 hover:text-navy-600 hover:bg-navy-50 rounded-xl transition-all">
                <i data-lucide="x" style="width:24px;height:24px;"></i>
            </button>
        </div>
        <form onsubmit="handleAddLead(event)" class="p-8 space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Nom Complet *</label>
                    <input type="text" id="new-name" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all" placeholder="Jean Dupont" required>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Email *</label>
                    <input type="email" id="new-email" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all" placeholder="jean@mail.com" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Téléphone *</label>
                    <input type="tel" id="new-phone" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all" placeholder="0612345678" required>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Secteur</label>
                    <select id="new-sector" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all appearance-none cursor-pointer">
                        <option value="">Choisir...</option>
                        <option value="assurance">Assurance</option>
                        <option value="renovation">Rénovation</option>
                        <option value="energie">Énergie</option>
                        <option value="finance">Finance</option>
                        <option value="garage">Garage</option>
                        <option value="telecom">Télécoms</option>
                    </select>
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Adresse / Ville</label>
                <input type="text" id="new-address" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all" placeholder="Paris 75001">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Besoin principal</label>
                    <input type="text" id="new-need" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all" placeholder="Assurance Auto">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Budget approx.</label>
                    <input type="text" id="new-budget" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all" placeholder="1500€">
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Créneau souhaité</label>
                <select id="new-time-slot" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all cursor-pointer">
                    <option value="Matin (09h – 12h)">Matin (09h – 12h)</option>
                    <option value="Midi (12h – 14h)">Midi (12h – 14h)</option>
                    <option value="Après-midi (14h – 18h)">Après-midi (14h – 18h)</option>
                    <option value="Soirée (18h – 20h)">Soirée (18h – 20h)</option>
                    <option value="Week-end (Samedi & Dimanche)">Week-end (Samedi & Dimanche)</option>
                </select>
            </div>
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closeAddModal()" class="flex-1 py-4 px-6 border border-navy-100 text-navy-600 font-bold rounded-2xl hover:bg-navy-50 transition-all active:scale-95">Annuler</button>
                <button type="submit" id="add-lead-btn" class="flex-1 py-4 px-6 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-2xl shadow-lg shadow-brand-500/20 transition-all active:scale-95">Demander le Lead</button>
            </div>
        </form>
    </div>
</div>

<!-- Details Modal -->
<div id="details-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-950/40 backdrop-blur-md hidden transition-all duration-300">
    <div class="glass-card bg-white/95 rounded-[3rem] shadow-[0_32px_80px_rgba(0,0,0,0.15)] w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden border-white animate-scale-in">
        <div id="details-header" class="p-8 border-b border-navy-50 bg-navy-50/10 flex-shrink-0"></div>
        <div id="details-body" class="p-8 overflow-y-auto custom-scrollbar flex-grow bg-white/50">
            <div id="details-top-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10"></div>
            <div id="details-action-area" class="mb-12"></div>
            <div id="details-history-area" class="space-y-6">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-8 h-1 bg-brand-500 rounded-full"></span>
                    <h3 class="text-xs font-black text-navy-400 uppercase tracking-widest">Historique & Commentaires</h3>
                </div>
                <div id="interactions-list" class="space-y-4 mb-8"></div>
                <div id="interaction-form-container" class="bg-navy-50/30 p-6 rounded-[2rem] border border-navy-100/50"></div>
            </div>
            <div class="mt-12 text-center">
                <button onclick="document.getElementById('details-modal').classList.add('hidden')" class="px-8 py-3 text-navy-400 font-bold hover:text-navy-900 transition-colors text-xs uppercase tracking-widest">Fermer les détails</button>
            </div>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div id="contact-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-950/40 backdrop-blur-md hidden transition-all duration-300">
    <div class="glass-card bg-white/95 rounded-[2.5rem] shadow-[0_32px_80px_rgba(0,0,0,0.15)] w-full max-w-lg overflow-hidden border-white animate-scale-in">
        <div class="p-8 border-b border-navy-50 flex justify-between items-center bg-navy-50/10">
            <div>
                <h2 class="text-2xl font-display font-extrabold text-navy-950 tracking-tight">Contacter le Lead</h2>
                <p class="text-navy-400 text-xs font-bold uppercase tracking-widest mt-1">Intervention & Note</p>
            </div>
            <button onclick="closeContactModal()" class="w-10 h-10 flex items-center justify-center text-navy-300 hover:text-navy-600 hover:bg-navy-50 rounded-xl transition-all">
                <i data-lucide="x" style="width:24px;height:24px;"></i>
            </button>
        </div>
        <div class="p-8 space-y-6">
            <div class="p-6 rounded-2xl bg-brand-50 border border-brand-100 flex items-center justify-between group">
                <div>
                    <p class="text-[10px] font-black text-brand-400 uppercase tracking-widest mb-1">Téléphone</p>
                    <p id="contact-phone-display" class="text-xl font-display font-black text-brand-700 font-mono">-</p>
                </div>
                <a id="contact-phone-link" href="#" class="w-14 h-14 bg-brand-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-brand-500/20 group-hover:scale-110 transition-transform">
                    <i data-lucide="phone" style="width:24px;height:24px;"></i>
                </a>
            </div>

            <!-- Project Summary Box -->
            <div id="contact-project-summary" class="p-5 rounded-2xl bg-navy-50/50 border border-navy-100 space-y-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                    <p class="text-[10px] font-black text-navy-400 uppercase tracking-widest">Résumé du besoin</p>
                </div>
                <p id="contact-need-summary" class="text-sm font-bold text-navy-950 line-clamp-2 italic">Chargement...</p>
                <div class="grid grid-cols-2 gap-4 pt-3 border-t border-navy-100/50">
                    <div>
                        <p class="text-[8px] font-black text-navy-400 uppercase tracking-widest mb-0.5">Secteur</p>
                        <p id="contact-sector-summary" class="text-[11px] font-black text-navy-950 uppercase tracking-widest">-</p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-navy-400 uppercase tracking-widest mb-0.5">Budget estimé</p>
                        <p id="contact-budget-summary" class="text-[11px] font-black text-navy-950 uppercase tracking-widest">-</p>
                    </div>
                </div>
            </div>
            
            <div class="space-y-1.5">
                <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Commentaire / Note d'appel</label>
                <textarea id="contact-comment" rows="4" class="w-full p-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-medium text-sm transition-all" placeholder="Résultat de l'appel, prochaines étapes..."></textarea>
            </div>

            <div class="flex gap-4">
                <button onclick="closeContactModal()" class="flex-1 py-4 px-6 border border-navy-100 text-navy-600 font-bold rounded-2xl hover:bg-navy-50 transition-all active:scale-95">Annuler</button>
                <button onclick="saveContactInteraction()" id="save-contact-btn" class="flex-1 py-4 px-6 bg-navy-950 hover:bg-navy-900 text-white font-bold rounded-2xl shadow-lg transition-all active:scale-95">Enregistrer & Terminer</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Lead Modal -->
<div id="edit-lead-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-950/40 backdrop-blur-md hidden transition-all duration-300">
    <div class="glass-card bg-white/95 rounded-[3rem] shadow-[0_32px_80px_rgba(0,0,0,0.15)] w-full max-w-2xl overflow-hidden border-white animate-scale-in">
        <div class="p-8 border-b border-navy-50 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-display font-extrabold text-navy-950 tracking-tight">Modifier le lead</h2>
                <p class="text-navy-400 text-xs font-bold uppercase tracking-widest mt-1">Édition des informations</p>
            </div>
            <button onclick="closeEditModal()" class="w-10 h-10 flex items-center justify-center text-navy-300 hover:text-navy-600 hover:bg-navy-50 rounded-xl transition-all">
                <i data-lucide="x" style="width:24px;height:24px;"></i>
            </button>
        </div>
        <form onsubmit="handleEditLead(event)" class="p-8 space-y-6">
            <input type="hidden" id="edit-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Nom complet *</label>
                    <input type="text" id="edit-name" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all" required>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Email</label>
                    <input type="email" id="edit-email" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Téléphone *</label>
                    <input type="tel" id="edit-phone" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all" required>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Secteur</label>
                    <select id="edit-sector" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all cursor-pointer">
                        <option value="">Sélectionner...</option>
                        <option value="assurance">Assurance</option>
                        <option value="renovation">Rénovation</option>
                        <option value="energie">Énergie</option>
                        <option value="finance">Finance</option>
                        <option value="garage">Garage</option>
                        <option value="telecom">Télécoms</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Adresse / Ville</label>
                    <input type="text" id="edit-address" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Statut</label>
                    <select id="edit-status" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all cursor-pointer">
                        <option value="pending">En attente</option>
                        <option value="processed">Traité</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Besoin principal</label>
                    <input type="text" id="edit-need" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Budget</label>
                    <input type="text" id="edit-budget" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all">
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-black text-navy-950 uppercase tracking-widest ml-1">Créneau souhaité</label>
                <select id="edit-time-slot" class="w-full h-12 px-4 rounded-xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-bold text-sm transition-all cursor-pointer">
                    <option value="Matin (09h – 12h)">Matin (09h – 12h)</option>
                    <option value="Midi (12h – 14h)">Midi (12h – 14h)</option>
                    <option value="Après-midi (14h – 18h)">Après-midi (14h – 18h)</option>
                    <option value="Soirée (18h – 20h)">Soirée (18h – 20h)</option>
                    <option value="Week-end (Samedi & Dimanche)">Week-end (Samedi & Dimanche)</option>
                </select>
            </div>
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closeEditModal()" class="flex-1 py-4 px-6 border border-navy-100 text-navy-600 font-bold rounded-2xl hover:bg-navy-50 transition-all">Annuler</button>
                <button type="submit" id="edit-lead-btn" class="flex-1 py-4 px-6 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-2xl shadow-lg shadow-brand-500/20 transition-all">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php
$safeToken = addslashes($token ?? '');
$extraScript = <<<'JS'
<script>
window.onerror = function(msg, url, line, col, error) {
    const el = document.getElementById('leads-container');
    if (el) el.innerHTML = `<div class="p-8 text-center text-red-500 bg-red-50 rounded-2xl border border-red-100 font-bold">Fatal Error: ${msg} <br><small>${url}:${line}</small></div>`;
    return false;
};
window.PHP_TOKEN = '__PHP_TOKEN__';
let allLeads = [];
let currentFilter = 'all';
let currentUser = null;

console.log('[DEBUG] Leads script initialized. window.PHP_TOKEN exists:', !!window.PHP_TOKEN);

async function loadLeads(apiFilter = 'all') {
    console.log('[DEBUG] Starting loadLeads with filter:', apiFilter);
    if (window.PHP_TOKEN) Auth.setToken(window.PHP_TOKEN);
    const container = document.getElementById('leads-container');
    
    try {
        console.log(`[DEBUG] Fetching /profile and /leads?filter=${apiFilter}...`);
        const [profile, data] = await Promise.all([
            apiFetch('/profile').catch(e => { console.error('Profile error:', e); throw e; }),
            apiFetch(`/leads?filter=${apiFilter}`).catch(e => { console.error('Leads error:', e); throw e; })
        ]);
        
        currentUser = profile.user || profile;
        allLeads = Array.isArray(data) ? data : [];
        renderLeads();
    } catch (err) {
        clearTimeout(timeout);
        console.error('[DEBUG] loadLeads caught error:', err);
        container.innerHTML = `
            <div class="p-12 text-center bg-red-50 rounded-[2.5rem] border border-red-100 shadow-xl animate-fade-in max-w-2xl mx-auto">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="alert-circle" style="width:32px;height:32px;"></i>
                </div>
                <p class="text-red-900 font-display font-black text-xl mb-2">Impossible de charger les leads</p>
                <div class="bg-white/50 p-4 rounded-xl border border-red-100 mb-6 font-mono text-xs text-red-500 overflow-x-auto text-left">
                    Error: ${err.message || 'Unknown error'}
                </div>
                <button onclick="loadLeads()" class="px-8 py-3 bg-red-600 text-white font-black rounded-xl hover:bg-red-700 transition-all active:scale-95 text-xs uppercase tracking-widest">Réessayer</button>
            </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

function setFilter(f) {
    currentFilter = f;
    const filters = ['recommended', 'all','pending','processed'];
    filters.forEach(id => {
        const btn = document.getElementById('filter-' + id);
        if (!btn) return;
        if (id === f) {
            btn.className = 'flex-1 lg:flex-none px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all bg-white shadow-sm text-navy-950';
        } else {
            btn.className = 'flex-1 lg:flex-none px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-navy-400 hover:text-navy-600';
        }
    });

    if (f === 'recommended') {
        loadLeads('recommended');
    } else if (f === 'all') {
        loadLeads('all');
    } else {
        renderLeads();
    }
}

function filterLeads() { renderLeads(); }

function renderLeads() {
    try {
        const search = document.getElementById('search-input').value.toLowerCase();
    const filtered = allLeads.filter(l => {
        // Correct status filter logic
        let matchFilter = true;
        if (currentFilter === 'pending') matchFilter = l.status === 'pending';
        else if (currentFilter === 'processed') matchFilter = l.status === 'processed';
        
        const name = (l.name || (l.first_name ? l.first_name + ' ' + (l.last_name || '') : '') || '').toLowerCase();
        const email = (l.email || '').toLowerCase();
        const matchSearch = !search || name.includes(search) || email.includes(search);
        return matchFilter && matchSearch;
    });

    const el = document.getElementById('leads-container');
    if (!filtered.length) {
        el.innerHTML = `<div class="flex flex-col items-center justify-center py-32 opacity-60">
            <div class="w-24 h-24 bg-white/50 rounded-[2.5rem] flex items-center justify-center mb-8 border border-white shadow-inner">
                <i data-lucide="inbox" class="text-navy-200" style="width:40px;height:40px;"></i>
            </div>
            <p class="text-2xl font-display font-extrabold text-navy-950 tracking-tight">Aucun lead trouvé</p>
            <p class="text-navy-400 font-bold text-sm mt-1 uppercase tracking-widest">Essayez d'autres filtres ou une autre recherche</p>
        </div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    el.innerHTML = `<div class="grid grid-cols-1 gap-6 animate-fade-in">${filtered.map((l, idx) => {
        const hasProfile = !!l.client_first_name;
        const name = hasProfile ? `${l.client_first_name} ${l.client_last_name || ''}`.trim() : (l.name || ((l.first_name || '') + ' ' + (l.last_name || '')).trim() || 'Inconnu');
        const isPending = l.status === 'pending';
        const isAssignedToMe = String(l.assigned_to) === String(currentUser?.id);
        const isUnassigned = !l.assigned_to;
        
        let statusBadge = '';
        if (isAssignedToMe) {
            let statusText = 'Traité';
            let statusClass = 'bg-accent-50 text-accent-600 ring-accent-500/10';
            
            if (l.status === 'pending') {
                statusText = 'En attente';
                statusClass = 'bg-amber-50 text-amber-600 ring-amber-500/10';
            } else if (l.status === 'quote_sent') {
                statusText = 'Devis envoyé';
                statusClass = 'bg-indigo-50 text-indigo-600 ring-indigo-500/10';
            } else if (l.status === 'completed') {
                statusText = 'Terminé';
                statusClass = 'bg-emerald-50 text-emerald-600 ring-emerald-500/10';
            }
            
            statusBadge = `<span class="badge py-2 px-4 rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-sm ring-1 ring-inset ${statusClass}">${statusText}</span>`;
        } else {
            statusBadge = `<span class="badge py-2 px-4 rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-sm ring-1 ring-inset bg-brand-50 text-brand-600 ring-brand-500/10">Disponible</span>`;
        }

        let actionBtn = '';
        if (isAssignedToMe) {
            actionBtn = `
                <button onclick="openDetails('${l.id}')" class="flex-1 lg:flex-none h-11 px-6 rounded-xl bg-white border border-navy-100 text-navy-600 font-bold text-xs hover:bg-navy-50 transition-all shadow-sm">Détails</button>
                <button onclick="openEditModal('${l.id}')" class="h-11 w-11 flex-shrink-0 flex items-center justify-center rounded-xl bg-navy-50/50 text-navy-400 hover:text-navy-700 border border-navy-100/50 hover:bg-navy-100 transition-all shadow-sm">
                    <i data-lucide="edit-3" style="width:18px;height:18px;"></i>
                </button>
                ${isPending ? `
                <button onclick="initiateContact('${l.id}')" class="h-11 px-6 flex-1 lg:flex-none flex items-center justify-center gap-2 rounded-xl bg-accent-500 hover:bg-accent-400 text-navy-950 font-black text-xs shadow-glow transition-all active:scale-95 group/call">
                    <i data-lucide="phone-forwarded" class="group-hover/call:animate-shake" style="width:16px;height:16px;"></i>
                    Appeler
                </button>
                ` : `
                <div class="h-11 w-11 flex-shrink-0 flex items-center justify-center rounded-xl bg-accent-50 text-accent-500">
                    <i data-lucide="check" style="width:20px;height:20px;"></i>
                </div>
                `}
            `;
        } else {
            actionBtn = `
                <button onclick="claimLead('${l.id}')" class="h-11 px-8 flex-1 lg:flex-none flex items-center justify-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-black text-xs shadow-glow transition-all active:scale-95 group/claim">
                    <i data-lucide="hand" class="group-hover/claim:scale-110 transition-transform" style="width:16px;height:16px;"></i>
                    Demander ce lead
                </button>
            `;
        }

        return `
        <div class="glass-card p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center hover:shadow-xl transition-all duration-500 border-white/60 group animate-fade-in-up" style="animation-delay: ${idx * 50}ms">
            <div class="lg:col-span-3 flex items-center gap-5">
                <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br ${isAssignedToMe ? 'from-brand-50 to-brand-100 text-brand-700' : 'from-navy-50 to-navy-100 text-navy-400'} flex items-center justify-center font-black text-xl border border-white shadow-sm flex-shrink-0 group-hover:scale-110 transition-transform">
                    ${name.charAt(0).toUpperCase()}
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-display font-black text-navy-950 truncate tracking-tight text-lg uppercase">${escapeHtml(name)}</h3>
                        ${hasProfile ? '<span class="px-1.5 py-0.5 rounded-md bg-accent-500/10 text-accent-400 text-[8px] font-black uppercase tracking-tighter border border-accent-500/20">Inscrit</span>' : ''}
                        ${(() => {
                            const sectorsStr = currentUser?.sectors || '[]';
                            let mySectors = [];
                            try {
                                mySectors = Array.isArray(sectorsStr) ? sectorsStr : JSON.parse(sectorsStr);
                            } catch(e) { 
                                mySectors = sectorsStr.split(',').map(s => s.trim().toLowerCase());
                            }
                            const match = mySectors.some(s => (l.sector || '').toLowerCase().includes(s.toLowerCase()));
                            return match ? '<span class="px-1.5 py-0.5 rounded-md bg-emerald-500/10 text-emerald-500 text-[8px] font-black uppercase tracking-tighter border border-emerald-500/20">Match</span>' : '';
                        })()}
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full ${isPending ? 'bg-amber-400' : 'bg-accent-500'}"></span>
                        <p class="text-[10px] text-navy-400 font-black uppercase tracking-widest">${formatRelativeTime(l.created_at)}</p>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-3 grid grid-cols-1 gap-2.5">
                <div class="flex items-center gap-3 text-[13px] text-navy-800 font-bold group-hover:translate-x-1 transition-transform">
                    <div class="w-8 h-8 rounded-lg bg-navy-50/50 flex items-center justify-center text-navy-400"><i data-lucide="phone" style="width:14px;height:14px;"></i></div>
                    <span class="truncate">${escapeHtml(l.client_profile_phone || l.phone || '-')}</span>
                </div>
                <div class="flex items-center gap-3 text-[13px] text-navy-800 font-bold group-hover:translate-x-1 transition-transform delay-75">
                    <div class="w-8 h-8 rounded-lg bg-navy-50/50 flex items-center justify-center text-navy-400"><i data-lucide="mail" style="width:14px;height:14px;"></i></div>
                    <span class="truncate text-xs">${escapeHtml(l.email || '-')}</span>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-accent-50/50 text-accent-700 rounded-xl text-[10px] font-black uppercase tracking-widest mb-3 border border-accent-500/10">
                    <i data-lucide="tag" style="width:10px;height:10px;"></i>
                    ${escapeHtml(l.need || l.service_type || l.sector || '-')}
                </div>
                <div class="space-y-1.5 ml-1">
                    <p class="text-[10px] text-navy-400 font-bold uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="wallet" style="width:12px;height:12px;"></i>
                        Budget: <span class="text-navy-950 font-black">${escapeHtml(l.budget || '-')}</span>
                    </p>
                </div>
            </div>

            <div class="lg:col-span-3 flex flex-col items-end gap-3 lg:border-l border-navy-50 lg:pl-6">
                <div class="flex items-center gap-2 mb-1">
                    ${statusBadge}
                </div>
                <div class="flex gap-2 w-full lg:w-auto items-center">
                    ${actionBtn}
                </div>
            </div>
        </div>`;
    }).join('')}</div>`;
    if (typeof lucide !== 'undefined') lucide.createIcons();
    } catch (err) {
        console.error('Render error:', err);
        document.getElementById('leads-container').innerHTML = `<div class="p-8 text-center text-red-500 bg-red-50 rounded-2xl border border-red-100 font-bold">Erreur d'affichage: ${err.message}</div>`;
    }
}

async function claimLead(id) {
    try {
        await apiFetch(`/leads/${id}/claim`, { method: 'POST' });
        showToast('Félicitations ! Vous avez récupéré ce lead.', 'success');
        loadLeads();
    } catch (err) {
        showToast(err.message || 'Erreur lors de la réclamation.', 'error');
    }
}

async function updateLeadStatus(id, newStatus, message) {
    try {
        await apiFetch('/leads/' + id, { 
            method: 'PATCH', 
            body: JSON.stringify({ status: newStatus }) 
        });
        showToast(message || 'Statut mis à jour.', 'success');
        await loadLeads();
        await openDetails(id); // Refresh details
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function markProcessed(id) {
    try { 
        await apiFetch('/leads/' + id, { method: 'PATCH', body: JSON.stringify({ status: 'processed' }) }); 
        loadLeads(); 
    } catch (err) {
        showToast(err.message, 'error');
    }
}

let activeContactLeadId = null;

function initiateContact(id) {
    const lead = allLeads.find(x => String(x.id) === String(id));
    if (!lead) return;

    activeContactLeadId = id;
    document.getElementById('contact-phone-display').textContent = lead.phone || '-';
    document.getElementById('contact-phone-link').href = `tel:${lead.phone}`;
    document.getElementById('contact-comment').value = '';
    
    // Populate Project Summary
    document.getElementById('contact-need-summary').textContent = lead.need || 'Aucun détail spécifié';
    document.getElementById('contact-sector-summary').textContent = lead.sector || '-';
    document.getElementById('contact-budget-summary').textContent = lead.budget || '-';

    const modal = document.getElementById('contact-modal');
    modal.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeContactModal() {
    document.getElementById('contact-modal').classList.add('hidden');
    activeContactLeadId = null;
}

async function saveContactInteraction() {
    if (!activeContactLeadId) return;

    const btn = document.getElementById('save-contact-btn');
    const comment = document.getElementById('contact-comment').value.trim();
    
    if (!comment) {
        showToast('Veuillez saisir un commentaire.', 'warning');
        return;
    }

    setButtonLoading(btn, true);
    try {
        // 1. Save interaction
        await apiFetch(`/leads/${activeContactLeadId}/interactions`, {
            method: 'POST',
            body: JSON.stringify({ comment })
        });

        // 2. Mark lead as processed
        await apiFetch('/leads/' + activeContactLeadId, { 
            method: 'PATCH', 
            body: JSON.stringify({ status: 'processed' }) 
        });

        showToast('Interaction enregistrée.', 'success');
        closeContactModal();
        loadLeads();
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        setButtonLoading(btn, false, 'Enregistrer & Terminer');
    }
}

function openAddModal() {
    const modal = document.getElementById('add-modal');
    modal.classList.remove('hidden');
    setTimeout(() => modal.classList.add('bg-navy-950/40'), 10);
}
function closeAddModal() {
    const modal = document.getElementById('add-modal');
    modal.classList.add('hidden');
}

function openEditModal(id) {
    const lead = allLeads.find(x => String(x.id) === String(id));
    if (!lead) return;

    document.getElementById('edit-id').value = lead.id || '';
    document.getElementById('edit-name').value = lead.name || '';
    document.getElementById('edit-email').value = lead.email || '';
    document.getElementById('edit-phone').value = lead.phone || '';
    document.getElementById('edit-sector').value = (lead.sector || '').toLowerCase();
    document.getElementById('edit-address').value = lead.address || '';
    document.getElementById('edit-status').value = lead.status || 'pending';
    document.getElementById('edit-need').value = lead.need || '';
    document.getElementById('edit-budget').value = lead.budget || '';
    const ts = lead.time_slot || '';
    document.getElementById('edit-time-slot').value = (ts === 'Non spécifié') ? '' : ts;

    const modal = document.getElementById('edit-lead-modal');
    modal.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeEditModal() {
    document.getElementById('edit-lead-modal').classList.add('hidden');
}

async function handleAddLead(e) {
    e.preventDefault();
    const btn = document.getElementById('add-lead-btn');
    setButtonLoading(btn, true);
    try {
        await apiFetch('/leads/manual', { method: 'POST', body: JSON.stringify({
            name: document.getElementById('new-name').value,
            email: document.getElementById('new-email').value,
            phone: document.getElementById('new-phone').value,
            sector: document.getElementById('new-sector').value,
            address: document.getElementById('new-address').value,
            need: document.getElementById('new-need').value,
            budget: document.getElementById('new-budget').value,
            time_slot: document.getElementById('new-time-slot').value,
        })});
        closeAddModal();
        loadLeads();
        showToast('Nouveau prospect enregistré !', 'success');
    } catch (err) {
        showToast(err.message, 'error');
        setButtonLoading(btn, false, 'Créer le Lead');
    }
}

async function handleEditLead(e) {
    e.preventDefault();
    const btn = document.getElementById('edit-lead-btn');
    const id = document.getElementById('edit-id').value;
    setButtonLoading(btn, true);
    try {
        await apiFetch('/leads/' + id, { method: 'PATCH', body: JSON.stringify({
            name: document.getElementById('edit-name').value,
            email: document.getElementById('edit-email').value,
            phone: document.getElementById('edit-phone').value,
            sector: document.getElementById('edit-sector').value,
            address: document.getElementById('edit-address').value,
            status: document.getElementById('edit-status').value,
            need: document.getElementById('edit-need').value,
            budget: document.getElementById('edit-budget').value,
            time_slot: document.getElementById('edit-time-slot').value,
        })});
        closeEditModal();
        await loadLeads();
        showToast('Mise à jour réussie !', 'success');
    } catch (err) {
        showToast(err.message || 'Erreur lors de la modification.', 'error');
    } finally {
        setButtonLoading(btn, false, 'Enregistrer');
    }
}

async function handleAddComment(e, leadId) {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    const comment = document.getElementById('interaction-comment').value;
    if (!comment.trim()) return;

    setButtonLoading(btn, true);
    try {
        await apiFetch(`/leads/${leadId}/interactions`, {
            method: 'POST',
            body: JSON.stringify({ comment })
        });
        document.getElementById('interaction-comment').value = '';
        await openDetails(leadId); // Refresh details to show new comment
        showToast('Commentaire ajouté !', 'success');
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        setButtonLoading(btn, false, 'Publier');
    }
}

async function openDetails(id) {
    const l = allLeads.find(x => String(x.id) === String(id));
    if (!l) return;
    const name = l.name || ((l.first_name || '') + ' ' + (l.last_name || '')).trim() || 'Inconnu';
    const isPending = l.status === 'pending';
    const isAssignedToMe = String(l.assigned_to) === String(currentUser?.id);
    const isUnassigned = !l.assigned_to;
    
    document.getElementById('details-header').innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-5 relative">
                <div class="w-16 h-16 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-black text-2xl shadow-xl">
                    ${name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <h2 class="text-3xl font-display font-black text-navy-950 tracking-tight leading-tight uppercase">${name}</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-block w-2 h-2 rounded-full ${isPending ? 'bg-amber-400' : 'bg-accent-500'}"></span>
                        <p class="text-[10px] font-black text-navy-400 uppercase tracking-widest leading-none">${isPending ? 'En attente de rappel' : 'Prospect traité'}</p>
                    </div>
                </div>
            </div>
            <button onclick="document.getElementById('details-modal').classList.add('hidden')" class="w-12 h-12 flex items-center justify-center text-navy-300 hover:text-navy-950 hover:bg-navy-50 rounded-2xl transition-all">
                <i data-lucide="x" style="width:28px;height:28px;"></i>
            </button>
        </div>
    `;

    document.getElementById('details-top-grid').innerHTML = `
        <div class="space-y-4">
            <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-navy-50 shadow-sm group">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="phone" style="width:18px;height:18px;"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-navy-300 uppercase tracking-widest mb-0.5">Téléphone</p>
                    <p class="font-bold text-navy-900">${escapeHtml(l.phone || '-')}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-navy-50 shadow-sm group">
                <div class="w-10 h-10 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="mail" style="width:18px;height:18px;"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-navy-300 uppercase tracking-widest mb-0.5">Email</p>
                    <p class="font-bold text-navy-900 text-sm truncate">${escapeHtml(l.email || '-')}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-navy-50 shadow-sm group">
                <div class="w-10 h-10 rounded-xl bg-navy-50 text-navy-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="map-pin" style="width:18px;height:18px;"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-navy-300 uppercase tracking-widest mb-0.5">Adresse</p>
                    <p class="font-bold text-navy-900 text-xs leading-none">${escapeHtml(l.address || '-')}</p>
                </div>
            </div>
        </div>

        <div class="p-6 rounded-[2rem] bg-navy-950 text-white shadow-xl relative overflow-hidden flex flex-col justify-center">
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-brand-500/10 rounded-full blur-xl"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
                        <i data-lucide="zap" class="text-accent-400" style="width:16px;height:16px;"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-brand-400 uppercase tracking-widest mb-0.5">Besoin principal</p>
                        <h4 class="text-lg font-display font-black tracking-tight">${escapeHtml(l.need || l.service_type || '-')}</h4>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5">
                    <div>
                        <p class="text-[8px] font-black text-navy-400 uppercase tracking-widest mb-1">Secteur</p>
                        <p class="text-[10px] font-bold text-accent-400 uppercase">${escapeHtml(l.sector || l.service_type || '-')}</p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-navy-400 uppercase tracking-widest mb-1">Budget</p>
                        <p class="text-sm font-display font-black text-white">${escapeHtml(l.budget || '-')}</p>
                    </div>
                </div>
                ${l.time_slot && l.time_slot !== 'Non spécifié' ? `
                <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between">
                    <span class="text-[8px] font-black text-navy-400 uppercase tracking-widest">Disponibilité</span>
                    <span class="px-2 py-0.5 bg-white/10 rounded text-[10px] font-bold">${escapeHtml(l.time_slot)}</span>
                </div>` : ''}
            </div>
        </div>
    `;

    document.getElementById('details-action-area').innerHTML = `
        ${isAssignedToMe ? `
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <button onclick="initiateContact('${l.id}')" class="flex items-center justify-center gap-3 w-full py-5 bg-navy-50 hover:bg-navy-100 text-navy-950 font-black rounded-2xl transition-all active:scale-95 text-[10px] uppercase tracking-widest group/call">
                <i data-lucide="phone-forwarded" class="group-hover/call:animate-shake" style="width:18px;height:18px;"></i>
                Contacter
            </button>
            
            ${l.status === 'pending' || l.status === 'processed' ? `
                <button onclick="updateLeadStatus('${l.id}', 'quote_sent', 'Devis marqué comme envoyé !')" class="flex items-center justify-center gap-3 w-full py-5 bg-accent-500 hover:bg-accent-400 text-navy-950 font-black rounded-2xl shadow-glow transition-all active:scale-95 text-[10px] uppercase tracking-widest">
                    <i data-lucide="file-text" style="width:18px;height:18px;"></i>
                    Envoyer le devis
                </button>
            ` : l.status === 'quote_sent' ? `
                <button onclick="updateLeadStatus('${l.id}', 'completed', 'Félicitations ! Projet terminé.')" class="flex items-center justify-center gap-3 w-full py-5 bg-emerald-500 hover:bg-emerald-400 text-white font-black rounded-2xl shadow-glow transition-all active:scale-95 text-[10px] uppercase tracking-widest">
                    <i data-lucide="check-circle" style="width:18px;height:18px;"></i>
                    Terminer le projet
                </button>
            ` : `
                <div class="flex items-center justify-center gap-3 w-full py-5 bg-navy-50 text-navy-400 font-black rounded-2xl text-[10px] uppercase tracking-widest">
                    <i data-lucide="check" style="width:18px;height:18px;"></i>
                    Projet Réalisé
                </div>
            `}
        </div>
        ` : isUnassigned ? `
        <button onclick="claimLead('${l.id}')" class="flex items-center justify-center gap-3 w-full py-5 bg-brand-600 hover:bg-brand-700 text-white font-black rounded-2xl shadow-glow transition-all active:scale-95 text-[10px] uppercase tracking-widest group/claim">
            <i data-lucide="hand" class="group-hover/claim:scale-110 transition-transform" style="width:20px;height:20px;"></i>
            Demander ce lead
        </button>
        ` : '<div class="p-4 text-center bg-navy-50 rounded-xl text-navy-400 font-bold text-xs uppercase tracking-widest">Prospect déjà assigné</div>'}
    `;

    const modal = document.getElementById('details-modal');
    modal.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // Reset list and form
    document.getElementById('interactions-list').innerHTML = `
        <div class="animate-pulse flex space-x-4 p-4">
            <div class="flex-1 space-y-4 py-1">
                <div class="h-4 bg-navy-50 rounded w-3/4"></div>
                <div class="h-4 bg-navy-50 rounded"></div>
            </div>
        </div>
    `;

    // Load interactions
    try {
        const interactions = await apiFetch(`/leads/${id}/interactions`);
        renderInteractions(interactions, id);
    } catch (err) {
        console.error('Failed to load interactions:', err);
    }
}

function renderInteractions(interactions, leadId) {
    const listEl = document.getElementById('interactions-list');
    if (!listEl) return;

    if (!interactions.length) {
        listEl.innerHTML = `<p class="text-navy-400 text-xs italic">Aucun historique pour le moment.</p>`;
    } else {
        listEl.innerHTML = interactions.map(i => {
            const isOwner = String(i.provider_id) === String(currentUser?.id);
            return `
            <div class="p-4 rounded-2xl bg-white border border-navy-50 shadow-sm animate-fade-in-up group/comment relative">
                <p class="text-sm text-navy-950 font-medium mb-1 pr-8">${escapeHtml(i.comment)}</p>
                <div class="flex items-center justify-between">
                    <p class="text-[10px] text-navy-400 font-black uppercase tracking-widest">${new Date(i.created_at).toLocaleString('fr-FR')}</p>
                    ${isOwner ? `
                        <button onclick="deleteComment('${i.id}', '${leadId}')" class="opacity-0 group-hover/comment:opacity-100 p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Supprimer">
                            <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                        </button>
                    ` : ''}
                </div>
            </div>
        `}).join('');
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();

    const formEl = document.getElementById('interaction-form-container');
    if (formEl) {
        formEl.innerHTML = `
            <form onsubmit="handleAddComment(event, '${leadId}')" class="space-y-4">
                <div class="relative">
                    <textarea id="interaction-comment" 
                              class="w-full p-4 rounded-2xl bg-navy-50/50 border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-medium text-sm transition-all" 
                              placeholder="Note sur l'appel ou contact..." 
                              rows="3" required></textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-navy-950 hover:bg-navy-900 text-white font-black rounded-xl transition-all active:scale-95 text-[10px] uppercase tracking-widest shadow-lg">
                    Publier le commentaire
                </button>
            </form>
        `;
    }
}

async function deleteComment(id, leadId) {
    if (!confirm('Voulez-vous vraiment supprimer ce commentaire ?')) return;
    
    try {
        await apiFetch(`/leads/interactions/${id}`, {
            method: 'DELETE'
        });
        showToast('Commentaire supprimé.', 'success');
        await openDetails(leadId); // Refresh to show deletion
    } catch (err) {
        showToast(err.message, 'error');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const search = params.get('search');
    if (search) {
        const input = document.getElementById('search-input');
        if (input) input.value = search;
    }
    loadLeads();
});
</script>
JS;
$extraScript = str_replace('__PHP_TOKEN__', $safeToken, $extraScript);
?>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>
