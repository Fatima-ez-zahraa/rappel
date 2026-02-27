<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Mes Clients';
$token = getToken();
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div class="animate-fade-in-up">
        <h1 class="text-4xl lg:text-5xl font-display font-black text-navy-950 tracking-tight leading-tight">Mes Clients</h1>
        <div class="flex items-center gap-3 mt-3">
            <div class="w-12 h-1 bg-brand-500 rounded-full"></div>
            <p class="text-navy-400 font-bold uppercase text-[10px] tracking-[0.2em]">Votre base de clients fidèles et contrats signés</p>
        </div>
    </div>
</div>

<div class="card p-4 md:p-6 mb-8 bg-white/50 backdrop-blur-xl border-white/60 shadow-premium rounded-[2.5rem]">
    <div class="grid md:grid-cols-[1fr,250px] gap-4">
        <div class="relative">
            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 text-navy-300" style="width:18px;height:18px;"></i>
            <input id="client-search" type="text" class="w-full pl-12 pr-4 py-4 rounded-2xl bg-white border border-navy-100 focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500/50 font-medium transition-all" placeholder="Rechercher un client (nom, email, tel)...">
        </div>
        <select id="client-sort" class="w-full px-4 py-4 rounded-2xl bg-white border border-navy-100 focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500/50 font-bold text-navy-950 transition-all cursor-pointer">
            <option value="recent">Plus récents</option>
            <option value="name_az">Nom A-Z</option>
        </select>
    </div>
</div>

<div id="clients-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="col-span-full py-20 flex flex-col items-center justify-center opacity-40">
        <div class="spinner spinner-dark mb-4"></div>
        <p class="font-black text-navy-900 text-xs uppercase tracking-widest">Chargement de vos clients...</p>
    </div>
</div>

<!-- Contact Modal -->
<div id="contact-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-950/40 backdrop-blur-md hidden transition-all duration-300">
    <div class="glass-card bg-white/95 rounded-[2.5rem] shadow-[0_32px_80px_rgba(0,0,0,0.15)] w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden border-white animate-scale-in">
        <div class="p-8 border-b border-navy-50 flex justify-between items-center bg-navy-50/10 flex-shrink-0">
            <div>
                <h2 class="text-2xl font-display font-extrabold text-navy-950 tracking-tight">Contacter le Client</h2>
                <p class="text-navy-400 text-[10px] font-black uppercase tracking-widest mt-1">Intervention & Note de suivi</p>
            </div>
            <button onclick="closeContactModal()" class="w-10 h-10 flex items-center justify-center text-navy-300 hover:text-navy-950 hover:bg-navy-50 rounded-xl transition-all">
                <i data-lucide="x" style="width:24px;height:24px;"></i>
            </button>
        </div>
        <div class="p-8 overflow-y-auto custom-scrollbar flex-grow space-y-8 bg-white/50">
            <div class="p-6 rounded-2xl bg-brand-600 text-white shadow-xl relative overflow-hidden group">
                <div class="absolute -top-6 -right-6 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black text-brand-200 uppercase tracking-widest mb-1">Numéro de téléphone</p>
                        <p id="contact-phone-display" class="text-2xl font-display font-black tracking-tight font-mono">-</p>
                    </div>
                    <a id="contact-phone-link" href="#" class="w-14 h-14 bg-white/10 hover:bg-white/20 text-white rounded-2xl flex items-center justify-center backdrop-blur-md transition-all group-hover:scale-110">
                        <i data-lucide="phone-outgoing" style="width:24px;height:24px;"></i>
                    </a>
                </div>
            </div>

            <!-- Client History / Context Box -->
            <div id="contact-client-context" class="p-5 rounded-2xl bg-navy-50/50 border border-navy-100 space-y-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent-500"></span>
                    <p class="text-[10px] font-black text-navy-400 uppercase tracking-widest">Historique Client</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[8px] font-black text-navy-400 uppercase tracking-widest mb-0.5">Total Investi</p>
                        <p id="contact-total-summary" class="text-sm font-display font-black text-navy-950">-</p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-navy-400 uppercase tracking-widest mb-0.5">Devis Signés</p>
                        <p id="contact-count-summary" class="text-sm font-display font-black text-navy-950">-</p>
                    </div>
                </div>
            </div>
            
                <textarea id="contact-comment" rows="4" class="w-full p-5 rounded-3xl bg-white border border-navy-100 focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500/50 font-medium text-sm transition-all shadow-sm" placeholder="Résultat de l'appel, prochaines étapes..."></textarea>
            </div>

            <div class="space-y-4 pt-4 border-t border-navy-50">
                <div class="flex items-center gap-3 ml-1">
                    <span class="w-6 h-1 bg-accent-500 rounded-full"></span>
                    <label class="text-[10px] font-black text-navy-950 uppercase tracking-widest">Historique des échanges</label>
                </div>
                <div id="interaction-list" class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                    <p class="text-navy-400 text-xs italic">Chargement de l'historique...</p>
                </div>
            </div>

            <div class="flex flex-col gap-3 pt-4">
                <button onclick="saveContactInteraction()" id="save-contact-btn" class="w-full py-5 bg-navy-950 hover:bg-navy-900 text-white font-black rounded-2xl shadow-xl transition-all active:scale-95 text-xs uppercase tracking-widest">Enregistrer l'interaction</button>
                <button onclick="closeContactModal()" class="w-full py-4 text-navy-400 font-bold hover:text-navy-900 transition-all text-[10px] uppercase tracking-widest">Annuler et fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Client Details Modal -->
