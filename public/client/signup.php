<?php
require_once __DIR__ . '/../includes/auth.php';
if (isLoggedIn() && isVerified()) {
    header('Location: /rappel/public/client/dashboard.php');
    exit;
}
$pageTitle = 'Espace Particulier - Inscription';
$email = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
</head>
<body class="bg-[#F8FAFC] font-sans antialiased overflow-x-hidden min-h-screen">

<!-- Background Blobs -->
<div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
    <div class="absolute rounded-full blur-[120px] opacity-20" style="top:-10%;left:-10%;width:50%;height:60%;background:rgba(59,130,246,0.15);animation:float 20s ease-in-out infinite;"></div>
    <div class="absolute rounded-full blur-[120px] opacity-20" style="bottom:-10%;right:-10%;width:60%;height:70%;background:rgba(124,203,99,0.1);animation:float 25s ease-in-out infinite 2s;"></div>
</div>

<div class="min-h-screen bg-transparent flex items-center justify-center p-6 font-sans relative overflow-hidden py-16">
    <!-- Back button -->
    <a href="/rappel/public/client/login.php" class="absolute top-8 left-8 flex items-center gap-2 text-navy-500 hover:text-navy-900 font-bold transition-all z-20 group">
        <i data-lucide="arrow-left" class="group-hover:-translate-x-1 transition-transform" style="width:20px;height:20px;"></i>
        Retour
    </a>

    <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_50%_0%,_rgba(59,130,246,0.05),transparent_50%)]"></div>

    <div class="w-full max-w-xl z-10 animate-fade-in-up">
        <div class="card p-10 space-y-10 border-white/60 bg-white/40 backdrop-blur-3xl shadow-premium rounded-[2.5rem]">
            <div class="text-center space-y-4">
                <a href="/rappel/public/" class="flex justify-center">
                    <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="h-12 w-auto object-contain">
                </a>
                <h1 class="text-3xl font-display font-bold text-navy-950 tracking-tight">Rejoignez Rappel</h1>
                <p class="text-navy-500 font-medium">Créez votre espace gratuitement pour un suivi optimal.</p>
            </div>

            <div id="signup-error" class="form-error hidden">
                <span class="w-1.5 h-1.5 rounded-full bg-red-600 flex-shrink-0"></span>
                <span id="signup-error-text"></span>
            </div>

            <form id="signup-form" class="grid grid-cols-2 gap-6" onsubmit="handleSignup(event)">
                <div class="col-span-1">
                    <label class="form-label">Prénom</label>
                    <input type="text" id="first_name" class="form-input rounded-2xl" placeholder="Jean" required>
                </div>
                <div class="col-span-1">
                    <label class="form-label">Nom</label>
                    <input type="text" id="last_name" class="form-input rounded-2xl" placeholder="Dupont" required>
                </div>
                <div class="col-span-2">
                    <label class="form-label">Email professionnel ou personnel</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-navy-400 pointer-events-none">
                            <i data-lucide="mail" style="width:20px;height:20px;"></i>
                        </div>
                        <input type="email" id="email" class="form-input pl-12 rounded-2xl" placeholder="jean.dupont@exemple.com" value="<?= htmlspecialchars($email) ?>" required>
                    </div>
                </div>
                <div class="col-span-2">
                    <label class="form-label">Téléphone</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-navy-400 pointer-events-none">
                            <i data-lucide="phone" style="width:20px;height:20px;"></i>
                        </div>
                        <input type="tel" id="phone" class="form-input pl-12 rounded-2xl" placeholder="06 00 00 00 00" required>
                    </div>
                </div>
                <div class="col-span-2">
                    <label class="form-label">Mot de passe</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-navy-400 pointer-events-none">
                            <i data-lucide="lock" style="width:20px;height:20px;"></i>
                        </div>
                        <input type="password" id="password" class="form-input pl-12 rounded-2xl" placeholder="••••••••" minlength="8" required>
                    </div>
                    <p class="text-[10px] text-navy-400 mt-2 font-medium">Minimum 8 caractères pour votre sécurité.</p>
                </div>

                <div class="col-span-2 space-y-4 pt-4">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" id="terms" class="mt-1" required>
                        <label for="terms" class="text-xs text-navy-500 font-medium">
                            J'accepte les <a href="/rappel/public/legal.php#cgu" class="text-brand-600 font-bold hover:underline">CGU</a> et la <a href="/rappel/public/legal.php#confidentialite" class="text-brand-600 font-bold hover:underline">politique de confidentialité</a>. Vos données sont protégées et vous en gardez le contrôle total.
                        </label>
                    </div>
                    
                    <button type="submit" id="signup-btn" class="btn btn-primary btn-lg w-full h-14 rounded-2xl shadow-premium text-lg font-bold mt-2">
                        Créer mon espace gratuit
                    </button>
                </div>
            </form>

            <div class="text-center pt-8 border-t border-navy-100/50">
                <p class="text-sm font-medium text-navy-400">
                    Déjà inscrit ?
                    <a href="/rappel/public/client/login.php" class="text-brand-600 font-bold hover:text-brand-700 transition-colors ml-1">
                        Connectez-vous ici
                    </a>
                </p>
            </div>
        </div>
        
        <!-- Reassurance Section -->
        <div class="mt-12 grid grid-cols-3 gap-6">
            <div class="text-center space-y-2">
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center mx-auto text-brand-600">
                    <i data-lucide="eye" style="width:20px;height:20px;"></i>
                </div>
                <p class="text-[10px] font-black uppercase text-navy-900 tracking-wider">Suivi en temps réel</p>
            </div>
            <div class="text-center space-y-2">
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center mx-auto text-brand-600">
                    <i data-lucide="file-text" style="width:20px;height:20px;"></i>
                </div>
                <p class="text-[10px] font-black uppercase text-navy-900 tracking-wider">Devis centralisés</p>
            </div>
            <div class="text-center space-y-2">
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center mx-auto text-brand-600">
                    <i data-lucide="shield-check" style="width:20px;height:20px;"></i>
                </div>
                <p class="text-[10px] font-black uppercase text-navy-900 tracking-wider">Souveraineté des données</p>
            </div>
        </div>
    </div>
