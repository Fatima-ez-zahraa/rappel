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
                Transparence et protection. Retrouvez l'ensemble des régles réagissant notre plateforme et vos données.
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

                    <div class="prose prose-navy max-w-none space-y-8 text-navy-900 leading-relaxed">
                        <div class="rounded-3xl border border-indigo-100 bg-indigo-50/40 p-6 lg:p-8 not-prose">
                            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-indigo-500 mb-4">Aperçu Rapide</p>
                            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <div class="rounded-2xl bg-white border border-indigo-100 px-4 py-3 text-sm font-semibold text-navy-800">Responsable: YLN DEVELOPPEMENT SAS</div>
                                <div class="rounded-2xl bg-white border border-indigo-100 px-4 py-3 text-sm font-semibold text-navy-800">Partage: 3 à 5 partenaires</div>
                                <div class="rounded-2xl bg-white border border-indigo-100 px-4 py-3 text-sm font-semibold text-navy-800">Conservation limitée</div>
                                <div class="rounded-2xl bg-white border border-indigo-100 px-4 py-3 text-sm font-semibold text-navy-800">Droits RGPD garantis</div>
                            </div>
                        </div>

                        <div class="space-y-6 rounded-3xl border border-navy-100 bg-white p-6 lg:p-8">
                            <h3 class="text-xl font-bold mb-2">1. Responsable du traitement et définition du Service</h3>
                            <p>Le service <strong>rappelez-moi.co</strong> (ci-après le « Service ») est édité par <strong>YLN DEVELOPPEMENT SAS</strong>, société par actions simplifiée immatriculée au RCS de Versailles sous le numéro 840 321 384.</p>
                            <p>Le Service agit comme responsable du traitement des données collectées via ses formulaires.</p>
                        </div>

                        <div class="space-y-6 rounded-3xl border border-navy-100 bg-white p-6 lg:p-8">
                            <h3 class="text-xl font-bold mb-2">2. Données collectées</h3>
                            <p>Le Service peut collecter :</p>
                            <div class="not-prose grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <div class="rounded-xl border border-navy-100 bg-navy-50/40 px-4 py-2 text-sm font-semibold text-navy-700">nom</div>
                                <div class="rounded-xl border border-navy-100 bg-navy-50/40 px-4 py-2 text-sm font-semibold text-navy-700">prénom</div>
                                <div class="rounded-xl border border-navy-100 bg-navy-50/40 px-4 py-2 text-sm font-semibold text-navy-700">téléphone</div>
                                <div class="rounded-xl border border-navy-100 bg-navy-50/40 px-4 py-2 text-sm font-semibold text-navy-700">email</div>
                                <div class="rounded-xl border border-navy-100 bg-navy-50/40 px-4 py-2 text-sm font-semibold text-navy-700">adresse</div>
                                <div class="rounded-xl border border-navy-100 bg-navy-50/40 px-4 py-2 text-sm font-semibold text-navy-700">contenu de la demande</div>
                                <div class="rounded-xl border border-navy-100 bg-navy-50/40 px-4 py-2 text-sm font-semibold text-navy-700">adresse IP</div>
                                <div class="rounded-xl border border-navy-100 bg-navy-50/40 px-4 py-2 text-sm font-semibold text-navy-700">horodatage</div>
                                <div class="rounded-xl border border-navy-100 bg-navy-50/40 px-4 py-2 text-sm font-semibold text-navy-700">preuve du consentement</div>
                            </div>
                        </div>

                        <div class="grid lg:grid-cols-2 gap-6 not-prose">
                            <div class="rounded-3xl border border-navy-100 bg-white p-6 lg:p-8">
                                <h3 class="text-xl font-bold mb-4 text-navy-900">3. Finalité</h3>
                                <ul class="space-y-3">
                                    <li class="flex items-start gap-3 text-sm font-medium text-navy-700"><i data-lucide="check-circle-2" class="w-4 h-4 mt-0.5 text-emerald-500"></i><span>permettre la mise en relation</span></li>
                                    <li class="flex items-start gap-3 text-sm font-medium text-navy-700"><i data-lucide="check-circle-2" class="w-4 h-4 mt-0.5 text-emerald-500"></i><span>transmettre la demande aux partenaires</span></li>
                                    <li class="flex items-start gap-3 text-sm font-medium text-navy-700"><i data-lucide="check-circle-2" class="w-4 h-4 mt-0.5 text-emerald-500"></i><span>assurer la conformité légale</span></li>
                                    <li class="flex items-start gap-3 text-sm font-medium text-navy-700"><i data-lucide="check-circle-2" class="w-4 h-4 mt-0.5 text-emerald-500"></i><span>sécuriser le Service</span></li>
                                </ul>
                            </div>
                            <div class="rounded-3xl border border-navy-100 bg-white p-6 lg:p-8">
                                <h3 class="text-xl font-bold mb-4 text-navy-900">4. Base légale</h3>
                                <ul class="space-y-3">
                                    <li class="flex items-start gap-3 text-sm font-medium text-navy-700"><i data-lucide="scale" class="w-4 h-4 mt-0.5 text-indigo-500"></i><span>le consentement</span></li>
                                    <li class="flex items-start gap-3 text-sm font-medium text-navy-700"><i data-lucide="scale" class="w-4 h-4 mt-0.5 text-indigo-500"></i><span>l’exécution du Service</span></li>
                                    <li class="flex items-start gap-3 text-sm font-medium text-navy-700"><i data-lucide="scale" class="w-4 h-4 mt-0.5 text-indigo-500"></i><span>l’intérêt légitime</span></li>
                                    <li class="flex items-start gap-3 text-sm font-medium text-navy-700"><i data-lucide="scale" class="w-4 h-4 mt-0.5 text-indigo-500"></i><span>les obligations légales</span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="space-y-6 rounded-3xl border border-navy-100 bg-white p-6 lg:p-8">
                            <h3 class="text-xl font-bold mb-2">5. Transmission aux partenaires</h3>
                            <p>Les données peuvent être transmises aux partenaires concernés, dans la limite de 3 à 5 partenaires selon votre choix.</p>
                            <p>Ces partenaires deviennent responsables indépendants du traitement.</p>
                        </div>

                        <div class="space-y-6 rounded-3xl border border-navy-100 bg-white p-6 lg:p-8">
                            <h3 class="text-xl font-bold mb-2">6. Conservation</h3>
                            <p>Les données sont conservées pendant une durée limitée et conforme aux obligations légales.</p>
                        </div>

                        <div class="space-y-6 rounded-3xl border border-navy-100 bg-white p-6 lg:p-8">
                            <h3 class="text-xl font-bold mb-2">7. Droits des utilisateurs</h3>
                            <p>L’utilisateur dispose des droits suivants :</p>
                            <div class="not-prose grid grid-cols-2 md:grid-cols-5 gap-3">
                                <div class="rounded-xl border border-indigo-100 bg-indigo-50/40 p-3 text-center text-xs font-black uppercase tracking-wide text-indigo-700">accès</div>
                                <div class="rounded-xl border border-indigo-100 bg-indigo-50/40 p-3 text-center text-xs font-black uppercase tracking-wide text-indigo-700">rectification</div>
                                <div class="rounded-xl border border-indigo-100 bg-indigo-50/40 p-3 text-center text-xs font-black uppercase tracking-wide text-indigo-700">suppression</div>
                                <div class="rounded-xl border border-indigo-100 bg-indigo-50/40 p-3 text-center text-xs font-black uppercase tracking-wide text-indigo-700">limitation</div>
                                <div class="rounded-xl border border-indigo-100 bg-indigo-50/40 p-3 text-center text-xs font-black uppercase tracking-wide text-indigo-700">opposition</div>
                            </div>
                            <p>Le Service met en œuvre des mesures techniques et organisationnelles appropriées.</p>
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
                            <h3 class="text-xl font-bold mb-4">Article 1 - Identification de l'éditeur et définition du Service</h3>
                            <p>Le service accessible à l’adresse <strong>rappelez-moi.co</strong> (ci-après le « Service ») est édité par <strong>YLN DEVELOPPEMENT SAS</strong>, société par actions simplifiée immatriculée au Registre du Commerce et des Sociétés de Versailles sous le numéro 840 321 384, dont le siège social est situé 1 bis rue de l’Étang de la Tour, 78120 Rambouillet, France.</p>
                            <p>Le « Service » désigne l’ensemble des fonctionnalités accessibles via <strong>rappelez-moi.co</strong> permettant la mise en relation entre utilisateurs et professionnels partenaires.</p>
                            <p>Toute référence au « Service » inclut :</p>
                            <ul>
                                <li>le site internet,</li>
                                <li>les fonctionnalités,</li>
                                <li>les formulaires,</li>
                                <li>les systèmes de mise en relation.</li>
                            </ul>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4">Article 2 - Objet du Service</h3>
                            <p>Le Service permet aux utilisateurs de solliciter volontairement la mise en relation avec des professionnels partenaires.</p>
                            <p>Le Service agit exclusivement comme intermédiaire technique de mise en relation.</p>
                            <p>Le Service ne fournit aucune prestation commerciale, technique ou professionnelle autre que la mise en relation.</p>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4">Article 3 - Acceptation des conditions</h3>
                            <p>L’utilisation du Service implique l’acceptation pleine et entière des présentes conditions.</p>
                            <p>L’utilisateur reconnaît :</p>
                            <ul>
                                <li>être majeur,</li>
                                <li>fournir des informations exactes,</li>
                                <li>agir de bonne foi.</li>
                            </ul>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4">Article 4 - Conformité à la réglementation sur le démarchage téléphonique</h3>
                            <p>Le Service repose exclusivement sur une démarche active et volontaire de l’utilisateur.</p>
                            <p>En soumettant une demande, l’utilisateur :</p>
                            <ul>
                                <li>sollicite explicitement un contact,</li>
                                <li>consent à être rappelé,</li>
                                <li>accepte d’être contacté dans le cadre de sa demande.</li>
                            </ul>
                            <p>Ce consentement constitue une base légale conforme à la réglementation applicable au démarchage téléphonique.</p>
                            <p>Le Service conserve la preuve du consentement conformément aux obligations légales et réglementaires.</p>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4">Article 5 - Nature du Service</h3>
                            <p>Le Service agit exclusivement comme intermédiaire technique.</p>
                            <p>Le Service :</p>
                            <ul>
                                <li>ne fournit pas les prestations proposées,</li>
                                <li>n’est pas mandataire,</li>
                                <li>n’est pas agent commercial,</li>
                                <li>n’est pas responsable des prestations.</li>
                            </ul>
                            <p>Toute relation contractuelle est établie exclusivement entre l’utilisateur et le professionnel partenaire.</p>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4">Article 6 - Responsabilité</h3>
                            <p>Le Service est soumis à une obligation de moyens.</p>
                            <p>Le Service ne garantit pas :</p>
                            <ul>
                                <li>le contact par un professionnel,</li>
                                <li>le délai de contact,</li>
                                <li>la qualité des prestations.</li>
                            </ul>
                            <p>Le Service ne pourra être tenu responsable des dommages indirects.</p>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4">Article 7 - Utilisation conforme</h3>
                            <p>L’utilisateur s’engage à ne pas :</p>
                            <ul>
                                <li>fournir de fausses informations,</li>
                                <li>utiliser le Service de manière abusive,</li>
                                <li>utiliser des systèmes automatisés.</li>
                            </ul>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4">Article 8 - Suspension ou interruption</h3>
                            <p>Le Service peut être suspendu ou interrompu à tout moment.</p>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4">Article 9 - Données personnelles</h3>
                            <p>Le Service collecte et traite des données conformément à la politique de confidentialité.</p>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4">Article 10 - Modification</h3>
                            <p>Les présentes conditions peuvent être modifiées à tout moment.</p>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-bold mb-4">Article 11 - Droit applicable</h3>
                            <p>Les présentes conditions sont soumises au droit français.</p>
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




