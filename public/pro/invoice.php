<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Facture';
$user = getCurrentUser();
$token = getToken();
$invoiceId = $_GET['id'] ?? '';
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8 animate-fade-in">
    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-10">
        <a href="settings.php" class="flex items-center gap-2 text-navy-500 hover:text-brand-600 font-bold transition-colors">
            <i data-lucide="arrow-left" style="width:18px;height:18px;"></i>
            <span>Retour aux paramètres</span>
        </a>
        <!-- <button onclick="window.print()" class="h-12 px-6 bg-navy-950 text-white rounded-xl font-bold flex items-center gap-2 shadow-lg hover:bg-black transition-all active:scale-95">
            <i data-lucide="printer" style="width:18px;height:18px;"></i>
            <span>Imprimer / PDF</span>
        </button> -->
    </div>

    <!-- Invoice Card -->
    <div id="invoice-paper" class="bg-white rounded-[2rem] shadow-premium overflow-hidden border border-navy-100 p-12 md:p-20 relative min-h-[1100px]">
        <!-- Watermark/bg-decoration -->
        <div class="absolute top-0 right-0 p-12 opacity-[0.03] pointer-events-none">
            <i data-lucide="file-text" style="width:400px;height:400px;"></i>
        </div>

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between gap-12 mb-20 relative z-10">
            <div>
                <img src="/rappel/public/assets/img/logo.png" alt="RAPPEL" class="h-10 mb-8">
                <div class="text-sm text-navy-500 font-medium space-y-1">
                    <p class="font-bold text-navy-950">Rappelez-moi S.A.S</p>
                    <p>123 Avenue des Champs-Élysées</p>
                    <p>75008 Paris, France</p>
                    <p>contact@rappel.fr</p>
                </div>
            </div>
            <div class="text-right">
                <h1 class="text-5xl font-black text-navy-950 mb-4 tracking-tighter uppercase">Facture</h1>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-navy-400 uppercase tracking-widest">Référence</p>
                    <p id="inv-number" class="text-xl font-bold text-brand-600 font-mono">Chargement...</p>
                </div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-20 relative z-10">
            <div class="space-y-4">
                <p class="text-[10px] font-black text-navy-400 uppercase tracking-widest">Adressée à</p>
                <div class="text-sm text-navy-500 font-medium space-y-1">
                    <p id="client-name" class="font-bold text-navy-950 text-lg"></p>
                    <p id="client-address"></p>
                    <p id="client-city"></p>
                    <p id="client-siret"></p>
                </div>
            </div>
            <div class="space-y-4 text-right">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-navy-400 uppercase tracking-widest">Date d'émission</p>
                    <p id="inv-date" class="text-sm font-bold text-navy-950"></p>
                </div>
                <div class="space-y-1 pt-4">
                    <p class="text-[10px] font-black text-navy-400 uppercase tracking-widest">Échéance</p>
                    <p id="inv-due" class="text-sm font-bold text-navy-950"></p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="mb-20 relative z-10">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b-2 border-navy-950 text-left">
                        <th class="py-4 text-[10px] font-black text-navy-950 uppercase tracking-widest">Désignation</th>
                        <th class="py-4 text-[10px] font-black text-navy-950 uppercase tracking-widest text-center">Qté</th>
                        <th class="py-4 text-[10px] font-black text-navy-950 uppercase tracking-widest text-right">Prix Unitaire</th>
                        <th class="py-4 text-[10px] font-black text-navy-950 uppercase tracking-widest text-right">Montant HT</th>
                    </tr>
                </thead>
                <tbody id="line-items" class="divide-y divide-navy-50">
                    <!-- Dynamic Items -->
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="flex justify-end relative z-10">
            <div class="w-full max-w-xs space-y-4">
                <div class="flex justify-between text-sm">
                    <span class="text-navy-400 font-bold uppercase tracking-widest text-[10px]">Total HT</span>
                    <span id="total-ht" class="font-bold text-navy-950"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-navy-400 font-bold uppercase tracking-widest text-[10px]">TVA (20%)</span>
                    <span id="total-tva" class="font-bold text-navy-950"></span>
                </div>
                <div class="h-px bg-navy-950 my-2"></div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-xs font-black text-navy-950 uppercase tracking-widest">Total TTC</span>
                    <span id="total-ttc" class="text-3xl font-black text-brand-600"></span>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="absolute bottom-20 left-12 right-12 border-t border-navy-50 pt-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                <div class="text-[10px] text-navy-400 font-medium leading-relaxed uppercase tracking-tight">
                    <p>Conditions de règlement : Paiement à réception.</p>
                    <p>En cas de retard de paiement, une pénalité de 3 fois le taux d'intérêt légal sera appliquée.</p>
                </div>
                <div class="text-right">
                    <span id="inv-status" class="inline-flex px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; }
    #dashboard-sidebar, 
    #dashboard-header,
    .flex.justify-between.items-center.mb-10 { display: none !important; }
    .max-w-4xl { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
    #invoice-paper { box-shadow: none !important; border: none !important; padding: 0 !important; }
    .shadow-premium { box-shadow: none !important; }
}
</style>

