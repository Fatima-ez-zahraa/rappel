<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Comment ça marche';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
    <style>
        .step-card {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .step-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
        }
        .guide-link.active {
            background: var(--brand-700);
            color: white;
            box-shadow: 0 4px 12px rgba(25, 53, 123, 0.2);
        }
    </style>
</head>
<body class="bg-[#D3D3D3] font-sans antialiased overflow-x-hidden min-h-screen">

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="min-h-screen pt-32 pb-24">
    <section class="container mx-auto px-6 lg:px-12">
        <div class="max-w-6xl mx-auto">
            <!-- Hero Section -->
            <header class="text-center mb-16 animate-fade-in-up">
               
                <h1 class="text-5xl lg:text-7xl font-display font-black text-navy-950 tracking-tight leading-tight mb-8">
                    Simplifions la <span class="text-gradient">Mise en Relation</span>
                </h1>
                <p class="text-navy-600 text-xl font-medium max-w-3xl mx-auto leading-relaxed">
                    Une plateforme conçue pour la nouvelle législation de 2026. 
                    Redonnez le pouvoir aux particuliers et offrez aux pros une conformité totale.
                </p>
            </header>

            <div class="grid lg:grid-cols-[280px,1fr] gap-12 items-start">
                <!-- Navigation -->
                <aside class="hidden lg:block sticky top-32">
                    <div class="glass-card rounded-3xl p-6 border border-white/60">
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-navy-400 mb-6 px-2">Navigation</p>
                        <nav id="guide-nav" class="space-y-3">
                            <a href="#intro" class="guide-link flex items-center gap-3 px-4 py-3 rounded-2xl text-navy-600 font-bold transition-all hover:bg-white/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-navy-200"></span>
                                Introduction
                            </a>
                            <a href="#particuliers" class="guide-link flex items-center gap-3 px-4 py-3 rounded-2xl text-navy-600 font-bold transition-all hover:bg-white/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-navy-200"></span>
                                Pour Particuliers
                            </a>
                            <a href="#pros" class="guide-link flex items-center gap-3 px-4 py-3 rounded-2xl text-navy-600 font-bold transition-all hover:bg-white/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-navy-200"></span>
                                Pour Professionnels
                            </a>
                            <a href="#avantages" class="guide-link flex items-center gap-3 px-4 py-3 rounded-2xl text-navy-600 font-bold transition-all hover:bg-white/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-navy-200"></span>
                                Vos Avantages
                            </a>
                        </nav>
                    </div>
                </aside>

                <!-- Content Area -->
                <div class="space-y-24">
                    <!-- Intro -->
                    <section id="intro" class="scroll-mt-32">
                        <div class="glass-card rounded-[2.5rem] p-10 lg:p-14 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-accent-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50"></div>
                            <h2 class="text-3xl font-display font-black text-navy-950 mb-8 flex items-center gap-4">
                                <span class="w-12 h-1 rounded-full bg-accent-500"></span>
                                Notre ADN
                            </h2>
                            <div class="prose prose-lg max-w-none text-navy-600">
                                <p class="mb-6 leading-relaxed">
                                    <strong class="text-brand-900">Rappelez-moi</strong> est une plateforme de mise en relation de confiance conçue pour répondre aux enjeux de la nouvelle législation française de juillet 2026.
                                </p>
                                <p class="leading-relaxed">
                                    Alors que la prospection téléphonique non sollicitée est désormais interdite, notre service permet aux particuliers de reprendre le contrôle sur leurs besoins et aux professionnels de contacter légalement des prospects ayant exprimé un consentement explicite.
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Particuliers -->
                    <section id="particuliers" class="scroll-mt-32">
                        <div class="flex items-center gap-4 mb-12">
                            <div class="w-14 h-14 rounded-2xl bg-accent-500 flex items-center justify-center text-white shadow-lg shadow-accent-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h2 class="text-4xl font-display font-black text-navy-950 tracking-tight">Le Parcours Particulier</h2>
                        </div>
                        
                        <div class="grid gap-6">
                            <?php 
                            $steps_particuliers = [
                                ['01', 'Expression du besoin', 'Sélectionnez votre catégorie (rénovation, auto, assurance, etc.) et remplissez un formulaire simplifié.', 'bg-accent-50 text-accent-700'],
                                ['02', 'Disponibilité', 'Indiquez vos créneaux idéaux pour ne jamais être dérangé à un moment inopportun.', 'bg-white border-accent-100 text-navy-900'],
                                ['03', 'Consentement (Opt-in)', 'Cochez la case de consentement explicite pour autoriser le rappel par nos partenaires.', 'bg-white border-accent-100 text-navy-900'],
                                ['04', 'Suivi de la demande', 'Recevez un récapitulatif par email ou SMS confirmant que votre demande est transmise. Il n\'y a plus qu\'à attendre l\'appel de l\'expert qualifié sans multiplier les recherches.', 'bg-accent-50 text-accent-700']
                            ];
                            foreach($steps_particuliers as $step): ?>
                            <div class="step-card group flex flex-col md:flex-row gap-6 p-8 rounded-[2rem] bg-white border border-brand-50 shadow-sm hover:border-accent-200">
                                <div class="flex-shrink-0 w-16 h-16 rounded-2xl font-black text-2xl flex items-center justify-center <?= $step[3] ?>">
                                    <?= $step[0] ?>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-navy-950 mb-3"><?= $step[1] ?></h3>
                                    <p class="text-navy-500 font-medium leading-relaxed"><?= $step[2] ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Pros -->
                    <section id="pros" class="scroll-mt-32">
                        <div class="flex items-center gap-4 mb-12">
                            <div class="w-14 h-14 rounded-2xl bg-brand-600 flex items-center justify-center text-white shadow-lg shadow-brand-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h2 class="text-4xl font-display font-black text-navy-950 tracking-tight">Le Parcours Professionnel</h2>
                        </div>
                        
                        <div class="grid gap-6">
                            <?php 
                            $steps_pros = [
                                ['01', 'Consultation', 'Accédez à un flux de demandes géolocalisées et segmentées par métier.', 'bg-brand-50 text-brand-700'],
                                ['02', 'Consentement horodaté', 'Débloquez les coordonnées et recevez un certificat de consentement pour chaque appel.', 'bg-white border-brand-100 text-navy-900'],
                                ['03', 'Rappel Conforme', 'Effectuez le rappel sur le créneau indiqué. Un prospect chaud garantit une meilleure conversion.', 'bg-brand-900 text-white']
                            ];
                            foreach($steps_pros as $step): ?>
                            <div class="step-card group flex flex-col md:flex-row gap-6 p-8 rounded-[2rem] bg-white border border-brand-50 shadow-sm hover:border-brand-200">
                                <div class="flex-shrink-0 w-16 h-16 rounded-2xl font-black text-2xl flex items-center justify-center <?= $step[3] ?>">
                                    <?= $step[0] ?>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-navy-950 mb-3"><?= $step[1] ?></h3>
                                    <p class="text-navy-500 font-medium leading-relaxed"><?= $step[2] ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Avantages -->
                    <section id="avantages" class="scroll-mt-32">
                        <h2 class="text-4xl font-display font-black text-navy-950 text-center mb-16">Pourquoi choisir Rappelez-moi ?</h2>
                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="glass-card p-10 rounded-[3rem] border border-accent-100 bg-accent-50/20">
                                <div class="w-12 h-12 rounded-xl bg-accent-100 text-accent-600 flex items-center justify-center mb-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-black text-navy-950 mb-4">Pour le particulier</h3>
                                <p class="text-navy-600 font-medium leading-relaxed">
                                    Service totalement gratuit. Finis les appels intrusifs et les arnaques. Vous ne parlez qu'aux entreprises que vous avez qualifiées.
                                </p>
                            </div>

                            <div class="glass-card p-10 rounded-[3rem] border border-brand-100 bg-brand-50/20">
                                <div class="w-12 h-12 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center mb-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-black text-navy-950 mb-4">Pour le professionnel</h3>
                                <p class="text-navy-600 font-medium leading-relaxed">
                                    Bouclier juridique total face à la loi 2026. Un flux constant de leads qualifiés (prospects chauds) ayant consenti explicitement à l'échange.
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- CTA -->
                    <section class="relative">
                        <div class="rounded-[3rem] p-12 lg:p-20 bg-navy-950 overflow-hidden relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-brand-800/50 to-transparent"></div>
                            <div class="relative z-10 text-center max-w-4xl mx-auto">
                                <h2 class="text-4xl lg:text-6xl font-display font-black text-white mb-8">Prêt à reprendre le contrôle ?</h2>
                                <p class="text-brand-100 text-xl font-medium mb-12">Choisissez votre parcours et connectez-vous dès maintenant.</p>
                                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                                    <a href="/rappel/public/#demande" class="btn btn-accent btn-xl w-full sm:w-auto shadow-2xl shadow-accent-950/20">Déposer un besoin</a>
                                    <a href="/rappel/public/pro/" class="btn bg-white/10 text-white border border-white/20 hover:bg-white/20 btn-xl w-full sm:w-auto backdrop-blur-sm">Je suis un Expert</a>
                                </div>
                            </div>
                            <!-- Background blobs for CTA -->
                            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-accent-500/20 rounded-full blur-[100px]"></div>
                            <div class="absolute -top-24 -right-24 w-64 h-64 bg-brand-500/20 rounded-full blur-[100px]"></div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="/rappel/public/assets/js/app.js?v=3.1"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const links = Array.from(document.querySelectorAll('.guide-link'));
    const sections = links
        .map(link => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

    const setActive = (id) => {
        links.forEach(link => {
            const active = link.getAttribute('href') === `#${id}`;
            link.classList.toggle('active', active);
        });
    };

    const observer = new IntersectionObserver((entries) => {
        const visible = entries
            .filter(e => e.isIntersecting)
            .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
        if (visible) setActive(visible.target.id);
    }, { rootMargin: '-25% 0px -60% 0px', threshold: [0.1, 0.3, 0.5] });

    sections.forEach(section => observer.observe(section));
});
</script>
</body>
</html>

