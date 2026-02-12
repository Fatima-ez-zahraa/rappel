import React from 'react';
import { HeroSection } from '../components/features/HeroSection';
import { FeaturesShowcase } from '../components/features/FeaturesShowcase';
import { LeadFormWizard } from '../components/features/LeadFormWizard';
import { ShieldCheck } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';

import { Header } from '../components/features/Header';
import { Logo } from '../components/ui/Logo';

const LandingPage = () => {
    return (
        <main className="min-h-screen bg-transparent">
            <Header />
            <HeroSection />

            <motion.div
                id="demande"
                initial={{ opacity: 0, y: 50 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true, margin: "-100px" }}
                transition={{ duration: 0.8 }}
                className="py-24 bg-transparent relative overflow-hidden"
            >
                <div className="container mx-auto px-6 lg:px-12 relative z-10">
                    <div className="text-center mb-16">
                        <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-accent-100 text-accent-700 text-sm font-bold mb-6 border border-accent-200">
                            <ShieldCheck size={16} />
                            <span>Simple & Sécurisé</span>
                        </div>
                        <h2 className="text-4xl lg:text-5xl font-display font-bold text-navy-950 tracking-tight">Configurez votre demande</h2>
                        <p className="text-navy-500 font-medium mt-4 text-lg">Cela ne prend que 2 minutes chrono.</p>
                    </div>
                    <LeadFormWizard />
                </div>
            </motion.div>

            <motion.div
                initial={{ opacity: 0 }}
                whileInView={{ opacity: 1 }}
                viewport={{ once: true }}
                transition={{ duration: 1 }}
            >
                <FeaturesShowcase />
            </motion.div>

            <footer className="relative bg-navy-950 text-white py-16 px-4 overflow-hidden">
                <div className="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-accent-500/50 to-transparent" />
                <div className="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8 relative z-10">
                    <div className="flex flex-col items-center md:items-start gap-4">
                        <p className="text-navy-300 text-sm max-w-xs text-center md:text-left font-medium">
                            La plateforme de mise en relation intelligente entre particuliers et experts de confiance.
                        </p>
                    </div>
                    <div className="flex flex-col items-center md:items-end gap-3">
                        <p className="text-navy-500 text-[10px] font-bold uppercase tracking-[0.2em] mt-4">
                            &copy; 2026 Rappelez-moi. Tous droits réservés.
                        </p>
                    </div>
                </div>
            </footer>
        </main>
    );
};

export default LandingPage;
