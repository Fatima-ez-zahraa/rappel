<?php
require_once __DIR__ . '/../includes/auth.php';
if (isLoggedIn() && isVerified()) {
    if (isAdmin()) {
        header('Location: /rappel/public/admin/dashboard.php');
        exit;
    }
    header('Location: /rappel/public/pro/dashboard.php');
    exit;
}
$pageTitle = 'Connexion Expert';
$redirect = $_GET['redirect'] ?? '/rappel/public/pro/dashboard.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
</head>
<body class="bg-[#D3D3D3] font-sans antialiased overflow-x-hidden min-h-screen">

<!-- Background Blobs -->
<div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
    <div class="absolute rounded-full blur-[120px] opacity-20" style="top:-10%;left:-10%;width:50%;height:60%;background:rgba(124,203,99,0.3);animation:float 20s ease-in-out infinite;"></div>
    <div class="absolute rounded-full blur-[120px] opacity-20" style="bottom:-10%;right:-10%;width:60%;height:70%;background:rgba(14,22,72,0.3);animation:float 25s ease-in-out infinite 2s;"></div>
</div>

<div class="min-h-screen bg-transparent flex items-center justify-center p-6 font-sans relative overflow-hidden">
    <!-- Back button -->
    <a href="/rappel/public/pro/" class="absolute top-8 left-8 flex items-center gap-2 text-navy-500 hover:text-navy-900 font-bold transition-all z-20 group">
        <i data-lucide="arrow-left" class="group-hover:-translate-x-1 transition-transform" style="width:20px;height:20px;"></i>
        Retour
    </a>

    <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_50%_0%,_rgba(124,203,99,0.08),transparent_50%)]"></div>

    <div class="w-full max-w-lg z-10 animate-fade-in-up">
        <div class="card p-10 space-y-10 border-white/60 bg-white/40 backdrop-blur-3xl shadow-premium rounded-[2.5rem]">
            <div class="text-center space-y-6">
                <a href="/rappel/public/" class="flex justify-center">
                    <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="h-12 w-auto object-contain">
                </a>
                <div class="space-y-2">
                    <h1 class="text-4xl font-display font-bold text-navy-950 tracking-tight">Accès Expert</h1>
                    <p class="text-navy-500 font-medium">Gérez vos opportunités et votre croissance.</p>
                </div>
            </div>

            <form id="login-form" class="space-y-8" onsubmit="handleLogin(event)">
                <div class="space-y-5">
                    <div>
                        <label class="form-label">Identifiant Professionnel</label>
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-navy-400">
                                <i data-lucide="mail" style="width:20px;height:20px;"></i>
                            </div>
                            <input type="email" id="login-email" class="form-input pl-12 rounded-2xl" placeholder="nom@expert.com" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Clé de Sécurité</label>
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-navy-400">
                                <i data-lucide="lock" style="width:20px;height:20px;"></i>
                            </div>
                            <input type="password" id="login-password" class="form-input pl-12 rounded-2xl" placeholder="••••••••" required>
                        </div>
                        <div class="flex justify-end pt-2">
                            <a href="/rappel/public/forgot-password.php" class="text-xs font-bold text-navy-400 hover:text-accent-600 transition-colors">
                                Mot de passe oublié ?
                            </a>
                        </div>
                    </div>
                </div>

                <div id="login-error" class="form-error hidden">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 flex-shrink-0"></span>
                    <span id="login-error-text"></span>
                </div>

                <button type="submit" id="login-btn"
                        class="btn btn-primary btn-lg w-full h-14 rounded-2xl shadow-premium text-lg font-bold">
                    Identifier la session
                </button>
            </form>

            <div class="text-center pt-8 border-t border-navy-100/50">
                <p class="text-sm font-medium text-navy-400">
                    Nouveau partenaire ?
                    <a href="/rappel/public/pro/signup.php" class="text-accent-600 font-bold hover:text-accent-700 transition-colors ml-1">
                        Créer un profil expert
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="/rappel/public/assets/js/app.js?v=3.1"></script>
<script>
const REDIRECT_URL = '<?= htmlspecialchars($redirect) ?>';

async function handleLogin(e) {
    e.preventDefault();
    const btn = document.getElementById('login-btn');
    const errEl = document.getElementById('login-error');
    const errText = document.getElementById('login-error-text');
    errEl.classList.add('hidden');
    setButtonLoading(btn, true);

    try {
        const data = await apiFetch('/auth/login', {
            method: 'POST',
            body: JSON.stringify({
                email: document.getElementById('login-email').value,
                password: document.getElementById('login-password').value,
            })
        });

        const token = data.session?.access_token || data.token || '';
        const user = data.user || {};

        // Store in PHP session
        await fetch('/rappel/public/api-session.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'set', token, user })
        });

        // Also store in sessionStorage for JS calls
        Auth.setToken(token);
        Auth.setUser(user);

        // Redirect based on role
        if (user.role === 'admin') {
            window.location.href = '/rappel/public/admin/dashboard.php';
        } else {
            window.location.href = REDIRECT_URL;
        }

    } catch (err) {
        errText.textContent = err.message || 'Identifiants invalides.';
        errEl.classList.remove('hidden');
        setButtonLoading(btn, false, 'Identifier la session');
    }
}

document.addEventListener('DOMContentLoaded', () => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
</script>
</body>
</html>
