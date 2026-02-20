<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Vérification Email';
if (!isLoggedIn()) {
    header('Location: /rappel/public/pro/login.php');
    exit;
}
if (isVerified()) {
    header('Location: /rappel/public/pro/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
</head>
<body class="bg-[#D3D3D3] font-sans antialiased overflow-x-hidden min-h-screen">

<div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
    <div class="absolute rounded-full blur-[120px] opacity-20" style="top:-10%;left:-10%;width:50%;height:60%;background:rgba(124,203,99,0.3);animation:float 20s ease-in-out infinite;"></div>
    <div class="absolute rounded-full blur-[120px] opacity-20" style="bottom:-10%;right:-10%;width:60%;height:70%;background:rgba(14,22,72,0.3);animation:float 25s ease-in-out infinite 2s;"></div>
</div>

<div class="min-h-screen bg-transparent flex items-center justify-center p-6 font-sans relative overflow-hidden">
    <a href="/rappel/public/pro/signup.php" class="absolute top-8 left-8 flex items-center gap-2 text-navy-500 hover:text-navy-900 font-bold transition-all z-20 group">
        <i data-lucide="arrow-left" class="group-hover:-translate-x-1 transition-transform" style="width:20px;height:20px;"></i>
        Retour
    </a>
    <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_50%_0%,_rgba(124,203,99,0.08),transparent_50%)]"></div>

    <div class="w-full max-w-lg z-10 animate-fade-in-up">
        <div class="card p-10 text-center space-y-8 border-white/60 bg-white/40 backdrop-blur-3xl shadow-premium rounded-[2.5rem]">
            <a href="/rappel/public/" class="flex justify-center">
                <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="h-12 w-auto object-contain">
            </a>
            <div class="w-20 h-20 bg-navy-950 rounded-3xl flex items-center justify-center mx-auto text-white shadow-glow">
                <i data-lucide="mail" style="width:36px;height:36px;"></i>
            </div>
            <div class="space-y-3">
                <h1 class="text-3xl font-display font-bold text-navy-950 tracking-tight">Activez votre compte</h1>
                <p class="text-navy-500 font-medium px-4">
                    Un code de vérification unique a été envoyé à votre adresse email professionnelle.
                </p>
            </div>

            <div class="space-y-6 pt-4">
                <div class="relative">
                    <input type="text" id="verify-code"
                           class="form-input text-center text-3xl tracking-[0.5em] font-mono h-16 rounded-2xl border-navy-100 bg-white/50"
                           placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                           oninput="this.value=this.value.replace(/\D/g,'').substring(0,6); checkCodeLength()">
                </div>

                <div id="verify-error" class="form-error hidden justify-center">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 flex-shrink-0"></span>
                    <span id="verify-error-text"></span>
                </div>

                <button onclick="handleVerify()" id="verify-btn"
                        class="btn btn-primary btn-lg w-full h-14 rounded-2xl shadow-premium text-lg font-bold"
                        disabled style="background:linear-gradient(135deg,#2e3d59,#0E1648);">
                    Vérifier mon identité
                </button>
            </div>

            <p class="text-sm font-medium text-navy-400">
                Problème de réception ?
                <button onclick="handleResend()" id="resend-btn"
                        class="text-accent-600 font-bold hover:text-accent-700 transition-colors ml-1">
                    Renvoyer le code
                </button>
            </p>
        </div>
    </div>
</div>

<script src="/rappel/public/assets/js/app.js"></script>
<script>
function checkCodeLength() {
    const code = document.getElementById('verify-code').value;
    const btn = document.getElementById('verify-btn');
    btn.disabled = code.length !== 6;
    btn.style.opacity = code.length === 6 ? '1' : '0.6';
}

async function handleVerify() {
    const code = document.getElementById('verify-code').value;
    const btn = document.getElementById('verify-btn');
    const errEl = document.getElementById('verify-error');
    const errText = document.getElementById('verify-error-text');
    errEl.classList.add('hidden');

    if (code.length !== 6) return;
    setButtonLoading(btn, true);

    try {
        // Get stored email
        const userEmail = sessionStorage.getItem('rappel_user_email') || '';
        const data = await apiFetch('/auth/verify-email', {
            method: 'POST',
            body: JSON.stringify({ email: userEmail, code })
        });

        // Update session with verified user
        const token = data.session?.access_token || Auth.getToken();
        const user = { ...(Auth.getUser() || {}), is_verified: true };
        await fetch('/rappel/public/api-session.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'set', token, user })
        });
        Auth.setToken(token);
        Auth.setUser(user);
        sessionStorage.removeItem('rappel_user_email');

        window.location.href = '/rappel/public/pro/dashboard.php';
    } catch (err) {
        errText.textContent = err.message || 'Code invalide. Veuillez réessayer.';
        errEl.classList.remove('hidden');
        setButtonLoading(btn, false, 'Vérifier mon identité');
    }
}

async function handleResend() {
    const btn = document.getElementById('resend-btn');
    const userEmail = sessionStorage.getItem('rappel_user_email') || '';
    if (!userEmail) {
        alert('Email non trouvé. Veuillez vous réinscrire.');
        return;
    }
    btn.disabled = true;
    btn.textContent = 'Envoi...';
    try {
        await apiFetch('/auth/resend-activation', {
            method: 'POST',
            body: JSON.stringify({ email: userEmail })
        });
        showToast('Un nouveau code a été envoyé à votre email!', 'success');
    } catch (err) {
        showToast(err.message || 'Erreur lors du renvoi.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Renvoyer le code';
    }
}

document.addEventListener('DOMContentLoaded', () => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
</script>
</body>
</html>
