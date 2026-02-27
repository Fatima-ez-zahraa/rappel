<?php
require_once __DIR__ . '/includes/auth.php';
$token = $_GET['token'] ?? '';
if (isLoggedIn() || empty($token)) {
    header('Location: /rappel/public/index.php');
    exit;
}
$pageTitle = 'Réinitialisation du mot de passe';
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
    <div class="w-full max-w-lg z-10 animate-fade-in-up">
        <div class="card p-10 space-y-10 border-white/60 bg-white/40 backdrop-blur-3xl shadow-premium rounded-[2.5rem]">
            <div class="text-center space-y-6">
                <a href="/rappel/public/" class="flex justify-center">
                    <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="h-12 w-auto object-contain">
                </a>
                <div class="space-y-2">
                    <h1 class="text-3xl font-display font-bold text-navy-950 tracking-tight">Nouveau mot de passe</h1>
                    <p class="text-navy-500 font-medium">Choisissez un nouveau mot de passe sécurisé pour votre compte.</p>
                </div>
            </div>

            <form id="reset-form" class="space-y-8" onsubmit="handleReset(event)">
                <input type="hidden" id="reset-token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="space-y-5">
                    <div>
                        <label class="form-label">Nouveau mot de passe</label>
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-navy-400">
                                <i data-lucide="lock" style="width:20px;height:20px;"></i>
                            </div>
                            <input type="password" id="reset-password" class="form-input pl-12 rounded-2xl" placeholder="••••••••" required minlength="8">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Confirmez le mot de passe</label>
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-navy-400">
                                <i data-lucide="lock" style="width:20px;height:20px;"></i>
                            </div>
                            <input type="password" id="confirm-password" class="form-input pl-12 rounded-2xl" placeholder="••••••••" required minlength="8">
                        </div>
                    </div>
                </div>

                <div id="reset-message" class="hidden p-4 rounded-2xl text-sm font-bold"></div>

                <button type="submit" id="reset-btn" class="btn btn-primary btn-lg w-full h-14 rounded-2xl shadow-premium text-lg font-bold">
                    Changer mon mot de passe
                </button>
            </form>
        </div>
    </div>
</div>

<script src="/rappel/public/assets/js/app.js?v=3.1"></script>
<script>
async function handleReset(e) {
    e.preventDefault();
    const btn = document.getElementById('reset-btn');
    const msgEl = document.getElementById('reset-message');
    const password = document.getElementById('reset-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const token = document.getElementById('reset-token').value;
    
    if (password !== confirmPassword) {
        msgEl.textContent = 'Les mots de passe ne correspondent pas.';
        msgEl.className = 'p-4 rounded-2xl text-sm font-bold bg-red-50 text-red-600 border border-red-100 block';
        msgEl.classList.remove('hidden');
        return;
    }

    setButtonLoading(btn, true);
    msgEl.classList.add('hidden');

    try {
        const res = await apiFetch('/auth/reset-password', {
            method: 'POST',
            body: JSON.stringify({ token, password })
        });
        
        msgEl.textContent = res.message + " Redirection...";
        msgEl.className = 'p-4 rounded-2xl text-sm font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 block';
        setButtonLoading(btn, false, 'Succès');
        
        setTimeout(() => {
            window.location.href = '/rappel/public/pro/login.php';
        }, 2000);
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
