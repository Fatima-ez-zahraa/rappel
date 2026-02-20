<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Comment ca marche';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body class="bg-[#D3D3D3] font-sans antialiased overflow-x-hidden min-h-screen">

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="min-h-screen pt-28 pb-20">
    <section class="container mx-auto px-6 lg:px-12">
        <div class="max-w-5xl mx-auto">
            <div class="card rounded-[2rem] p-8 lg:p-12 mb-8 bg-gradient-to-br from-white/90 to-white/70 border border-white/80">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-accent-100 text-accent-700 text-xs font-bold uppercase tracking-wider mb-5">
                    Parcours utilisateur
                </div>
                <h1 class="text-4xl lg:text-6xl font-display font-bold text-navy-950 tracking-tight leading-tight">Comment ca marche</h1>
                <p class="text-navy-600 text-lg font-medium mt-5 max-w-3xl leading-relaxed">
                    Rappelez-moi est une plateforme de mise en relation de confiance concue pour repondre aux enjeux de la nouvelle legislation francaise de juillet 2026.
                    Alors que la prospection telephonique non sollicitee est desormais interdite, notre service permet aux particuliers de reprendre le controle sur leurs
                    besoins de services et aux professionnels de contacter legalement des prospects ayant exprime un consentement explicite.
                </p>
            </div>

            <div class="grid lg:grid-cols-[260px,1fr] gap-6">
                <aside class="lg:sticky lg:top-28 h-max">
                    <div class="card rounded-2xl p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-navy-400 mb-3">Sommaire</p>
                        <nav class="space-y-2 text-sm font-semibold">
                            <a href="#intro" class="guide-link block px-3 py-2 rounded-xl bg-navy-50 text-navy-700 hover:bg-accent-50 hover:text-accent-700 transition-colors">Introduction</a>
                            <a href="#particuliers" class="guide-link block px-3 py-2 rounded-xl bg-navy-50 text-navy-700 hover:bg-accent-50 hover:text-accent-700 transition-colors">Parcours particuliers</a>
                            <a href="#pros" class="guide-link block px-3 py-2 rounded-xl bg-navy-50 text-navy-700 hover:bg-accent-50 hover:text-accent-700 transition-colors">Parcours professionnels</a>
                            <a href="#avantages" class="guide-link block px-3 py-2 rounded-xl bg-navy-50 text-navy-700 hover:bg-accent-50 hover:text-accent-700 transition-colors">Avantages</a>
                        </nav>
                    </div>
                </aside>

                <div class="space-y-6">
                    <section id="intro" class="card rounded-2xl p-7">
                        <h2 class="text-2xl font-display font-bold text-navy-950 mb-3">Introduction</h2>
                        <p class="text-navy-600 leading-relaxed">
                            Rappelez-moi est une plateforme de mise en relation de confiance concue pour repondre aux enjeux de la nouvelle legislation francaise de juillet 2026.
                            Alors que la prospection telephonique non sollicitee est desormais interdite, notre service permet aux particuliers de reprendre le controle sur leurs besoins de services
                            et aux professionnels de contacter legalement des prospects ayant exprime un consentement explicite.
                        </p>
                    </section>

                    <section id="particuliers" class="card rounded-2xl p-7">
                        <h2 class="text-2xl font-display font-bold text-navy-950 mb-5">Le Parcours pour les Particuliers</h2>
                        <div class="space-y-4">
                            <article class="rounded-2xl border border-navy-100 bg-navy-50/50 p-5">
                                <p class="text-xs font-bold uppercase tracking-wider text-accent-600 mb-2">Etape 1</p>
                                <h3 class="text-lg font-bold text-navy-900 mb-2">Expression du besoin et qualification</h3>
                                <p class="text-navy-600 leading-relaxed">L'utilisateur se rend sur la plateforme et selectionne la categorie de service dont il a besoin (renovation energetique, mecanique automobile, courtage en assurance, etc.). Il remplit un formulaire simplifie decrivant la nature de son projet ou de sa panne.</p>
                            </article>
                            <article class="rounded-2xl border border-navy-100 bg-navy-50/50 p-5">
                                <p class="text-xs font-bold uppercase tracking-wider text-accent-600 mb-2">Etape 2</p>
                                <h3 class="text-lg font-bold text-navy-900 mb-2">Parametrage de la disponibilite</h3>
                                <p class="text-navy-600 leading-relaxed">Pour eviter d'etre derange a des moments inopportuns, l'utilisateur indique ses creneaux de disponibilite ideaux. Il peut preciser s'il souhaite etre rappele immediatement ou a une date ulterieure specifique.</p>
                            </article>
                            <article class="rounded-2xl border border-navy-100 bg-navy-50/50 p-5">
                                <p class="text-xs font-bold uppercase tracking-wider text-accent-600 mb-2">Etape 3</p>
                                <h3 class="text-lg font-bold text-navy-900 mb-2">Consentement et validation (Opt-in)</h3>
                                <p class="text-navy-600 leading-relaxed">C'est l'etape cle : avant de valider, l'utilisateur coche une case de consentement explicite. Ce geste autorise legalement une selection de professionnels partenaires a le contacter par telephone pour repondre precisement a sa demande.</p>
                            </article>
                            <article class="rounded-2xl border border-navy-100 bg-navy-50/50 p-5">
                                <p class="text-xs font-bold uppercase tracking-wider text-accent-600 mb-2">Etape 4</p>
                                <h3 class="text-lg font-bold text-navy-900 mb-2">Suivi de la demande</h3>
                                <p class="text-navy-600 leading-relaxed">L'utilisateur recoit un recapitulatif par email ou SMS confirmant que sa demande est transmise. Il n'a plus qu'a attendre l'appel du professionnel sans avoir a multiplier les recherches de son cote.</p>
                            </article>
                        </div>
                    </section>

                    <section id="pros" class="card rounded-2xl p-7">
                        <h2 class="text-2xl font-display font-bold text-navy-950 mb-5">Le Parcours pour les Professionnels</h2>
                        <div class="space-y-4">
                            <article class="rounded-2xl border border-navy-100 bg-white p-5">
                                <p class="text-xs font-bold uppercase tracking-wider text-brand-600 mb-2">Etape 1</p>
                                <h3 class="text-lg font-bold text-navy-900 mb-2">Consultation des opportunites</h3>
                                <p class="text-navy-600 leading-relaxed">Le professionnel (artisan, garagiste, assureur) se connecte a son espace dedie. Il accede a un flux de demandes geolocalisees et segmentees par metier. Chaque fiche indique le besoin du client sans reveler ses coordonnees completes dans un premier temps.</p>
                            </article>
                            <article class="rounded-2xl border border-navy-100 bg-white p-5">
                                <p class="text-xs font-bold uppercase tracking-wider text-brand-600 mb-2">Etape 2</p>
                                <h3 class="text-lg font-bold text-navy-900 mb-2">Acquisition du contact et preuve de consentement</h3>
                                <p class="text-navy-600 leading-relaxed">Le professionnel selectionne la demande qui l'interesse. En validant cette selection, il debloque les coordonnees telephoniques. La plateforme genere automatiquement un certificat de consentement horodate, prouvant que l'appel ne constitue pas un demarchage abusif selon la loi de 2026.</p>
                            </article>
                            <article class="rounded-2xl border border-navy-100 bg-white p-5">
                                <p class="text-xs font-bold uppercase tracking-wider text-brand-600 mb-2">Etape 3</p>
                                <h3 class="text-lg font-bold text-navy-900 mb-2">Prise de contact conforme</h3>
                                <p class="text-navy-600 leading-relaxed">Le professionnel effectue le rappel sur le creneau indique par le client. Puisque le prospect attend cet appel et a un besoin reel, l'echange est plus constructif, respectueux et presente un taux de conversion bien superieur a la prospection traditionnelle.</p>
                            </article>
                        </div>
                    </section>

                    <section id="avantages" class="card rounded-2xl p-7">
                        <h2 class="text-2xl font-display font-bold text-navy-950 mb-5">Les Avantages de la Plateforme</h2>
                        <div class="grid md:grid-cols-2 gap-4">
                            <article class="rounded-2xl border border-accent-100 bg-accent-50/60 p-5">
                                <p class="text-sm font-bold text-accent-700 mb-2">Pour le particulier</p>
                                <p class="text-navy-700 leading-relaxed">Le service est totalement gratuit. Il garantit la fin des appels intrusifs puisque seuls les professionnels choisis par l'algorithme de la plateforme sont autorises a appeler. C'est un gain de temps considerable et une securite contre les arnaques telephoniques.</p>
                            </article>
                            <article class="rounded-2xl border border-brand-100 bg-brand-50/60 p-5">
                                <p class="text-sm font-bold text-brand-700 mb-2">Pour le professionnel</p>
                                <p class="text-navy-700 leading-relaxed">Il s'agit d'un bouclier juridique indispensable face aux sanctions liees a la loi sur la prospection. C'est aussi un outil de performance : le professionnel ne perd plus de temps a appeler des listes de numeros froids, il ne traite que des "prospects chauds" qui ont activement demande une solution.</p>
                            </article>
                        </div>
                    </section>

                    <section class="card rounded-2xl p-7 bg-gradient-to-r from-brand-900 to-brand-700 text-white">
                        <h2 class="text-2xl font-display font-bold mb-2">Pret a passer a l'action ?</h2>
                        <p class="text-brand-100 font-medium">Choisissez votre parcours et commencez en quelques clics.</p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="/rappel/public/#demande" class="btn bg-white text-brand-800 hover:bg-brand-50 rounded-xl px-7">Faire une demande</a>
                            <a href="/rappel/public/pro/" class="btn border border-white/30 text-white hover:bg-white/10 rounded-xl px-7">Espace Expert</a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="/rappel/public/assets/js/app.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const links = Array.from(document.querySelectorAll('.guide-link'));
    const sections = links
        .map(link => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

    const setActive = (id) => {
        links.forEach(link => {
            const active = link.getAttribute('href') === `#${id}`;
            link.classList.toggle('bg-accent-100', active);
            link.classList.toggle('text-accent-700', active);
            link.classList.toggle('bg-navy-50', !active);
            link.classList.toggle('text-navy-700', !active);
        });
    };

    const observer = new IntersectionObserver((entries) => {
        const visible = entries
            .filter(e => e.isIntersecting)
            .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
        if (visible) setActive(visible.target.id);
    }, { rootMargin: '-25% 0px -60% 0px', threshold: [0.2, 0.4, 0.6] });

    sections.forEach(section => observer.observe(section));
    if (sections[0]) setActive(sections[0].id);
});
</script>
</body>
</html>