<div id="details-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-950/40 backdrop-blur-md hidden transition-all duration-300">
    <div class="glass-card bg-white/95 rounded-[3rem] shadow-[0_32px_80px_rgba(0,0,0,0.15)] w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden border-white animate-scale-in">
        <div class="p-8 border-b border-navy-50 flex justify-between items-center bg-navy-50/10 flex-shrink-0">
            <div>
                <h2 id="details-client-name" class="text-3xl font-display font-black text-navy-950 tracking-tight">-</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="w-2 h-2 rounded-full bg-accent-500 animate-pulse"></span>
                    <p class="text-navy-400 text-[10px] font-black uppercase tracking-widest">Client Fidèle • Dossier Complet</p>
                </div>
                <!-- Link to Lead UI -->
                <div id="link-lead-container" class="mt-4 hidden p-4 bg-blue-50/50 border border-blue-100 rounded-2xl animate-fade-in">
                    <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <i data-lucide="link" style="width:12px;height:12px;"></i> Action Requise : Associer à un Lead
                    </p>
                    <div class="flex gap-2">
                        <select id="link-lead-select" class="flex-1 px-4 py-2 bg-white border border-blue-200 rounded-xl text-xs font-bold text-navy-950 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all">
                            <option value="">Chargement des leads...</option>
                        </select>
                        <button id="link-lead-btn" onclick="handleLinkClientToLead()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-blue-500/20 transition-all active:scale-95 disabled:opacity-50">
                            Lier
                        </button>
                    </div>
                </div>
            </div>
            <button onclick="closeDetailsModal()" class="w-12 h-12 flex items-center justify-center text-navy-300 hover:text-navy-950 hover:bg-navy-50 rounded-2xl transition-all">
                <i data-lucide="x" style="width:28px;height:28px;"></i>
            </button>
        </div>
        
        <div class="p-8 overflow-y-auto custom-scrollbar flex-grow bg-white/50 space-y-10">
            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-[2rem] bg-navy-50/30 border border-navy-100/50 group hover:bg-white hover:shadow-xl transition-all">
                    <p class="text-[9px] font-black text-navy-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i data-lucide="mail" class="text-brand-500" style="width:12px;height:12px;"></i> Email
                    </p>
                    <p id="details-email" class="text-sm font-bold text-navy-950 truncate">-</p>
                </div>
                <div class="p-6 rounded-[2rem] bg-navy-50/30 border border-navy-100/50 group hover:bg-white hover:shadow-xl transition-all">
                    <p class="text-[9px] font-black text-navy-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i data-lucide="phone" class="text-accent-500" style="width:12px;height:12px;"></i> Téléphone
                    </p>
                    <p id="details-phone" class="text-sm font-bold text-navy-950">-</p>
                </div>
                <div class="p-6 rounded-[2rem] bg-navy-50/30 border border-navy-100/50 group hover:bg-white hover:shadow-xl transition-all">
                    <p class="text-[9px] font-black text-navy-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i data-lucide="map-pin" class="text-red-500" style="width:12px;height:12px;"></i> Adresse
                    </p>
                    <p id="details-address" class="text-sm font-bold text-navy-950 truncate">-</p>
                </div>
            </div>
            
            <!-- Lead Details Section (New) -->
            <div id="details-lead-info" class="hidden p-8 rounded-[2.5rem] bg-navy-950 text-white shadow-2xl relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-brand-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center border border-white/10 group-hover:rotate-12 transition-transform">
                            <i data-lucide="zap" class="text-accent-400" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-brand-400 uppercase tracking-widest mb-1">Besoin Principal (Lead)</p>
                            <h3 id="details-lead-need" class="text-xl font-display font-black tracking-tight">-</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 pt-6 border-t border-white/10">
                        <div>
                            <p class="text-[9px] font-black text-navy-400 uppercase tracking-widest mb-1.5">Secteur</p>
                            <p id="details-lead-sector" class="text-xs font-bold text-accent-400 uppercase tracking-widest">-</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-navy-400 uppercase tracking-widest mb-1.5">Budget Estimé</p>
                            <div class="flex items-center gap-2">
                                <i data-lucide="wallet" class="text-navy-400" style="width:12px;height:12px;"></i>
                                <p id="details-lead-budget" class="text-sm font-display font-black text-white">-</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-navy-400 uppercase tracking-widest mb-1.5">Disponibilité</p>
                            <p id="details-lead-time" class="text-[10px] font-bold text-navy-200">-</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-navy-400 uppercase tracking-widest mb-1.5">Date Demande</p>
                            <p id="details-lead-date" class="text-[10px] font-bold text-navy-200">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Tabs / Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Quotes History -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-1 bg-brand-500 rounded-full"></span>
                        <h3 class="text-xs font-black text-navy-950 uppercase tracking-widest">Historique des Devis</h3>
                    </div>
                    <div id="details-quotes-list" class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Quotes injected here -->
                    </div>
                </div>

                <!-- Interactions Feed -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-1 bg-accent-500 rounded-full"></span>
                        <h3 class="text-xs font-black text-navy-950 uppercase tracking-widest">Compte-rendu & Suivi</h3>
                    </div>
                    <div id="details-interaction-list" class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Interactions injected here -->
                    </div>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-navy-50 text-center">
                <button onclick="closeDetailsModal()" class="px-12 py-4 text-navy-400 font-bold hover:text-navy-950 transition-all text-xs uppercase tracking-[0.2em]">Fermer le dossier client</button>
            </div>
        </div>
    </div>
