import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Button } from '../ui/Button';
import { Shield, Hammer, Car, Smartphone, ArrowRight, CheckCircle2 } from 'lucide-react';
import { cn } from '../../lib/utils';

const sectors = [
    {
        id: 'assurance',
        label: 'Assurance',
        icon: Shield,
        color: 'bg-indigo-500',
        description: 'Protection optimale pour votre famille et vos biens.',
        image: 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&q=80&w=800' // Placeholder
    },
    {
        id: 'renovation',
        label: 'Rénovation',
        icon: Hammer,
        color: 'bg-orange-500',
        description: 'Transformez votre habitat avec des artisans certifiés.',
        image: 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&q=80&w=800'
    },
    {
        id: 'garage',
        label: 'Garage',
        icon: Car,
        color: 'bg-blue-500',
        description: 'Entretien et réparations par des experts automobiles.',
        image: 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?auto=format&fit=crop&q=80&w=800'
    },
    {
        id: 'telecom',
        label: 'Télécom',
        icon: Smartphone,
        color: 'bg-purple-500',
        description: 'Forfaits et connexions adaptés à vos besoins.',
        image: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=800'
    }
];

import { useNavigate } from 'react-router-dom';

const HeroSection = () => {
    const navigate = useNavigate();
    const [activeSector, setActiveSector] = useState(0);

    useEffect(() => {
        const timer = setInterval(() => {
            setActiveSector((prev) => (prev + 1) % sectors.length);
        }, 5000);
        return () => clearInterval(timer);
    }, []);

    return (
        <section className="relative w-full min-h-screen flex items-center justify-center overflow-hidden bg-transparent pt-20">
            {/* Background Decorations */}
            <div className="absolute top-20 left-10 w-64 h-64 bg-accent-500/10 rounded-full blur-3xl animate-float" />
            <div className="absolute bottom-20 right-10 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl animate-float" style={{ animationDelay: '-3s' }} />

            <div className="container mx-auto px-6 lg:px-12 grid lg:grid-cols-2 gap-16 items-center z-10">

                {/* Text Content */}
                <div className="space-y-10">
                    <motion.div
                        initial={{ opacity: 0, x: -30 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 0.8, ease: "easeOut" }}
                    >
                        <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-accent-100/50 backdrop-blur-md text-accent-700 text-sm font-bold mb-8 border border-accent-200/50">
                            <CheckCircle2 size={16} />
                            <span className="tracking-wide">CONFORME LOI 2025-594</span>
                        </div>
                        <h1 className="text-5xl lg:text-7xl font-display font-bold text-navy-950 leading-[1.1] tracking-tight">
                            Le rappel que vous attendez, <br />
                            <span className="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-600">
                                au moment où vous le décidez.
                            </span>
                        </h1>
                        <p className="text-xl text-navy-600/80 max-w-lg mt-8 leading-relaxed font-sans font-medium">
                            Reprenez le contrôle sur vos communications. Connectez-vous avec des experts au moment idéal.
                        </p>
                    </motion.div>

                    <motion.div
                        className="flex flex-wrap gap-5"
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.6, delay: 0.3 }}
                    >
                        <Button
                            size="lg"
                            showShine
                            className="rounded-[2rem] px-10"
                            onClick={() => document.getElementById('demande')?.scrollIntoView({ behavior: 'smooth' })}
                        >
                            Commencer <ArrowRight className="ml-2 w-5 h-5" />
                        </Button>
                        <Button
                            variant="glass"
                            size="lg"
                            className="rounded-[2rem] px-10 border-navy-100 text-navy-900"
                            onClick={() => navigate('/pro')}
                        >
                            Espace Pro
                        </Button>
                    </motion.div>

                    {/* Sector Navigation (Desktop) */}
                    <div className="hidden lg:flex gap-4 pt-10">
                        {sectors.map((sector, index) => (
                            <button
                                key={sector.id}
                                onClick={() => setActiveSector(index)}
                                className={cn(
                                    "flex items-center gap-3 p-4 rounded-3xl transition-all duration-500 border-2",
                                    activeSector === index
                                        ? "bg-white shadow-premium border-accent-500/20 scale-105"
                                        : "bg-white/20 hover:bg-white/50 border-transparent backdrop-blur-sm"
                                )}
                            >
                                <div className={cn("p-2 rounded-xl text-white shadow-lg", sector.color)}>
                                    <sector.icon size={20} />
                                </div>
                                <span className={cn(
                                    "font-bold text-sm tracking-wide",
                                    activeSector === index ? "text-navy-950" : "text-navy-400"
                                )}>
                                    {sector.label}
                                </span>
                            </button>
                        ))}
                    </div>
                </div>

                {/* Visual / Carousel */}
                <div className="relative h-[550px] w-full lg:h-[700px]">
                    {/* Decorative Ring */}
                    <div className="absolute -inset-4 border-2 border-dashed border-navy-200/30 rounded-[4rem] animate-[spin_20s_linear_infinite]" />

                    <AnimatePresence mode="wait">
                        <motion.div
                            key={activeSector}
                            initial={{ opacity: 0, scale: 0.9, rotateY: 10 }}
                            animate={{ opacity: 1, scale: 1, rotateY: 0 }}
                            exit={{ opacity: 0, scale: 0.9, rotateY: -10 }}
                            transition={{ duration: 0.7, ease: [0.16, 1, 0.3, 1] }}
                            className="relative w-full h-full rounded-[3.5rem] overflow-hidden shadow-premium border-[12px] border-white/80 backdrop-blur-md"
                        >
                            <img
                                src={sectors[activeSector].image}
                                alt={sectors[activeSector].label}
                                className="w-full h-full object-cover transition-transform duration-700 hover:scale-110"
                            />
                            <div className="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-navy-900/20 to-transparent flex flex-col justify-end p-10 lg:p-14">
                                <motion.div
                                    initial={{ opacity: 0, y: 30 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{ delay: 0.3 }}
                                >
                                    <div className={cn("inline-flex p-4 rounded-2xl mb-6 text-white shadow-2xl backdrop-blur-xl bg-white/10 border border-white/20", sectors[activeSector].color)}>
                                        {React.createElement(sectors[activeSector].icon, { size: 40 })}
                                    </div>
                                    <h3 className="text-4xl font-display font-bold text-white mb-3 tracking-tight">{sectors[activeSector].label}</h3>
                                    <p className="text-slate-200 text-xl font-medium leading-relaxed max-w-sm">{sectors[activeSector].description}</p>
                                </motion.div>
                            </div>
                        </motion.div>
                    </AnimatePresence>

                    {/* Floating UI Elements */}
                    <motion.div
                        className="absolute -right-12 top-20 bg-white/90 backdrop-blur-xl p-5 rounded-[2rem] shadow-premium border border-white/50 flex items-center gap-4 z-20"
                        animate={{ y: [0, -15, 0] }}
                        transition={{ duration: 5, repeat: Infinity, ease: "easeInOut" }}
                    >
                        <div className="w-12 h-12 bg-accent-100 rounded-2xl flex items-center justify-center text-accent-600 shadow-inner-light">
                            <Shield size={24} />
                        </div>
                        <div>
                            <p className="text-xs text-navy-400 font-bold uppercase tracking-wider">Confiance</p>
                            <p className="text-base font-display font-bold text-navy-900">100% Vérifié</p>
                        </div>
                    </motion.div>

                </div>

            </div>
        </section>
    );
};

export { HeroSection };
