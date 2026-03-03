<?php
require_once __DIR__ . '/includes/auth.php';
requireAuth(false);
$pageTitle = 'Document Devis';
$user = getCurrentUser();
$token = getToken();
$quoteId = trim((string)($_GET['id'] ?? ''));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
    <style>
    body { background:#f3f4f6; }
    .doc-wrap { max-width: 960px; margin: 1.5rem auto; padding: 0 1rem; }
    .doc-sheet { background:#fff; border:1px solid #e5e7eb; border-radius: 1.25rem; box-shadow: 0 10px 30px rgba(15,23,42,.06); overflow:hidden; }
    .doc-top { padding: 1.25rem 1.25rem 1rem; border-bottom:1px solid #eef2f7; }
    .doc-body { padding: 1.25rem; }
    .doc-grid { display:grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
    .doc-label { font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .09em; color: #94a3b8; }
    .doc-val { font-size: .95rem; font-weight: 700; color: #0f172a; }
    .doc-viewer { margin-top: 1rem; border:1px solid #e5e7eb; border-radius: .9rem; overflow:hidden; background:#fff; }
    .doc-actions { display:flex; gap:.5rem; flex-wrap:wrap; }
    .doc-btn { display:inline-flex; align-items:center; gap:.4rem; border:1px solid #dbe2ea; border-radius:.75rem; padding:.55rem .8rem; font-size:.75rem; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#0f172a; text-decoration:none; background:#fff; }
    .doc-btn.primary { background:#0E1648; color:#fff; border-color:#0E1648; }
    @media (max-width: 720px) { .doc-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="doc-wrap">
    <div class="mb-3 flex items-center justify-between">
        <a href="javascript:history.back()" class="doc-btn"><i data-lucide="arrow-left" style="width:14px;height:14px;"></i>Retour</a>
        <!-- <button onclick="window.print()" class="doc-btn"><i data-lucide="printer" style="width:14px;height:14px;"></i>Imprimer</button> -->
    </div>

    <div class="doc-sheet">
        <div class="doc-top flex items-start justify-between gap-3">
            <div>
                <p class="doc-label">Document Devis</p>
                <h1 id="doc-title" class="text-xl font-display font-black text-navy-950">Chargement...</h1>
            </div>
            <span id="doc-status" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-100 text-slate-600">-</span>
        </div>

        <div class="doc-body">
            <div class="doc-grid">
                <div><p class="doc-label">Client</p><p id="doc-client" class="doc-val">-</p></div>
                <div><p class="doc-label">Expert</p><p id="doc-provider" class="doc-val">-</p></div>
                <div><p class="doc-label">Montant</p><p id="doc-amount" class="doc-val">-</p></div>
                <div><p class="doc-label">Date</p><p id="doc-date" class="doc-val">-</p></div>
            </div>

            <div class="mt-4">
                <p class="doc-label mb-1">Description</p>
                <p id="doc-description" class="text-sm text-slate-700 leading-relaxed">-</p>
            </div>

            <div class="mt-5">
                <div class="doc-actions">
                    <a id="doc-open" href="#" target="_blank" rel="noopener noreferrer" class="doc-btn primary hidden"><i data-lucide="file-text" style="width:14px;height:14px;"></i>Ouvrir le fichier du devis</a>
                    <a id="doc-download" href="#" target="_blank" rel="noopener noreferrer" class="doc-btn hidden"><i data-lucide="download" style="width:14px;height:14px;"></i>Télécharger</a>
                </div>
                <div id="doc-list" class="mt-2 flex flex-wrap gap-2"></div>
                <div id="doc-viewer" class="doc-viewer hidden"></div>
            </div>
        </div>
    </div>
</div>

<script src="/rappel/public/assets/js/app.js?v=4.1"></script>
<script>
const TOKEN = '<?= addslashes((string)$token) ?>';
const QUOTE_ID = '<?= addslashes($quoteId) ?>';
if (TOKEN) Auth.setToken(TOKEN);

function statusLabel(s) {
    const k = String(s || '').toLowerCase();
    return ({draft:'Brouillon', sent:'Envoy\u00e9', envoye:'Envoy\u00e9', attente_client:'En attente client', accepted:'Accept\u00e9', rejected:'Refus\u00e9', refuse:'Refus\u00e9', signe:'Sign\u00e9', completed:'R\u00e9alis\u00e9', realise:'R\u00e9alis\u00e9'})[k] || (s || 'Inconnu');
}

function statusClass(s) {
    const k = String(s || '').toLowerCase();
    if (k === 'accepted' || k === 'signe') return 'bg-emerald-50 text-emerald-700';
    if (k === 'attente_client' || k === 'draft' || k === 'sent' || k === 'envoye') return 'bg-amber-50 text-amber-700';
    if (k === 'completed' || k === 'realise') return 'bg-emerald-50 text-emerald-700';
    if (k === 'rejected' || k === 'refuse') return 'bg-red-50 text-red-700';
    return 'bg-slate-100 text-slate-600';
}

function fmtAmount(v) {
    const n = parseFloat(v || 0);
    return `${n.toLocaleString('fr-FR')} EUR`;
}

function getQuoteDocs(q) {
    if (Array.isArray(q?.docs) && q.docs.length) return q.docs.filter(Boolean);
    return q?.doc_path ? [q.doc_path] : [];
}

function setDocViewer(path) {
    const viewer = document.getElementById('doc-viewer');
    if (!path) {
        viewer.classList.add('hidden');
        viewer.innerHTML = '';
        return;
    }
    const href = encodeURI(String(path));
    const ext = href.split('.').pop().toLowerCase().split('?')[0];
    if (ext === 'pdf') {
        viewer.innerHTML = `<iframe src="${href}" style="width:100%;height:70vh;border:0;"></iframe>`;
        viewer.classList.remove('hidden');
        return;
    }
    if (['png','jpg','jpeg','webp','gif'].includes(ext)) {
        viewer.innerHTML = `<img src="${href}" alt="Document devis" style="width:100%;height:auto;display:block;">`;
        viewer.classList.remove('hidden');
        return;
    }
    viewer.classList.add('hidden');
    viewer.innerHTML = '';
}

async function loadQuoteDoc() {
    if (!QUOTE_ID) {
        document.getElementById('doc-title').textContent = 'Devis introuvable';
        return;
    }
    try {
        const q = await apiFetch(`/quotes/${encodeURIComponent(QUOTE_ID)}`);
        document.getElementById('doc-title').textContent = escapeHtml(q.project_name || q.need || `Devis #${q.id}`);
        document.getElementById('doc-client').textContent = q.client_name || '-';
        document.getElementById('doc-provider').textContent = q.provider_company || q.provider_name || '-';
        document.getElementById('doc-amount').textContent = fmtAmount(q.amount);
        document.getElementById('doc-date').textContent = new Date(q.created_at || Date.now()).toLocaleDateString('fr-FR');
        document.getElementById('doc-description').textContent = q.description || q.project_name || q.need || '-';

        const st = document.getElementById('doc-status');
        st.textContent = statusLabel(q.status);
        st.className = `px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${statusClass(q.status)}`;

        const openBtn = document.getElementById('doc-open');
        const dlBtn = document.getElementById('doc-download');
        const docList = document.getElementById('doc-list');
        const docs = getQuoteDocs(q);
        if (docs.length) {
            const href = encodeURI(String(docs[0]));
            openBtn.href = href;
            dlBtn.href = href;
            openBtn.classList.remove('hidden');
            dlBtn.classList.remove('hidden');
            setDocViewer(href);
            docList.innerHTML = docs.map((doc, index) => `<a href="${encodeURI(String(doc))}" target="_blank" rel="noopener noreferrer" class="doc-btn">
                <i data-lucide="paperclip" style="width:14px;height:14px;"></i>Document ${index + 1}
            </a>`).join('');
        } else {
            docList.innerHTML = '';
        }
    } catch (e) {
        document.getElementById('doc-title').textContent = e.message || 'Erreur de chargement du devis';
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', loadQuoteDoc);
</script>
</body>
</html>
