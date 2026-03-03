<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Devis';
$token = getToken();
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-display font-bold text-navy-950">Devis</h1>
        <p class="text-navy-500 font-medium mt-1">Suivez vos devis, filtrez rapidement et gardez une vision claire de votre pipeline.</p>
    </div>
    <button onclick="openCreateQuote()" class="btn btn-primary rounded-xl px-6 shadow-premium">
        <i data-lucide="plus" style="width:18px;height:18px;"></i>
        Nouveau devis
    </button>
</div>

<div id="quotes-stats" class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="card p-4">
        <p class="text-xs uppercase tracking-wider font-bold text-navy-400 mb-1">Total devis</p>
        <p id="stat-total" class="text-2xl font-display font-bold text-navy-950">0</p>
    </div>
    <div class="card p-4">
        <p class="text-xs uppercase tracking-wider font-bold text-navy-400 mb-1">En attente</p>
        <p id="stat-pending" class="text-2xl font-display font-bold text-amber-700">0</p>
    </div>
    <div class="card p-4">
        <p class="text-xs uppercase tracking-wider font-bold text-navy-400 mb-1">Accept&eacute;s</p>
        <p id="stat-accepted" class="text-2xl font-display font-bold text-accent-700">0</p>
    </div>
    <div class="card p-4">
        <p class="text-xs uppercase tracking-wider font-bold text-navy-400 mb-1">Montant cumule</p>
        <p id="stat-amount" class="text-2xl font-display font-bold text-brand-700">0 EUR</p>
    </div>
</div>

<div class="card p-4 md:p-5 mb-5">
    <div class="grid md:grid-cols-[1fr,220px,220px] gap-3">
        <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-navy-300" style="width:16px;height:16px;"></i>
            <input id="q-search" type="text" class="form-input pl-10 rounded-xl" placeholder="Rechercher un client, une description...">
        </div>
        <select id="q-filter-status" class="form-select rounded-xl">
            <option value="all">Tous les statuts</option>
            <option value="attente_client">En attente client</option>
            <option value="accepted">Accept&eacute; par client</option>
            <option value="rejected">Refus&eacute; par client</option>
            <option value="completed">R&eacute;alis&eacute;</option>
        </select>
        <select id="q-sort" class="form-select rounded-xl">
            <option value="recent">Plus recents</option>
            <option value="oldest">Plus anciens</option>
            <option value="amount_desc">Montant decroissant</option>
            <option value="amount_asc">Montant croissant</option>
            <option value="client_az">Client A-Z</option>
        </select>
    </div>
    <div class="mt-3 flex items-center justify-between">
        <p id="quotes-count" class="text-sm text-navy-500 font-medium">Chargement...</p>
        <button id="q-reset-filters" type="button" class="text-sm font-bold text-brand-700 hover:text-brand-600 transition-colors">Reinitialiser</button>
    </div>
</div>

<div id="lead-opportunities" class="card p-4 md:p-5 mb-5 hidden">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-black text-navy-950 uppercase tracking-widest">Demandes a deviser</h2>
        <p id="lead-opportunities-count" class="text-xs text-navy-500 font-bold">0 lead</p>
    </div>
    <div id="lead-opportunities-list" class="grid gap-3 md:grid-cols-2"></div>
</div>

<div id="quotes-container">
    <div class="flex items-center justify-center py-20">
        <div class="spinner spinner-dark"></div>
    </div>
</div>

