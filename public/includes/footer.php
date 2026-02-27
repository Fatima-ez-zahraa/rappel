<!-- Footer -->
<footer class="relative bg-[#020617] text-white pt-24 pb-12 px-6 overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-accent-500/50 to-transparent"></div>
    <div class="absolute -top-24 -left-24 w-64 h-64 bg-accent-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-20">
            <!-- Brand & Mission -->
            <div class="space-y-8 animate-fade-in-up">
                <a href="/rappel/public/" class="inline-block transition-transform hover:scale-105 active:scale-95">
                    <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="h-12 w-auto object-contain brightness-0 invert">
                </a>
                <p class="text-navy-300 text-lg leading-relaxed font-medium max-w-md">
                    Connecter les particuliers aux meilleurs experts à l'instant T. 
                    Une solution innovante pour des échanges humains, sécurisés et sans harcèlement.
                </p>
                <!-- Social links (Placeholders) -->
                <div class="flex gap-4">
                    <?php foreach(['twitter', 'facebook', 'linkedin', 'instagram'] as $social): ?>
                    <a href="#" class="w-11 h-11 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-navy-400 hover:bg-accent-500 hover:text-white hover:border-accent-500/50 transition-all duration-300 hover:-translate-y-1">
                        <i data-lucide="<?= $social ?>" style="width:20px;height:20px;"></i>
                    </a>  
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Links Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-10 animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="space-y-6">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-white/30">Navigation</h4>
                    <ul class="space-y-4">
                        <li><a href="/rappel/public/" class="text-navy-300 hover:text-accent-400 transition-colors font-bold text-sm">Accueil</a></li>
                        <li><a href="/rappel/public/comment-ca-marche.php" class="text-navy-300 hover:text-accent-400 transition-colors font-bold text-sm">Comment ça marche</a></li>
                        <li><a href="/rappel/public/pro/" class="text-navy-300 hover:text-accent-400 transition-colors font-bold text-sm">Espace Expert</a></li>
                    </ul>
                </div>
                <div class="space-y-6">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-white/30">Légal</h4>
                    <ul class="space-y-4">
                        <li><a href="/rappel/public/legal.php#cookies" class="text-navy-300 hover:text-accent-400 transition-colors font-bold text-sm">Cookies</a></li>
                        <li><a href="/rappel/public/legal.php#confidentialite" class="text-navy-300 hover:text-accent-400 transition-colors font-bold text-sm">Confidentialité</a></li>
                        <li><a href="/rappel/public/legal.php#cgu" class="text-navy-300 hover:text-accent-400 transition-colors font-bold text-sm">CGU</a></li>
                    </ul>
                </div>
                <!-- <div class="space-y-6 col-span-2 sm:col-span-1 border-t border-white/5 pt-10 sm:border-0 sm:pt-0">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-white/30">Contact</h4>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-navy-300 font-bold text-sm">
                            <div class="w-8 h-8 rounded-lg bg-accent-500/10 flex items-center justify-center text-accent-500">
                                <i data-lucide="mail" style="width:14px;height:14px;"></i>
                            </div>
                            <span class="truncate">contact@rappelez-moi.co</span>
                        </li>
                    </ul>
                </div> -->
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="pt-10 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex flex-col md:flex-row items-center gap-4">
                <p class="text-navy-500 text-[11px] font-black uppercase tracking-[0.2em]">
                    &copy; <?= date('Y') ?> Rappelez-moi.co
                </p>
            </div>
            <div class="flex items-center gap-4">
                <a href="/rappel/public/admin/login.php" class="group flex items-center gap-3 px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-navy-500 hover:text-accent-500 hover:bg-white/10 hover:border-white/20 transition-all text-[10px] font-black uppercase tracking-widest leading-none">
                    <i data-lucide="lock" class="group-hover:rotate-12 transition-transform" style="width:12px;height:12px;"></i>
                    Accès Administration
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- Cookie Banner -->
<?php include __DIR__ . '/cookie_banner.php'; ?>
