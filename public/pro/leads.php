<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Mes Leads';
$token = getToken();
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-display font-bold text-navy-950">Mes Leads</h1>
        <p class="text-navy-500 font-medium mt-1">Gérez vos contacts et opportunités d'affaires</p>
    </div>
    <button onclick="openAddModal()" class="btn btn-primary rounded-xl px-6 shadow-premium">
        <i data-lucide="user-plus" style="width:18px;height:18px;"></i>
        Ajouter un lead
    </button>
</div>

<!-- Filters -->
<div class="card p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
    <div class="relative w-full md:w-72">
        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-navy-400">
            <i data-lucide="search" style="width:16px;height:16px;"></i>
        </div>
        <input type="text" id="search-input" placeholder="Rechercher un lead..."
               class="w-full pl-10 pr-4 h-11 bg-navy-50 border border-navy-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 font-medium"
               oninput="filterLeads()">
    </div>
    <div class="flex gap-2 flex-wrap">
        <button onclick="setFilter('all')" id="filter-all" class="btn btn-primary btn-sm rounded-xl">Tous</button>
        <button onclick="setFilter('pending')" id="filter-pending" class="btn btn-outline btn-sm rounded-xl">En attente</button>
        <button onclick="setFilter('processed')" id="filter-processed" class="btn btn-outline btn-sm rounded-xl">Traités</button>
    </div>
</div>

<!-- Leads List -->
<div id="leads-container">
    <div class="flex items-center justify-center py-20">
        <div class="text-center">
            <div class="spinner spinner-dark mx-auto mb-4"></div>
            <p class="text-navy-500 font-bold text-sm">Chargement des leads...</p>
        </div>
    </div>
</div>

<!-- Add Lead Modal -->
<div id="add-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-scale-in">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h2 class="text-xl font-bold text-navy-900">Ajouter un Lead Manuel</h2>
            <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-white transition-colors">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <form onsubmit="handleAddLead(event)" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nom Complet *</label>
                    <input type="text" id="new-name" class="form-input" placeholder="Jean Dupont" required>
                </div>
                <div>
                    <label class="form-label">Email *</label>
                    <input type="email" id="new-email" class="form-input" placeholder="jean@gmail.com" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Téléphone *</label>
                    <input type="tel" id="new-phone" class="form-input" placeholder="0612345678" required>
                </div>
                <div>
                    <label class="form-label">Secteur</label>
                    <select id="new-sector" class="form-select">
                        <option value="">Sélectionner...</option>
                        <option value="assurance">Assurance</option>
                        <option value="renovation">Rénovation</option>
                        <option value="energie">Énergie</option>
                        <option value="finance">Finance</option>
                        <option value="garage">Garage</option>
                        <option value="telecom">Télécoms</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Adresse / Ville</label>
                <input type="text" id="new-address" class="form-input" placeholder="Paris 75001">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Besoin principal</label>
                    <input type="text" id="new-need" class="form-input" placeholder="Assurance Auto">
                </div>
                <div>
                    <label class="form-label">Budget Approx.</label>
                    <input type="text" id="new-budget" class="form-input" placeholder="1500€">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeAddModal()" class="btn btn-outline flex-1 rounded-xl">Annuler</button>
                <button type="submit" id="add-lead-btn" class="btn btn-primary flex-1 rounded-xl">Créer le Lead</button>
            </div>
        </form>
    </div>
</div>

<!-- Details Modal -->
<div id="details-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-scale-in">
        <div id="details-content"></div>
    </div>
</div>

<!-- Edit Lead Modal -->
<div id="edit-lead-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-scale-in">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h2 class="text-xl font-bold text-navy-900">Modifier le lead</h2>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-white transition-colors">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <form onsubmit="handleEditLead(event)" class="p-6 space-y-4">
            <input type="hidden" id="edit-id">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nom complet *</label>
                    <input type="text" id="edit-name" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" id="edit-email" class="form-input">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Téléphone *</label>
                    <input type="tel" id="edit-phone" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Secteur</label>
                    <select id="edit-sector" class="form-select">
                        <option value="">Sélectionner...</option>
                        <option value="assurance">Assurance</option>
                        <option value="renovation">Rénovation</option>
                        <option value="energie">Énergie</option>
                        <option value="finance">Finance</option>
                        <option value="garage">Garage</option>
                        <option value="telecom">Télécoms</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Adresse / Ville</label>
                    <input type="text" id="edit-address" class="form-input">
                </div>
                <div>
                    <label class="form-label">Statut</label>
                    <select id="edit-status" class="form-select">
                        <option value="pending">En attente</option>
                        <option value="processed">Traité</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Besoin principal</label>
                    <input type="text" id="edit-need" class="form-input">
                </div>
                <div>
                    <label class="form-label">Budget</label>
                    <input type="text" id="edit-budget" class="form-input">
                </div>
            </div>
            <div>
                <label class="form-label">Créneau souhaité</label>
                <input type="text" id="edit-time-slot" class="form-input" placeholder="matin / après-midi / date...">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeEditModal()" class="btn btn-outline flex-1 rounded-xl">Annuler</button>
                <button type="submit" id="edit-lead-btn" class="btn btn-primary flex-1 rounded-xl">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php