<!-- Create Quote Modal -->
<div id="quote-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-navy-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-scale-in">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h2 class="text-xl font-bold text-navy-900">Nouveau devis</h2>
            <button type="button" onclick="closeCreateQuote()" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-white transition-colors">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <form onsubmit="handleCreateQuote(event)" class="p-6 space-y-4">
            <div>
                <label class="form-label">Lead associé (Optionnel)</label>
                <select id="q-lead-select" class="form-select mb-2" onchange="onLeadSelectChange('q')">
                    <option value="">-- Sélectionner un lead --</option>
                </select>
                <p class="text-[9px] text-navy-400 font-bold uppercase tracking-widest px-1 mb-2">Ou saisir manuellement :</p>
                <label class="form-label">Client *</label>
                <input type="text" id="q-client" class="form-input" placeholder="Nom du client" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Montant (EUR) *</label>
                    <input type="number" id="q-amount" class="form-input" placeholder="1500" min="1" step="0.01" required>
                </div>
                <div>
                    <label class="form-label">Statut</label>
                    <select id="q-status" class="form-select">
                        <option value="attente_client">En attente client</option>
                        <option value="accepted">Accept&eacute; client</option>
                        <option value="rejected">Refus&eacute; client</option>
                        <option value="completed">R&eacute;alis&eacute;</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Description</label>
                <textarea id="q-desc" class="form-textarea" placeholder="Details de la prestation..."></textarea>
            </div>
            <div>
                <label class="form-label">Documents (optionnel)</label>
                <input type="file" id="q-doc" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" multiple>
                <p class="text-[10px] text-navy-400 mt-1">Formats acceptes: PDF, DOC/DOCX, XLS/XLSX, PNG, JPG (max 10MB chacun, max 10 fichiers)</p>
            </div>
            <div id="q-form-error" class="form-error hidden"></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeCreateQuote()" class="btn btn-outline flex-1 rounded-xl">Annuler</button>
                <button type="submit" id="create-quote-btn" class="btn btn-primary flex-1 rounded-xl">Creer le devis</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Quote Modal -->
<div id="edit-quote-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-navy-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-scale-in">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h2 class="text-xl font-bold text-navy-900">Modifier le devis</h2>
            <button type="button" onclick="closeEditQuote()" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-white transition-colors">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <form onsubmit="handleEditQuote(event)" class="p-6 space-y-4">
            <input type="hidden" id="eq-id">
            <div>
                <label class="form-label">Lead associé (Optionnel)</label>
                <select id="eq-lead-select" class="form-select mb-2" onchange="onLeadSelectChange('eq')">
                    <option value="">-- Sélectionner un lead --</option>
                </select>
                <p class="text-[9px] text-navy-400 font-bold uppercase tracking-widest px-1 mb-2">Ou modifier manuellement :</p>
                <label class="form-label">Client *</label>
                <input type="text" id="eq-client" class="form-input" placeholder="Nom du client" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Montant (EUR) *</label>
                    <input type="number" id="eq-amount" class="form-input" placeholder="1500" min="1" step="0.01" required>
                </div>
                <div>
                    <label class="form-label">Statut</label>
                    <select id="eq-status" class="form-select">
                        <option value="attente_client">En attente client</option>
                        <option value="accepted">Accept&eacute; client</option>
                        <option value="rejected">Refus&eacute; client</option>
                        <option value="completed">R&eacute;alis&eacute;</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Description / Projet</label>
                <textarea id="eq-project" class="form-textarea" placeholder="Details de la prestation..."></textarea>
            </div>
            <div>
                <label class="form-label">Documents (optionnel)</label>
                <input type="file" id="eq-doc" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" multiple>
                <p class="text-[10px] text-navy-400 mt-1">Formats acceptes: PDF, DOC/DOCX, XLS/XLSX, PNG, JPG (max 10MB chacun, max 10 fichiers)</p>
                <div id="eq-current-docs" class="hidden mt-2 space-y-1"></div>
            </div>
            <div id="eq-form-error" class="form-error hidden"></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeEditQuote()" class="btn btn-outline flex-1 rounded-xl">Annuler</button>
                <button type="submit" id="edit-quote-btn" class="btn btn-primary flex-1 rounded-xl">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php
$safeToken = addslashes($token ?? '');
?>
<script>
const PHP_TOKEN = '<?php echo $safeToken; ?>';

const statusColors = {
    draft: 'bg-navy-50 text-navy-600',
    sent: 'bg-blue-50 text-blue-700',
    envoye: 'bg-blue-50 text-blue-700',
    attente_client: 'bg-amber-50 text-amber-700',
    accepted: 'bg-accent-50 text-accent-700',
    rejected: 'bg-red-50 text-red-700',
    signe: 'bg-accent-50 text-accent-700',
    refuse: 'bg-red-50 text-red-700',
    completed: 'bg-emerald-50 text-emerald-700',
    realise: 'bg-emerald-50 text-emerald-700',
};
const statusLabels = {
    draft: 'Brouillon',
    sent: 'Envoy\u00e9',
    envoye: 'Envoy\u00e9',
    attente_client: 'En attente client',
    accepted: 'Accept\u00e9 par client',
    rejected: 'Refus\u00e9 par client',
    signe: 'Accept\u00e9 par client',
    refuse: 'Refus\u00e9 par client',
    completed: 'R\u00e9alis\u00e9',
    realise: 'R\u00e9alis\u00e9',
};

