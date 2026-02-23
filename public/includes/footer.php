<!-- Footer -->
<footer class="relative bg-navy-950 text-white py-16 px-6 overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-accent-500/50 to-transparent"></div>
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-10 relative z-10">
        
        <!-- Brand & Description -->
        <div class="flex flex-col items-center md:items-start gap-6 max-w-sm">
            <a href="/rappel/public/" class="transition-opacity hover:opacity-80">
                <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="h-10 w-auto object-contain brightness-0 invert">
            </a>
            <p class="text-navy-300 text-sm leading-relaxed text-center md:text-left font-medium">
                La plateforme de mise en relation intelligente entre particuliers et experts de confiance. Simple, sécurisé et 100% conforme.
            </p>
        </div>

        <!-- Links & Legal -->
        <div class="flex flex-col items-center md:items-end gap-6 text-center md:text-right">
            <div class="flex flex-wrap justify-center md:justify-end gap-6">
                <a href="/rappel/public/legal.php#cookies" class="text-navy-400 hover:text-white transition-colors text-sm font-semibold">Politique Cookies</a>
                <a href="/rappel/public/legal.php#confidentialite" class="text-navy-400 hover:text-white transition-colors text-sm font-semibold">Confidentialité</a>
                <a href="/rappel/public/legal.php#cgu" class="text-navy-400 hover:text-white transition-colors text-sm font-semibold">CGU</a>
            </div>
            
            <div class="flex flex-col md:flex-row items-center gap-4 pt-4 border-t border-navy-900 w-full md:w-auto">
                <p class="text-navy-500 text-[10px] font-bold uppercase tracking-[0.2em]">
                    &copy; <?= date('Y') ?> Rappelez-moi. Tous droits réservés.
                </p>
                <span class="hidden md:block text-navy-800">|</span>
                <a href="/rappel/public/admin/login.php" class="text-navy-600 hover:text-accent-500 text-xs font-bold transition-colors">
                    Accès Administration
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- Cookie Banner -->
<?php include __DIR__ . '/cookie_banner.php'; ?>
