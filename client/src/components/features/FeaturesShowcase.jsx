import React from 'react';
import { motion } from 'framer-motion';
import { ShieldCheck, UserCheck, Zap, Lock } from 'lucide-react';

const features = [
    {
        icon: ShieldCheck,
        title: "Zero Spam Garanti",
        description: "Vos coordonnées restent masquées jusqu'à votre validation. Fini le harcèlement téléphonique."
    },
    {
        icon: UserCheck,
        title: "Experts Vérifiés",
        description: "Chaque professionnel est audité : SIRET, assurance décennale et avis clients contrôlés."
    },
    {
        icon: Zap,
        title: "Rappel Instantané",
        description: "Choisissez votre créneau. Le professionnel s'engage à vous contacter à l'heure précise."
    },
    {
        icon: Lock,
        title: "Données Sécurisées",
        description: "Hébergement en France, conforme RGPD. Vous restez maître de vos informations."
    }
];

const FeaturesShowcase = () => {
    return (
        <section className="py-24 bg-white">
            <div className="container mx-auto px-4">
                <div className="text-center max-w-2xl mx-auto mb-16">
                    <h2 className="text-3xl lg:text-4xl font-bold text-navy-900 mb-4"> Pourquoi choisir Rappelez-moi ?</h2>
                    <p className="text-slate-500 text-lg">Une plateforme pensée pour votre tranquillité et votre sécurité.</p>
                </div>

                <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    {features.map((feature, index) => (
                        <motion.div
                            key={index}
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            transition={{ delay: index * 0.1, duration: 0.5 }}
                            className="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-lg transition-all duration-300 group"
                        >
                            <div className="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-emerald-500 mb-6 group-hover:scale-110 transition-transform duration-300">
                                <feature.icon size={24} />
                            </div>
                            <h3 className="text-xl font-bold text-navy-900 mb-3">{feature.title}</h3>
                            <p className="text-slate-500 leading-relaxed">{feature.description}</p>
                        </motion.div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export { FeaturesShowcase };
