<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Plans & Tarifs';
$token = getToken();
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<!-- Header Section -->
<div class="mb-16 text-center animate-fade-in-up">
    <h1 class="text-4xl lg:text-5xl font-display font-black text-navy-950 tracking-tight leading-tight">Plans & Tarifs</h1>
    <div class="flex items-center justify-center gap-3 mt-4">
        <div class="w-12 h-1 bg-accent-500 rounded-full"></div>
        <p class="text-navy-400 font-bold uppercase text-[10px] tracking-[0.2em]">Propulsez votre activité au niveau supérieur</p>
    </div>
    
    <div id="pricing-notification" class="max-w-xl mx-auto mt-8 hidden"></div>
</div>

<!-- Pricing Grid -->
<div id="pricing-grid" class="grid lg:grid-cols-3 gap-8 max-w-6xl mx-auto mb-20 px-4">
    <?php for ($i = 0; $i < 3; $i++): ?>
    <div class="glass-card p-10 animate-pulse rounded-[3rem] border-white/60 bg-white/40 h-[600px] flex flex-col items-center">
        <div class="w-16 h-4 bg-navy-50 rounded-full mb-8"></div>
        <div class="h-10 bg-navy-50 rounded-xl w-3/4 mb-10"></div>
        <div class="space-y-4 w-full mb-12">
            <div class="h-4 bg-navy-50 rounded w-full"></div>
            <div class="h-4 bg-navy-50 rounded w-5/6"></div>
            <div class="h-4 bg-navy-50 rounded w-4/5"></div>
        </div>
        <div class="mt-auto w-full h-14 bg-navy-50 rounded-2xl"></div>
    </div>
    <?php endfor; ?>
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
            icon: 'trending-up',
            color: 'brand',
            features: ['10 leads qualifiés inclus', '1 secteur d\'activité', 'Ciblage départemental', 'E-mails de confirmation', 'Support par ticket']
        },
        {
            id: 'default-acceleration',
            name: 'Accélération',
            price: 249,
            currency: 'EUR',
            icon: 'zap',
            color: 'accent',
            featured: true,
            features: ['30 leads haute qualité', 'Multi-secteurs illimités', 'Ciblage régional / National', 'Preuves de consentement SMS', 'CRM intégré & API', 'Support prioritaire VIP']
        },
        {
            id: 'default-flex',
            name: 'Flexibilité',
            price: 12,
            currency: 'EUR',
            icon: 'layers',
            color: 'navy',
            features: ['Zéro coût fixe mensuel', 'Paiement au lead réel', 'Recharge crédit instantanée', 'Volume de leads illimité', 'Accès dashboard global']
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

    grid.innerHTML = normalized.map((plan, index) => {
        const isFeatured = plan.featured || index === 1;
        const title = escapeHtml(plan.name || 'Forfait');
        const priceLabel = formatPrice(plan.price, plan.currency);
        const features = Array.isArray(plan.features) ? plan.features : [];
        const btnLabel = isFeatured ? 'Activer le plan' : 'Commander';
        const icon = plan.icon || 'star';
        
        return `
            <div class="glass-card p-10 flex flex-col relative transition-all duration-700 group border-white/60 animate-fade-in-up h-full
                        ${isFeatured ? 'bg-navy-950/95 border-navy-800 shadow-[0_30px_60px_rgba(0,0,0,0.25)] scale-105 z-10 !bg-opacity-100' : 'bg-white/40 shadow-premium hover:shadow-2xl'}" 
                 style="animation-delay: ${index * 150}ms; ${isFeatured ? 'background-color: #0a1036;' : ''}">
                
                ${isFeatured ? `
                <div class="absolute -top-5 left-1/2 -translate-x-1/2 bg-accent-500 text-navy-950 text-[10px] font-black px-5 py-2 rounded-full uppercase tracking-[0.2em] shadow-lg shadow-accent-500/30">
                    Recommandé
                </div>` : ''}

                <div class="mb-10 text-center">
                    <div class="w-16 h-16 mx-auto rounded-[1.5rem] flex items-center justify-center mb-6 shadow-inner ring-1 transition-transform duration-500 group-hover:scale-110
                                ${isFeatured ? 'bg-white/10 text-accent-400 ring-white/10' : 'bg-brand-50 text-brand-600 ring-brand-100'}">
                        <i data-lucide="${icon}" style="width:32px;height:32px;"></i>
                    </div>
                    <h3 class="text-2xl font-display font-black tracking-tight uppercase mb-4 ${isFeatured ? 'text-white' : 'text-navy-950'}">${title}</h3>
                    <div class="flex items-baseline justify-center gap-1 ${isFeatured ? 'text-white' : 'text-navy-950'}">
                        <span class="text-5xl lg:text-6xl font-black tracking-tighter">${priceLabel.replace(',00', '').replace('€', '')}</span>
                        <span class="text-lg font-bold">€</span>
                        <span class="font-bold uppercase text-[10px] tracking-widest ml-1 ${isFeatured ? 'text-navy-400' : 'text-navy-500'}">/ mois</span>
                    </div>
                </div>

                <ul class="space-y-5 flex-grow mb-10 border-t border-b py-8 ${isFeatured ? 'border-white/10' : 'border-navy-50'}">
                    ${features.map((item) => `
                        <li class="flex items-start gap-4 ${isFeatured ? 'text-white' : 'text-navy-700'} font-bold text-xs uppercase tracking-tight">
                            <div class="w-6 h-6 rounded-lg ${isFeatured ? 'bg-accent-500/20 text-accent-400' : 'bg-accent-100 text-accent-600'} flex items-center justify-center flex-shrink-0 mt-[-2px]">
                                <i data-lucide="check" style="width:14px;height:14px;stroke-width:4"></i>
                            </div>
                            <span class="leading-tight">${escapeHtml(item)}</span>
                        </li>
                    `).join('') || '<li class="text-navy-400 text-sm italic">Contactez-nous pour le détail</li>'}
                </ul>

                <button onclick="buyPlan('${plan.id}')" 
                        class="w-full py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all active:scale-95 shadow-lg group-hover:-translate-y-1
                           ${isFeatured ? 'bg-accent-500 text-navy-950 hover:bg-accent-400 shadow-accent-500/20' : 'bg-navy-950 text-white hover:bg-navy-800 shadow-navy-900/20'}">
                    ${btnLabel}
                </button>
                
                <p class="text-center mt-6 text-[10px] font-black uppercase tracking-widest ${isFeatured ? 'text-navy-300' : 'text-navy-600'} opacity-80">
                    Sans engagement · Annulable à tout moment
                </p>
            </div>
        `;
    }).join('');

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

async function buyPlan(planId) {
    if (!planId) return;
    
    const btn = event.currentTarget;
    const originalText = btn.innerText;
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="animate-spin" style="width:20px;height:20px;margin:0 auto;"></i>';
    if (typeof lucide !== 'undefined') lucide.createIcons();

    try {
        const response = await apiFetch('/payments/create-checkout-session', {
            method: 'POST',
            body: JSON.stringify({ plan_id: planId })
        });

        if (response.url) {
            window.location.href = response.url;
        } else {
            throw new Error(response.error || 'Erreur lors de la création de la session de paiement.');
        }
    } catch (err) {
        console.error('Checkout error:', err);
        if (typeof Toast !== 'undefined') {
            Toast.error(err.message);
        } else {
            alert('Erreur: ' + err.message);
        }
        btn.disabled = false;
        btn.innerText = originalText;
    }
}

function handleNotifications() {
    const params = new URLSearchParams(window.location.search);
    const notif = document.getElementById('pricing-notification');
    if (!notif) return;

    if (params.get('success')) {
        notif.className = "max-w-xl mx-auto mt-8 p-6 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 text-emerald-800 animate-fade-in";
        notif.innerHTML = `
            <div class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle-2"></i>
            </div>
            <div>
                <p class="font-black uppercase tracking-widest text-[10px] mb-1">Succès</p>
                <p class="text-sm font-bold">Votre abonnement a été activé avec succès !</p>
            </div>
        `;
        notif.classList.remove('hidden');
    } else if (params.get('canceled')) {
        notif.className = "max-w-xl mx-auto mt-8 p-6 bg-amber-50 border border-amber-100 rounded-2xl flex items-center gap-4 text-amber-800 animate-fade-in";
        notif.innerHTML = `
            <div class="w-12 h-12 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-circle"></i>
            </div>
            <div>
                <p class="font-black uppercase tracking-widest text-[10px] mb-1">Information</p>
                <p class="text-sm font-bold">Le paiement a été annulé. Vous pouvez réessayer quand vous le souhaitez.</p>
            </div>
        `;
        notif.classList.remove('hidden');
    }

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

async function loadPricingPlans() {
    if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);
    handleNotifications();
    try {
        const plans = await apiFetch('/plans');
        renderPricingPlans(plans);
        // Auto-select plan from URL param (e.g. after checkout.php redirect)
        const selectParam = new URLSearchParams(window.location.search).get('select');
        if (selectParam && Array.isArray(plans)) {
            const match = plans.find(p => p.name && p.name.toLowerCase() === selectParam.toLowerCase());
            if (match) {
                // Small delay to allow render
                setTimeout(() => {
                    const btns = document.querySelectorAll('[onclick]');
                    btns.forEach(btn => {
                        if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes(match.id)) {
                            btn.click();
                        }
                    });
                }, 500);
            }
        }
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

