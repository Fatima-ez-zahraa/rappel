<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Mentions Légales & Politiques';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
    <style>
        .legal-section { scroll-margin-top: 120px; }
        .sidebar-link.active {
            background: rgba(124, 203, 99, 0.1);
            color: #7CCB63;
            border-color: rgba(124, 203, 99, 0.3);
            font-weight: 700;
        }
        .sidebar-link.active i { transform: scale(1.1); color: #7CCB63; }
        #back-to-top.visible { opacity: 1; transform: translateY(0); pointer-events: auto; }
    </style>
</head>
<body class="bg-[#D3D3D3] font-sans antialiased overflow-x-hidden min-h-screen">

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="pt-32 pb-24">
    <div class="container mx-auto px-6 lg:px-12">
        
        <!-- Header Section -->
        <div class="max-w-4xl mx-auto text-center mb-16 animate-fade-in-up">
            <h1 class="text-5xl lg:text-7xl font-display font-black text-navy-950 mb-6 tracking-tight">
                Informations <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-600">Légales</span>
            </h1>
            <p class="text-xl text-navy-600/80 font-medium max-w-2xl mx-auto leading-relaxed">
                Transparence et protection. Retrouvez l'ensemble des règles régissant notre plateforme et vos données.
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 mt-12">
            
            <!-- Sidebar Navigation (Sticky) -->
            <aside class="lg:w-1/4 animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="sticky top-32 space-y-4">
                    <nav class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/80 shadow-premium p-4 flex flex-col gap-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-navy-400 px-4 py-2 border-b border-navy-50 mb-2">Sommaire</p>
                        
                        <a href="#cookies" class="sidebar-link group flex items-center gap-4 px-4 py-3.5 rounded-2xl border border-transparent hover:bg-white hover:shadow-sm transition-all duration-300 text-navy-700 font-bold" data-target="cookies">
                            <i data-lucide="cookie" class="w-5 h-5 text-navy-300 group-hover:text-accent-500 transition-colors"></i>
                            <span class="text-sm">Politique Cookies</span>
                        </a>
                        
                        <a href="#confidentialite" class="sidebar-link group flex items-center gap-4 px-4 py-3.5 rounded-2xl border border-transparent hover:bg-white hover:shadow-sm transition-all duration-300 text-navy-700 font-bold" data-target="confidentialite">
                            <i data-lucide="shield" class="w-5 h-5 text-navy-300 group-hover:text-indigo-500 transition-colors"></i>
                            <span class="text-sm">POLITIQUE DE CONFIDENTIALITÉ</span>
                        </a>
                        
                        <a href="#cgu" class="sidebar-link group flex items-center gap-4 px-4 py-3.5 rounded-2xl border border-transparent hover:bg-white hover:shadow-sm transition-all duration-300 text-navy-700 font-bold" data-target="cgu">
                            <i data-lucide="file-text" class="w-5 h-5 text-navy-300 group-hover:text-emerald-500 transition-colors"></i>
                            <span class="text-sm">CONDITIONS GÉNÉRALES D’UTILISATION</span>
                        </a>
                    </nav>
                    
                
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="lg:w-3/4 space-y-12 h-fit">
                
                <!-- SECTION : POLITIQUE COOKIES -->
                <section id="cookies" class="legal-section bg-white/80 backdrop-blur-xl rounded-[2.5rem] border border-white/80 shadow-premium p-10 lg:p-14 animate-fade-in-up" style="animation-delay: 0.2s">
                    <div class="flex items-center gap-6 mb-10 pb-6 border-b border-navy-50">
                        <div class="w-16 h-16 bg-accent-100 rounded-[1.5rem] flex items-center justify-center text-accent-600 shadow-inner">
                            <i data-lucide="cookie" style="width:32px;height:32px;"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-display font-black text-navy-950 tracking-tight">Politique Cookies</h2>
                            <p class="text-navy-400 text-xs font-bold uppercase tracking-[0.2em] mt-2">Dernière mise à jour : 18 Février 2026</p>
                        </div>
                    </div>
                    
                    <div class="prose prose-navy max-w-none space-y-10 text-navy-900 leading-relaxed">
                        <div class="bg-navy-50/30 p-8 rounded-3xl border border-navy-100/50">
                            <h3 class="text-xl font-bold mb-4 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-navy-950 text-white flex items-center justify-center text-sm font-black">1</span>
                                Définition
                            </h3>
                            <p class="text-lg">Le Service utilise des cookies nécessaires à son fonctionnement et, avec votre consentement, des cookies de mesure d’audience.</p>
                        </div>

                        <div>
                            <h3 class="text-xl font-black mb-6 text-navy-950">2. Finalités détaillées</h3>
                            <div class="grid md:grid-cols-3 gap-6">
                                <div class="p-6 bg-white rounded-2xl border border-navy-100 shadow-sm">
                                    <div class="text-accent-600 mb-3"><i data-lucide="settings" class="w-6 h-6"></i></div>
                                    <h4 class="font-bold text-sm mb-2">Technique</h4>
                                    <p class="text-xs text-navy-500">Essentiels pour la navigation et la sécurité du formulaire.</p>
                                </div>
                                <div class="p-6 bg-white rounded-2xl border border-navy-100 shadow-sm">
                                    <div class="text-blue-500 mb-3"><i data-lucide="bar-chart" class="w-6 h-6"></i></div>
                                    <h4 class="font-bold text-sm mb-2">Audience</h4>
                                    <p class="text-xs text-navy-500">Statistiques anonymes via Google Analytics (si accepté).</p>
                                </div>
                                <div class="p-6 bg-white rounded-2xl border border-navy-100 shadow-sm">
                                    <div class="text-teal-500 mb-3"><i data-lucide="zap" class="w-6 h-6"></i></div>
                                    <h4 class="font-bold text-sm mb-2">Amélioration</h4>
                                    <p class="text-xs text-navy-500">Personnalisation de votre expérience utilisateur.</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-navy-950 text-white flex items-center justify-center text-sm font-black">3</span>
                                Gestion de vos préférences
                            </h3>
                            <p>Les cookies non nécessaires nécessitent votre consentement préalable express. Vous pouvez à tout moment retirer votre consentement ou modifier vos choix via le centre de gestion accessible depuis le bas de chaque page du site.</p>
                            <button onclick="localStorage.removeItem('cookie_consent'); location.reload();" class="btn btn-outline btn-sm rounded-xl px-6 flex items-center gap-2 text-navy-600">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                Réinitialiser mon choix cookies
                            </button>
                        </div>
                    </div>
                </section>

                <!-- SECTION : POLITIQUE DE CONFIDENTIALITÉ -->
                <section id="confidentialite" class="legal-section bg-white/80 backdrop-blur-xl rounded-[2.5rem] border border-white/80 shadow-premium p-10 lg:p-14 animate-fade-in-up" style="animation-delay: 0.3s">
                    <div class="flex items-center gap-6 mb-10 pb-6 border-b border-navy-50">
                        <div class="w-16 h-16 bg-indigo-100 rounded-[1.5rem] flex items-center justify-center text-indigo-600 shadow-inner">
                            <i data-lucide="shield" style="width:32px;height:32px;"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-display font-black text-navy-950 tracking-tight">POLITIQUE DE CONFIDENTIALITÉ</h2>
                            <p class="text-navy-400 text-xs font-bold uppercase tracking-[0.2em] mt-2">Dernière mise à jour : 18 Février 2026</p>
                        </div>
                    </div>

                    <div class="prose prose-navy max-w-none space-y-10 text-navy-900 leading-relaxed">
                        <div class="space-y-6">
                            <h3 class="text-xl font-bold flex items-center gap-3">1. Responsable du traitement</h3>
                            <p>Le service <strong>rappellez-moi.co</strong> est édité par <strong>YLN DEVELOPPEMENT SAS</strong>. Nous nous engageons à protéger vos données personnelles conformément au Règlement Général sur la Protection des Données (RGPD).</p>
                        </div>

                        <div class="bg-indigo-50/30 p-8 rounded-3xl border border-indigo-100/50 grid md:grid-cols-2 gap-10">
                            <div>
                                <h4 class="text-sm font-bold uppercase text-indigo-600 mb-4 tracking-widest">Données collectées</h4>
                                <ul class="space-y-3">
                                    <li class="flex items-center gap-3 text-sm font-bold"><i data-lucide="check" class="w-4 h-4 text-emerald-500"></i> Identité (Nom, Prénom)</li>
                                    <li class="flex items-center gap-3 text-sm font-bold"><i data-lucide="check" class="w-4 h-4 text-emerald-500"></i> Contact (Tel, Email, CP)</li>
                                    <li class="flex items-center gap-3 text-sm font-bold"><i data-lucide="check" class="w-4 h-4 text-emerald-500"></i> Métadonnées (IP, Date)</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold uppercase text-indigo-600 mb-4 tracking-widest">Base Légale</h4>
                                <p class="text-sm leading-relaxed font-medium">Le traitement repose sur l'<strong>intérêt légitime</strong> de répondre à votre demande et le <strong>consentement</strong> explicite recueilli lors de l'envoi du formulaire.</p>
                                <p class="text-sm">L’utilisation du Service implique l’acceptation des CONDITIONS GÉNÉRALES D’UTILISATION. En validant, vous acceptez notre POLITIQUE DE CONFIDENTIALITÉ.</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold">2. Vos droits</h3>
                            <p>En vertu de la loi « Informatique et Libertés » et du RGPD, vous disposez des droits suivants sur vos données :</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="p-4 rounded-2xl bg-white border border-navy-100 text-center">
                                    <div class="text-indigo-500 mb-2 mx-auto"><i data-lucide="eye" class="w-5 h-5 mx-auto"></i></div>
                                    <span class="text-[10px] font-black uppercase">Accès</span>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border border-navy-100 text-center">
                                    <div class="text-emerald-500 mb-2 mx-auto"><i data-lucide="edit-3" class="w-5 h-5 mx-auto"></i></div>
                                    <span class="text-[10px] font-black uppercase">Rectif.</span>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border border-navy-100 text-center">
                                    <div class="text-red-500 mb-2 mx-auto"><i data-lucide="trash-2" class="w-5 h-5 mx-auto"></i></div>
                                    <span class="text-[10px] font-black uppercase">Oubli</span>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border border-navy-100 text-center">
                                    <div class="text-orange-500 mb-2 mx-auto"><i data-lucide="lock" class="w-5 h-5 mx-auto"></i></div>
                                    <span class="text-[10px] font-black uppercase">Limit.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- SECTION : CGU -->
                <section id="cgu" class="legal-section bg-white/80 backdrop-blur-xl rounded-[2.5rem] border border-white/80 shadow-premium p-10 lg:p-14 animate-fade-in-up" style="animation-delay: 0.4s">
                    <div class="flex items-center gap-6 mb-10 pb-6 border-b border-navy-50">
                        <div class="w-16 h-16 bg-emerald-100 rounded-[1.5rem] flex items-center justify-center text-emerald-600 shadow-inner">
                            <i data-lucide="file-text" style="width:32px;height:32px;"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-display font-black text-navy-950 tracking-tight">CONDITIONS GÉNÉRALES D’UTILISATION</h2>
                            <p class="text-navy-400 text-xs font-bold uppercase tracking-[0.2em] mt-2">Dernière mise à jour : 18 Février 2026</p>
                        </div>
                    </div>

                    <div class="prose prose-navy max-w-none space-y-10 text-navy-900 leading-relaxed">
                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4">Article 1 - Objet</h3>
                            <p>Les présentes CONDITIONS GÉNÉRALES D’UTILISATION régissent l'utilisation de la plateforme <strong>rappelez-moi.co</strong>. Elles ont pour objet de définir les modalités de mise en relation entre les utilisateurs et les professionnels.</p>
                        </div>
                        
                        <div class="p-8 bg-emerald-50/30 rounded-3xl border border-emerald-100/50">
                            <h3 class="text-xl font-bold mb-4">Article 2 - Responsabilité</h3>
                            <p><strong>Rappelez-moi.co</strong> agit en qualité d'intermédiaire. À ce titre, nous ne sommes pas responsables de la qualité, du prix ou du retard d'exécution des services fournis par nos partenaires professionnels. Le contrat de prestation de service intervient exclusivement entre l'utilisateur et le professionnel désigné.</p>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4">Article 3 - Propriété Intellectuelle</h3>
                            <p>L’intégralité des contenus du Service (logos, textes, graphisme) est protégée par le droit d'auteur. Toute reproduction sans autorisation est interdite.</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

<!-- Back to Top Button -->
<button id="back-to-top" onclick="window.scrollTo({top:0, behavior:'smooth'})" class="fixed bottom-10 left-10 w-12 h-12 bg-white/80 backdrop-blur-xl border border-navy-100 rounded-2xl shadow-premium flex items-center justify-center text-navy-950 opacity-0 transition-all duration-300 pointer-events-none z-50 hover:bg-white hover:-translate-y-1">
    <i data-lucide="chevron-up" class="w-6 h-6"></i>
</button>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="/rappel/public/assets/js/app.js?v=2.0"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // --- Scrollspy ---
        const sections = document.querySelectorAll('.legal-section');
        const navLinks = document.querySelectorAll('.sidebar-link');
        
        const observerOptions = {
            root: null,
            rootMargin: '-150px 0px -60% 0px',
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.getAttribute('id');
                    updateActiveLink(id);
                }
            });
        }, observerOptions);

        sections.forEach(section => observer.observe(section));

        function updateActiveLink(id) {
            navLinks.forEach(link => {
                const target = link.getAttribute('data-target');
                link.classList.toggle('active', target === id);
            });
        }

        // --- Back to Top ---
        const btt = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                btt.classList.add('visible');
            } else {
                btt.classList.remove('visible');
            }
        });

        // Smooth scroll for nav links
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('data-target');
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                    // Update URL without jump
                    history.pushState(null, null, `#${targetId}`);
                }
            });
        });
    });
</script>
</body>
</html>