</div>

<?php 
$safeToken = addslashes($token ?? '');
$extraScript = <<<'JS'
<script>
const PHP_TOKEN = 'RAPPEL_PHP_TOKEN_PLACEHOLDER';
let allClients = [];

async function loadClients() {
    if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);
    try {
        const quotes = await apiFetch('/quotes');
        // Include ALL clients with at least one quote (not just accepted/signed)
        const allQuotes = Array.isArray(quotes) ? quotes : [];
        
        // Group by client (lead_id takes priority for deduplication)
        const clientsMap = {};
        allQuotes.forEach(q => {
            const key = q.lead_id ? `lead_${q.lead_id}` : `name_${q.client_name || q.client}`;
            if (!clientsMap[key]) {
                clientsMap[key] = {
                    name: q.client_name || q.client || 'Client',
                    id: q.id,
                    lead_id: q.lead_id || null,
                    phone: q.contact_phone || null,
                    email: q.contact_email || null,
                    address: q.contact_address || null,
                    client_first_name: q.client_first_name || null,
                    client_last_name: q.client_last_name || null,
                    client_profile_phone: q.client_profile_phone || null,
                    client_profile_city: q.client_profile_city || null,
                    total_amount: 0,
                    quotes_count: 0,
                    last_quote_date: q.created_at,
                    overall_status: q.status || 'draft',
                    quotes: []
                };
            } else {
                if (q.lead_id && !clientsMap[key].lead_id) clientsMap[key].lead_id = q.lead_id;
                if (q.contact_phone && !clientsMap[key].phone) clientsMap[key].phone = q.contact_phone;
                if (q.contact_email && !clientsMap[key].email) clientsMap[key].email = q.contact_email;
                if (q.contact_address && !clientsMap[key].address) clientsMap[key].address = q.contact_address;
                if (q.client_first_name && !clientsMap[key].client_first_name) {
                    clientsMap[key].client_first_name = q.client_first_name;
                    clientsMap[key].client_last_name = q.client_last_name;
                    clientsMap[key].client_profile_phone = q.client_profile_phone;
                    clientsMap[key].client_profile_city = q.client_profile_city;
                }
                // Escalate status: accepted/signe > attente_client > sent > draft
                const hierarchy = ['accepted', 'signe', 'attente_client', 'sent', 'draft', 'rejected'];
                const current = hierarchy.indexOf(clientsMap[key].overall_status);
                const incoming = hierarchy.indexOf(q.status || 'draft');
                if (incoming < current) clientsMap[key].overall_status = q.status;
            }
            clientsMap[key].total_amount += parseFloat(q.amount || 0);
            clientsMap[key].quotes_count++;
            clientsMap[key].quotes.push(q);
            if (new Date(q.created_at) > new Date(clientsMap[key].last_quote_date)) {
                clientsMap[key].last_quote_date = q.created_at;
            }
        });

        allClients = Object.values(clientsMap);
        renderClients();
    } catch (err) {
        console.error('Failed to load clients:', err);
        document.getElementById('clients-container').innerHTML = `<div class="col-span-full py-20 text-center"><p class="text-red-500 font-bold">${err.message}</p></div>`;
    }
}

