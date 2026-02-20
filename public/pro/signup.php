<?php
require_once __DIR__ . '/../includes/auth.php';
if (isLoggedIn() && isVerified()) {
    header('Location: /rappel/public/pro/dashboard.php');
    exit;
}
$pageTitle = 'Créer un compte Expert';
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

<div class="min-h-screen bg-transparent flex items-center justify-center p-6 font-sans relative overflow-hidden py-16">
    <a href="/rappel/public/pro/" class="absolute top-8 left-8 flex items-center gap-2 text-navy-500 hover:text-navy-900 font-bold transition-all z-20 group">
        <i data-lucide="arrow-left" class="group-hover:-translate-x-1 transition-transform" style="width:20px;height:20px;"></i>
        Retour
    </a>

    <div class="w-full max-w-2xl z-10 animate-fade-in-up">
        <div class="card p-10 space-y-10 border-white/60 bg-white/40 backdrop-blur-3xl shadow-premium rounded-[2.5rem]">
            <!-- Header -->
            <div class="text-center space-y-4">
                <a href="/rappel/public/" class="flex justify-center">
                    <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="h-12 w-auto object-contain">
                </a>
                <h1 class="text-4xl font-display font-bold text-navy-950 tracking-tight">Créer votre profil Expert</h1>
                <p class="text-navy-500 font-medium">Rejoignez notre réseau de professionnels certifiés.</p>
            </div>

            <!-- Step Indicator -->
            <div class="flex items-center justify-between">
                <?php
                $steps = [
                    ['num'=>1,'label'=>'Entreprise','icon'=>'building-2'],
                    ['num'=>2,'label'=>'Contact','icon'=>'user'],
                    ['num'=>3,'label'=>'Activité','icon'=>'hammer'],
                ];
                foreach ($steps as $i => $step): ?>
                <div class="flex items-center gap-3">
                    <div id="step-dot-<?= $step['num'] ?>"
                         class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300
                                <?= $step['num'] === 1 ? 'bg-brand-900 text-white shadow-md' : 'bg-navy-100 text-navy-400' ?>">
                        <i data-lucide="<?= $step['icon'] ?>" style="width:18px;height:18px;"></i>
                    </div>
                    <span id="step-label-<?= $step['num'] ?>" class="text-sm font-bold hidden sm:block
                          <?= $step['num'] === 1 ? 'text-navy-900' : 'text-navy-300' ?>">
                        <?= $step['label'] ?>
                    </span>
                </div>
                <?php if ($i < count($steps)-1): ?>
                <div class="flex-1 h-0.5 bg-navy-100 mx-2" id="step-line-<?= $step['num'] ?>"></div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Error / Success -->
            <div id="signup-error" class="form-error hidden">
                <span class="w-1.5 h-1.5 rounded-full bg-red-600 flex-shrink-0"></span>
                <span id="signup-error-text"></span>
            </div>

            <!-- STEP 1: Company -->
            <div id="signup-step-1" class="space-y-5">
                <h2 class="text-2xl font-display font-bold text-navy-950">Votre Entreprise</h2>

                <!-- SIRET / SIREN lookup -->
                <div>
                    <label class="form-label">Numéro SIRET ou SIREN *</label>
                    <div class="flex gap-3">
                        <div class="relative flex-1">
                            <input type="text" id="siret" class="form-input rounded-2xl pr-10" placeholder="Ex : 12345678901234" maxlength="20" inputmode="numeric"
                                   oninput="onSiretInput(this.value)">
                            <div id="siret-spinner" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                                <svg class="animate-spin h-4 w-4 text-accent-500" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                            </div>
                        </div>
                        <button onclick="lookupSiret()" id="siret-btn" class="btn btn-outline rounded-2xl px-5 whitespace-nowrap" title="Rechercher">
                            <i data-lucide="search" style="width:18px;height:18px;"></i>
                        </button>
                    </div>
                    <p id="siret-status" class="text-xs font-medium mt-2 text-navy-400"></p>
                </div>

                <!-- Auto-filled company info card -->
                <div id="company-info-card" class="hidden bg-gradient-to-br from-accent-50 to-white border border-accent-100 rounded-2xl p-5 space-y-4 transition-all">
                    <div class="flex items-center gap-2 text-accent-700 text-xs font-bold uppercase tracking-wide">
                        <i data-lucide="building-2" style="width:14px;height:14px;"></i>
                        Informations Auto-remplies
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="col-span-2">
                            <span class="text-navy-400 font-bold uppercase text-[10px] tracking-wider">Raison Sociale</span>
                            <p id="info-company-name" class="font-bold text-navy-900 mt-0.5">—</p>
                        </div>
                        <div>
                            <span class="text-navy-400 font-bold uppercase text-[10px] tracking-wider">Forme Juridique</span>
                            <p id="info-legal-form" class="font-semibold text-navy-700 mt-0.5">—</p>
                        </div>
                        <div>
                            <span class="text-navy-400 font-bold uppercase text-[10px] tracking-wider">Année de création</span>
                            <p id="info-creation-year" class="font-semibold text-navy-700 mt-0.5">—</p>
                        </div>
                        <div>
                            <span class="text-navy-400 font-bold uppercase text-[10px] tracking-wider">Code Postal</span>
                            <p id="info-zip" class="font-semibold text-navy-700 mt-0.5">—</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-navy-400 font-bold uppercase text-[10px] tracking-wider">Adresse du siège</span>
                            <p id="info-address" class="font-semibold text-navy-700 mt-0.5">—</p>
                        </div>
                    </div>
                </div>

                <!-- Editable fields (pre-filled by lookup) -->
                <div>
                    <label class="form-label">Nom de l'entreprise *</label>
                    <input type="text" id="company_name" class="form-input rounded-2xl" placeholder="Dupont & Associés">
                </div>
                <div>
                    <label class="form-label">Adresse *</label>
                    <input type="text" id="address" class="form-input rounded-2xl" placeholder="12 rue de la Paix">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Code Postal *</label>
                        <input type="text" id="zip_code" class="form-input rounded-2xl" placeholder="75001" maxlength="5">
                    </div>
                    <div>
                        <label class="form-label">Ville *</label>
                        <input type="text" id="city" class="form-input rounded-2xl" placeholder="Paris">
                    </div>
                </div>
                <!-- Hidden fields stored for backend -->
                <input type="hidden" id="legal_form" value="">
                <input type="hidden" id="creation_year" value="">

                <button onclick="goToStep(2)" class="btn btn-primary btn-lg w-full rounded-2xl shadow-premium">
                    Continuer <i data-lucide="arrow-right" style="width:20px;height:20px;"></i>
                </button>
            </div>

            <!-- STEP 2: Contact -->
            <div id="signup-step-2" class="space-y-6 hidden">
                <h2 class="text-2xl font-display font-bold text-navy-950">Vos Coordonnées</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Prénom *</label>
                        <input type="text" id="first_name" class="form-input rounded-2xl" placeholder="Jean">
                    </div>
                    <div>
                        <label class="form-label">Nom *</label>
                        <input type="text" id="last_name" class="form-input rounded-2xl" placeholder="Dupont">
                    </div>
                </div>
                <div>
                    <label class="form-label">Email professionnel *</label>
                    <input type="email" id="email" class="form-input rounded-2xl" placeholder="jean@dupont.fr">
                </div>
                <div>
                    <label class="form-label">Téléphone *</label>
                    <input type="tel" id="phone" class="form-input rounded-2xl" placeholder="06 12 34 56 78">
                </div>
                <div>
                    <label class="form-label">Mot de passe *</label>
                    <input type="password" id="password" class="form-input rounded-2xl" placeholder="Minimum 8 caractères">
                </div>
                <div class="flex gap-4">
                    <button onclick="goToStep(1)" class="btn btn-outline btn-lg flex-1 rounded-2xl">Retour</button>
                    <button onclick="goToStep(3)" class="btn btn-primary btn-lg flex-1 rounded-2xl shadow-premium">
                        Continuer <i data-lucide="arrow-right" style="width:20px;height:20px;"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 3: Activity -->
            <div id="signup-step-3" class="space-y-6 hidden">
                <h2 class="text-2xl font-display font-bold text-navy-950">Votre Activité</h2>
                <div>
                    <label class="form-label">Secteur d'activité *</label>
                    <select id="sector" class="form-select rounded-2xl">
                        <option value="">Sélectionnez un secteur</option>
                        <option value="assurance">Assurance</option>
                        <option value="renovation">Rénovation / BTP</option>
                        <option value="energie">Énergie / Isolation</option>
                        <option value="finance">Finance / Crédit</option>
                        <option value="garage">Garage / Automobile</option>
                        <option value="telecom">Télécoms</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Zone d'intervention *</label>
                    <select id="zone" class="form-select rounded-2xl">
                        <option value="">Sélectionnez une zone</option>
                        <option value="local">Local (ville)</option>
                        <option value="departemental">Départemental</option>
                        <option value="regional">Régional</option>
                        <option value="national">National</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Description de votre activité</label>
                    <textarea id="description" class="form-textarea rounded-2xl" placeholder="Décrivez votre activité et vos spécialités..."></textarea>
                </div>
                <div class="p-4 bg-navy-50 rounded-2xl text-sm text-navy-600 font-medium flex items-start gap-3">
                    <i data-lucide="shield-check" class="text-accent-600 flex-shrink-0 mt-0.5" style="width:18px;height:18px;"></i>
                    <span>En créant votre compte, vous acceptez nos <a href="#" class="text-accent-600 font-bold">Conditions Générales</a> et notre <a href="#" class="text-accent-600 font-bold">Politique de Confidentialité</a>.</span>
                </div>
                <div class="flex gap-4">
                    <button onclick="goToStep(2)" class="btn btn-outline btn-lg flex-1 rounded-2xl">Retour</button>
                    <button onclick="handleSignup()" id="signup-btn" class="btn btn-primary btn-lg flex-1 rounded-2xl shadow-premium">
                        Créer mon compte
                    </button>
                </div>
            </div>

            <div class="text-center pt-4 border-t border-navy-100/50">
                <p class="text-sm font-medium text-navy-400">
                    Déjà partenaire ?
                    <a href="/rappel/public/pro/login.php" class="text-accent-600 font-bold hover:text-accent-700 transition-colors ml-1">
                        Se connecter
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="/rappel/public/assets/js/app.js"></script>
<script>
let currentStep = 1;

