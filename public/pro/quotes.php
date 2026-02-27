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
        <p class="text-xs uppercase tracking-wider font-bold text-navy-400 mb-1">Acceptes</p>
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
            <option value="sent">Envoye</option>
            <option value="accepted">Accepte</option>
            <option value="rejected">Refuse</option>
            <option value="draft">Brouillon</option>
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
                        <option value="sent">Envoye</option>
                        <option value="accepted">Accepte</option>
                        <option value="rejected">Refuse</option>
                        <option value="draft">Brouillon</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Description</label>
                <textarea id="q-desc" class="form-textarea" placeholder="Details de la prestation..."></textarea>
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
                        <option value="sent">Envoye</option>
                        <option value="accepted">Accepte</option>
                        <option value="rejected">Refuse</option>
                        <option value="draft">Brouillon</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Description / Projet</label>
                <textarea id="eq-project" class="form-textarea" placeholder="Details de la prestation..."></textarea>
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
    attente_client: 'bg-amber-50 text-amber-700',
    accepted: 'bg-accent-50 text-accent-700',
    rejected: 'bg-red-50 text-red-700',
};
const statusLabels = {
    draft: 'Brouillon',
    sent: 'Envoye',
    attente_client: 'En attente client',
    accepted: 'Accepte',
    rejected: 'Refuse',
};

const state = {
    quotes: [],
    query: '',
    status: 'all',
    sort: 'recent',
};

function normalizeStatus(value) {
    return String(value || '').trim().toLowerCase();
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
    const pending = allQuotes.filter(q => normalizeStatus(q.status) === 'attente_client').length;
    const accepted = allQuotes.filter(q => normalizeStatus(q.status) === 'accepted').length;
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
            </div>
            <div class="md:text-right md:min-w-[180px]">
                <p class="text-2xl font-display font-bold text-navy-950">${amount}</p>
                <p class="text-xs text-navy-400 font-medium">Cree le ${createdAt}</p>
            </div>
            <div class="flex md:flex-col gap-2 md:min-w-[130px]">
                <button type="button" onclick="openEditQuote('${q.id}')" class="btn btn-outline btn-sm rounded-xl">Modifier</button>
            </div>
        </article>`;
    }).join('')}</div>`;

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function refreshQuotesUI() {
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
    document.getElementById('eq-form-error').classList.add('hidden');
    
    const modal = document.getElementById('edit-quote-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('eq-client').focus();
}

function closeEditQuote() {
    const modal = document.getElementById('edit-quote-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('eq-form-error').classList.add('hidden');
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

    setButtonLoading(btn, true);
    try {
        await apiFetch('/quotes', {
            method: 'POST',
            body: JSON.stringify({
                client_name: clientName,
                lead_id: leadId || null,
                amount: amount,
                status: status,
                project_name: description,
                description: description,
            }),
        });

        document.getElementById('q-client').value = '';
        document.getElementById('q-amount').value = '';
        document.getElementById('q-status').value = 'attente_client';
        document.getElementById('q-desc').value = '';
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

    setButtonLoading(btn, true);
    try {
        await apiFetch(`/quotes/${id}`, {
            method: 'PATCH',
            body: JSON.stringify({
                client_name: clientName,
                lead_id: leadId || null,
                amount: amount,
                status: status,
                project_name: projectName,
                description: projectName,
            }),
        });

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