const state = {
    quotes: [],
    query: '',
    status: 'all',
    sort: 'recent',
};
let editQuoteRemovedDocs = [];

function normalizeStatus(value) {
    const k = String(value || '').trim().toLowerCase();
    if (k === 'signe') return 'accepted';
    if (k === 'refuse') return 'rejected';
    if (k === 'envoye' || k === 'envoyé') return 'sent';
    if (k === 'realise' || k === 'réalisé') return 'completed';
    return k;
}

function formatAmount(value) {
    const num = parseFloat(value || 0);
    return `${num.toLocaleString('fr-FR', { minimumFractionDigits: num % 1 === 0 ? 0 : 2, maximumFractionDigits: 2 })} EUR`;
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function getQuoteDocs(q) {
    if (Array.isArray(q?.docs) && q.docs.length) return q.docs.filter(Boolean);
    return q?.doc_path ? [q.doc_path] : [];
}

async function loadQuotes() {
    if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);
    try {
        const [quotesData] = await Promise.all([
            apiFetch('/quotes'),
            loadLeads()
        ]);
        state.quotes = Array.isArray(quotesData) ? quotesData : [];
        refreshQuotesUI();
    } catch (err) {
        document.getElementById('quotes-container').innerHTML = `<div class="form-error">${escapeHtml(err.message)}</div>`;
        document.getElementById('quotes-count').textContent = 'Erreur de chargement';
    }
}

function getFilteredSortedQuotes() {
    const query = state.query.trim().toLowerCase();

    let result = state.quotes.filter((q) => {
        const status = normalizeStatus(q.status);
        if (state.status !== 'all' && status !== state.status) return false;
        if (!query) return true;

        const haystack = [
            q.client_name,
            q.client,
            q.project_name,
            q.description,
        ].map(v => String(v || '').toLowerCase()).join(' ');

        return haystack.includes(query);
    });

    result = result.slice().sort((a, b) => {
        const dateA = new Date(a.created_at || 0).getTime();
        const dateB = new Date(b.created_at || 0).getTime();
        const amountA = parseFloat(a.amount || 0);
        const amountB = parseFloat(b.amount || 0);
        const nameA = String(a.client_name || a.client || '').toLowerCase();
        const nameB = String(b.client_name || b.client || '').toLowerCase();

        switch (state.sort) {
            case 'oldest': return dateA - dateB;
            case 'amount_desc': return amountB - amountA;
            case 'amount_asc': return amountA - amountB;
            case 'client_az': return nameA.localeCompare(nameB, 'fr');
            case 'recent':
            default: return dateB - dateA;
        }
    });

    return result;
}

function renderStats(allQuotes) {
    const total = allQuotes.length;
    const pending = allQuotes.filter(q => ['attente_client', 'sent', 'draft'].includes(normalizeStatus(q.status))).length;
    const accepted = allQuotes.filter(q => ['accepted', 'signe'].includes(normalizeStatus(q.status))).length;
    const amount = allQuotes.reduce((sum, q) => sum + parseFloat(q.amount || 0), 0);

    document.getElementById('stat-total').textContent = String(total);
    document.getElementById('stat-pending').textContent = String(pending);
    document.getElementById('stat-accepted').textContent = String(accepted);
    document.getElementById('stat-amount').textContent = formatAmount(amount);
}

