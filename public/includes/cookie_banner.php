<!-- Cookie Banner Component -->
<div id="cookie-banner" class="fixed bottom-6 left-6 right-6 md:left-auto md:right-8 md:max-w-md z-[100] transform translate-y-32 opacity-0 transition-all duration-700 ease-out pointer-events-none">
    <div class="bg-navy-950/95 backdrop-blur-2xl border border-white/10 rounded-[2rem] p-8 shadow-[0_20px_50px_rgba(0,0,0,0.5)] flex flex-col gap-6 ring-1 ring-white/5">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-accent-500/10 rounded-2xl flex items-center justify-center text-accent-500 shrink-0">
                <i data-lucide="cookie" style="width:24px;height:24px;"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-lg font-display font-bold text-white mb-2">Gestion des cookies</h4>
                <p class="text-navy-300 text-sm leading-relaxed">
                    Ce Service utilise des cookies nécessaires à son fonctionnement et, avec votre consentement, des cookies de mesure d’audience. 
                    Vous pouvez accepter, refuser ou personnaliser votre choix.
                </p>
            </div>
        </div>
        
        <div class="flex flex-col gap-3">
            <div class="flex gap-3">
                <button onclick="acceptCookies()" class="flex-1 px-6 py-3.5 bg-accent-500 hover:bg-accent-400 text-navy-950 font-bold rounded-2xl transition-all shadow-lg shadow-accent-500/20 active:scale-95">
                    Accepter
                </button>
                <button onclick="refuseCookies()" class="flex-1 px-6 py-3.5 bg-navy-900 hover:bg-navy-850 text-white font-bold rounded-2xl border border-white/5 transition-all active:scale-95">
                    Refuser
                </button>
            </div>
            <a href="/rappel/public/legal.php#cookies" class="text-center py-2 text-xs font-bold text-navy-500 hover:text-navy-300 uppercase tracking-widest transition-colors">
                Personnaliser &amp; Politique Cookies
            </a>
        </div>
    </div>
</div>
