<?php

?>
<!-- Background Blobs -->
<div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
    <div id="blob-green" class="absolute rounded-full blur-[120px] opacity-20"
         style="top:-10%;left:-10%;width:50%;height:60%;background:rgba(124,203,99,0.3);animation:float 20s ease-in-out infinite;"></div>
    <div id="blob-navy" class="absolute rounded-full blur-[120px] opacity-20"
         style="bottom:-10%;right:-10%;width:60%;height:70%;background:rgba(14,22,72,0.3);animation:float 25s ease-in-out infinite 2s;"></div>
</div>

<header id="main-header" class="fixed top-0 left-0 w-full z-50 transition-all duration-500 py-5">
    <div class="container mx-auto px-6 lg:px-12 flex items-center justify-between">

        <!-- Logo -->
        <a href="/rappel/public/" class="flex items-center transition-opacity hover:opacity-80">
            <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="h-9 w-auto object-contain">
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden lg:flex items-center gap-10">
            <a href="/rappel/public/comment-ca-marche.php"
               class="text-sm font-semibold text-navy-600 hover:text-accent-600 transition-colors">
                Comment ça marche
            </a>
            <a href="/rappel/public/#demande"
               class="text-sm font-semibold text-navy-600 hover:text-accent-600 transition-colors scroll-link"
               data-target="demande">
                Chercher un prestataire
            </a>
            <a href="/rappel/public/#legal"
               class="text-sm font-semibold text-navy-600 hover:text-accent-600 transition-colors scroll-link"
               data-target="legal">
                Mentions légales
            </a>
        </nav>

        <!-- CTA Desktop -->
        <div class="hidden lg:flex items-center gap-6">
            <a href="/rappel/public/pro/login.php"
               class="text-sm font-bold text-navy-700 hover:text-accent-600 transition-colors">
                Connexion
            </a>
            <a href="/rappel/public/pro/"
               class="btn btn-primary btn-sm rounded-xl px-6">
                Espace Expert
            </a>
        </div>

        <!-- Mobile Toggle -->
        <button id="mobile-menu-btn"
                class="lg:hidden p-2.5 rounded-xl bg-white/80 border border-navy-100 text-navy-900 shadow-sm transition-all">
            <svg id="icon-menu" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            <svg id="icon-close" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden absolute top-full left-0 w-full bg-white/95 backdrop-blur-2xl border-b border-navy-100 p-8 shadow-xl lg:hidden flex-col gap-6">
        <a href="/rappel/public/comment-ca-marche.php" class="text-xl font-bold text-navy-950 text-left block">Comment ça marche</a>
        <a href="/rappel/public/#demande" class="text-xl font-bold text-navy-950 text-left block scroll-link" data-target="demande">Chercher un prestataire</a>
        <a href="/rappel/public/#legal" class="text-xl font-bold text-navy-950 text-left block scroll-link" data-target="legal">Mentions légales</a>
        <div class="flex flex-col gap-4 pt-6 border-t border-navy-50">
            <a href="/rappel/public/pro/login.php" class="btn btn-outline w-full h-14 rounded-2xl text-lg font-bold text-center">Connexion Expert</a>
            <a href="/rappel/public/pro/" class="btn btn-primary w-full h-14 rounded-2xl text-lg font-bold text-center">Rejoindre le Réseau</a>
        </div>
    </div>
</header>
