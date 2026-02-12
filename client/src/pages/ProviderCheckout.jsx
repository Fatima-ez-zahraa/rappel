import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/useAuth';
import { Card } from '../components/ui/Card';
import { Button } from '../components/ui/Button';
import { Input } from '../components/ui/Input';
import { Check, ShieldCheck, Lock, CreditCard } from 'lucide-react';
import { cn } from '../lib/utils';
import { api } from '../lib/api';
import { Logo } from '../components/ui/Logo';

const ProviderCheckout = () => {
    const navigate = useNavigate();
    const { user } = useAuth();
    const location = useLocation();

    // Default to subscription if no state provided
    const { plan, type } = location.state || {
        plan: {
            name: 'Pro Pack (30 Leads)',
            price: '249€/mois',
            features: ['Jusqu\'à 30 leads/mois', '3 secteurs d\'activité', 'Ciblage régional']
        },
        type: 'subscription'
    };

    const isSubscription = type !== 'one-time';

    const [loading, setLoading] = useState(false);

    const handlePayment = async () => {
        try {
            setLoading(true);
            const { url } = await api.payments.createCheckout({
                type,
                planName: plan.name
            });
            window.location.href = url;
        } catch (error) {
            console.error('Checkout error:', error);
            alert("Erreur lors de la redirection vers le paiement.");
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-slate-50 font-sans">
            {/* Simple Header for Checkout */}
            <header className="bg-white border-b border-slate-200 py-4 px-6 flex items-center justify-between">
                <div className="flex items-center gap-4">
                    <Logo className="cursor-pointer" onClick={() => navigate('/')} />
                    <span className="text-slate-400 font-normal text-sm border-l border-slate-200 pl-4 h-6 flex items-center font-sans">Paiement Sécurisé</span>
                </div>
                <div className="flex items-center gap-2 text-accent-700 text-sm font-bold bg-accent-50 px-4 py-1.5 rounded-full border border-accent-100">
                    <Lock size={14} /> Sécurisation SSL 256-bits
                </div>
            </header>

            <main className="max-w-5xl mx-auto p-6 md:py-12 grid md:grid-cols-2 gap-8">

                {/* Left Column: Form */}
                <div className="space-y-6">
                    <div>
                        <h2 className="text-2xl font-bold text-navy-900">
                            {isSubscription ? "Finalisez votre inscription" : "Finalisez votre commande"}
                        </h2>
                        <p className="text-slate-500">
                            {isSubscription
                                ? "Créez votre compte professionnel pour accéder aux leads."
                                : "Paiement sécurisé pour l'achat de crédits."}
                        </p>
                    </div>

                    {isSubscription && (
                        <Card className="p-6 space-y-4">
                            <h3 className="font-semibold text-navy-900 border-b border-slate-100 pb-2 mb-4">1. Informations de l'entreprise</h3>
                            <Input label="Nom de l'entreprise" placeholder="Ex: Rénov'Plus" />
                            <Input label="SIRET" placeholder="123 456 789 00012" />
                            <div className="grid grid-cols-2 gap-4">
                                <Input label="Nom du gérant" placeholder="Dupont" />
                                <Input label="Prénom" placeholder="Jean" />
                            </div>
                        </Card>
                    )}

                    <Card className="p-6 space-y-4">
                        <h3 className="font-semibold text-navy-900 border-b border-slate-100 pb-2 mb-4">
                            {isSubscription ? "2. Paiement" : "Moyen de paiement"}
                        </h3>

                        <div className="p-4 border border-brand-100 bg-brand-50/50 rounded-xl flex items-center gap-3 mb-4">
                            <CreditCard className="text-brand-600" />
                            <span className="text-sm text-brand-900 font-bold">Carte Bancaire (Visa, Mastercard, CB)</span>
                        </div>

                        <Input label="Numéro de carte" placeholder="0000 0000 0000 0000" />
                        <div className="grid grid-cols-2 gap-4">
                            <Input label="Date d'expiration" placeholder="MM/AA" />
                            <Input label="CVC" placeholder="123" />
                        </div>
                    </Card>

                    <Button size="lg" className="w-full bg-accent-500 hover:bg-accent-600 text-white font-bold py-8 rounded-2xl shadow-glow transition-all" onClick={handlePayment} isLoading={loading}>
                        {isSubscription ? `Activer mon plan (${plan.price})` : `Payer ${plan.price}`}
                    </Button>
                    <p className="text-xs text-center text-slate-400">En cliquant, vous acceptez les CGV et la Politique de Confidentialité.</p>
                </div>

                {/* Right Column: Order Summary */}
                <div className="md:pl-8">
                    <div className="sticky top-24 space-y-6">
                        <Card className="p-6 bg-navy-900 text-white border-0 shadow-2xl relative overflow-hidden">
                            {/* Background Pattern */}
                            <div className="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-16 -mt-16" />

                            <div className="relative z-10">
                                <h3 className="text-lg font-semibold text-slate-300 mb-1">Récapitulatif de votre commande</h3>
                                <div className="text-3xl font-bold text-white mb-6">{plan.name}</div>

                                <div className="space-y-4 mb-8">
                                    {plan.features.map((feature, i) => (
                                        <div key={i} className="flex items-center gap-3 text-slate-300 text-sm">
                                            <div className="w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                                                <Check size={12} />
                                            </div>
                                            {feature}
                                        </div>
                                    ))}
                                </div>

                                <div className="border-t border-white/10 pt-4 flex justify-between items-end">
                                    <span className="text-slate-400">Total aujourd'hui</span>
                                    <span className="text-2xl font-bold text-white">{plan.price}</span>
                                </div>
                            </div>
                        </Card>

                        <div className="flex items-start gap-3 p-4 bg-accent-50 rounded-2xl border border-accent-100 text-accent-800 text-sm">
                            <ShieldCheck size={20} className="shrink-0 mt-0.5 text-accent-600" />
                            <p className="font-medium text-navy-900"><strong>Garantie Satisfait ou Remboursé</strong>. Si vous n'êtes pas satisfait durant les 14 premiers jours, nous vous remboursons intégralement.</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    );
};

export default ProviderCheckout;
