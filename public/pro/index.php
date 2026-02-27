<?php
require_once __DIR__ . '/../includes/auth.php';
// If already logged in, redirect to dashboard
if (isLoggedIn() && isVerified()) {
    header('Location: /rappel/public/pro/dashboard.php');
    exit;
}
$pageTitle = 'Espace Expert';
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

<div class="font-sans text-navy-900 bg-transparent min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="w-full bg-white/70 backdrop-blur-xl border-b border-navy-100/50 py-4 px-8 fixed top-0 z-50 flex justify-between items-center shadow-glass">
        <a href="/rappel/public/" class="flex items-center hover:opacity-80 transition-opacity">
            <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="h-9 w-auto object-contain">
        </a>
        <div class="flex items-center gap-8 text-sm font-bold text-navy-600">
            <a href="#tarifs" class="hover:text-accent-600 transition-colors">Tarifs</a>
            <a href="#temoignages" class="hover:text-accent-600 transition-colors">Témoignage</a>
            <a href="/rappel/public/pro/login.php" class="btn btn-primary btn-sm shadow-premium rounded-xl px-5">Connexion Expert</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-40 pb-24 px-6 text-center overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-[radial-gradient(circle_at_50%_-20%,_rgba(16,185,129,0.1),transparent_70%)]"></div>
        <div class="max-w-5xl mx-auto space-y-8 relative z-10 animate-fade-in-up">
            <h1 class="text-5xl md:text-7xl font-display font-bold text-navy-950 leading-tight tracking-tight">
                Transformez votre croissance avec des <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-accent-600 to-brand-600">
                    Leads à Haute Conversion
                </span>
            </h1>
            <p class="text-navy-600/80 text-xl md:text-2xl max-w-3xl mx-auto font-medium">
                Accédez en temps réel aux demandes de particuliers qualifiés et validez vos objectifs de vente.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-5 pt-6">
                <a href="#tarifs" class="btn btn-primary btn-lg rounded-2xl px-12 shadow-premium">Découvrir nos solutions</a>
                <a href="/rappel/public/pro/signup.php" class="btn btn-outline btn-lg rounded-2xl px-12 border-navy-200">Créer mon profil expert</a>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="bg-transparent py-16 px-6">
        <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-12">
            <?php
            $stats = [
                ['value'=>'35k+','label'=>'Demandes traitées'],
                ['value'=>'450+','label'=>'Experts actifs'],
                ['value'=>'82%','label'=>'Taux de transformation'],
                ['value'=>'2min','label'=>'Délai moyen de mise en relation'],
            ];
            foreach ($stats as $stat): ?>
            <div class="space-y-2 text-center group">
                <div class="text-4xl md:text-5xl font-display font-bold text-navy-950 group-hover:text-accent-600 transition-colors"><?= $stat['value'] ?></div>
                <div class="text-xs font-bold text-navy-400 uppercase tracking-[0.2em]"><?= $stat['label'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="tarifs" class="py-32 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-5xl font-display font-bold text-navy-950 mb-6">Investissement à la Performance</h2>
                <p class="text-navy-500 text-lg font-medium">Choisissez le modèle qui correspond à votre ambition.</p>
            </div>
            <div class="grid lg:grid-cols-3 gap-10 items-stretch">

                <!-- Croissance -->
                <div class="card flex flex-col p-10 bg-white/40 border-white/60">
                    <h3 class="text-2xl font-bold text-navy-950 mb-2">Croissance</h3>
                    <p class="text-navy-500 mb-8 font-medium">Pour les professionnels locaux.</p>
                    <div class="flex items-baseline gap-2 mb-10">
                        <span class="text-5xl font-bold text-navy-950">99€</span>
                        <span class="text-navy-400 font-bold">/mois</span>
                    </div>
                    <ul class="space-y-5 flex-grow mb-12">
                        <?php foreach (["10 leads qualifiés inclus","1 secteur d'activité","Ciblage départemental","Preuves de consentement SMS"] as $item): ?>
                        <li class="flex items-center gap-4 text-navy-700 font-medium">
                            <div class="w-5 h-5 rounded-full bg-accent-100 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="check-circle" class="text-accent-600" style="width:14px;height:14px;"></i>
                            </div>
                            <?= $item ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="#" onclick="selectPlan('croissance'); return false;" class="btn btn-outline w-full rounded-xl border-navy-200 text-center">Sélectionner</a>
                </div>

                <!-- Accélération (Popular) -->
                <div class="card flex flex-col p-10 bg-navy-900 border-accent-500/20 scale-105 shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4">
                        <i data-lucide="zap" class="text-accent-400 animate-pulse" style="width:24px;height:24px;fill:currentColor;"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Accélération</h3>
                    <p class="text-navy-300 mb-8 font-medium">Pour les structures en expansion.</p>
                    <div class="flex items-baseline gap-2 mb-10 text-white">
                        <span class="text-6xl font-bold">249€</span>
                        <span class="text-navy-400 font-bold">/mois</span>
                    </div>
                    <ul class="space-y-5 flex-grow mb-12">
                        <?php foreach (["30 leads haute qualité","Ciblage régional illimité","CRM intégré avec API","Support VIP 24/7","Garantie de remplacement lead"] as $item): ?>
                        <li class="flex items-center gap-4 text-navy-100 font-medium">
                            <div class="w-5 h-5 rounded-full bg-accent-500 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="check-circle" class="text-navy-950" style="width:14px;height:14px;"></i>
                            </div>
                            <?= $item ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="#" onclick="selectPlan('acceleration'); return false;" class="btn btn-accent w-full rounded-xl shadow-premium text-center">Choisir l'Excellence</a>
                </div>

                <!-- Flexibilité -->
                <div class="card flex flex-col p-10 bg-white/40 border-white/60">
                    <h3 class="text-2xl font-bold text-navy-950 mb-2">Flexibilité</h3>
                    <p class="text-navy-500 mb-8 font-medium">Payez à l'usage réel.</p>
                    <div class="flex items-baseline gap-2 mb-10">
                        <span class="text-5xl font-bold text-navy-950">12€</span>
                        <span class="text-navy-400 font-bold">/lead</span>
                    </div>
                    <ul class="space-y-5 flex-grow mb-12 text-navy-700">
                        <?php foreach (["Zéro coût fixe hebdomadaire","Volume illimité","Recharge crédit instantanée","Accès dashboard global"] as $item): ?>
                        <li class="flex items-center gap-4 font-medium">
                            <div class="w-5 h-5 rounded-full bg-accent-100 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="check-circle" class="text-accent-600" style="width:14px;height:14px;"></i>
                            </div>
                            <?= $item ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="#" onclick="selectPlan('flexibilite'); return false;" class="btn btn-outline w-full rounded-xl border-navy-200 text-center">Commander</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Grid -->
    <section class="py-32 px-6 bg-navy-900 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-accent-500/10 rounded-full -mr-64 -mt-64 blur-[120px]"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-accent-500/5 rounded-full -ml-64 -mb-64 blur-[120px]"></div>
        <div class="max-w-6xl mx-auto relative z-10">
            <div class="text-left mb-20 max-w-2xl">
                <h2 class="text-4xl md:text-5xl font-display font-bold text-white mb-6">L'ingénierie du Lead</h2>
                <p class="text-navy-300 text-xl font-medium leading-relaxed">
                    Nous utilisons une technologie de pointe pour transformer chaque clic en une opportunité commerciale qualifiée et conforme.
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $benefits = [
                    ['icon'=>'target','title'=>'Exclusivité Territoriale','desc'=>"Ne soyez plus en concurrence avec 5 autres prestataires. Nous gérons la distribution pour maximiser votre ROI.",'color'=>'text-brand-600 bg-brand-50'],
                    ['icon'=>'shield-check','title'=>'Conformité Totale','desc'=>'Certification RGPD & Loi 2025. Chaque lead est accompagné de sa preuve de consentement horodatée.','color'=>'text-accent-600 bg-accent-50'],
                    ['icon'=>'zap','title'=>'Instantanéité Absolue','desc'=>"Réception par SMS/Email en temps réel. Appelez le prospect pendant qu'il est encore sur son écran.",'color'=>'text-brand-600 bg-brand-50'],
                    ['icon'=>'bar-chart-3','title'=>'Optimisation du Pipeline','desc'=>"Dashboard analytique complet pour suivre votre coût d'acquisition et vos performances commerciales.",'color'=>'text-accent-600 bg-accent-50'],
                    ['icon'=>'bell','title'=>'Filtres Surgitaux','desc'=>'Définissez vos critères précis : type de projet, budget minimum, zone géographique au code postal.','color'=>'text-brand-600 bg-brand-50'],
                    ['icon'=>'file-text','title'=>'Zéro Abonnements','desc'=>"Modèle à la performance. Vous ne payez que pour les leads que vous décidez d'ouvrir.",'color'=>'text-accent-600 bg-accent-50'],
                ];
                foreach ($benefits as $b): ?>
                <div class="card p-10 hover:shadow-premium group bg-white/10 backdrop-blur-md border-white/10 hover:border-accent-500/30 transition-all duration-500">
                    <div class="w-14 h-14 rounded-[1.25rem] flex items-center justify-center mb-8 group-hover:rotate-6 transition-transform duration-300 <?= $b['color'] ?>">
                        <i data-lucide="<?= $b['icon'] ?>" style="width:28px;height:28px;"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4"><?= $b['title'] ?></h3>
                    <p class="text-navy-300 leading-relaxed font-medium"><?= $b['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="py-24 px-6 text-center">
        <div class="max-w-4xl mx-auto rounded-[3.5rem] bg-[#0E1648] p-16 shadow-premium relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-accent-500/10 rounded-full -ml-32 -mb-32 blur-3xl transition-all duration-1000 group-hover:scale-125"></div>
            <h2 class="text-4xl font-display font-bold text-white mb-8 relative z-10">Prêt à changer de dimension ?</h2>
            <a href="/rappel/public/pro/signup.php" class="btn btn-lg rounded-2xl font-bold shadow-xl relative z-10 inline-flex" style="background:white;color:#0E1648;">
                Commencer l'intégration
            </a>
        </div>
    </section>

    <footer class="py-20 px-6 border-t border-navy-100/30">
        <div class="max-w-7xl mx-auto flex flex-col items-center gap-8">
            <p class="text-navy-400 font-bold uppercase tracking-widest text-[10px]">
                &copy; 2026 Rappelez-moi Expert - Solution de Leads Certifiés
            </p>
        </div>
    </footer>
</div>

<script src="/rappel/public/assets/js/app.js?v=3.1"></script>
<script>
document.addEventListener('DOMContentLoaded', () => { if (typeof lucide !== 'undefined') lucide.createIcons(); });

let _landingPlans = [];
(async () => {
    try {
        const res = await fetch('/rappel/api/plans', { headers: { 'Accept': 'application/json' } });
        if (res.ok) _landingPlans = await res.json();
    } catch(e) {}
})();

function selectPlan(name) {
    const token = sessionStorage.getItem('rappel_token') || '';
    if (token) {
        window.location.href = '/rappel/public/pro/pricing.php?select=' + encodeURIComponent(name);
    } else {
        window.location.href = '/rappel/public/pro/signup.php?intent=' + encodeURIComponent(name);
    }
}
</script>
</body>
</html>