function renderQuotes(quotes) {
    const container = document.getElementById('quotes-container');
    const countText = document.getElementById('quotes-count');

    if (!state.quotes.length) {
        container.innerHTML = `<div class="text-center py-20 bg-white/50 rounded-3xl border border-dashed border-navy-200">
            <i data-lucide="file-text" class="text-navy-300 mx-auto mb-4" style="width:48px;height:48px;"></i>
            <p class="font-bold text-navy-400">Aucun devis pour le moment.</p>
            <p class="text-sm text-navy-400 mt-1">Commencez par creer votre premier devis.</p>
            <button onclick="openCreateQuote()" class="btn btn-primary btn-sm rounded-xl mt-4">Creer mon premier devis</button>
        </div>`;
        countText.textContent = '0 resultat';
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    if (!quotes.length) {
        container.innerHTML = `<div class="text-center py-20 bg-white/50 rounded-3xl border border-dashed border-navy-200">
            <i data-lucide="search-x" class="text-navy-300 mx-auto mb-4" style="width:48px;height:48px;"></i>
            <p class="font-bold text-navy-400">Aucun devis ne correspond a votre recherche.</p>
            <button onclick="resetFilters()" class="btn btn-outline btn-sm rounded-xl mt-4">Reinitialiser les filtres</button>
        </div>`;
        countText.textContent = '0 resultat filtre';
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    countText.textContent = `${quotes.length} resultat${quotes.length > 1 ? 's' : ''} sur ${state.quotes.length}`;

    container.innerHTML = `<div class="space-y-3">${quotes.map((q) => {
        const status = normalizeStatus(q.status);
        const hasProfile = !!q.client_first_name;
        const displayName = hasProfile ? `${q.client_first_name} ${q.client_last_name || ''}`.trim() : (q.client_name || q.client || 'Client');
        const client = escapeHtml(displayName);
        const lead = q.lead_id ? allLeads.find(l => String(l.id) === String(q.lead_id)) : null;
        const description = escapeHtml(q.project_name || q.description || '');
        const docs = getQuoteDocs(q);
        const createdAt = new Date(q.created_at || Date.now()).toLocaleDateString('fr-FR');
        const amount = formatAmount(q.amount || 0);
        const label = statusLabels[status] || escapeHtml(q.status || 'Inconnu');
        const statusColor = statusColors[status] || 'bg-navy-50 text-navy-600';

        return `
        <article class="card p-5 flex flex-col md:flex-row md:items-center gap-4 hover:shadow-premium transition-all">
            <div class="flex-1 min-w-0">
                <div class="flex items-center flex-wrap gap-2 mb-2">
                    <h3 class="font-bold text-navy-950 truncate">${client}</h3>
                    ${hasProfile ? '<span class="px-2 py-0.5 rounded-lg bg-accent-500/10 text-accent-500 text-[8px] font-black uppercase tracking-widest border border-accent-500/20">Client Inscrit</span>' : ''}
                    ${lead ? `<span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-blue-100">Lead: ${escapeHtml(lead.name)}</span>` : ''}
                    <span class="badge ${statusColor}">${label}</span>
                </div>
                <p class="text-sm text-navy-500 font-medium ${description ? '' : 'italic'}">${description || 'Aucune description'}</p>
                ${docs.length ? `<div class="mt-2 flex flex-wrap items-center gap-2">
                    ${docs.slice(0, 2).map((doc, index) => `<a href="${encodeURI(String(doc))}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs font-bold text-brand-700 hover:text-brand-600 transition-colors"><i data-lucide="paperclip" style="width:14px;height:14px;"></i>Document ${index + 1}</a>`).join('')}
                    ${docs.length > 2 ? `<span class="text-xs text-navy-400 font-bold">+${docs.length - 2} autres</span>` : ''}
                </div>` : ''}
            </div>
            <div class="md:text-right md:min-w-[180px]">
                <p class="text-2xl font-display font-bold text-navy-950">${amount}</p>
                <p class="text-xs text-navy-400 font-medium">Cree le ${createdAt}</p>
            </div>
            <div class="flex md:flex-col gap-2 md:min-w-[130px]">
                <a href="/rappel/public/quote-view.php?id=${encodeURIComponent(q.id)}" class="btn btn-outline btn-sm rounded-xl text-center">Voir devis</a>
                ${['accepted','signe'].includes(status) ? `<button type="button" onclick="markQuoteCompleted('${q.id}')" class="btn btn-primary btn-sm rounded-xl">Marquer r&eacute;alis&eacute;</button>` : ''}
                <button type="button" onclick="openEditQuote('${q.id}')" class="btn btn-outline btn-sm rounded-xl">Modifier</button>
            </div>
        </article>`;
    }).join('')}</div>`;

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function getLeadLatestQuoteStatusMap() {
    const map = new Map();
    (state.quotes || []).forEach((q) => {
        const leadId = String(q?.lead_id || '').trim();
        if (!leadId) return;
        const current = map.get(leadId);
        const nextDate = new Date(q.created_at || 0).getTime();
        const currentDate = current ? new Date(current.created_at || 0).getTime() : -1;
        if (!current || nextDate >= currentDate) {
            map.set(leadId, { status: normalizeStatus(q.status), created_at: q.created_at || null });
        }
    });
    return map;
}

function getLeadsToQuote() {
    const latestQuoteByLead = getLeadLatestQuoteStatusMap();
    return (allLeads || []).filter((lead) => {
        const id = String(lead?.id || '').trim();
        if (!id) return false;
        const latest = latestQuoteByLead.get(id);
        if (!latest) return true; // no quote yet
        return ['rejected', 'refuse'].includes(String(latest.status || '').toLowerCase());
    });
}

function openCreateQuoteWithLead(leadId) {
    openCreateQuote();
    const select = document.getElementById('q-lead-select');
    if (!select) return;
    select.value = String(leadId || '');
    onLeadSelectChange('q');
}

function renderLeadOpportunities() {
    const wrap = document.getElementById('lead-opportunities');
    const countEl = document.getElementById('lead-opportunities-count');
    const listEl = document.getElementById('lead-opportunities-list');
    if (!wrap || !countEl || !listEl) return;

    const latestQuoteByLead = getLeadLatestQuoteStatusMap();
    const leads = getLeadsToQuote();
    if (!leads.length) {
        wrap.classList.add('hidden');
        listEl.innerHTML = '';
        countEl.textContent = '0 lead';
        return;
    }

    wrap.classList.remove('hidden');
    countEl.textContent = `${leads.length} lead${leads.length > 1 ? 's' : ''}`;
    listEl.innerHTML = leads.slice(0, 8).map((l) => {
        const name = escapeHtml(l.name || 'Client');
        const need = escapeHtml(l.need || 'Projet');
        const sector = escapeHtml(l.sector || 'general');
        const status = escapeHtml(l.status || '-');
        const latest = latestQuoteByLead.get(String(l.id || '').trim());
        const canResend = latest && ['rejected', 'refuse'].includes(String(latest.status || '').toLowerCase());
        return `
            <div class="rounded-2xl border border-navy-100 bg-white p-4 flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-black text-navy-950 truncate">${name}</p>
                    <p class="text-xs text-navy-500 mt-1 truncate">${need}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-widest">${sector}</span>
                        <span class="px-2 py-0.5 rounded-lg bg-navy-50 text-navy-500 text-[9px] font-black uppercase tracking-widest">${status}</span>
                        ${canResend ? '<span class="px-2 py-0.5 rounded-lg bg-red-50 text-red-600 text-[9px] font-black uppercase tracking-widest">Devis refuse</span>' : ''}
                    </div>
                </div>
                <button type="button" onclick="openCreateQuoteWithLead('${escapeHtml(l.id)}')" class="btn btn-primary btn-sm rounded-xl whitespace-nowrap">Creer devis</button>
            </div>
        `;
    }).join('');
}

function refreshQuotesUI() {
    renderLeadOpportunities();
    renderStats(state.quotes);
    renderQuotes(getFilteredSortedQuotes());
}

function resetFilters() {
    state.query = '';
    state.status = 'all';
    state.sort = 'recent';

    document.getElementById('q-search').value = '';
    document.getElementById('q-filter-status').value = 'all';
    document.getElementById('q-sort').value = 'recent';
    refreshQuotesUI();
}

function openCreateQuote() {
    const modal = document.getElementById('quote-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('q-lead-select').value = '';
    document.getElementById('q-client').value = '';
    document.getElementById('q-amount').value = '';
    document.getElementById('q-desc').value = '';
    document.getElementById('q-doc').value = '';
    document.getElementById('q-client').focus();
}

function closeCreateQuote() {
    const modal = document.getElementById('quote-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('q-form-error').classList.add('hidden');
}

function openEditQuote(id) {
    const quote = state.quotes.find((q) => String(q.id) === String(id));
    if (!quote) return;

    document.getElementById('eq-id').value = quote.id || '';
    document.getElementById('eq-lead-select').value = quote.lead_id || '';
    document.getElementById('eq-client').value = quote.client_name || quote.client || '';
    document.getElementById('eq-amount').value = quote.amount || '';
    document.getElementById('eq-status').value = normalizeStatus(quote.status) || 'attente_client';
    document.getElementById('eq-project').value = quote.project_name || '';
    document.getElementById('eq-doc').value = '';
    document.getElementById('eq-form-error').classList.add('hidden');
    editQuoteRemovedDocs = [];
    const currentDocsWrap = document.getElementById('eq-current-docs');
    const docs = getQuoteDocs(quote);
    if (docs.length) {
        currentDocsWrap.innerHTML = docs.map((doc, index) => `<div class="flex items-center gap-2">
            <a href="${encodeURI(String(doc))}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs font-bold text-brand-700 hover:text-brand-600 transition-colors">
                <i data-lucide="paperclip" style="width:14px;height:14px;"></i>Document actuel ${index + 1}
            </a>
            <button type="button" onclick="markDocForRemoval(decodeURIComponent('${encodeURIComponent(String(doc))}'))" class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600 hover:text-red-500">
                <i data-lucide="trash-2" style="width:13px;height:13px;"></i>Supprimer
            </button>
        </div>`).join('');
        currentDocsWrap.classList.remove('hidden');
    } else {
        currentDocsWrap.innerHTML = '';
        currentDocsWrap.classList.add('hidden');
    }
    
    const modal = document.getElementById('edit-quote-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    if (typeof lucide !== 'undefined') lucide.createIcons();
    document.getElementById('eq-client').focus();
}

function closeEditQuote() {
    const modal = document.getElementById('edit-quote-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('eq-form-error').classList.add('hidden');
    document.getElementById('eq-doc').value = '';
    const currentDocsWrap = document.getElementById('eq-current-docs');
    currentDocsWrap.innerHTML = '';
    currentDocsWrap.classList.add('hidden');
    editQuoteRemovedDocs = [];
}

function renderEditCurrentDocs() {
    const id = document.getElementById('eq-id').value;
    const quote = state.quotes.find((q) => String(q.id) === String(id));
    const currentDocsWrap = document.getElementById('eq-current-docs');
    if (!quote || !currentDocsWrap) return;
    const docs = getQuoteDocs(quote);
    const visibleDocs = docs.filter((doc) => !editQuoteRemovedDocs.includes(String(doc)));
    if (!visibleDocs.length) {
        currentDocsWrap.innerHTML = '<p class="text-[11px] text-navy-400 font-medium">Tous les documents existants seront supprimés après enregistrement.</p>';
        currentDocsWrap.classList.remove('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }
    currentDocsWrap.innerHTML = visibleDocs.map((doc, index) => `<div class="flex items-center gap-2">
        <a href="${encodeURI(String(doc))}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs font-bold text-brand-700 hover:text-brand-600 transition-colors">
            <i data-lucide="paperclip" style="width:14px;height:14px;"></i>Document actuel ${index + 1}
        </a>
        <button type="button" onclick="markDocForRemoval(decodeURIComponent('${encodeURIComponent(String(doc))}'))" class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600 hover:text-red-500">
            <i data-lucide="trash-2" style="width:13px;height:13px;"></i>Supprimer
        </button>
    </div>`).join('');
    currentDocsWrap.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function markDocForRemoval(path) {
    const value = String(path || '').trim();
    if (!value) return;
    if (!editQuoteRemovedDocs.includes(value)) {
        editQuoteRemovedDocs.push(value);
    }
    renderEditCurrentDocs();
}

async function handleCreateQuote(e) {
    e.preventDefault();
    const btn = document.getElementById('create-quote-btn');
    const formError = document.getElementById('q-form-error');
    formError.classList.add('hidden');

    const clientName = document.getElementById('q-client').value.trim();
    const leadId = document.getElementById('q-lead-select').value;
    const amountRaw = document.getElementById('q-amount').value;
    const amount = parseFloat(amountRaw);
    const status = document.getElementById('q-status').value;
    const description = document.getElementById('q-desc').value.trim();
    const docInput = document.getElementById('q-doc');
    const docFiles = Array.from(docInput?.files || []);

    if (!clientName) {
        formError.textContent = 'Le nom du client est requis.';
        formError.classList.remove('hidden');
        return;
    }
    if (!Number.isFinite(amount) || amount <= 0) {
        formError.textContent = 'Saisissez un montant valide superieur a 0.';
        formError.classList.remove('hidden');
        return;
    }
    if (docFiles.length > 10) {
        formError.textContent = 'Maximum 10 documents par devis.';
        formError.classList.remove('hidden');
        return;
    }
    if (docFiles.some((f) => f.size > 10 * 1024 * 1024)) {
        formError.textContent = 'Un document depasse 10MB.';
        formError.classList.remove('hidden');
        return;
    }

    setButtonLoading(btn, true);
    try {
        const apiBase = window.API_BASE_URL || (window.location.pathname.startsWith('/rappel') ? '/rappel/api' : '/api');
        const token = Auth.getToken() || PHP_TOKEN || '';
        const formData = new FormData();
        formData.append('client_name', clientName);
        formData.append('lead_id', leadId || '');
        formData.append('amount', String(amount));
        formData.append('status', status);
        formData.append('project_name', description);
        formData.append('description', description);
        docFiles.forEach((file) => formData.append('doc[]', file));

        const res = await fetch(`${apiBase}/quotes`, {
            method: 'POST',
            headers: token ? { 'Authorization': `Bearer ${token}` } : {},
            body: formData,
        });
        const payload = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(payload.error || payload.message || 'Erreur lors de la creation du devis.');

        document.getElementById('q-client').value = '';
        document.getElementById('q-amount').value = '';
        document.getElementById('q-status').value = 'attente_client';
        document.getElementById('q-desc').value = '';
        document.getElementById('q-doc').value = '';
        document.getElementById('q-lead-select').value = ''; // Reset lead select
        closeCreateQuote();
        await loadQuotes();
        showToast('Devis cree avec succes.', 'success');
    } catch (err) {
        formError.textContent = err.message || 'Erreur lors de la creation du devis.';
        formError.classList.remove('hidden');
        showToast(err.message || 'Erreur lors de la creation du devis.', 'error');
    } finally {
        setButtonLoading(btn, false, 'Creer le devis');
    }
}

async function handleEditQuote(e) {
    e.preventDefault();
    const btn = document.getElementById('edit-quote-btn');
    const formError = document.getElementById('eq-form-error');
    formError.classList.add('hidden');

    const id = document.getElementById('eq-id').value.trim();
    const clientName = document.getElementById('eq-client').value.trim();
    const leadId = document.getElementById('eq-lead-select').value;
    const amountRaw = document.getElementById('eq-amount').value;
    const amount = parseFloat(amountRaw);
    const status = document.getElementById('eq-status').value;
    const projectName = document.getElementById('eq-project').value.trim();
    const docInput = document.getElementById('eq-doc');
    const docFiles = Array.from(docInput?.files || []);

    if (!id) {
        formError.textContent = 'Devis introuvable.';
        formError.classList.remove('hidden');
        return;
    }
    if (!clientName) {
        formError.textContent = 'Le nom du client est requis.';
        formError.classList.remove('hidden');
        return;
    }
    if (!Number.isFinite(amount) || amount <= 0) {
        formError.textContent = 'Saisissez un montant valide superieur a 0.';
        formError.classList.remove('hidden');
        return;
    }
    if (docFiles.length > 10) {
        formError.textContent = 'Maximum 10 documents par devis.';
        formError.classList.remove('hidden');
        return;
    }
    if (docFiles.some((f) => f.size > 10 * 1024 * 1024)) {
        formError.textContent = 'Un document depasse 10MB.';
        formError.classList.remove('hidden');
        return;
    }

    setButtonLoading(btn, true);
    try {
        const apiBase = window.API_BASE_URL || (window.location.pathname.startsWith('/rappel') ? '/rappel/api' : '/api');
        const token = Auth.getToken() || PHP_TOKEN || '';
        const formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('client_name', clientName);
        formData.append('lead_id', leadId || '');
        formData.append('amount', String(amount));
        formData.append('status', status);
        formData.append('project_name', projectName);
        formData.append('description', projectName);
        if (editQuoteRemovedDocs.length) {
            formData.append('remove_docs', JSON.stringify(editQuoteRemovedDocs));
        }
        docFiles.forEach((file) => formData.append('doc[]', file));

        const res = await fetch(`${apiBase}/quotes/${id}`, {
            method: 'POST',
            headers: token ? { 'Authorization': `Bearer ${token}` } : {},
            body: formData,
        });
        const payload = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(payload.error || payload.message || 'Erreur lors de la modification du devis.');

        closeEditQuote();
        await loadQuotes();
        showToast('Devis modifie avec succes.', 'success');
    } catch (err) {
        formError.textContent = err.message || 'Erreur lors de la modification du devis.';
        formError.classList.remove('hidden');
        showToast(err.message || 'Erreur lors de la modification du devis.', 'error');
    } finally {
        setButtonLoading(btn, false, 'Enregistrer');
    }
}

async function markQuoteCompleted(id) {
    if (!confirm('Confirmer que ce devis est r\u00e9alis\u00e9 ?')) return;
    try {
        await apiFetch(`/quotes/${id}`, {
            method: 'PATCH',
            body: JSON.stringify({ status: 'completed' }),
        });
        await loadQuotes();
        showToast('Devis marqu\u00e9 comme r\u00e9alis\u00e9.', 'success');
    } catch (err) {
        showToast(err.message || 'Erreur lors de la mise a jour.', 'error');
    }
}

function bindQuotesEvents() {
    document.getElementById('q-search').addEventListener('input', (e) => {
        state.query = e.target.value;
        refreshQuotesUI();
    });
    document.getElementById('q-filter-status').addEventListener('change', (e) => {
        state.status = e.target.value;
        refreshQuotesUI();
    });
    document.getElementById('q-sort').addEventListener('change', (e) => {
        state.sort = e.target.value;
        refreshQuotesUI();
    });
    document.getElementById('q-reset-filters').addEventListener('click', resetFilters);

    const modal = document.getElementById('quote-modal');
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeCreateQuote();
    });
    const editModal = document.getElementById('edit-quote-modal');
    editModal.addEventListener('click', (e) => {
        if (e.target === editModal) closeEditQuote();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeCreateQuote();
            closeEditQuote();
        }
    });

    document.getElementById('q-lead-select').addEventListener('change', () => onLeadSelectChange('q'));
    document.getElementById('eq-lead-select').addEventListener('change', () => onLeadSelectChange('eq'));
}

let allLeads = [];

async function loadLeads() {
    try {
        const data = await apiFetch('/leads');
        allLeads = Array.isArray(data) ? data : [];
        populateLeadSelects();
    } catch (err) {
        console.error('Failed to load leads:', err);
    }
}

function populateLeadSelects() {
    const selects = ['q-lead-select', 'eq-lead-select'];
    selects.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        
        const currentValue = el.value;
        el.innerHTML = '<option value="">-- Sélectionner un lead --</option>' + 
            allLeads.map(l => `<option value="${l.id}">${l.name} (${l.sector || 'Sans secteur'})</option>`).join('');
        el.value = currentValue;
    });
}

function onLeadSelectChange(prefix) {
    const select = document.getElementById(`${prefix}-lead-select`);
    const clientInput = document.getElementById(`${prefix}-client`);
    const projectInput = document.getElementById(prefix === 'q' ? 'q-desc' : 'eq-project');
    
    if (!select || !clientInput) return;
    
    const leadId = select.value;
    if (!leadId) return;
    
    const lead = allLeads.find(l => String(l.id) === String(leadId));
    if (lead) {
        if (!clientInput.value.trim()) {
            clientInput.value = lead.name;
        }
        if (projectInput && !projectInput.value.trim()) {
            projectInput.value = lead.need || '';
        }
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    const params = new URLSearchParams(window.location.search);
    const search = params.get('search');
    if (search) {
        state.query = search;
        const input = document.getElementById('q-search');
        if (input) input.value = search;
    }
    
    bindQuotesEvents();
    if (typeof lucide !== 'undefined') lucide.createIcons();
    await loadQuotes();
});
</script>
<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>

