<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Paramètres';
$user = getCurrentUser();
$token = getToken();
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<div class="mb-8">
    <h1 class="text-3xl font-display font-bold text-navy-950">Paramètres</h1>
    <p class="text-navy-500 font-medium mt-1">Gérez votre profil et vos préférences</p>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- Profile Card -->
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-8">
            <h2 class="text-xl font-display font-bold text-navy-950 mb-6">Informations du profil</h2>
            <form onsubmit="handleUpdateProfile(event)" class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Prénom</label>
                        <input type="text" id="s-firstname" class="form-input"
                               value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" placeholder="Jean">
                    </div>
                    <div>
                        <label class="form-label">Nom</label>
                        <input type="text" id="s-lastname" class="form-input"
                               value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" placeholder="Dupont">
                    </div>
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" id="s-email" class="form-input"
                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="jean@exemple.com">
                </div>
                <div>
                    <label class="form-label">Téléphone</label>
                    <input type="tel" id="s-phone" class="form-input"
                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="06 12 34 56 78">
                </div>
                <div>
                    <label class="form-label">Entreprise</label>
                    <input type="text" id="s-company" class="form-input"
                           value="<?= htmlspecialchars($user['company_name'] ?? '') ?>" placeholder="Dupont & Associés">
                </div>
                <div>
                    <label class="form-label">SIRET</label>
                    <input type="text" id="s-siret" class="form-input"
                           value="<?= htmlspecialchars($user['siret'] ?? '') ?>" placeholder="12345678901234" maxlength="14">
                </div>
                <div id="profile-msg" class="hidden"></div>
                <button type="submit" id="save-profile-btn" class="btn btn-primary rounded-xl px-8 shadow-premium">
                    Enregistrer les modifications
                </button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="card p-8">
            <h2 class="text-xl font-display font-bold text-navy-950 mb-6">Changer le mot de passe</h2>
            <form onsubmit="handleChangePassword(event)" class="space-y-5">
                <div>
                    <label class="form-label">Mot de passe actuel</label>
                    <input type="password" id="s-current-pwd" class="form-input" placeholder="••••••••">
                </div>
                <div>
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" id="s-new-pwd" class="form-input" placeholder="Minimum 8 caractères">
                </div>
                <div>
                    <label class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="s-confirm-pwd" class="form-input" placeholder="••••••••">
                </div>
                <div id="pwd-msg" class="hidden"></div>
                <button type="submit" id="change-pwd-btn" class="btn btn-outline rounded-xl px-8">
                    Mettre à jour le mot de passe
                </button>
            </form>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-6">
        <!-- Account Status -->
        <div class="card p-6">
            <h3 class="font-bold text-navy-950 mb-4">Statut du compte</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-navy-500">Email vérifié</span>
                    <span class="badge <?= ($user['is_verified'] ?? false) ? 'bg-accent-50 text-accent-700' : 'bg-amber-50 text-amber-700' ?>">
                        <?= ($user['is_verified'] ?? false) ? 'Oui' : 'Non' ?>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-navy-500">Abonnement</span>
                    <span class="badge <?= hasSubscription() ? 'bg-accent-50 text-accent-700' : 'bg-navy-50 text-navy-600' ?>">
                        <?= hasSubscription() ? 'Actif' : 'Inactif' ?>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-navy-500">Rôle</span>
                    <span class="badge bg-brand-50 text-brand-700 capitalize"><?= htmlspecialchars($user['role'] ?? 'provider') ?></span>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card p-6 border-red-100">
            <h3 class="font-bold text-red-700 mb-4">Zone de danger</h3>
            <p class="text-sm text-navy-500 font-medium mb-4">La déconnexion mettra fin à votre session active.</p>
            <a href="/rappel/public/logout.php"
               class="btn btn-outline w-full rounded-xl border-red-200 text-red-600 hover:bg-red-50 text-center">
                <i data-lucide="log-out" style="width:16px;height:16px;"></i>
                Se déconnecter
            </a>
        </div>
    </div>
</div>

<?php
$safeToken = addslashes($token ?? '');
$extraScript = <<<'JS'
<script>
const PHP_TOKEN = 'RAPPEL_PHP_TOKEN_PLACEHOLDER';
if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);

async function handleUpdateProfile(e) {
    e.preventDefault();
    const btn = document.getElementById('save-profile-btn');
    const msg = document.getElementById('profile-msg');
    msg.className = 'hidden';
    setButtonLoading(btn, true);
    try {
        const res = await apiFetch('/profile', { method: 'PATCH', body: JSON.stringify({
            first_name: document.getElementById('s-firstname').value,
            last_name: document.getElementById('s-lastname').value,
            email: document.getElementById('s-email').value,
            phone: document.getElementById('s-phone').value,
            company_name: document.getElementById('s-company').value,
            siret: document.getElementById('s-siret').value,
        })});

        const updatedUser = res.user || {};
        if (Object.keys(updatedUser).length) {
            Auth.setUser(updatedUser);
            await fetch('/rappel/public/api-session.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'set', token: Auth.getToken() || PHP_TOKEN, user: updatedUser })
            });
        }
        msg.className = 'form-success';
        msg.textContent = 'Profil mis à jour avec succès!';
        showToast('Profil mis à jour!', 'success');
    } catch (err) {
        msg.className = 'form-error';
        msg.textContent = err.message;
    } finally {
        setButtonLoading(btn, false, 'Enregistrer les modifications');
    }
}

async function handleChangePassword(e) {
    e.preventDefault();
    const btn = document.getElementById('change-pwd-btn');
    const msg = document.getElementById('pwd-msg');
    const newPwd = document.getElementById('s-new-pwd').value;
    const confirmPwd = document.getElementById('s-confirm-pwd').value;
    msg.className = 'hidden';
    if (newPwd !== confirmPwd) {
        msg.className = 'form-error';
        msg.textContent = 'Les mots de passe ne correspondent pas.';
        return;
    }
    if (newPwd.length < 8) {
        msg.className = 'form-error';
        msg.textContent = 'Le mot de passe doit contenir au moins 8 caractères.';
        return;
    }
    setButtonLoading(btn, true);
    try {
        await apiFetch('/auth/change-password', { method: 'POST', body: JSON.stringify({
            current_password: document.getElementById('s-current-pwd').value,
            new_password: newPwd,
        })});
        msg.className = 'form-success';
        msg.textContent = 'Mot de passe mis à jour avec succès!';
        document.getElementById('s-current-pwd').value = '';
        document.getElementById('s-new-pwd').value = '';
        document.getElementById('s-confirm-pwd').value = '';
    } catch (err) {
        msg.className = 'form-error';
        msg.textContent = err.message;
    } finally {
        setButtonLoading(btn, false, 'Mettre à jour le mot de passe');
    }
}
</script>
JS;
$extraScript = str_replace('RAPPEL_PHP_TOKEN_PLACEHOLDER', $safeToken, $extraScript);
?>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>
