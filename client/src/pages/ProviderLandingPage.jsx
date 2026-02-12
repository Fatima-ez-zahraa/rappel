import React from 'react';
import { Button } from '../components/ui/Button';
import { Card } from '../components/ui/Card';
import { useNavigate } from 'react-router-dom';
import { Target, ShieldCheck, MapPin, BarChart3, Bell, FileText, CheckCircle, Smartphone, Home, Zap, Lightbulb, PenTool } from 'lucide-react';
import { motion } from 'framer-motion';
import { Logo } from '../components/ui/Logo';

const ProviderLandingPage = () => {
    const navigate = useNavigate();

    const stats = [
        { value: '35k+', label: 'Demandes traitées' },
        { value: '450+', label: 'Experts actifs' },
        { value: '82%', label: 'Taux de transformation' },
        { value: '2min', label: 'Délai moyen de mise en relation' },
    ];

    const benefits = [
        {
            icon: Target,
            title: 'Exclusivité Territoriale',
            desc: 'Ne soyez plus en concurrence avec 5 autres prestataires. Nous gérons la distribution pour maximiser votre ROI.',
            color: 'text-brand-600 bg-brand-50'
        },
        {
            icon: ShieldCheck,
            title: 'Conformité Totale',
            desc: 'Certification RGPD & Loi 2025. Chaque lead est accompagné de sa preuve de consentement horodatée.',
            color: 'text-accent-600 bg-accent-50'
        },
        {
            icon: Zap,
            title: 'Instantanéité Absolue',
            desc: 'Réception par SMS/Email en temps réel. Appelez le prospect pendant qu\'il est encore sur son écran.',
            color: 'text-brand-600 bg-brand-50'
        },
        {
            icon: BarChart3,
            title: 'Optimisation du Pipeline',
            desc: 'Dashboard analytique complet pour suivre votre coût d\'acquisition et vos performances commerciales.',
            color: 'text-accent-600 bg-accent-50'
        },
        {
            icon: Bell,
            title: 'Filtres Surgitaux',
            desc: 'Définissez vos critères précis : type de projet, budget minimum, zone géographique au code postal.',
            color: 'text-brand-600 bg-brand-50'
        },
        {
            icon: FileText,
            title: 'Zéro Abonnements',
            desc: 'Modèle à la performance. Vous ne payez que pour les leads que vous décidez d\'ouvrir.',
            color: 'text-accent-600 bg-accent-50'
        }
    ];

    return (
        <div className="font-sans text-navy-900 bg-transparent min-h-screen flex flex-col">

            {/* Navbar */}
            <nav className="w-full bg-white/70 backdrop-blur-xl border-b border-navy-100/50 py-4 px-8 fixed top-0 z-50 flex justify-between items-center shadow-glass">
                <Logo className="h-9 cursor-pointer" onClick={() => navigate('/')} />

                <div className="flex items-center gap-8 text-sm font-bold text-navy-600">
                    <a href="#tarifs" className="hover:text-accent-600 transition-colors">Tarifs</a>
                    <a href="#temoignages" className="hover:text-accent-600 transition-colors">Temoignage</a>
                    <Button size="sm" showShine onClick={() => navigate('/pro/login')} className="shadow-premium">
                        Connexion Expert
                    </Button>
                </div>
            </nav>

            {/* Hero Section */}
            <section className="relative pt-40 pb-24 px-6 text-center overflow-hidden">
                <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-[radial-gradient(circle_at_50%_-20%,_rgba(16,185,129,0.1),transparent_70%)]" />

                <div className="max-w-5xl mx-auto space-y-8 relative z-10">
                    <motion.div
                        initial={{ opacity: 0, scale: 0.9 }}
                        animate={{ opacity: 1, scale: 1 }}
                        transition={{ duration: 0.8 }}
                    >
                        <h1 className="text-5xl md:text-7xl font-display font-bold text-navy-950 leading-tight tracking-tight">
                            Transformez votre croissance avec des <br />
                            <span className="text-transparent bg-clip-text bg-gradient-to-r from-accent-600 to-brand-600">
                                Leads à Haute Conversion
                            </span>
                        </h1>
                    </motion.div>

                    <motion.p
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.2 }}
                        className="text-navy-600/80 text-xl md:text-2xl max-w-3xl mx-auto font-medium"
                    >
                        Accédez en temps réel aux demandes de particuliers qualifiés et validez vos objectifs de vente.
                    </motion.p>

                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.4 }}
                        className="flex flex-col sm:flex-row justify-center gap-5 pt-6"
                    >
                        <Button size="lg" showShine className="rounded-2xl px-12 shadow-premium">
                            Découvrir nos solutions
                        </Button>
                        <Button size="lg" variant="outline" className="rounded-2xl px-12 border-navy-200" onClick={() => navigate('/pro/signup')}>
                            Créer mon profil expert
                        </Button>
                    </motion.div>
                </div>
            </section>

            {/* Stats */}
            <section className="bg-transparent py-16 px-6">
                <div className="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-12">
                    {stats.map((stat, i) => (
                        <div key={i} className="space-y-2 text-center group">
                            <div className="text-4xl md:text-5xl font-display font-bold text-navy-950 group-hover:text-accent-600 transition-colors">{stat.value}</div>
                            <div className="text-xs font-bold text-navy-400 uppercase tracking-[0.2em]">{stat.label}</div>
                        </div>
                    ))}
                </div>
            </section>

            {/* Pricing Section */}
            <section id="tarifs" className="py-32 px-6">
                <div className="max-w-7xl mx-auto">
                    <div className="text-center mb-20">
                        <h2 className="text-4xl md:text-5xl font-display font-bold text-navy-950 mb-6">Investissement à la Performance</h2>
                        <p className="text-navy-500 text-lg font-medium">Choisissez le modèle qui correspond à votre ambition.</p>
                    </div>

                    <div className="grid lg:grid-cols-3 gap-10 items-stretch">
                        {/* Starter */}
                        <Card className="flex flex-col p-10 bg-white/40 border-white/60">
                            <h3 className="text-2xl font-bold text-navy-950 mb-2">Croissance</h3>
                            <p className="text-navy-500 mb-8 font-medium">Pour les professionnels locaux.</p>
                            <div className="flex items-baseline gap-2 mb-10">
                                <span className="text-5xl font-bold text-navy-950">99€</span>
                                <span className="text-navy-400 font-bold">/mois</span>
                            </div>
                            <ul className="space-y-5 flex-grow mb-12">
                                {['10 leads qualifiés inclus', '1 secteur d\'activité', 'Ciblage départemental', 'Preuves de consentement SMS'].map((item, i) => (
                                    <li key={i} className="flex items-center gap-4 text-navy-700 font-medium">
                                        <div className="w-5 h-5 rounded-full bg-accent-100 flex items-center justify-center">
                                            <CheckCircle size={14} className="text-accent-600" />
                                        </div>
                                        {item}
                                    </li>
                                ))}
                            </ul>
                            <Button variant="outline" className="w-full rounded-xl border-navy-200" onClick={() => navigate('/pro/checkout', { state: { plan: { name: 'Croissance', price: '99€/mois' } } })}>
                                Sélectionner
                            </Button>
                        </Card>

                        {/* Business (Popular) */}
                        <Card className="flex flex-col p-10 bg-navy-900 border-accent-500/20 scale-105 shadow-2xl relative overflow-hidden group">
                            <div className="absolute top-0 right-0 p-4">
                                <Zap className="text-accent-400 animate-pulse" fill="currentColor" />
                            </div>
                            <h3 className="text-2xl font-bold text-white mb-2">Accélération</h3>
                            <p className="text-navy-300 mb-8 font-medium">Pour les structures en expansion.</p>
                            <div className="flex items-baseline gap-2 mb-10 text-white">
                                <span className="text-6xl font-bold">249€</span>
                                <span className="text-navy-400 font-bold">/mois</span>
                            </div>
                            <ul className="space-y-5 flex-grow mb-12">
                                {['30 leads haute qualité', 'Ciblage régional illimité', 'CRM intégré avec API', 'Support VIP 24/7', 'Garantie de remplacement lead'].map((item, i) => (
                                    <li key={i} className="flex items-center gap-4 text-navy-100 font-medium">
                                        <div className="w-5 h-5 rounded-full bg-accent-500 flex items-center justify-center">
                                            <CheckCircle size={14} className="text-navy-950" />
                                        </div>
                                        {item}
                                    </li>
                                ))}
                            </ul>
                            <Button showShine className="w-full rounded-xl shadow-premium" onClick={() => navigate('/pro/checkout', { state: { plan: { name: 'Accélération', price: '249€/mois' } } })}>
                                Choisir l'Excellence
                            </Button>
                        </Card>

                        {/* On Demand */}
                        <Card className="flex flex-col p-10 bg-white/40 border-white/60">
                            <h3 className="text-2xl font-bold text-navy-950 mb-2">Flexibilité</h3>
                            <p className="text-navy-500 mb-8 font-medium">Payez à l'usage réel.</p>
                            <div className="flex items-baseline gap-2 mb-10">
                                <span className="text-5xl font-bold text-navy-950">12€</span>
                                <span className="text-navy-400 font-bold">/lead</span>
                            </div>
                            <ul className="space-y-5 flex-grow mb-12 text-navy-700">
                                {['Zéro coût fixe hebdomadaire', 'Volume illimité', 'Recharge crédit instantanée', 'Accès dashboard global'].map((item, i) => (
                                    <li key={i} className="flex items-center gap-4 font-medium">
                                        <div className="w-5 h-5 rounded-full bg-accent-100 flex items-center justify-center">
                                            <CheckCircle size={14} className="text-accent-600" />
                                        </div>
                                        {item}
                                    </li>
                                ))}
                            </ul>
                            <Button variant="outline" className="w-full rounded-xl border-navy-200" onClick={() => navigate('/pro/checkout', { state: { plan: { name: 'Flexibilité', price: 'A la carte' } } })}>
                                Commander
                            </Button>
                        </Card>
                    </div>
                </div>
            </section>

            {/* Benefits Grid */}
            <section className="py-32 px-6 bg-navy-900 relative overflow-hidden">
                <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-accent-500/10 rounded-full -mr-64 -mt-64 blur-[120px]" />
                <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-accent-500/5 rounded-full -ml-64 -mb-64 blur-[120px]" />

                <div className="max-w-6xl mx-auto relative z-10">
                    <div className="text-left mb-20 max-w-2xl">
                        <h2 className="text-4xl md:text-5xl font-display font-bold text-white mb-6">L'ingénierie du Lead</h2>
                        <p className="text-navy-300 text-xl font-medium leading-relaxed">
                            Nous utilisons une technologie de pointe pour transformer chaque clic en une opportunité commerciale qualifiée et conforme.
                        </p>
                    </div>
                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {benefits.map((b, i) => (
                            <Card key={i} className="p-10 hover:shadow-premium group bg-white/10 backdrop-blur-md border-white/10 hover:border-accent-500/30 transition-all duration-500">
                                <div className={`w-14 h-14 rounded-[1.25rem] flex items-center justify-center mb-8 shadow-inner-light group-hover:rotate-6 transition-transform duration-300 ${b.color}`}>
                                    <b.icon size={28} />
                                </div>
                                <h3 className="text-2xl font-bold text-white mb-4">{b.title}</h3>
                                <p className="text-navy-300 leading-relaxed font-medium">
                                    {b.desc}
                                </p>
                            </Card>
                        ))}
                    </div>
                </div>
            </section>

            {/* CTA Final */}
            <section className="py-24 px-6 text-center">
                <div className="max-w-4xl mx-auto rounded-[3.5rem] bg-[#0E1648] p-16 shadow-premium relative overflow-hidden group">
                    <div className="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-3xl" />
                    <div className="absolute bottom-0 left-0 w-64 h-64 bg-accent-500/10 rounded-full -ml-32 -mb-32 blur-3xl transition-all duration-1000 group-hover:scale-125" />

                    <h2 className="text-4xl font-display font-bold text-white mb-8 relative z-10">Prêt à changer de dimension ?</h2>
                    <Button size="lg" className="bg-white text-[#0E1648] hover:bg-navy-50 px-12 rounded-2xl font-bold shadow-xl relative z-10" onClick={() => navigate('/pro/signup')}>
                        Commencer l'intégration
                    </Button>
                </div>
            </section>

            <footer className="py-20 px-6 border-t border-navy-100/30">
                <div className="max-w-7xl mx-auto flex flex-col items-center gap-8">
                    <p className="text-navy-400 font-bold uppercase tracking-widest text-[10px]">
                        &copy; 2026 Rappelez-moi Expert - Solution de Leads Certifiés
                    </p>
                </div>
            </footer>
        </div >
    );
};

export default ProviderLandingPage;