</div>

<script src="/rappel/public/assets/js/app.js?v=3.1"></script>
<script>
async function handleSignup(e) {
    e.preventDefault();
    const btn = document.getElementById('signup-btn');
    const errEl = document.getElementById('signup-error');
    const errText = document.getElementById('signup-error-text');
    errEl.classList.add('hidden');
    setButtonLoading(btn, true);

    const payload = {
        firstName: document.getElementById('first_name').value.trim(),
        first_name: document.getElementById('first_name').value.trim(),
        lastName: document.getElementById('last_name').value.trim(),
        last_name: document.getElementById('last_name').value.trim(),
        email: document.getElementById('email').value.trim(),
        phone: document.getElementById('phone').value.trim(),
        password: document.getElementById('password').value,
        role: 'client'
    };

    try {
        const data = await apiFetch('/auth/signup', {
            method: 'POST',
            body: JSON.stringify(payload)
        });

        const token = data.session?.access_token || data.token || '';
        const user = data.user || {};

        // Store email for verification if needed
        sessionStorage.setItem('rappel_user_email', payload.email);

        // Store in PHP session
        await fetch('/rappel/public/api-session.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'set', token, user })
        });

        Auth.setToken(token);
        Auth.setUser(user);

        // Redirect to verify (client also needs to verify email)
        window.location.href = '/rappel/public/pro/verify.php';

    } catch (err) {
        errText.textContent = err.message || 'Erreur lors de l\'inscription.';
        errEl.classList.remove('hidden');
        setButtonLoading(btn, false, 'Créer mon espace gratuit');
    }
}

document.addEventListener('DOMContentLoaded', () => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
</script>
</body>
</html>
