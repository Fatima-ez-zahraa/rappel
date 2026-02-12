import React from 'react';
import { Card } from '../components/ui/Card';
import { Button } from '../components/ui/Button';
import { Check, ShoppingBag, Zap } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/useAuth';

const BuyLeadsPage = () => {
    const navigate = useNavigate();

    const handlePurchase = (plan) => {
        // reuse the checkout page
        navigate('/pro/checkout', { state: { plan, type: 'one-time' } });
    };

    return (
        <div className="max-w-5xl mx-auto space-y-8 animate-fade-in">
            <div className="text-center space-y-4 mb-12 pt-8">
                <h1 className="text-4xl font-display font-bold text-navy-950 tracking-tight">Boostez votre activité</h1>
                <p className="text-xl text-navy-800 font-bold max-w-2xl mx-auto">
                    Achetez des leads qualifiés dans votre zone d'intervention.
                    Sans abonnement, sans engagement.
                </p>
            </div>

            <div className="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                {/* Single Lead Option */}
                <Card className="p-8 border-2 border-slate-100 hover:border-brand-200 transition-all relative overflow-hidden group rounded-[2.5rem] bg-white/60 backdrop-blur-xl">
                    <div className="absolute top-0 left-0 w-full h-1 bg-slate-200 group-hover:bg-brand-500 transition-colors" />
                    <div className="mb-6">
                        <div className="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4 group-hover:bg-brand-100 transition-colors">
                            <Zap size={28} />
                        </div>
                        <h3 className="text-2xl font-bold text-navy-950">Lead à l'unité</h3>
                        <p className="text-navy-700 font-bold mt-2">Idéal pour tester ou pour un besoin ponctuel.</p>
                    </div>
                    <div className="mb-8">
                        <div className="flex items-baseline gap-1">
                            <span className="text-4xl font-bold text-navy-950">9€</span>
                            <span className="text-navy-700 font-bold">/lead</span>
                        </div>
                        <p className="text-xs text-navy-500 font-bold mt-1">HT (TVA 20%)</p>
                    </div>

                    <ul className="space-y-3 mb-8">
                        <li className="flex items-center gap-3 text-sm text-navy-800 font-bold">
                            <Check className="text-emerald-500" size={18} /> Lead qualifié et vérifié
                        </li>
                        <li className="flex items-center gap-3 text-sm text-navy-800 font-bold">
                            <Check className="text-emerald-500" size={18} /> Exclusivité 24h
                        </li>
                        <li className="flex items-center gap-3 text-sm text-navy-800 font-bold">
                            <Check className="text-emerald-500" size={18} /> Détails complets du projet
                        </li>
                    </ul>

                    <Button className="w-full bg-white text-navy-900 border border-slate-200 hover:bg-slate-50" onClick={() => handlePurchase({ name: 'Lead Unité', price: '9€', features: ['1 Lead qualifié', 'Détails complets'] })}>
                        Acheter 1 Lead
                    </Button>
                </Card>

                {/* Bulk Pack Option */}
                <Card className="p-8 border-2 border-accent-500 relative overflow-hidden shadow-premium bg-navy-950 text-white rounded-[2.5rem]">
                    <div className="absolute top-0 right-0 bg-accent-500 text-white text-[10px] font-bold px-4 py-1.5 rounded-bl-2xl uppercase tracking-widest">
                        MEILLEURE OFFRE
                    </div>
                    <div className="mb-6">
                        <div className="w-14 h-14 rounded-2xl bg-accent-500 flex items-center justify-center mb-4 text-white shadow-glow">
                            <ShoppingBag size={28} />
                        </div>
                        <h3 className="text-2xl font-bold mb-2">Pack Pro</h3>
                        <p className="text-navy-300 font-medium text-sm">Pour les professionnels qui veulent accélérer.</p>
                    </div>
                    <div className="mb-8">
                        <div className="flex items-baseline gap-1">
                            <span className="text-5xl font-bold text-white">125€</span>
                            <span className="text-navy-400 font-bold">/pack</span>
                        </div>
                        <p className="text-xs text-accent-400 font-bold mt-2 font-display">Soit 8.33€ le lead (Économisez 10€)</p>
                    </div>

                    <ul className="space-y-3 mb-8">
                        <li className="flex items-center gap-3 text-sm text-navy-100 font-bold">
                            <Check className="text-accent-500" size={18} /> <strong className="text-white">15 Leads qualifiés</strong>
                        </li>
                        <li className="flex items-center gap-3 text-sm text-navy-100 font-bold">
                            <Check className="text-accent-500" size={18} /> Priorité sur les nouveaux leads
                        </li>
                        <li className="flex items-center gap-3 text-sm text-navy-100 font-bold">
                            <Check className="text-accent-500" size={18} /> Support dédié prioritaire
                        </li>
                    </ul>

                    <Button className="w-full bg-accent-500 hover:bg-accent-600 text-white shadow-glow font-bold py-6 rounded-2xl transition-all" onClick={() => handlePurchase({ name: 'Pack Pro (15 Leads)', price: '125€', features: ['15 Leads qualifiés', 'Priorité 48h', 'Support dédié'] })}>
                        Commander le Pack
                    </Button>
                </Card>
            </div>

            <p className="text-center text-sm text-navy-700 font-bold mt-8 pb-8">
                Paiement sécurisé par Stripe. Facture disponible immédiatement.
            </p>
        </div>
    );
};

export default BuyLeadsPage;