function goToStep(step) {
    // Validate current step
    if (step > currentStep) {
        if (!validateStep(currentStep)) return;
    }
    currentStep = step;

    [1,2,3].forEach(s => {
        document.getElementById(`signup-step-${s}`).classList.toggle('hidden', s !== step);
        const dot = document.getElementById(`step-dot-${s}`);
        const label = document.getElementById(`step-label-${s}`);
        if (s < step) {
            dot.className = 'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 bg-accent-500 text-white shadow-md';
            dot.innerHTML = '<i data-lucide="check" style="width:18px;height:18px;"></i>';
            label && (label.className = 'text-sm font-bold hidden sm:block text-accent-600');
        } else if (s === step) {
            dot.className = 'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 bg-brand-900 text-white shadow-md';
            const icons = ['building-2','user','hammer'];
            dot.innerHTML = `<i data-lucide="${icons[s-1]}" style="width:18px;height:18px;"></i>`;
            label && (label.className = 'text-sm font-bold hidden sm:block text-navy-900');
        } else {
            dot.className = 'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 bg-navy-100 text-navy-400';
            const icons = ['building-2','user','hammer'];
            dot.innerHTML = `<i data-lucide="${icons[s-1]}" style="width:18px;height:18px;"></i>`;
            label && (label.className = 'text-sm font-bold hidden sm:block text-navy-300');
        }
    });
    if (typeof lucide !== 'undefined') lucide.createIcons();
    document.getElementById('signup-error').classList.add('hidden');
}

