import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Card } from '../components/ui/Card';
import { api } from '../lib/api';
import {
    TrendingUp,
    Users,
    Target,
    DollarSign,
    Loader2,
    ArrowUpRight,
    Briefcase
} from 'lucide-react';
import { AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';
import { cn } from '../lib/utils';

const PerformancePage = () => {
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchPerformance = async () => {
            try {
                const data = await api.stats.fetch();
                setStats(data);
            } catch (error) {
                console.error('Error fetching performance stats:', error);
            } finally {
                setLoading(false);
            }
        };
        fetchPerformance();
    }, []);

    if (loading) {
        return (
            <div className="flex flex-col items-center justify-center py-20">
                <Loader2 className="w-8 h-8 text-accent-500 animate-spin mb-4" />
                <p className="text-navy-700 font-bold">Analyse de vos performances...</p>
            </div>
        );
    }

    const conversionRate = stats?.totalLeads > 0
        ? Math.round((stats.totalQuotes / stats.totalLeads) * 100)
        : 0;

    const cards = [
        {
            title: 'Chiffre d\'Affaires',
            value: `${(stats?.totalRevenue || 0).toLocaleString()} €`,
            sub: 'Signé et validé',
            icon: DollarSign,
            color: 'text-accent-600',
            bg: 'bg-accent-50'
        },
        {
            title: 'Taux de Transformation',
            value: `${conversionRate} %`,
            sub: 'Leads vers Devis',
            icon: Target,
            color: 'text-brand-600',
            bg: 'bg-brand-50'
        },
        {
            title: 'Total Leads',
            value: stats?.totalLeads || 0,
            sub: 'Demandes reçues',
            icon: Users,
            color: 'text-navy-600',
            bg: 'bg-navy-50'
        },
        {
            title: 'Devis Émis',
            value: stats?.totalQuotes || 0,
            sub: 'Propositions envoyées',
            icon: Briefcase,
            color: 'text-accent-500',
            bg: 'bg-accent-50'
        },
    ];

    return (
        <div className="space-y-8 max-w-6xl mx-auto">
            <header>
                <h1 className="text-3xl font-bold text-navy-950 tracking-tight">Analyses et Performances</h1>
                <p className="text-navy-800 font-bold mt-1">Mesurez l'efficacité de votre activité commerciale</p>
            </header>

            {/* KPI Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {cards.map((card, idx) => (
                    <motion.div
                        key={card.title}
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: idx * 0.1 }}
                    >
                        <Card className="p-6">
                            <div className="flex justify-between items-start mb-4">
                                <div className={cn("p-2 rounded-xl", card.bg, card.color)}>
                                    <card.icon size={24} />
                                </div>
                                <span className={cn(
                                    "text-xs font-bold px-2 py-1 rounded-full flex items-center gap-1",
                                    (stats?.revenueGrowth || 0) >= 0 ? "text-accent-600 bg-accent-50" : "text-red-600 bg-red-50"
                                )}>
                                    <ArrowUpRight size={12} className={(stats?.revenueGrowth || 0) < 0 ? "rotate-90" : ""} />
                                    {(stats?.revenueGrowth || 0) >= 0 ? '+' : ''}{stats?.revenueGrowth || 0}%
                                </span>
                            </div>
                            <h3 className="text-navy-700 text-sm font-bold">{card.title}</h3>
                            <div className="text-2xl font-bold text-navy-950 mt-1">{card.value}</div>
                            <p className="text-xs text-navy-500 font-bold mt-2">{card.sub}</p>
                        </Card>
                    </motion.div>
                ))}
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <Card className="lg:col-span-2 p-8">
                    <div className="flex justify-between items-center mb-8">
                        <div>
                            <h3 className="font-bold text-navy-950 text-xl font-display">Croissance des Revenus</h3>
                            <p className="text-sm text-navy-700 font-bold">Performance hebdomadaire</p>
                        </div>
                    </div>

                    <div className="h-[250px] w-full mt-4">
                        <ResponsiveContainer width="100%" height="100%">
                            <AreaChart data={stats?.weeklyData || []}>
                                <defs>
                                    <linearGradient id="performanceGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="5%" stopColor="#7CCB63" stopOpacity={0.3} />
                                        <stop offset="95%" stopColor="#7CCB63" stopOpacity={0} />
                                    </linearGradient>
                                </defs>
                                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                                <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                                <YAxis axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                                <Tooltip
                                    contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 15px -3px rgba(0,0,0,0.1)' }}
                                />
                                <Area type="monotone" dataKey="revenue" stroke="#7CCB63" strokeWidth={3} fill="url(#performanceGradient)" />
                            </AreaChart>
                        </ResponsiveContainer>
                    </div>

                    <div className="mt-8 pt-8 border-t border-slate-100">
                        <div className="grid grid-cols-2 gap-8">
                            <div>
                                <div className="flex justify-between text-sm mb-2">
                                    <span className="font-medium text-navy-900">Conversion Leads/Devis</span>
                                    <span className="text-blue-600 font-bold">{conversionRate}%</span>
                                </div>
                                <div className="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <motion.div
                                        className="h-full bg-accent-500"
                                        initial={{ width: 0 }}
                                        animate={{ width: `${conversionRate}%` }}
                                        transition={{ duration: 1 }}
                                    />
                                </div>
                            </div>
                            <div>
                                <div className="flex justify-between text-sm mb-2">
                                    <span className="font-medium text-navy-900">Objectif Mensuel</span>
                                    <span className="text-emerald-600 font-bold">100%</span>
                                </div>
                                <div className="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div className="h-full bg-accent-500 w-full" />
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>

                {/* Insights */}
                <Card className="p-8 bg-navy-900 text-white border-none relative overflow-hidden">
                    <div className="relative z-10">
                        <TrendingUp className="text-blue-400 mb-4" size={32} />
                        <h3 className="text-xl font-bold mb-4">Recommandation</h3>
                        <p className="text-navy-200 text-sm leading-relaxed mb-6">
                            {conversionRate < 20
                                ? "Votre taux de conversion est inférieur à la moyenne du secteur. Essayez de rappeler vos prospects dans les 15 minutes suivant leur demande pour augmenter vos chances de signature de 50%."
                                : "Excellent travail ! Votre taux de conversion est exemplaire. Pensez à augmenter votre pack de leads pour passer à l'échelle supérieure."}
                        </p>
                        <button
                            onClick={() => alert('Fonctionnalité en développement - Contactez le support pour optimiser vos rappels')}
                            className="w-full py-3 bg-accent-500 hover:bg-accent-600 rounded-xl font-bold transition-colors text-sm text-white shadow-glow"
                        >
                            Optimiser mes rappels
                        </button>
                    </div>
                    <div className="absolute -bottom-10 -right-10 opacity-10">
                        <TrendingUp size={200} />
                    </div>
                </Card>
            </div>
        </div>
    );
};

export default PerformancePage;