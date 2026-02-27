<?php
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) {
    header('Location: /rappel/public/index.php');
    exit;
}
$pageTitle = 'Mot de passe oublié';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body class="bg-[#F8FAFC] font-sans antialiased overflow-x-hidden min-h-screen">

<div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
    <div class="absolute rounded-full blur-[120px] opacity-20" style="top:-10%;left:-10%;width:50%;height:60%;background:rgba(59,130,246,0.15);animation:float 20s ease-in-out infinite;"></div>
</div>

<div class="min-h-screen bg-transparent flex items-center justify-center p-6 font-sans relative overflow-hidden">
    <a href="javascript:history.back()" class="absolute top-8 left-8 flex items-center gap-2 text-navy-500 hover:text-navy-900 font-bold transition-all z-20 group">
        <i data-lucide="arrow-left" class="group-hover:-translate-x-1 transition-transform" style="width:20px;height:20px;"></i>
        Retour
    </a>

    <div class="w-full max-w-lg z-10 animate-fade-in-up">
        <div class="card p-10 space-y-10 border-white/60 bg-white/40 backdrop-blur-3xl shadow-premium rounded-[2.5rem]">
            <div class="text-center space-y-6">
                <a href="/rappel/public/" class="flex justify-center">
                    <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="h-12 w-auto object-contain">
                </a>
                <div class="space-y-2">
                    <h1 class="text-3xl font-display font-bold text-navy-950 tracking-tight text-center">Mot de passe oublié ?</h1>
                    <p class="text-navy-500 font-medium">Saisissez votre adresse email pour recevoir un lien de réinitialisation.</p>
                </div>
            </div>

            <form id="forgot-form" class="space-y-8" onsubmit="handleForgot(event)">
                <div>
                    <label class="form-label">Votre adresse email</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-navy-400">
                            <i data-lucide="mail" style="width:20px;height:20px;"></i>
                        </div>
                        <input type="email" id="forgot-email" class="form-input pl-12 rounded-2xl" placeholder="jean.dupont@exemple.com" required>
                    </div>
                </div>

                <div id="forgot-message" class="hidden p-4 rounded-2xl text-sm font-bold"></div>

                <button type="submit" id="forgot-btn" class="btn btn-primary btn-lg w-full h-14 rounded-2xl shadow-premium text-lg font-bold">
                    Envoyer le lien
                </button>
            </form>
        </div>
    </div>
</div>

<script src="/rappel/public/assets/js/app.js?v=3.1"></script>
<script>
async function handleForgot(e) {
    e.preventDefault();
    const btn = document.getElementById('forgot-btn');
    const msgEl = document.getElementById('forgot-message');
    const email = document.getElementById('forgot-email').value;
    
    setButtonLoading(btn, true);
    msgEl.classList.add('hidden');

    try {
        const res = await apiFetch('/auth/forgot-password', {
            method: 'POST',
            body: JSON.stringify({ email })
        });
        
        msgEl.textContent = res.message;
        msgEl.className = 'p-4 rounded-2xl text-sm font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 block';
        setButtonLoading(btn, false, 'Email envoyé');
        btn.disabled = true;
    } catch (err) {
        msgEl.textContent = err.message;
        msgEl.className = 'p-4 rounded-2xl text-sm font-bold bg-red-50 text-red-600 border border-red-100 block';
        setButtonLoading(btn, false, 'Réessayer');
    }
}
document.addEventListener('DOMContentLoaded', () => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
</script>
</body>
</html>