function validateStep(step) {
    const errEl = document.getElementById('signup-error');
    const errText = document.getElementById('signup-error-text');
    const showErr = (msg) => { errText.textContent = msg; errEl.classList.remove('hidden'); return false; };

    if (step === 1) {
        if (!document.getElementById('company_name').value.trim()) return showErr('Le nom de l\'entreprise est requis.');
        if (!document.getElementById('siret').value.trim()) return showErr('Le numéro SIRET est requis.');
        if (!document.getElementById('address').value.trim()) return showErr('L\'adresse est requise.');
        if (!document.getElementById('zip_code').value.trim()) return showErr('Le code postal est requis.');
        if (!document.getElementById('city').value.trim()) return showErr('La ville est requise.');
    }
    if (step === 2) {
        if (!document.getElementById('first_name').value.trim()) return showErr('Le prénom est requis.');
        if (!document.getElementById('last_name').value.trim()) return showErr('Le nom est requis.');
        if (!document.getElementById('email').value.trim()) return showErr('L\'email est requis.');
        if (!document.getElementById('phone').value.trim()) return showErr('Le téléphone est requis.');
        const pwd = document.getElementById('password').value;
        if (pwd.length < 8) return showErr('Le mot de passe doit contenir au moins 8 caractères.');
    }
    return true;
}

let siretLookupTimeout = null;

function onSiretInput(val) {
    const input = document.getElementById('siret');
    const clean = val.replace(/\D/g,'').slice(0, 14);
    if (input.value !== clean) input.value = clean;
    if (siretLookupTimeout) clearTimeout(siretLookupTimeout);
    if (clean.length === 14 || clean.length === 9) {
        siretLookupTimeout = setTimeout(() => lookupSiret(), 600);
    } else if (clean.length > 0) {
        setStatus('Saisissez 9 chiffres (SIREN) ou 14 chiffres (SIRET)', 'neutral');
    } else {
        setStatus('', 'neutral');
    }
}

