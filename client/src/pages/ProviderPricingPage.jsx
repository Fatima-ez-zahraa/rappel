import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Button } from '../components/ui/Button';
import { CheckCircle, Zap, Building2, TrendingUp } from 'lucide-react';
import { cn } from '../lib/utils';
import { Card } from '../components/ui/Card';
import { api } from '../lib/api';

const ProviderPricingPage = () => {
    const navigate = useNavigate();
    const [cgu, setCgu] = useState(false);
    const [rgpd, setRgpd] = useState(false);

    const handleSelectPlan = async (plan) => {
        if (!cgu || !rgpd) {
            alert("Veuillez accepter les conditions pour continuer.");
            return;
        }
        try {
            const type = (plan.price.includes('/mois') || plan.name === 'À la carte') ? 'subscription' : 'one-time';
            const data = await api.payments.createCheckout({
                type,
                planName: plan.name
            });
            if (data.url) {
                window.location.href = data.url;
            }
        } catch (error) {
            console.error('Checkout error:', error);
            alert(`Erreur lors de l'initialisation du paiement : ${error.message}`);
        }
    };

    return (
        <div className="min-h-screen bg-transparent py-20 px-4 font-sans relative overflow-hidden">
            {/* Animated background elements */}
            <div className="absolute inset-0 overflow-hidden -z-10">
                <div className="absolute -top-40 -right-40 w-96 h-96 bg-brand-200/20 rounded-full blur-3xl animate-pulse"></div>
                <div className="absolute -bottom-40 -left-40 w-96 h-96 bg-accent-200/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
                <div className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-brand-50/10 rounded-full blur-3xl animate-pulse delay-500"></div>
            </div>

            <div className="max-w-7xl mx-auto relative z-10">
                <div className="text-center mb-16">
                    <div className="inline-flex items-center gap-2 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full border border-white/20 shadow-lg mb-6">
                        <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span className="text-sm font-medium text-gray-700">Votre compte est validé</span>
                    </div>
                    <h1 className="text-5xl md:text-7xl font-display font-bold mb-6 text-navy-950 leading-tight tracking-tight">
                        Choisissez votre <span className="text-accent-500">plan de vol</span>
                    </h1>
                    <p className="text-xl text-navy-500 max-w-3xl mx-auto leading-relaxed font-medium">
                        Propulsez votre business avec des leads hautement qualifiés et un CRM intelligent.
                        <span className="font-bold text-navy-900"> Directement dans votre espace partenaire.</span>
                    </p>
                </div>

                <div className="grid md:grid-cols-3 gap-8 items-start mb-12">
                    {/* Starter */}
                    <Card className="group relative p-8 hover:shadow-premium transition-all duration-500 border-navy-100/50 bg-white/60 backdrop-blur-xl rounded-[2.5rem] overflow-hidden">
                        <div className="relative z-10">
                            <div className="w-12 h-12 bg-navy-50 rounded-2xl flex items-center justify-center mb-6 text-navy-600 font-bold group-hover:scale-110 transition-transform">
                                <Building2 size={24} />
                            </div>
                            <h3 className="text-2xl font-bold text-navy-950 mb-2">Starter</h3>
                            <p className="text-navy-400 font-medium mb-6 uppercase tracking-widest text-[10px]">Parfait pour commencer</p>
                            <div className="flex items-end justify-start gap-1 mb-8">
                                <span className="text-5xl font-bold text-navy-950">99€</span>
                                <span className="text-navy-400 font-bold mb-1">/mois</span>
                            </div>
                            <ul className="space-y-4 text-left mb-8">
                                {['Jusqu\'à 10 leads qualifiés / mois', '1 secteur d\'activité ciblé', 'Ciblage départemental précis', 'Dashboard intuitif inclus'].map((item, i) => (
                                    <li key={i} className="flex items-start gap-3 text-sm text-navy-600 font-medium">
                                        <CheckCircle size={18} className="text-accent-500 shrink-0 mt-0.5" />
                                        {item}
                                    </li>
                                ))}
                            </ul>
                            <Button onClick={() => handleSelectPlan({ name: 'Starter', price: '99€/mois', features: ['10 leads/mois', '1 secteur', 'Dept'] })} className="w-full bg-navy-950 hover:bg-navy-900 text-white font-bold py-6 rounded-2xl shadow-premium transition-all" disabled={!cgu || !rgpd}>
                                Sélectionner Starter
                            </Button>
                        </div>
                    </Card>

                    {/* Business */}
                    <Card className="group relative p-8 shadow-premium border-2 border-accent-500 bg-navy-950 scale-105 transform md:-translate-y-4 transition-all duration-500 overflow-hidden rounded-[3rem]">
                        <div className="absolute top-0 right-0 bg-accent-500 text-white px-6 py-2 rounded-bl-[1.5rem] text-xs font-bold uppercase tracking-[0.2em] shadow-xl flex items-center gap-2">
                            <Zap size={14} fill="currentColor" /> Recommandé
                        </div>
                        <div className="relative z-10 text-white">
                            <div className="w-12 h-12 bg-accent-500/20 rounded-2xl flex items-center justify-center mb-6 text-accent-500">
                                <TrendingUp size={24} />
                            </div>
                            <h3 className="text-2xl font-bold mb-2">Business</h3>
                            <p className="text-navy-300 font-medium mb-6 uppercase tracking-widest text-[10px]">Pour les leaders du marché</p>
                            <div className="flex items-end justify-start gap-1 mb-8">
                                <span className="text-6xl font-bold text-white">249€</span>
                                <span className="text-navy-300 font-bold mb-1">/mois</span>
                            </div>
                            <ul className="space-y-4 text-left mb-8">
                                {['Jusqu\'à 30 leads Ultra-Qualifiés', '3 secteurs d\'activité stratégiques', 'Ciblage régional étendu', 'CRM Pro & Analytics VIP', 'Priorité de distribution'].map((item, i) => (
                                    <li key={i} className="flex items-start gap-3 text-sm text-white/90 font-medium">
                                        <CheckCircle size={18} className="text-accent-500 shrink-0 mt-0.5" />
                                        {item}
                                    </li>
                                ))}
                            </ul>
                            <Button size="lg" onClick={() => handleSelectPlan({ name: 'Business', price: '249€/mois', features: ['30 leads/mois', '3 secteurs', 'Régional', 'CRM'] })} className="w-full bg-accent-500 hover:bg-accent-600 text-white font-bold py-8 rounded-2xl shadow-glow transition-all" disabled={!cgu || !rgpd}>
                                Booster mon business
                            </Button>
                        </div>
                    </Card>

                    {/* A la carte */}
                    <Card className="group relative p-8 hover:shadow-premium transition-all duration-500 border-navy-100/50 bg-white/40 backdrop-blur-xl rounded-[2.5rem] overflow-hidden">
                        <div className="relative z-10">
                            <div className="w-12 h-12 bg-accent-50 rounded-2xl flex items-center justify-center mb-6 text-accent-700 font-bold group-hover:rotate-12 transition-transform">
                                <Zap size={24} />
                            </div>
                            <h3 className="text-2xl font-bold text-navy-950 mb-2">À la carte</h3>
                            <p className="text-navy-400 font-medium mb-6 uppercase tracking-widest text-[10px]">Paiement à la performance</p>
                            <div className="flex items-end justify-start gap-1 mb-8">
                                <span className="text-5xl font-bold text-navy-950">9€</span>
                                <span className="text-navy-400 font-bold mb-1">/lead</span>
                            </div>
                            <ul className="space-y-4 text-left mb-8">
                                {['Paiement unique par lead converti', 'Flexibilité totale sans engagement', 'Accès à tous les secteurs', 'Ciblage ultra-précis sur mesure'].map((item, i) => (
                                    <li key={i} className="flex items-start gap-3 text-sm text-navy-600 font-medium">
                                        <CheckCircle size={18} className="text-accent-500 shrink-0 mt-0.5" />
                                        {item}
                                    </li>
                                ))}
                            </ul>
                            <Button onClick={() => handleSelectPlan({ name: 'À la carte', price: '9€/lead', features: ['Paiement au lead', 'Sans engagement'] })} className="w-full bg-navy-50 text-navy-900 border-2 border-navy-100 hover:bg-navy-100 font-bold py-6 rounded-2xl transition-all" disabled={!cgu || !rgpd}>
                                performance pure
                            </Button>
                        </div>
                    </Card>
                </div>

                {/* Legal Checkboxes */}
                <div className="bg-white p-6 rounded-xl shadow-sm border border-slate-100 max-w-2xl mx-auto">
                    <h4 className="font-bold text-navy-900 mb-4">Confirmez vos engagements</h4>
                    <div className="space-y-4">
                        <label className="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" className="mt-1 w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500" checked={cgu} onChange={(e) => setCgu(e.target.checked)} />
                            <span className="text-sm text-slate-600">J'accepte les <span className="underline">Conditions Générales d'Utilisation</span> de la plateforme.</span>
                        </label>
                        <label className="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" className="mt-1 w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500" checked={rgpd} onChange={(e) => setRgpd(e.target.checked)} />
                            <span className="text-sm text-slate-600">Je m'engage à respecter la RGPD et la <span className="font-semibold text-slate-800">Loi n° 2025-594 du 30 juin 2025</span> relative au démarchage téléphonique.</span>
                        </label>
                    </div>
                </div>

            </div>
        </div>
    );
};

export default ProviderPricingPage;