function renderClients() {
    const search = document.getElementById('client-search').value.toLowerCase();
    const sort = document.getElementById('client-sort').value;
    
    let filtered = allClients.filter(c => c.name.toLowerCase().includes(search));

    filtered.sort((a, b) => {
        if (sort === 'name_az') return a.name.localeCompare(b.name);
        if (sort === 'amount_desc') return b.total_amount - a.total_amount;
        return new Date(b.last_quote_date) - new Date(a.last_quote_date);
    });

    const el = document.getElementById('clients-container');
    if (!filtered.length) {
        el.innerHTML = `
            <div class="col-span-full py-32 bg-white/30 rounded-[3rem] border border-dashed border-navy-200 flex flex-col items-center justify-center animate-fade-in">
                <div class="w-20 h-20 bg-navy-50 rounded-[2rem] flex items-center justify-center mb-6">
                    <i data-lucide="users-2" class="text-navy-200" style="width:40px;height:40px;"></i>
                </div>
                <p class="text-navy-400 font-black uppercase tracking-widest text-xs">Aucun client trouvé</p>
            </div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    el.innerHTML = filtered.map((c, idx) => `
        <div class="glass-card p-8 rounded-[2.5rem] bg-white/40 border-white/60 hover:bg-white hover:shadow-2xl hover:shadow-brand-500/5 transition-all duration-500 group animate-fade-in-up" style="animation-delay: ${idx * 100}ms">
            <div class="flex items-center justify-between mb-8">
                <div class="w-16 h-16 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-black text-2xl shadow-xl shadow-brand-500/20 group-hover:scale-110 transition-transform duration-500">
                    ${c.name.charAt(0).toUpperCase()}
                </div>
                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-accent-50 text-accent-500 shadow-sm">
                    <i data-lucide="shield-check" style="width:20px;height:20px;"></i>
                </div>
            </div>
            
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-2xl font-display font-black text-navy-950 uppercase tracking-tight group-hover:text-brand-600 transition-colors">
                        ${escapeHtml(c.client_first_name ? `${c.client_first_name} ${c.client_last_name || ''}` : c.name)}
                    </h3>
                    ${c.client_first_name ? '<span class="px-2 py-0.5 rounded-lg bg-accent-500/10 text-accent-500 text-[8px] font-black uppercase tracking-widest border border-accent-500/20">Inscrit</span>' : ''}
                </div>
                <p class="text-[10px] text-navy-400 font-black uppercase tracking-widest flex items-center gap-2">
                    ${ (() => {
                        const s = c.overall_status;
                        if (s === 'accepted' || s === 'signe') return '<span class="w-2 h-2 rounded-full bg-accent-500"></span>';
                        if (s === 'attente_client') return '<span class="w-2 h-2 rounded-full bg-amber-400"></span>';
                        if (s === 'sent') return '<span class="w-2 h-2 rounded-full bg-blue-400"></span>';
                        return '<span class="w-2 h-2 rounded-full bg-navy-300"></span>';
                    })() }
                    ${c.quotes_count} Devis
                    &bull;
                    ${ (() => {
                        const s = c.overall_status;
                        if (s === 'accepted' || s === 'signe') return '<span class="text-accent-600">Accepté</span>';
                        if (s === 'attente_client') return '<span class="text-amber-600">En attente</span>';
                        if (s === 'sent') return '<span class="text-blue-600">Envoyé</span>';
                        if (s === 'rejected') return '<span class="text-red-500">Refusé</span>';
                        return '<span class="text-navy-400">Brouillon</span>';
                    })() }
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-6 mt-6 border-t border-navy-50/50">
                <div>
                    <p class="text-[9px] text-navy-400 font-black uppercase tracking-widest mb-1.5">Investissement</p>
                    <p class="text-xl font-display font-black text-navy-950">${c.total_amount.toLocaleString()} €</p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] text-navy-400 font-black uppercase tracking-widest mb-1.5">Dernier contrat</p>
                    <p class="text-[11px] font-black text-navy-950 uppercase tracking-widest">${new Date(c.last_quote_date).toLocaleDateString('fr-FR')}</p>
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <button onclick="openClientDetails('${encodeURIComponent(c.name)}')" class="flex-1 py-4 px-4 bg-navy-950 hover:bg-navy-900 text-white font-black rounded-xl text-[10px] uppercase tracking-widest transition-all active:scale-95">
                    Historique Devis
                </button>
                <button onclick="initiateContact('${c.lead_id}', '${c.phone || ''}')" class="flex-1 py-4 px-4 bg-accent-500 hover:bg-accent-400 text-navy-950 font-black rounded-xl text-[10px] uppercase tracking-widest transition-all active:scale-95">
                    Contacter
                </button>
                <button onclick="openClientDetails('${encodeURIComponent(c.name)}')" class="w-14 h-14 flex items-center justify-center rounded-xl bg-white border border-navy-100 text-navy-400 hover:text-navy-900 transition-all shadow-sm group/user" title="Voir les détails du lead">
                    <i data-lucide="user" class="group-hover/user:scale-110 transition-transform" style="width:20px;height:20px;"></i>
                </button>
            </div>
        </div>
    `).join('');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

let activeContactLeadId = null;
let allLeads = [];

async function loadLeads() {
    if (typeof PHP_TOKEN !== 'undefined' && PHP_TOKEN) Auth.setToken(PHP_TOKEN);
    try {
        const data = await apiFetch('/leads');
        allLeads = Array.isArray(data) ? data : [];
    } catch (err) {
        console.error('Failed to load leads:', err);
    }
}

async function loadInteractions(leadId, targetId = 'interaction-list') {
    const listEl = document.getElementById(targetId);
    if (!listEl) return;
    
    listEl.innerHTML = `<p class="text-navy-400 text-xs italic">Chargement...</p>`;
    
    try {
        const interactions = await apiFetch(`/leads/${leadId}/interactions`);
        renderInteractions(interactions, leadId, targetId);
    } catch (err) {
        console.error('Failed to load interactions:', err);
        listEl.innerHTML = `<p class="text-red-400 text-xs">Erreur lors du chargement de l'historique.</p>`;
    }
}