async function lookupSiret() {
    const raw = document.getElementById('siret').value.trim().replace(/\D/g,'');
    if (raw.length !== 9 && raw.length !== 14) {
        setStatus('Saisissez 9 chiffres (SIREN) ou 14 chiffres (SIRET)', 'error');
        return;
    }

    const btn     = document.getElementById('siret-btn');
    const spinner = document.getElementById('siret-spinner');
    btn.disabled  = true;
    spinner.classList.remove('hidden');
    setStatus('Recherche en cours...', 'neutral');

    try {
        // Use our local API proxy to avoid CORS/SSL issues
        const d = await apiFetch(`/company/lookup?siret=${raw}`);

        // Fill inputs
        if (d.company_name)  document.getElementById('company_name').value = d.company_name;
        if (d.address)       document.getElementById('address').value      = d.address;
        if (d.zip_code)      document.getElementById('zip_code').value     = d.zip_code;
        if (d.city)          document.getElementById('city').value         = d.city;
        document.getElementById('legal_form').value    = d.legal_form    || '';
        document.getElementById('creation_year').value = d.creation_year || '';

        // Update info card
        document.getElementById('info-company-name').textContent  = d.company_name  || '—';
        document.getElementById('info-legal-form').textContent    = d.legal_form    || '—';
        document.getElementById('info-creation-year').textContent = d.creation_year || '—';
        document.getElementById('info-zip').textContent           = d.zip_code      || '—';
        document.getElementById('info-address').textContent       = d.address       || '—';

        document.getElementById('company-info-card').classList.remove('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();

        setStatus('✓ Entreprise trouvée et remplie automatiquement', 'success');
    } catch (err) {
        console.error('Lookup error:', err);
        setStatus('Entreprise non trouvée — vérifiez le numéro ou remplissez manuellement.', 'error');
    } finally {
        btn.disabled = false;
        spinner.classList.add('hidden');
    }
}

function setStatus(msg, type) {
    const el = document.getElementById('siret-status');
    el.textContent = msg;
    el.className = 'text-xs font-medium mt-2 ' + {
        success: 'text-accent-600',
        error:   'text-red-500',
        neutral: 'text-navy-400',
    }[type];
}

async function handleSignup() {
    if (!validateStep(3)) return;
    const sector = document.getElementById('sector').value;
    const zone = document.getElementById('zone').value;
    if (!sector || !zone) {
        document.getElementById('signup-error-text').textContent = 'Veuillez sélectionner un secteur et une zone.';
        document.getElementById('signup-error').classList.remove('hidden');
        return;
    }

    const btn = document.getElementById('signup-btn');
    setButtonLoading(btn, true);

    const payload = {
        companyName: document.getElementById('company_name').value.trim(),
        company_name: document.getElementById('company_name').value.trim(),
        siret: document.getElementById('siret').value.trim(),
        address: document.getElementById('address').value.trim(),
        zip: document.getElementById('zip_code').value.trim(),
        zip_code: document.getElementById('zip_code').value.trim(),
        city: document.getElementById('city').value.trim(),
        firstName: document.getElementById('first_name').value.trim(),
        first_name: document.getElementById('first_name').value.trim(),
        lastName: document.getElementById('last_name').value.trim(),
        last_name: document.getElementById('last_name').value.trim(),
        email: document.getElementById('email').value.trim(),
        phone: document.getElementById('phone').value.trim(),
        password: document.getElementById('password').value,
        legalForm: document.getElementById('legal_form').value.trim(),
        creationYear: document.getElementById('creation_year').value.trim(),
        legal_form: document.getElementById('legal_form').value.trim(),
        creation_year: document.getElementById('creation_year').value.trim(),
        sector,
        zone,
        description: document.getElementById('description').value.trim(),
    };

    try {
        const data = await apiFetch('/auth/signup', { method: 'POST', body: JSON.stringify(payload) });
        const token = data.session?.access_token || data.token || '';
        const user = data.user || {};

        // Store email for verification
        sessionStorage.setItem('rappel_user_email', payload.email);

        // Store in PHP session
        await fetch('/rappel/public/api-session.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'set', token, user })
        });
        Auth.setToken(token);
        Auth.setUser(user);

        window.location.href = '/rappel/public/pro/verify.php';
    } catch (err) {
        document.getElementById('signup-error-text').textContent = err.message || 'Erreur lors de l\'inscription.';
        document.getElementById('signup-error').classList.remove('hidden');
        setButtonLoading(btn, false, 'Créer mon compte');
    }
}

document.addEventListener('DOMContentLoaded', () => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
</script>
</body>
</html>
