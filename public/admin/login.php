<?php
require_once __DIR__ . '/../includes/auth.php';
if (isLoggedIn() && isAdmin()) {
    header('Location: /rappel/public/admin/dashboard.php');
    exit;
}
$pageTitle = 'Admin - Connexion';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
    <style>
        body.admin-login-dark {
            background: linear-gradient(145deg, #060d1f 0%, #0a1230 35%, #0e1648 65%, #081028 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }
        .admin-login-orb-1 {
            position: fixed;
            top: -15%;
            left: -10%;
            width: 55%;
            height: 65%;
            background: radial-gradient(circle, rgba(35, 75, 174, 0.14) 0%, rgba(14, 22, 72, 0.05) 55%, transparent 70%);
            pointer-events: none;
            z-index: 0;
            animation: float 20s ease-in-out infinite;
        }
        .admin-login-orb-2 {
            position: fixed;
            bottom: -20%;
            right: -15%;
            width: 60%;
            height: 65%;
            background: radial-gradient(circle, rgba(124, 203, 99, 0.07) 0%, transparent 65%);
            pointer-events: none;
            z-index: 0;
            animation: float 26s ease-in-out infinite 4s;
        }
        .grid-lines {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background-image:
                linear-gradient(rgba(35, 75, 174, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(35, 75, 174, 0.03) 1px, transparent 1px);
            background-size: 64px 64px;
        }
        .admin-login-card {
            background: rgba(8, 14, 40, 0.88);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(35, 75, 174, 0.22);
            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.6),
                0 0 0 1px rgba(35, 75, 174, 0.06) inset,
                0 0 60px rgba(14, 22, 72, 0.3);
            border-radius: 2rem;
        }
        .admin-divider {
            background: linear-gradient(90deg, transparent, rgba(35, 75, 174, 0.3), rgba(124, 203, 99, 0.15), transparent);
            height: 1px;
            border: none;
        }
        .admin-login-input {
            background: rgba(6, 10, 28, 0.7) !important;
            border: 1px solid rgba(35, 75, 174, 0.3) !important;
            color: #f1f5f9 !important;
        }
        .admin-login-input::placeholder {
            color: #3d5277 !important;
        }
        .admin-login-input:focus {
            border-color: #7CCB63 !important;
            box-shadow: 0 0 0 3px rgba(124, 203, 99, 0.18) !important;
            outline: none;
        }
        .input-icon {
            color: #234bae;
        }
        .admin-login-btn {
            background: linear-gradient(135deg, #234bae, #0E1648);
            box-shadow: 0 4px 25px rgba(35, 75, 174, 0.5);
            transition: all 0.25s ease;
            color: #fff;
            font-weight: 700;
            border: 1px solid rgba(101, 129, 199, 0.25);
            cursor: pointer;
            width: 100%;
            height: 3.5rem;
            border-radius: 1rem;
            font-size: 1rem;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .admin-login-btn:hover {
            background: linear-gradient(135deg, #2d5cc4, #1e4093);
            box-shadow: 0 6px 35px rgba(35, 75, 174, 0.65);
            transform: translateY(-2px);
        }
        .admin-login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .admin-label {
            color: #7786a0;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.375rem;
            display: block;
            margin-left: 0.25rem;
        }
        .admin-error-box {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            background: rgba(127, 29, 29, 0.25);
            border: 1px solid rgba(220, 38, 38, 0.25);
            border-radius: 0.875rem;
            color: #fca5a5;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .admin-back-link {
            color: #3d5277;
            transition: color 0.2s;
        }
        .admin-back-link:hover {
            color: #7CCB63;
        }
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.35rem 0.95rem;
            border-radius: 9999px;
            background: rgba(35, 75, 174, 0.2);
            border: 1px solid rgba(101, 129, 199, 0.3);
            box-shadow: 0 6px 20px rgba(14, 22, 72, 0.28);
        }
        .admin-logo-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 1.8rem;
            border-radius: 1.25rem;
            background: linear-gradient(180deg, rgba(35, 75, 174, 0.18), rgba(14, 22, 72, 0.1));
            border: 1px solid rgba(101, 129, 199, 0.32);
            box-shadow: 0 12px 34px rgba(14, 22, 72, 0.5), inset 0 1px 0 rgba(147, 197, 253, 0.14);
        }
        .admin-logo-img {
            height: clamp(96px, 11vw, 138px);
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 16px 34px rgba(14, 22, 72, 0.62));
        }
        .admin-login-title {
            font-size: clamp(2rem, 3.6vw, 2.45rem);
            color: #f8fafc;
            text-shadow: 0 8px 22px rgba(14, 22, 72, 0.5);
        }
        .admin-login-subtitle {
            color: #7f91b1;
            font-size: 0.98rem;
            margin-top: 0.5rem;
        }
        .input-action-btn {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.75rem;
            border: 1px solid rgba(35, 75, 174, 0.28);
            background: rgba(10, 16, 48, 0.65);
            color: #93c5fd;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .input-action-btn:hover {
            background: rgba(35, 75, 174, 0.25);
            color: #dbeafe;
        }
        .input-hint {
            margin-top: 0.45rem;
            margin-left: 0.25rem;
            color: #5b6e91;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .caps-lock-warning {
            color: #fca5a5;
            display: none;
        }
        .caps-lock-warning.show {
            display: block;
        }
    </style>
</head>
<body class="admin-login-dark font-sans antialiased overflow-x-hidden">

<div class="admin-login-orb-1"></div>
<div class="admin-login-orb-2"></div>
<div class="grid-lines"></div>

<div class="min-h-screen flex items-center justify-center p-6 relative z-10">
    <a href="/rappel/public/" class="admin-back-link absolute top-8 left-8 flex items-center gap-2 font-bold z-20 group text-sm">
        <i data-lucide="arrow-left" class="group-hover:-translate-x-1 transition-transform" style="width:18px;height:18px;"></i>
        Retour au site
    </a>

    <div class="w-full max-w-lg animate-fade-in-up">
        <div class="admin-login-card p-10 space-y-8">
            <div class="text-center space-y-5">
                <a href="/rappel/public/" class="flex justify-center mb-2">
                    <span class="admin-logo-wrap">
                        <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="admin-logo-img">
                    </span>
                </a>

                <div>
                    <h1 class="font-display font-bold tracking-tight admin-login-title">Administration</h1>
                    <p class="admin-login-subtitle">Reserve aux administrateurs autorises.</p>
                </div>
            </div>

            <hr class="admin-divider">

            <form id="admin-login-form" class="space-y-5" onsubmit="handleAdminLogin(event)">
                <div>
                    <label class="admin-label" for="admin-email">Email administrateur</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 input-icon">
                            <i data-lucide="mail" style="width:18px;height:18px;"></i>
                        </div>
                        <input type="email" id="admin-email" class="form-input admin-login-input pl-12 rounded-2xl" placeholder="admin@rappel.fr" autocomplete="username" autofocus required>
                    </div>
                    <p class="input-hint">Utilisez l'email administrateur.</p>
                </div>

                <div>
                    <label class="admin-label" for="admin-password">Mot de passe</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 input-icon">
                            <i data-lucide="lock" style="width:18px;height:18px;"></i>
                        </div>
                        <input type="password" id="admin-password" class="form-input admin-login-input pl-12 pr-14 rounded-2xl" placeholder="********" autocomplete="current-password" required>
                        <button type="button" id="toggle-password" class="input-action-btn" aria-label="Afficher le mot de passe">
                            <i data-lucide="eye" id="toggle-password-icon" style="width:16px;height:16px;"></i>
                        </button>
                    </div>
                    <p id="caps-lock-hint" class="input-hint caps-lock-warning">Verr. Maj active.</p>
                </div>

                <div id="admin-error" class="admin-error-box hidden" aria-live="polite">
                    <i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0;"></i>
                    <span id="admin-error-text"></span>
                </div>

                <button type="submit" id="admin-login-btn" class="admin-login-btn mt-2">
                    <i data-lucide="log-in" style="width:18px;height:18px;"></i>
                    Acceder au panneau
                </button>
            </form>

            <p class="text-center" style="color:#253147;font-size:0.75rem;">
                Connexion securisee - Rappelez-moi &copy; <?= date('Y') ?>
            </p>
        </div>
    </div>
</div>

<script src="/rappel/public/assets/js/app.js"></script>
<script>
async function handleAdminLogin(e) {
    e.preventDefault();
    const btn = document.getElementById('admin-login-btn');
    const errEl = document.getElementById('admin-error');
    const errText = document.getElementById('admin-error-text');
    errEl.classList.add('hidden');

    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Connexion...';

    try {
        const data = await apiFetch('/auth/login', {
            method: 'POST',
            body: JSON.stringify({
                email: document.getElementById('admin-email').value,
                password: document.getElementById('admin-password').value,
            })
        });

        const token = data.session?.access_token || data.token || '';
        const user = data.user || {};

        if (user.role !== 'admin') {
            throw new Error('Acces non autorise. Compte administrateur requis.');
        }

        await fetch('/rappel/public/api-session.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'set', token, user })
        });

        Auth.setToken(token);
        Auth.setUser(user);
        window.location.href = '/rappel/public/admin/dashboard.php';
    } catch (err) {
        errText.textContent = err.message || 'Identifiants invalides.';
        errEl.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = originalContent;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

function initAdminLoginUi() {
    const passwordInput = document.getElementById('admin-password');
    const toggleBtn = document.getElementById('toggle-password');
    const capsHint = document.getElementById('caps-lock-hint');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', () => {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            const icon = document.getElementById('toggle-password-icon');
            if (icon) {
                icon.setAttribute('data-lucide', isHidden ? 'eye-off' : 'eye');
            }
            toggleBtn.setAttribute('aria-label', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });

        passwordInput.addEventListener('keyup', (event) => {
            if (!capsHint) return;
            const on = event.getModifierState && event.getModifierState('CapsLock');
            capsHint.classList.toggle('show', !!on);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initAdminLoginUi();
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
</body>
</html>

