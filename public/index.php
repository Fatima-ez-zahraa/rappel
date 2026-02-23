<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Accueil';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body class="bg-[#D3D3D3] font-sans antialiased overflow-x-hidden min-h-screen">

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="min-h-screen bg-transparent">

    <!-- ===== HERO SECTION ===== -->
    <section class="relative w-full min-h-screen flex items-center justify-center overflow-hidden bg-transparent pt-20">
        <!-- Background Decorations -->
        <div class="absolute top-20 left-10 w-64 h-64 bg-accent-500/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl animate-float" style="animation-delay:-3s"></div>

        <div class="container mx-auto px-6 lg:px-12 grid lg:grid-cols-2 gap-16 items-center z-10">

            <!-- Text Content -->
            <div class="space-y-10 animate-fade-in-up">
                <div>
                    <h1 class="text-5xl lg:text-7xl font-display font-bold text-navy-950 leading-[1.1] tracking-tight">
                        Le rappel que vous attendez, <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-600">
                            au moment où vous le décidez.
                        </span>
                    </h1>
                    <p class="text-xl text-navy-600/80 max-w-lg mt-8 leading-relaxed font-medium">
                        Reprenez le contrôle sur vos communications. Connectez-vous avec des experts au moment idéal.
                    </p>
                </div>

                <div class="flex flex-wrap gap-5">
                    <button onclick="document.getElementById('demande').scrollIntoView({behavior:'smooth'})"
                            class="btn btn-primary btn-lg rounded-[2rem] px-10 flex items-center gap-2">
                        Commencer
                        <i data-lucide="arrow-right" style="width:20px;height:20px;"></i>
                    </button>
                    <a href="/rappel/public/pro/"
                       class="btn btn-outline btn-lg rounded-[2rem] px-10">
                        Espace Pro
                    </a>
                </div>

                <!-- Sector Navigation -->
                <div class="hidden lg:flex gap-4 pt-10" id="sector-nav">
                    <button onclick="setActiveSector(0)" class="sector-btn flex items-center gap-3 p-4 rounded-3xl transition-all duration-500 border-2 bg-white shadow-premium border-accent-500/20 scale-105" data-index="0">
                        <div class="p-2 rounded-xl text-white shadow-lg bg-indigo-500"><i data-lucide="shield" style="width:20px;height:20px;"></i></div>
                        <span class="font-bold text-sm tracking-wide text-navy-950">Assurance</span>
                    </button>
                    <button onclick="setActiveSector(1)" class="sector-btn flex items-center gap-3 p-4 rounded-3xl transition-all duration-500 border-2 bg-white/20 hover:bg-white/50 border-transparent backdrop-blur-sm" data-index="1">
                        <div class="p-2 rounded-xl text-white shadow-lg bg-orange-500"><i data-lucide="hammer" style="width:20px;height:20px;"></i></div>
                        <span class="font-bold text-sm tracking-wide text-navy-400">Rénovation</span>
                    </button>
                    <button onclick="setActiveSector(2)" class="sector-btn flex items-center gap-3 p-4 rounded-3xl transition-all duration-500 border-2 bg-white/20 hover:bg-white/50 border-transparent backdrop-blur-sm" data-index="2">
                        <div class="p-2 rounded-xl text-white shadow-lg bg-blue-500"><i data-lucide="car" style="width:20px;height:20px;"></i></div>
                        <span class="font-bold text-sm tracking-wide text-navy-400">Garage</span>
                    </button>
                    <button onclick="setActiveSector(3)" class="sector-btn flex items-center gap-3 p-4 rounded-3xl transition-all duration-500 border-2 bg-white/20 hover:bg-white/50 border-transparent backdrop-blur-sm" data-index="3">
                        <div class="p-2 rounded-xl text-white shadow-lg bg-purple-500"><i data-lucide="smartphone" style="width:20px;height:20px;"></i></div>
                        <span class="font-bold text-sm tracking-wide text-navy-400">Télécom</span>
                    </button>
                </div>
            </div>

            <!-- Visual Carousel -->
            <div class="relative h-[550px] w-full lg:h-[700px]">
                <div class="absolute -inset-4 border-2 border-dashed border-navy-200/30 rounded-[4rem]" style="animation:spin 20s linear infinite;"></div>

                <div id="sector-image" class="relative w-full h-full rounded-[3.5rem] overflow-hidden shadow-premium border-[12px] border-white/80 backdrop-blur-md transition-all duration-700">
                    <img id="sector-img" src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&q=80&w=800"
                         alt="Assurance" class="w-full h-full object-cover transition-transform duration-700 hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-navy-900/20 to-transparent flex flex-col justify-end p-10 lg:p-14">
                        <div>
                            <div id="sector-icon-wrap" class="inline-flex p-4 rounded-2xl mb-6 text-white shadow-2xl backdrop-blur-xl bg-indigo-500/80 border border-white/20">
                                <i data-lucide="shield" style="width:40px;height:40px;"></i>
                            </div>
                            <h3 id="sector-title" class="text-4xl font-display font-bold text-white mb-3 tracking-tight">Assurance</h3>
                            <p id="sector-desc" class="text-slate-200 text-xl font-medium leading-relaxed max-w-sm">Protection optimale pour votre famille et vos biens.</p>
                        </div>
                    </div>
                </div>

                <!-- Floating Badge -->
                <div class="absolute -right-4 lg:-right-12 top-20 bg-white/90 backdrop-blur-xl p-5 rounded-[2rem] shadow-premium border border-white/50 flex items-center gap-4 z-20 animate-float">
                    <div class="w-12 h-12 bg-accent-100 rounded-2xl flex items-center justify-center text-accent-600">
                        <i data-lucide="shield-check" style="width:24px;height:24px;"></i>
                    </div>
                    <div>
                        <p class="text-xs text-navy-400 font-bold uppercase tracking-wider">Confiance</p>
                        <p class="text-base font-display font-bold text-navy-900">100% Vérifié</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== LEAD FORM SECTION ===== -->
    <section id="demande" class="py-24 bg-transparent relative overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-1/3 left-1/4 w-96 h-96 bg-accent-400/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-brand-500/5 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-6 lg:px-12 relative z-10">
            <div class="text-center mb-14">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-accent-100 text-accent-700 text-sm font-bold mb-5 border border-accent-200">
                    <i data-lucide="shield-check" style="width:14px;height:14px;"></i>
                    <span>Simple &amp; Sécurisé</span>
                </div>
                <h2 class="text-4xl lg:text-5xl font-display font-bold text-navy-950 tracking-tight">Configurez votre demande</h2>
                <p class="text-navy-500 font-medium mt-3 text-lg">En 4 étapes, moins de 2 minutes.</p>
            </div>

            <!-- Lead Form Wizard -->
            <div class="max-w-2xl mx-auto">
                <div class="bg-white/70 backdrop-blur-xl rounded-[2rem] shadow-[0_8px_60px_rgba(0,0,0,0.10)] border border-white/80 p-8 lg:p-10">

                    <!-- ── Progress bar + Step indicator ── -->
                    <div class="mb-10">
                        <!-- Step labels row -->
                        <div class="flex items-start justify-between mb-4 relative">
                            <!-- Background track -->
                            <div class="absolute top-5 left-5 right-5 h-0.5 bg-navy-100 -z-10"></div>
                            <!-- Active track (animated by JS) -->
                            <div id="step-progress-bar" class="absolute top-5 left-5 h-0.5 bg-gradient-to-r from-accent-400 to-brand-500 -z-10 transition-all duration-500" style="width:0%"></div>

                            <?php
                            $wizardSteps = [
                                ['num'=>1,'label'=>'Service',  'icon'=>'layers'],
                                ['num'=>2,'label'=>'Créneau',  'icon'=>'clock'],
                                ['num'=>3,'label'=>'Infos',    'icon'=>'user'],
                                ['num'=>4,'label'=>'Validation','icon'=>'check-circle'],
                            ];
                            foreach ($wizardSteps as $ws): ?>
                            <div class="flex flex-col items-center gap-2 w-1/4">
                                <div id="step-dot-<?= $ws['num'] ?>"
                                     class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-400 bg-white border-2 border-navy-100 text-navy-300 shadow-sm
                                            <?= $ws['num'] === 1 ? '!bg-brand-600 !border-brand-600 !text-white shadow-lg shadow-brand-200' : '' ?>">
                                    <i data-lucide="<?= $ws['icon'] ?>" style="width:18px;height:18px;"></i>
                                </div>
                                <span id="step-label-<?= $ws['num'] ?>" class="text-[11px] font-bold transition-colors duration-300 <?= $ws['num'] === 1 ? 'text-brand-600' : 'text-navy-300' ?>">
                                    <?= $ws['label'] ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- ── Step 1: Service Selection ── -->
                    <div id="lead-step-1" class="space-y-7 animate-fade-in">
                        <div class="text-center">
                            <h3 class="text-2xl font-display font-bold text-navy-950">Quel service recherchez-vous ?</h3>
                            <p class="text-navy-400 text-sm mt-1">Sélectionnez le domaine qui vous correspond.</p>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <?php
                            $services = [
                                ['id'=>'assurance', 'label'=>'Assurance',   'icon'=>'shield',      'gradient'=>'from-indigo-500 to-blue-600',   'bg'=>'bg-indigo-50',  'text'=>'text-indigo-600'],
                                ['id'=>'renovation','label'=>'Rénovation',  'icon'=>'hammer',      'gradient'=>'from-orange-400 to-red-500',    'bg'=>'bg-orange-50',  'text'=>'text-orange-600'],
                                ['id'=>'energie',   'label'=>'Énergie',     'icon'=>'zap',         'gradient'=>'from-yellow-400 to-amber-500',  'bg'=>'bg-yellow-50',  'text'=>'text-yellow-600'],
                                ['id'=>'finance',   'label'=>'Finance',     'icon'=>'trending-up', 'gradient'=>'from-emerald-400 to-green-600', 'bg'=>'bg-green-50',   'text'=>'text-green-600'],
                                ['id'=>'garage',    'label'=>'Garage',      'icon'=>'car',         'gradient'=>'from-sky-400 to-cyan-600',      'bg'=>'bg-sky-50',     'text'=>'text-sky-600'],
                                ['id'=>'telecom',   'label'=>'Télécoms',    'icon'=>'smartphone',  'gradient'=>'from-violet-500 to-purple-600', 'bg'=>'bg-purple-50',  'text'=>'text-purple-600'],
                            ];
                            foreach ($services as $s): ?>
                            <button onclick="selectService('<?= $s['id'] ?>')"
                                    class="service-btn group relative flex flex-col items-center gap-3 p-5 rounded-2xl border-2 border-transparent
                                           bg-navy-50/60 hover:bg-white hover:border-navy-100 hover:shadow-xl
                                           transition-all duration-300 overflow-hidden"
                                    data-service="<?= $s['id'] ?>" data-gradient="<?= $s['gradient'] ?>">
                                <!-- Glow on hover/active -->
                                <div class="service-glow absolute inset-0 opacity-0 group-hover:opacity-5 bg-gradient-to-br <?= $s['gradient'] ?> transition-opacity duration-300 rounded-2xl"></div>
                                <div class="w-13 h-13 p-3 rounded-xl <?= $s['bg'] ?> <?= $s['text'] ?> group-hover:scale-110 transition-transform duration-300 shadow-sm relative z-10">
                                    <i data-lucide="<?= $s['icon'] ?>" style="width:26px;height:26px;"></i>
                                </div>
                                <span class="font-bold text-sm text-navy-700 group-hover:text-navy-950 relative z-10"><?= $s['label'] ?></span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                        <button onclick="goToLeadStep(2)" id="btn-step1-next"
                                class="btn btn-primary btn-lg w-full rounded-2xl hidden shadow-lg shadow-brand-400/20 flex items-center justify-center gap-2">
                            Continuer vers le créneau
                            <i data-lucide="arrow-right" style="width:20px;height:20px;"></i>
                        </button>
                    </div>

                    <!-- ── Step 2: Time Slot Selection ── -->
                    <div id="lead-step-2" class="space-y-6 hidden animate-fade-in">
                        <div class="text-center">
                            <h3 class="text-2xl font-display font-bold text-navy-950">Quand souhaitez-vous être rappelé ?</h3>
                            <p class="text-navy-400 text-sm mt-1">Choisissez le créneau qui vous convient.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <?php
                            $slots = [
                                ['id'=>'matin',     'label'=>'Matin',      'time'=>'09h – 12h', 'icon'=>'sun',           'color'=>'text-amber-500',   'bg'=>'bg-amber-50'],
                                ['id'=>'midi',      'label'=>'Midi',       'time'=>'12h – 14h', 'icon'=>'coffee',        'color'=>'text-orange-500',  'bg'=>'bg-orange-50'],
                                ['id'=>'apres-midi','label'=>'Après-midi', 'time'=>'14h – 18h', 'icon'=>'sunset',        'color'=>'text-sky-500',     'bg'=>'bg-sky-50'],
                                ['id'=>'soir',      'label'=>'Soirée',     'time'=>'18h – 20h', 'icon'=>'moon',          'color'=>'text-indigo-500',  'bg'=>'bg-indigo-50'],
                                ['id'=>'weekend',   'label'=>'Week-end',   'time'=>'Samedi & Dimanche', 'icon'=>'calendar-clock', 'color'=>'text-violet-500','bg'=>'bg-violet-50'],
                            ];
                            foreach ($slots as $i => $slot):
                                $fullWidth = $slot['id'] === 'weekend' ? 'sm:col-span-2' : '';
                            ?>
                            <button onclick="selectTimeSlot('<?= $slot['id'] ?>')"
                                    class="slot-btn group relative w-full flex items-center gap-4 p-4 rounded-2xl border-2 border-navy-100 bg-white
                                           hover:border-accent-300 hover:shadow-md transition-all duration-200 text-left <?= $fullWidth ?>"
                                    data-slot="<?= $slot['id'] ?>">
                                <div class="w-12 h-12 rounded-xl <?= $slot['bg'] ?> <?= $slot['color'] ?> flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <i data-lucide="<?= $slot['icon'] ?>" style="width:22px;height:22px;"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-navy-900 leading-tight"><?= $slot['label'] ?></p>
                                    <p class="text-xs text-navy-400 mt-0.5"><?= $slot['time'] ?></p>
                                </div>
                                <!-- Radio dot -->
                                <div class="w-5 h-5 rounded-full border-2 border-navy-200 flex items-center justify-center slot-check shrink-0 transition-all duration-200">
                                    <div class="w-2.5 h-2.5 rounded-full bg-accent-500 scale-0 transition-transform duration-200"></div>
                                </div>
                            </button>
                            <?php endforeach; ?>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button onclick="goToLeadStep(1)" class="btn btn-outline flex-1 rounded-2xl border-navy-200 text-navy-500 hover:bg-navy-50">
                                <i data-lucide="arrow-left" style="width:18px;height:18px;"></i> Retour
                            </button>
                            <button onclick="goToLeadStep(3)" id="btn-step2-next" class="btn btn-primary flex-1 rounded-2xl shadow-lg shadow-brand-400/20" disabled>
                                Continuer <i data-lucide="arrow-right" style="width:18px;height:18px;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ── Step 3: Contact Info ── -->
                    <div id="lead-step-3" class="space-y-5 hidden animate-fade-in">
                        <div class="text-center">
                            <h3 class="text-2xl font-display font-bold text-navy-950">Vos coordonnées</h3>
                            <p class="text-navy-400 text-sm mt-1">Pour que l'expert puisse vous rappeler.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-navy-400 uppercase tracking-wider">Prénom</label>
                                <div class="relative">
                                    <i data-lucide="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-navy-300" style="width:16px;height:16px;"></i>
                                    <input type="text" id="lead-firstname" class="form-input pl-10 rounded-xl bg-navy-50/60 border-navy-100 focus:bg-white" placeholder="Jean">
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-navy-400 uppercase tracking-wider">Nom</label>
                                <div class="relative">
                                    <i data-lucide="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-navy-300" style="width:16px;height:16px;"></i>
                                    <input type="text" id="lead-lastname" class="form-input pl-10 rounded-xl bg-navy-50/60 border-navy-100 focus:bg-white" placeholder="Dupont">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-navy-400 uppercase tracking-wider">Téléphone <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <i data-lucide="phone" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-navy-300" style="width:16px;height:16px;"></i>
                                <input type="tel" id="lead-phone" class="form-input pl-10 rounded-xl bg-navy-50/60 border-navy-100 focus:bg-white" placeholder="06 12 34 56 78">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-navy-400 uppercase tracking-wider">Email <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <i data-lucide="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-navy-300" style="width:16px;height:16px;"></i>
                                <input type="email" id="lead-email" class="form-input pl-10 rounded-xl bg-navy-50/60 border-navy-100 focus:bg-white" placeholder="jean@exemple.com">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-navy-400 uppercase tracking-wider">Code Postal</label>
                            <div class="relative">
                                <i data-lucide="map-pin" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-navy-300" style="width:16px;height:16px;"></i>
                                <input type="text" id="lead-zip" class="form-input pl-10 rounded-xl bg-navy-50/60 border-navy-100 focus:bg-white" placeholder="75001" maxlength="5">
                            </div>
                        </div>

                        <div class="flex gap-3 pt-1">
                            <button onclick="goToLeadStep(2)" class="btn btn-outline flex-1 rounded-2xl border-navy-200 text-navy-500 hover:bg-navy-50">
                                <i data-lucide="arrow-left" style="width:18px;height:18px;"></i> Retour
                            </button>
                            <button onclick="goToLeadStep(4)" class="btn btn-primary flex-1 rounded-2xl shadow-lg shadow-brand-400/20">
                                Récapitulatif <i data-lucide="arrow-right" style="width:18px;height:18px;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ── Step 4: Summary + Submit ── -->
                    <div id="lead-step-4" class="space-y-5 hidden animate-fade-in">
                        <div class="text-center">
                            <h3 class="text-2xl font-display font-bold text-navy-950">Récapitulatif</h3>
                            <p class="text-navy-400 text-sm mt-1">Vérifiez vos informations avant d'envoyer.</p>
                        </div>

                        <div class="bg-gradient-to-br from-navy-50 to-white border border-navy-100 rounded-2xl p-5 shadow-sm space-y-3" id="lead-summary">
                            <!-- Filled by JS -->
                        </div>

                        <div class="p-5 bg-accent-50/80 border border-accent-100 rounded-2xl text-[13px] text-navy-600 font-medium space-y-3">
                            <div class="flex items-start gap-3">
                                <i data-lucide="shield-check" class="text-accent-500 shrink-0 mt-0.5" style="width:16px;height:16px;"></i>
                                <div class="space-y-3">
                                    <p>
                                        En validant ce formulaire, vous demandez expressément à être contacté par un ou plusieurs professionnels partenaires via le Service <strong>rappellez-moi.co</strong>.
                                    </p>
                                    <p>
                                        Vous consentez à être contacté par téléphone, SMS ou email dans le cadre exclusif de votre demande. Ce consentement est conforme à la réglementation applicable au démarchage téléphonique et au RGPD.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Mandatory Checkbox -->
                        <div class="flex items-start gap-3 px-2 py-1">
                            <div class="flex items-center h-5">
                                <input id="consent-checkbox" type="checkbox" class="w-5 h-5 rounded border-navy-200 text-accent-600 focus:ring-accent-500 cursor-pointer">
                            </div>
                            <label for="consent-checkbox" class="text-sm font-bold text-navy-900 cursor-pointer select-none">
                                J’accepte d’être contacté conformément au texte ci-dessus. <span class="text-red-500">*</span>
                            </label>
                        </div>

                        <div id="lead-error" class="bg-red-50 text-red-600 p-4 rounded-xl text-sm font-bold border border-red-100 hidden flex items-center gap-2">
                            <i data-lucide="alert-circle" style="width:16px;height:16px;"></i>
                            <span></span>
                        </div>

                        <!-- Success panel -->
                        <div id="lead-success" class="hidden text-center py-6 animate-fade-in">
                            <div class="relative w-28 h-28 mx-auto mb-6">
                                <div class="absolute inset-0 rounded-full bg-green-100 animate-ping opacity-30"></div>
                                <div class="relative w-28 h-28 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-xl shadow-green-200">
                                    <i data-lucide="check" class="text-white" style="width:52px;height:52px;stroke-width:3;"></i>
                                </div>
                            </div>
                            <h4 class="text-2xl font-display font-bold text-navy-950 mb-2">Demande envoyée !</h4>
                            <p class="text-navy-500 font-medium mb-1">Un conseiller va vous rappeler <span id="success-slot-label" class="text-accent-600 font-bold"></span>.</p>
                            <p class="text-xs text-navy-400 mb-6">Gardez votre téléphone à portée de main.</p>
                            <div class="bg-green-50 border border-green-100 rounded-2xl p-4 mb-6 text-left flex items-start gap-3">
                                <i data-lucide="clock" class="text-green-600 shrink-0 mt-0.5" style="width:18px;height:18px;"></i>
                                <p class="text-sm text-green-700 font-medium">Vous recevrez une confirmation par email dans quelques instants.</p>
                            </div>
                            <button onclick="resetLeadForm()" class="btn btn-outline btn-lg w-full rounded-2xl border-navy-200 hover:bg-navy-50 text-navy-600 flex items-center justify-center gap-2">
                                <i data-lucide="plus-circle" style="width:18px;height:18px;"></i>
                                Faire une nouvelle demande
                            </button>
                        </div>

                        <div id="lead-form-actions" class="flex flex-col gap-4">
                            <div class="flex gap-3">
                                <button onclick="goToLeadStep(3)" class="btn btn-outline flex-1 rounded-2xl border-navy-200 text-navy-500">Modifier</button>
                                <button onclick="submitLead()" id="btn-submit-lead" class="btn btn-primary flex-1 rounded-2xl shadow-lg shadow-brand-400/20 flex items-center justify-center gap-2">
                                    <i data-lucide="send" style="width:18px;height:18px;"></i>
                                    Valider ma demande
                                </button>
                            </div>
                            
                            <div class="space-y-1 mt-2">
                                <p class="text-[11px] text-center text-navy-400 font-medium italic">
                                    En validant, vous acceptez notre <a href="/rappel/public/legal.php#confidentialite" class="text-navy-600 font-bold hover:underline">Politique de confidentialité</a>.
                                </p>
                                <p class="text-[11px] text-center text-navy-400 font-medium italic">
                                    L’utilisation du Service implique l’acceptation des <a href="/rappel/public/legal.php#cgu" class="text-navy-600 font-bold hover:underline">CGU</a>.
                                </p>
                            </div>
                        </div>
                    </div>

                </div><!-- /card -->
            </div><!-- /max-w -->
        </div>
    </section>


    <!-- ===== FEATURES SECTION ===== -->
    <section class="py-24 bg-white" id="features">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-navy-900 mb-4">Pourquoi choisir Rappelez-moi ?</h2>
                <p class="text-slate-500 text-lg">Une plateforme pensée pour votre tranquillité et votre sécurité.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php
                $features = [
                    ['icon'=>'shield-check','title'=>'Zero Spam Garanti','desc'=>"Vos coordonnées restent masquées jusqu'à votre validation. Fini le harcèlement téléphonique."],
                    ['icon'=>'user-check','title'=>'Experts Vérifiés','desc'=>'Chaque professionnel est audité : SIRET, assurance décennale et avis clients contrôlés.'],
                    ['icon'=>'zap','title'=>'Rappel Instantané','desc'=>"Choisissez votre créneau. Le professionnel s'engage à vous contacter à l'heure précise."],
                    ['icon'=>'lock','title'=>'Données Sécurisées','desc'=>'Hébergement en France, conforme RGPD. Vous restez maître de vos informations.'],
                ];
                foreach ($features as $i => $f): ?>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-lg transition-all duration-300 group animate-fade-in-up"
                     style="animation-delay:<?= $i * 0.1 ?>s">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-emerald-500 mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="<?= $f['icon'] ?>" style="width:24px;height:24px;"></i>
                    </div>
                    <h3 class="text-xl font-bold text-navy-900 mb-3"><?= $f['title'] ?></h3>
                    <p class="text-slate-500 leading-relaxed"><?= $f['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/includes/footer.php'; ?>
<!-- Dashboard JS -->
<script src="/rappel/public/assets/js/app.js?v=3.0"></script>
<script>
// ---- Sector Carousel ----
const sectors = [
    { label:'Assurance', icon:'shield', color:'bg-indigo-500', img:'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&q=80&w=800', desc:'Protection optimale pour votre famille et vos biens.' },
    { label:'Rénovation', icon:'hammer', color:'bg-orange-500', img:'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&q=80&w=800', desc:'Transformez votre habitat avec des artisans certifiés.' },
    { label:'Garage', icon:'car', color:'bg-blue-500', img:'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?auto=format&fit=crop&q=80&w=800', desc:'Entretien et réparations par des experts automobiles.' },
    { label:'Télécom', icon:'smartphone', color:'bg-purple-500', img:'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=800', desc:'Forfaits et connexions adaptés à vos besoins.' },
];
let activeSector = 0;
let sectorTimer;

function setActiveSector(index) {
    activeSector = index;
    const s = sectors[index];
    document.getElementById('sector-img').src = s.img;
    document.getElementById('sector-img').alt = s.label;
    document.getElementById('sector-title').textContent = s.label;
    document.getElementById('sector-desc').textContent = s.desc;
    const wrap = document.getElementById('sector-icon-wrap');
    wrap.className = `inline-flex p-4 rounded-2xl mb-6 text-white shadow-2xl backdrop-blur-xl ${s.color}/80 border border-white/20`;
    wrap.innerHTML = `<i data-lucide="${s.icon}" style="width:40px;height:40px;"></i>`;
    document.querySelectorAll('.sector-btn').forEach((btn, i) => {
        if (i === index) {
            btn.className = 'sector-btn flex items-center gap-3 p-4 rounded-3xl transition-all duration-500 border-2 bg-white shadow-premium border-accent-500/20 scale-105';
            btn.querySelector('span').className = 'font-bold text-sm tracking-wide text-navy-950';
        } else {
            btn.className = 'sector-btn flex items-center gap-3 p-4 rounded-3xl transition-all duration-500 border-2 bg-white/20 hover:bg-white/50 border-transparent backdrop-blur-sm';
            btn.querySelector('span').className = 'font-bold text-sm tracking-wide text-navy-400';
        }
    });
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function startSectorTimer() {
    sectorTimer = setInterval(() => {
        setActiveSector((activeSector + 1) % sectors.length);
    }, 5000);
}

// ---- Lead Form Wizard ----
let selectedService = '';
let selectedTimeSlot = '';
let currentLeadStep = 1;

function selectService(id) {
    selectedService = id;
    document.querySelectorAll('.service-btn').forEach(btn => {
        if (btn.dataset.service === id) {
            btn.classList.add('border-accent-400', '!bg-white', 'shadow-xl', 'ring-2', 'ring-accent-200', 'scale-[1.03]');
            btn.classList.remove('border-transparent', 'bg-navy-50/60');
        } else {
            btn.classList.remove('border-accent-400', '!bg-white', 'shadow-xl', 'ring-2', 'ring-accent-200', 'scale-[1.03]');
            btn.classList.add('border-transparent', 'bg-navy-50/60');
        }
    });
    document.getElementById('btn-step1-next').classList.remove('hidden');
}

function selectTimeSlot(id) {
    selectedTimeSlot = id;
    document.querySelectorAll('.slot-btn').forEach(btn => {
        const check = btn.querySelector('.slot-check');
        const dot   = check.querySelector('div');
        if (btn.dataset.slot === id) {
            btn.classList.add('border-accent-400', 'bg-accent-50/40', 'shadow-md');
            btn.classList.remove('border-navy-100', 'bg-white');
            check.classList.add('border-accent-500', 'bg-accent-500');
            check.classList.remove('border-navy-200');
            dot.classList.remove('scale-0');
            dot.classList.add('scale-100');
        } else {
            btn.classList.remove('border-accent-400', 'bg-accent-50/40', 'shadow-md');
            btn.classList.add('border-navy-100', 'bg-white');
            check.classList.remove('border-accent-500', 'bg-accent-500');
            check.classList.add('border-navy-200');
            dot.classList.add('scale-0');
            dot.classList.remove('scale-100');
        }
    });
    document.getElementById('btn-step2-next').disabled = false;
}

function goToLeadStep(step) {
    // Validate Step 3 (Info) -> Step 4 (Confirm)
    if (step === 4) {
        const phone     = document.getElementById('lead-phone').value.trim();
        const email     = document.getElementById('lead-email').value.trim();
        const firstname = document.getElementById('lead-firstname').value.trim();
        const lastname  = document.getElementById('lead-lastname').value.trim();

        if (!firstname || !phone || !email) {
            ['lead-firstname', 'lead-phone', 'lead-email'].forEach(id => {
                const el = document.getElementById(id);
                if (!el.value.trim()) el.classList.add('border-red-300', 'bg-red-50');
                else el.classList.remove('border-red-300', 'bg-red-50');
            });
            return;
        }

        // Build Summary — read label from slot button's first <p>
        const serviceLabel = (document.querySelector(`.service-btn[data-service="${selectedService}"] span`) || {}).innerText || selectedService;
        const slotPrimary  = document.querySelector(`.slot-btn[data-slot="${selectedTimeSlot}"] p`);
        const slotLabel    = slotPrimary ? slotPrimary.innerText : selectedTimeSlot;

        document.getElementById('lead-summary').innerHTML = `
            <div class="flex items-center justify-between border-b border-navy-100 pb-3">
                <span class="text-sm font-bold text-navy-400">Service</span>
                <span class="font-bold text-navy-900 bg-navy-50 px-3 py-1 rounded-lg">${serviceLabel}</span>
            </div>
            <div class="flex items-center justify-between border-b border-navy-100 pb-3">
                <span class="text-sm font-bold text-navy-400">Créneau</span>
                <span class="font-bold text-navy-900 bg-navy-50 px-3 py-1 rounded-lg">${slotLabel}</span>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-1">
                <div>
                    <span class="block text-xs font-bold text-navy-400 uppercase">Contact</span>
                    <span class="font-bold text-navy-900">${firstname} ${lastname}</span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-navy-400 uppercase">Email</span>
                    <span class="font-bold text-navy-900 truncate">${email}</span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-navy-400 uppercase">Téléphone</span>
                    <span class="font-bold text-navy-900">${phone}</span>
                </div>
            </div>
        `;
    }

    currentLeadStep = step;

    // Progress bar: 0% at step 1, 100% at step 4
    const progressBar = document.getElementById('step-progress-bar');
    if (progressBar) {
        const pct = ((step - 1) / 3) * 100;
        progressBar.style.width = pct + '%';
    }

    [1,2,3,4].forEach(s => {
        const container = document.getElementById(`lead-step-${s}`);
        if (container) container.classList.toggle('hidden', s !== step);

        const dot   = document.getElementById(`step-dot-${s}`);
        const label = document.getElementById(`step-label-${s}`);
        if (!dot || !label) return;

        const icons = ['layers','clock','user','check-circle'];

        if (s < step) {
            // Completed
            dot.className = 'w-10 h-10 rounded-full bg-accent-500 text-white flex items-center justify-center shadow-md transition-all duration-400';
            dot.innerHTML = '<i data-lucide="check" style="width:18px;height:18px;"></i>';
            label.className = 'text-[11px] font-bold text-accent-600 transition-colors duration-300';
        } else if (s === step) {
            // Active
            dot.className = 'w-10 h-10 rounded-full bg-brand-600 border-2 border-brand-600 text-white flex items-center justify-center shadow-lg shadow-brand-200 scale-110 transition-all duration-400';
            dot.innerHTML = `<i data-lucide="${icons[s-1]}" style="width:18px;height:18px;"></i>`;
            label.className = 'text-[11px] font-bold text-brand-600 transition-colors duration-300';
        } else {
            // Future
            dot.className = 'w-10 h-10 rounded-full bg-white border-2 border-navy-100 text-navy-300 flex items-center justify-center shadow-sm transition-all duration-400';
            dot.innerHTML = `<i data-lucide="${icons[s-1]}" style="width:18px;height:18px;"></i>`;
            label.className = 'text-[11px] font-bold text-navy-300 transition-colors duration-300';
        }
    });

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

async function submitLead() {
    const btn = document.getElementById('btn-submit-lead');
    const errEl = document.querySelector('#lead-error span');
    const errContainer = document.getElementById('lead-error');

    const checkbox = document.getElementById('consent-checkbox');
    if (checkbox && !checkbox.checked) {
        errEl.textContent = 'Veuillez accepter d\'être contacté pour valider votre demande.';
        errContainer.classList.remove('hidden');
        checkbox.closest('div').parentElement.classList.add('animate-shake', 'text-red-500');
        setTimeout(() => {
            checkbox.closest('div').parentElement.classList.remove('animate-shake', 'text-red-500');
        }, 1000);
        return;
    }

    errContainer.classList.add('hidden');
    setButtonLoading(btn, true);

    const payload = {
        service_type: selectedService,
        time_slot: selectedTimeSlot,
        first_name: document.getElementById('lead-firstname').value.trim(),
        last_name: document.getElementById('lead-lastname').value.trim(),
        phone: document.getElementById('lead-phone').value.trim(),
        email: document.getElementById('lead-email').value.trim(),
        zip_code: document.getElementById('lead-zip').value.trim(),
    };

    try {
        await apiFetch('/leads', { method: 'POST', body: JSON.stringify(payload) });

        // Hide form elements (NOT their parent — that would hide lead-success too!)
        document.getElementById('lead-summary').classList.add('hidden');
        document.getElementById('lead-form-actions').classList.add('hidden');
        // Hide the privacy notice (sibling of lead-summary)
        const privacyNotice = document.querySelector('#lead-step-4 .bg-accent-50\\/80');
        if (privacyNotice) privacyNotice.classList.add('hidden');

        // Show the recap title hidden
        const recapTitle = document.querySelector('#lead-step-4 h3');
        if (recapTitle) recapTitle.classList.add('hidden');

        // Show success with slot label
        const slotEl = document.querySelector(`.slot-btn[data-slot="${selectedTimeSlot}"] span`);
        const slotLabel = slotEl ? slotEl.innerText.toLowerCase() : 'dans le créneau choisi';
        const slotSpan = document.getElementById('success-slot-label');
        if (slotSpan) slotSpan.textContent = slotLabel;

        const successEl = document.getElementById('lead-success');
        successEl.classList.remove('hidden');

        // Recreate icons for the newly visible elements
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Smooth scroll to success message
        successEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Mark all step dots as complete
        [1,2,3,4].forEach(s => {
            const dot = document.getElementById(`step-dot-${s}`);
            if (dot) {
                dot.className = 'w-8 h-8 rounded-full bg-accent-500 text-white flex items-center justify-center text-sm font-bold shadow-sm transition-all';
                dot.innerHTML = '<i data-lucide="check" style="width:16px;height:16px;"></i>';
            }
        });
        if (typeof lucide !== 'undefined') lucide.createIcons();

    } catch (err) {
        errEl.textContent = err.message || 'Une erreur est survenue.';
        errContainer.classList.remove('hidden');
        setButtonLoading(btn, false, 'Valider ma demande');
    }
}

function resetLeadForm() {
    // Reset state
    selectedService = '';
    selectedTimeSlot = '';

    // Reset inputs
    ['lead-firstname','lead-lastname','lead-phone','lead-email','lead-zip'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.value = ''; el.classList.remove('border-red-300','bg-red-50'); }
    });

    // Reset service buttons
    document.querySelectorAll('.service-btn').forEach(btn => {
        btn.classList.remove('border-accent-500','bg-accent-50/50','ring-2','ring-accent-200');
        btn.classList.add('border-navy-50','bg-navy-50/50','hover:bg-white');
    });
    document.getElementById('btn-step1-next').classList.add('hidden');

    // Reset slot buttons
    document.querySelectorAll('.slot-btn').forEach(btn => {
        btn.classList.remove('border-accent-500','bg-accent-50/30','ring-1','ring-accent-200');
        btn.classList.add('border-navy-50','bg-white');
        const check = btn.querySelector('.slot-check');
        if (check) {
            check.classList.remove('border-accent-500');
            check.classList.add('border-navy-200');
            const dot = check.querySelector('div');
            if (dot) dot.classList.add('hidden');
        }
    });
    document.getElementById('btn-step2-next').disabled = true;

    // Restore hidden elements
    document.getElementById('lead-summary').classList.remove('hidden');
    document.getElementById('lead-form-actions').classList.remove('hidden');
    const privacyNotice = document.querySelector('#lead-step-4 .bg-accent-50\\/80');
    if (privacyNotice) privacyNotice.classList.remove('hidden');
    const recapTitle = document.querySelector('#lead-step-4 h3');
    if (recapTitle) recapTitle.classList.remove('hidden');

    // Hide success panel
    document.getElementById('lead-success').classList.add('hidden');
    document.getElementById('lead-error').classList.add('hidden');

    // Go back to step 1
    goToLeadStep(1);
    document.getElementById('demande').scrollIntoView({ behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', () => {
    startSectorTimer();
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
</body>
</html>