$safeToken = addslashes($token ?? '');
$extraScript = <<<'JS'
<script>
const PHP_TOKEN = 'RAPPEL_PHP_TOKEN_PLACEHOLDER';
let allLeads = [];
let currentFilter = 'all';

async function loadLeads() {
    if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);
    try {
        const data = await apiFetch('/leads');
        allLeads = Array.isArray(data) ? data : [];
        renderLeads();
    } catch (err) {
        document.getElementById('leads-container').innerHTML = `<div class="form-error">${err.message}</div>`;
    }
}

function setFilter(f) {
    currentFilter = f;
    ['all','pending','processed'].forEach(id => {
        const btn = document.getElementById('filter-' + id);
        if (id === f) {
            btn.className = 'btn btn-primary btn-sm rounded-xl';
        } else {
            btn.className = 'btn btn-outline btn-sm rounded-xl';
        }
    });
    renderLeads();
}

function filterLeads() { renderLeads(); }

function renderLeads() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const filtered = allLeads.filter(l => {
        const matchFilter = currentFilter === 'all' || l.status === currentFilter;
        const name = (l.name || l.first_name + ' ' + l.last_name || '').toLowerCase();
        const email = (l.email || '').toLowerCase();
        const matchSearch = !search || name.includes(search) || email.includes(search);
        return matchFilter && matchSearch;
    });

    const el = document.getElementById('leads-container');
    if (!filtered.length) {
        el.innerHTML = `<div class="text-center py-20 bg-white/50 rounded-3xl border border-dashed border-navy-200">
            <i data-lucide="inbox" class="text-navy-300 mx-auto mb-4" style="width:48px;height:48px;"></i>
            <p class="font-bold text-navy-400">Aucun lead trouvé.</p>
        </div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    el.innerHTML = `<div class="space-y-4">${filtered.map(l => {
        const name = l.name || ((l.first_name || '') + ' ' + (l.last_name || '')).trim() || 'Inconnu';
        const isPending = l.status === 'pending';
        return `
        <div class="card p-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-center hover:shadow-premium transition-all duration-200">
            <div class="md:col-span-3 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 font-bold text-lg border border-brand-100 shadow-sm flex-shrink-0">
                    ${name.charAt(0).toUpperCase()}
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-navy-950 truncate">${name}</h3>
                    <p class="text-sm text-navy-400 font-medium">${l.time || 'Récemment'}</p>
                </div>
            </div>
            <div class="md:col-span-3 space-y-1">
                <div class="flex items-center gap-2 text-sm text-navy-700 font-medium">
                    <i data-lucide="phone" class="text-navy-400 flex-shrink-0" style="width:14px;height:14px;"></i>
                    <span class="truncate">${l.phone || '-'}</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-navy-700 font-medium">
                    <i data-lucide="mail" class="text-navy-400 flex-shrink-0" style="width:14px;height:14px;"></i>
                    <span class="truncate">${l.email || '-'}</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-navy-700 font-medium">
                    <i data-lucide="map-pin" class="text-navy-400 flex-shrink-0" style="width:14px;height:14px;"></i>
                    <span class="truncate">${l.address || l.zip_code || '-'}</span>
                </div>
            </div>
            <div class="md:col-span-3">
                <span class="inline-block px-3 py-1 bg-accent-50 text-accent-700 rounded-full text-xs font-bold mb-2 uppercase tracking-wide">
                    ${l.need || l.service_type || l.sector || '-'}
                </span>
                ${l.time_slot ? `<p class="text-xs text-navy-400 font-medium"><i data-lucide="clock" style="width:11px;height:11px;display:inline;vertical-align:middle;"></i> ${l.time_slot}</p>` : ''}
                <p class="text-xs text-navy-500 font-bold mt-1">Budget: <span class="font-black text-navy-950">${l.budget || '-'}</span></p>
            </div>
            <div class="md:col-span-3 flex flex-col md:items-end gap-2">
                <span class="badge ${isPending ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700'}">
                    ${isPending ? 'En attente' : 'Traité'}
                </span>
                <div class="flex gap-2">
                    <button onclick="openDetails('${l.id}')" class="btn btn-outline btn-sm rounded-xl">Détails</button>
                    <button onclick="openEditModal('${l.id}')" class="btn btn-outline btn-sm rounded-xl">Modifier</button>
                    <a href="tel:${l.phone}" onclick="markProcessed('${l.id}')" class="btn btn-sm rounded-xl bg-accent-500 hover:bg-accent-600 text-white shadow-sm">
                        <i data-lucide="phone" style="width:16px;height:16px;"></i>
                    </a>
                </div>
            </div>
        </div>`;
    }).join('')}</div>`;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

async function markProcessed(id) {
    try { await apiFetch('/leads/' + id, { method: 'PATCH', body: JSON.stringify({ status: 'processed' }) }); loadLeads(); } catch {}
}

function openAddModal() {
    document.getElementById('add-modal').classList.remove('hidden');
}
function closeAddModal() {
    document.getElementById('add-modal').classList.add('hidden');
}

function openEditModal(id) {
    const lead = allLeads.find(x => String(x.id) === String(id));
    if (!lead) return;

    document.getElementById('edit-id').value = lead.id || '';
    document.getElementById('edit-name').value = lead.name || '';
    document.getElementById('edit-email').value = lead.email || '';
    document.getElementById('edit-phone').value = lead.phone || '';
    document.getElementById('edit-sector').value = lead.sector || '';
    document.getElementById('edit-address').value = lead.address || '';
    document.getElementById('edit-status').value = lead.status || 'pending';
    document.getElementById('edit-need').value = lead.need || '';
    document.getElementById('edit-budget').value = lead.budget || '';
    document.getElementById('edit-time-slot').value = lead.time_slot || '';

    document.getElementById('edit-lead-modal').classList.remove('hidden');
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
        })});
        closeAddModal();
        loadLeads();
        showToast('Lead ajouté avec succès!', 'success');
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
        showToast('Lead modifié avec succès!', 'success');
    } catch (err) {
        showToast(err.message || 'Erreur lors de la modification.', 'error');
    } finally {
        setButtonLoading(btn, false, 'Enregistrer');
    }
}

function openDetails(id) {
    const l = allLeads.find(x => x.id === id);
    if (!l) return;
    const name = l.name || ((l.first_name || '') + ' ' + (l.last_name || '')).trim() || 'Inconnu';
    const isPending = l.status === 'pending';
    document.getElementById('details-content').innerHTML = `
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <div>
                <h2 class="text-xl font-bold text-navy-900">${name}</h2>
                <p class="text-sm text-slate-500">Détails du prospect</p>
            </div>
            <button onclick="document.getElementById('details-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-white transition-colors">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <div class="p-8 grid md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Informations de contact</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-slate-700">
                            <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600"><i data-lucide="phone" style="width:16px;height:16px;"></i></div>
                            <span class="font-medium">${l.phone || '-'}</span>
                        </div>
                        <div class="flex items-center gap-3 text-slate-700">
                            <div class="w-8 h-8 rounded-lg bg-accent-50 flex items-center justify-center text-accent-600"><i data-lucide="mail" style="width:16px;height:16px;"></i></div>
                            <span class="font-medium">${l.email || '-'}</span>
                        </div>
                        <div class="flex items-center gap-3 text-slate-700">
                            <div class="w-8 h-8 rounded-lg bg-neutral-50 flex items-center justify-center text-neutral-600"><i data-lucide="map-pin" style="width:16px;height:16px;"></i></div>
                            <span class="font-medium">${l.address || '-'}</span>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Statut actuel</h3>
                    <div class="flex items-center justify-between">
                        <span class="badge ${isPending ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700'}">
                            ${isPending ? 'En attente' : 'Traité'}
                        </span>
                        ${isPending ? `<button onclick="markProcessed('${l.id}'); document.getElementById('details-modal').classList.add('hidden');" class="btn btn-outline btn-sm rounded-xl text-xs">Marquer traité</button>` : ''}
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Besoin & Projet</h3>
                    <div class="p-4 bg-indigo-50/50 rounded-xl border border-indigo-100">
                        <p class="text-indigo-900 font-semibold mb-1">${l.need || l.service_type || '-'}</p>
                        <p class="text-sm text-indigo-600/80 mb-3 capitalize">${l.sector || l.service_type || ''}</p>
                        ${l.time_slot ? `<div class="flex items-center gap-2 text-xs text-indigo-700 mb-3"><i data-lucide="clock" style="width:14px;height:14px;"></i> Rappel souhaité : <strong>${l.time_slot}</strong></div>` : ''}
                        <div class="flex justify-between items-center pt-3 border-t border-indigo-100">
                            <span class="text-xs text-indigo-700/60 italic">Budget estimé</span>
                            <span class="font-bold text-indigo-900">${l.budget || '-'}</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <a href="tel:${l.phone}" class="btn btn-accent w-full rounded-xl py-4 shadow-premium font-bold text-center">
                        <i data-lucide="phone" style="width:20px;height:20px;"></i> Appeler maintenant
                    </a>
                    <button onclick="document.getElementById('details-modal').classList.add('hidden')" class="btn btn-outline w-full rounded-xl">Fermer</button>
                </div>
            </div>
        </div>`;
    document.getElementById('details-modal').classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', loadLeads);
</script>
JS;
$extraScript = str_replace('RAPPEL_PHP_TOKEN_PLACEHOLDER', $safeToken, $extraScript);
?>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>
