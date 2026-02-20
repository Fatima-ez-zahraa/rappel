<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Plans & Tarifs';
$token = getToken();
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<div class="mb-8 text-center">
    <h1 class="text-3xl font-display font-bold text-navy-950">Plans & Tarifs</h1>
    <p class="text-navy-500 font-medium mt-2">Choisissez le plan adapte a votre activite</p>
</div>

<div id="pricing-grid" class="grid lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
    <div class="card p-8 animate-pulse">
        <div class="h-6 bg-navy-100 rounded w-1/2 mb-4"></div>
        <div class="h-10 bg-navy-100 rounded w-2/3 mb-6"></div>
        <div class="h-3 bg-navy-100 rounded w-full mb-3"></div>
        <div class="h-3 bg-navy-100 rounded w-5/6 mb-3"></div>
        <div class="h-3 bg-navy-100 rounded w-2/3 mb-8"></div>
        <div class="h-11 bg-navy-100 rounded w-full"></div>
    </div>
</div>

<?php
$safeToken = addslashes($token ?? '');
$extraScript = <<<'JS'
<script>
const PHP_TOKEN = '__PHP_TOKEN__';

function escapeHtml(input) {
    return String(input ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function defaultPlans() {
    return [
        {
            id: 'default-growth',
            name: 'Croissance',
            price: 99,
            currency: 'EUR',
            features: ['10 leads qualifies inclus', '1 secteur d activite', 'Ciblage departemental', 'Preuves de consentement SMS']
        },
        {
            id: 'default-acceleration',
            name: 'Acceleration',
            price: 249,
            currency: 'EUR',
            features: ['30 leads haute qualite', 'Ciblage regional illimite', 'CRM integre avec API', 'Support VIP 24/7', 'Garantie de remplacement lead']
        },
        {
            id: 'default-flex',
            name: 'Flexibilite',
            price: 12,
            currency: 'EUR',
            features: ['Zero cout fixe', 'Volume illimite', 'Recharge credit instantanee', 'Acces dashboard global']
        }
    ];
}

function formatPrice(price, currency) {
    const value = Number(price || 0);
    const code = (currency || 'EUR').toUpperCase();
    try {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: code, maximumFractionDigits: value % 1 === 0 ? 0 : 2 }).format(value);
    } catch {
        return `${value} ${code}`;
    }
}

function renderPricingPlans(plans) {
    const grid = document.getElementById('pricing-grid');
    if (!grid) return;

    const normalized = (Array.isArray(plans) && plans.length) ? plans : defaultPlans();
    const featuredIndex = normalized.length > 1 ? 1 : 0;

    grid.innerHTML = normalized.map((plan, index) => {
        const isFeatured = index === featuredIndex;
        const title = escapeHtml(plan.name || 'Forfait');
        const priceLabel = escapeHtml(formatPrice(plan.price, plan.currency));
        const features = Array.isArray(plan.features) ? plan.features : [];
        const btnLabel = isFeatured ? 'Choisir ce plan' : (index === normalized.length - 1 ? 'Commander' : 'Selectionner');

        return `
            <div class="card p-8 flex flex-col ${isFeatured ? 'bg-navy-900 border-accent-500/20 shadow-2xl relative overflow-hidden' : 'bg-white/40 border-white/60'}">
                ${isFeatured ? '<div class="absolute top-4 right-4 bg-accent-500 text-white text-xs font-bold px-3 py-1 rounded-full">Populaire</div>' : ''}
                <h3 class="text-xl font-bold ${isFeatured ? 'text-white' : 'text-navy-950'} mb-2">${title}</h3>
                <p class="${isFeatured ? 'text-navy-300' : 'text-navy-500'} mb-6 font-medium text-sm">Plan gere par l administration.</p>
                <div class="flex items-baseline gap-2 mb-8 ${isFeatured ? 'text-white' : ''}">
                    <span class="text-4xl ${isFeatured ? 'lg:text-5xl' : ''} font-bold">${priceLabel}</span>
                    <span class="${isFeatured ? 'text-navy-400' : 'text-navy-400'} font-bold">/mois</span>
                </div>
                <ul class="space-y-4 flex-grow mb-8">
                    ${features.map((item) => `
                        <li class="flex items-center gap-3 ${isFeatured ? 'text-navy-100' : 'text-navy-700'} font-medium text-sm">
                            <div class="w-5 h-5 rounded-full ${isFeatured ? 'bg-accent-500' : 'bg-accent-100'} flex items-center justify-center flex-shrink-0">
                                <i data-lucide="check" class="${isFeatured ? 'text-navy-950' : 'text-accent-600'}" style="width:12px;height:12px;"></i>
                            </div>
                            ${escapeHtml(item)}
                        </li>
                    `).join('') || '<li class="text-navy-400 text-sm">Aucune fonctionnalite renseignee.</li>'}
                </ul>
                <a href="#" class="btn ${isFeatured ? 'btn-accent shadow-premium' : 'btn-outline border-navy-200'} w-full rounded-xl text-center">${btnLabel}</a>
            </div>
        `;
    }).join('');

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

async function loadPricingPlans() {
    if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);
    try {
        const plans = await apiFetch('/plans');
        renderPricingPlans(plans);
    } catch (err) {
        console.error('Pricing load error:', err);
        renderPricingPlans(defaultPlans());
    }
}

document.addEventListener('DOMContentLoaded', loadPricingPlans);
</script>
JS;
$extraScript = str_replace('__PHP_TOKEN__', $safeToken, $extraScript);
?>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>