<script>
const INVOICE_ID = '<?= htmlspecialchars($invoiceId) ?>';
const PHP_TOKEN = '<?= addslashes($token) ?>';
const PHP_USER = <?= json_encode($user) ?>;

async function init() {
    console.log('Invoice Init - ID:', INVOICE_ID);
    
    if (!INVOICE_ID) {
        window.location.href = 'settings.php';
        return;
    }
    
    if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);
    if (PHP_USER) Auth.setUser(PHP_USER);

    // Auto-print check
    const urlParams = new URLSearchParams(window.location.search);
    const shouldPrint = urlParams.get('print') === '1';

    try {
        console.log('Fetching invoice data...');
        const inv = await apiFetch(`/invoices/${INVOICE_ID}`);
        console.log('Invoice data received:', inv);
        
        const user = PHP_USER || Auth.getUser();
        if (!user) {
            console.error('User data missing');
            throw new Error('Informations utilisateur manquantes (session expirée ?)');
        }

        // Fill Data
        document.getElementById('inv-number').textContent = inv.invoice_number;
        document.getElementById('inv-date').textContent = new Date(inv.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
        
        // Due date (default +15 days)
        const dueDate = new Date(inv.created_at);
        dueDate.setDate(dueDate.getDate() + 15);
        document.getElementById('inv-due').textContent = dueDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });

        // Client Info
        document.getElementById('client-name').textContent = user.company_name || (user.first_name + ' ' + user.last_name);
        document.getElementById('client-address').textContent = user.address || '';
        document.getElementById('client-city').textContent = (user.zip || '') + ' ' + (user.city || '');
        document.getElementById('client-siret').textContent = user.siret ? 'SIRET : ' + user.siret : '';

        // Status
        const statusEl = document.getElementById('inv-status');
        statusEl.textContent = inv.status === 'paid' ? 'Payée' : 'En attente';
        statusEl.className += inv.status === 'paid' ? ' bg-emerald-50 text-emerald-600 border-emerald-100' : ' bg-amber-50 text-amber-600 border-amber-100';

        // Amounts
        const totalHT = parseFloat(inv.amount);
        const tva = totalHT * 0.2;
        const totalTTC = totalHT + tva;
        const currency = inv.currency || '€';

        document.getElementById('total-ht').textContent = totalHT.toFixed(2) + ' ' + currency;
        document.getElementById('total-tva').textContent = tva.toFixed(2) + ' ' + currency;
        document.getElementById('total-ttc').textContent = totalTTC.toFixed(2) + ' ' + currency;

        // Line Items
        const list = document.getElementById('line-items');
        list.innerHTML = `
            <tr>
                <td class="py-6">
                    <p class="font-bold text-navy-900 text-base">Service d'acquisition de prospects</p>
                    <p class="text-sm text-navy-400 mt-1">Abonnement expert mensuel</p>
                </td>
                <td class="py-6 text-center text-sm font-bold text-navy-950">1</td>
                <td class="py-6 text-right text-sm font-bold text-navy-950">${totalHT.toFixed(2)} ${currency}</td>
                <td class="py-6 text-right text-sm font-black text-navy-950">${totalHT.toFixed(2)} ${currency}</td>
            </tr>
        `;

        if (typeof lucide !== 'undefined') lucide.createIcons();
        if (shouldPrint) {
            setTimeout(() => window.print(), 500);
        }
    } catch (err) {
        console.error('Invoice display error:', err);
        const errorBox = document.getElementById('error-box');
        const errorMsg = document.getElementById('error-message');
        const debugInfo = document.getElementById('debug-info');
        const invPaper = document.getElementById('invoice-paper');
        
        errorMsg.textContent = err.message || 'Une erreur inconnue est survenue.';
        debugInfo.textContent = `ID: ${INVOICE_ID}\nError: ${err.stack || err}\nContext: ${window.location.href}`;
        
        errorBox.classList.remove('hidden');
        invPaper.classList.add('opacity-50', 'pointer-events-none');
        
        document.getElementById('inv-number').textContent = 'Erreur';
        showToast('Impossible de charger la facture', 'error');
    }
}

document.addEventListener('DOMContentLoaded', init);
</script>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>