function renderInteractions(interactions, leadId, targetId = 'interaction-list') {
    const listEl = document.getElementById(targetId);
    if (!listEl) return;

    if (!interactions.length) {
        listEl.innerHTML = `<p class="text-navy-400 text-xs italic">Aucun historique pour le moment.</p>`;
    } else {
        listEl.innerHTML = interactions.map(i => `
            <div class="p-4 rounded-2xl bg-white border border-navy-50 shadow-sm animate-fade-in-up group/comment relative">
                <p class="text-xs text-navy-950 font-medium mb-1 pr-8">${escapeHtml(i.comment)}</p>
                <div class="flex items-center justify-between">
                    <p class="text-[9px] text-navy-400 font-black uppercase tracking-widest">${new Date(i.created_at).toLocaleString('fr-FR')}</p>
                    <button onclick="deleteComment('${i.id}', '${leadId}', '${targetId}')" class="opacity-0 group-hover/comment:opacity-100 p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Supprimer">
                        <i data-lucide="trash-2" style="width:12px;height:12px;"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

async function deleteComment(id, leadId, targetId = 'interaction-list') {
    if (!confirm('Voulez-vous vraiment supprimer ce commentaire ?')) return;
    try {
        await apiFetch(`/leads/interactions/${id}`, { method: 'DELETE' });
        showToast('Commentaire supprimé.', 'success');
        await loadInteractions(leadId, targetId);
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function openClientDetails(nameEncoded) {
    const name = decodeURIComponent(nameEncoded);
    const c = allClients.find(client => client.name === name);
    if (!c) return;

    document.getElementById('details-client-name').textContent = c.name;
    document.getElementById('details-email').textContent = c.email || 'Non renseigné';
    document.getElementById('details-phone').textContent = c.phone || 'Non renseigné';
    document.getElementById('details-address').textContent = c.address || 'Non renseignée';

    // Fetch and show Lead Info if available
    const leadInfoEl = document.getElementById('details-lead-info');
    if (leadInfoEl) {
        if (c.lead_id) {
            leadInfoEl.classList.remove('hidden');
            document.getElementById('details-lead-need').textContent = 'Chargement...';
            apiFetch(`/leads/${c.lead_id}`).then(lead => {
                // Update basic info with profile data if available
                if (lead.client_first_name) {
                    const fullName = `${lead.client_first_name} ${lead.client_last_name || ''}`.trim();
                    document.getElementById('details-client-name').innerHTML = `${escapeHtml(fullName)} <span class="ml-2 px-2 py-0.5 rounded-lg bg-accent-500/10 text-accent-500 text-[9px] font-black uppercase tracking-widest border border-accent-500/20">Client Inscrit</span>`;
                    document.getElementById('details-phone').textContent = lead.client_profile_phone || lead.phone || 'Non renseigné';
                    
                    const addr = [lead.client_profile_address, lead.client_profile_zip, lead.client_profile_city].filter(Boolean).join(', ');
                    if (addr) document.getElementById('details-address').textContent = addr;
                }

                document.getElementById('details-lead-need').textContent = lead.need || lead.service_type || 'Non spécifié';
                document.getElementById('details-lead-sector').textContent = lead.sector || lead.service_type || '-';
                document.getElementById('details-lead-budget').textContent = lead.budget ? `${parseFloat(lead.budget).toLocaleString()} €` : '-';
                document.getElementById('details-lead-time').textContent = lead.time_slot || 'Non spécifiée';
                document.getElementById('details-lead-date').textContent = new Date(lead.created_at).toLocaleDateString('fr-FR');
            }).catch(err => {
                console.error('Lead fetch error:', err);
                document.getElementById('details-lead-need').textContent = 'Erreur lors du chargement';
            });
        } else {
            leadInfoEl.classList.add('hidden');
        }
    }
    

    // Handle Link to Lead UI
    const linkContainer = document.getElementById('link-lead-container');
    const linkSelect = document.getElementById('link-lead-select');
    if (linkContainer && linkSelect) {
        if (c.lead_id) {
            linkContainer.classList.add('hidden');
        } else {
            linkContainer.classList.remove('hidden');
            linkSelect.innerHTML = '<option value="">-- Sélectionner un lead --</option>' + 
                allLeads.map(l => `<option value="${l.id}">${l.name} (${l.sector || 'Sans secteur'})</option>`).join('');
            
            // Set up handle function for this specific client
            window.handleLinkClientToLead = async () => {
                const leadId = linkSelect.value;
                if (!leadId) {
                    showToast('Veuillez sélectionner un lead.', 'warning');
                    return;
                }
                const btn = document.getElementById('link-lead-btn');
                setButtonLoading(btn, true);
                try {
                    // Update all quotes for this client to create the link
                    const updatePromises = c.quotes.map(q => apiFetch(`/quotes/${q.id}`, {
                        method: 'PATCH',
                        body: JSON.stringify({ lead_id: leadId })
                    }));
                    await Promise.all(updatePromises);
                    
                    showToast('Client associé au lead avec succès.', 'success');
                    await loadClients(); // Refresh everything
                    closeDetailsModal();
                } catch (err) {
                    showToast(err.message, 'error');
                } finally {
                    setButtonLoading(btn, false, 'Lier');
                }
            };
        }
    }

    const quotesList = document.getElementById('details-quotes-list');
    if (quotesList) {
        if (!c.quotes || !c.quotes.length) {
            quotesList.innerHTML = `<p class="text-navy-400 text-xs italic">Aucun devis trouvé.</p>`;
        } else {
            quotesList.innerHTML = c.quotes.map(q => `
                <div class="p-4 rounded-2xl bg-white border border-navy-50 shadow-sm flex items-center justify-between group hover:border-brand-200 transition-all">
                    <div>
                        <p class="text-sm font-bold text-navy-950">${escapeHtml(q.project_name || 'Projet sans nom')}</p>
                        <p class="text-[10px] text-navy-400 font-black uppercase tracking-widest mt-1">${new Date(q.created_at).toLocaleDateString('fr-FR')}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-brand-600">${parseFloat(q.amount).toLocaleString()} €</p>
                        <div class="mt-1">
                            <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-widest ${q.status === 'accepted' || q.status === 'signe' ? 'bg-accent-100 text-accent-700' : 'bg-navy-100 text-navy-600'}">
                                ${q.status}
                            </span>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    }

    const interactionList = document.getElementById('details-interaction-list');
    if (interactionList) {
        if (c.lead_id) {
            loadInteractions(c.lead_id, 'details-interaction-list');
        } else {
            interactionList.innerHTML = `<p class="p-4 rounded-xl bg-amber-50 text-amber-700 text-[10px] font-medium border border-amber-100 italic">
                Note: Ce client est issu d'un devis manuel. L'historique des notes n'est pas disponible.
            </p>`;
        }
    }

    const modal = document.getElementById('details-modal');
    modal.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeDetailsModal() {
    document.getElementById('details-modal').classList.add('hidden');
}

function initiateContact(leadId, phone) {
    // Find client in state to get summary data
    const client = allClients.find(c => c.lead_id === leadId || c.phone === phone);
    
    activeContactLeadId = (leadId && leadId !== 'null') ? leadId : null;
    document.getElementById('contact-phone-display').textContent = phone || 'Non renseigné';
    document.getElementById('contact-phone-link').href = phone ? `tel:${phone}` : '#';
    document.getElementById('contact-comment').value = '';

    // Populate Client Summary Box
    if (client) {
        document.getElementById('contact-total-summary').textContent = (client.total_amount || 0).toLocaleString() + ' €';
        document.getElementById('contact-count-summary').textContent = (client.quotes_count || 0) + ' devis';
    } else {
        document.getElementById('contact-total-summary').textContent = '-';
        document.getElementById('contact-count-summary').textContent = '-';
    }

    const listEl = document.getElementById('interaction-list');
    if (!activeContactLeadId) {
        listEl.innerHTML = `<p class="p-4 rounded-xl bg-amber-50 text-amber-700 text-[10px] font-medium border border-amber-100 italic">
            Note: Ce client est issu d'un devis manuel. Vous pouvez l'appeler, mais l'historique des notes ne sera pas disponible car aucun "Lead" correspondant n'a pu être identifié.
        </p>`;
    } else {
        loadInteractions(activeContactLeadId);
    }

    const modal = document.getElementById('contact-modal');
    modal.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeContactModal() {
    document.getElementById('contact-modal').classList.add('hidden');
    activeContactLeadId = null;
}

async function saveContactInteraction() {
    if (!activeContactLeadId) {
        showToast("L'historique des notes n'est pas disponible pour ce client (pas de Lead associé).", 'warning');
        return;
    }

    const btn = document.getElementById('save-contact-btn');
    const comment = document.getElementById('contact-comment').value.trim();
    
    if (!comment) {
        showToast('Veuillez saisir un commentaire.', 'warning');
        return;
    }

    setButtonLoading(btn, true);
    try {
        await apiFetch(`/leads/${activeContactLeadId}/interactions`, {
            method: 'POST',
            body: JSON.stringify({ comment })
        });

        showToast('Interaction enregistrée.', 'success');
        document.getElementById('contact-comment').value = '';
        await loadInteractions(activeContactLeadId);
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        setButtonLoading(btn, false, 'Enregistrer');
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    await loadLeads();
    loadClients();
    document.getElementById('client-search').addEventListener('input', renderClients);
    document.getElementById('client-sort').addEventListener('change', renderClients);
});
</script>
JS;
$extraScript = str_replace('RAPPEL_PHP_TOKEN_PLACEHOLDER', $safeToken, $extraScript);
?>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>
