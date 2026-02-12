import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Button } from '../ui/Button';
import { Card } from '../ui/Card';
import { Input } from '../ui/Input';
import { ProgressBar } from '../ui/ProgressBar';
import { Shield, Hammer, Car, Smartphone, Check, ArrowRight, ArrowLeft, FileText, User, AlertTriangle, Lock, Info } from 'lucide-react';
import { cn } from '../../lib/utils';
import { api } from '../../lib/api';

const steps = [
    { id: 1, title: 'Secteur', icon: Shield },
    { id: 2, title: 'Projet', icon: FileText },
    { id: 3, title: 'Disponibilité', icon: Info },
    { id: 4, title: 'Coordonnées', icon: User },
    { id: 5, title: 'Consentement', icon: Check },
];

const timeSlots = [
    { id: 'matin', label: 'Matin (9h-12h)' },
    { id: 'midi', label: 'Midi (12h-14h)' },
    { id: 'apres-midi', label: 'Après-midi (14h-18h)' },
    { id: 'soir', label: 'Soir (18h-20h)' },
    { id: 'weekend', label: 'Week-end' },
];

const sectors = [
    { id: 'assurance', label: 'Assurance', icon: Shield },
    { id: 'renovation', label: 'Rénovation', icon: Hammer },
    { id: 'garage', label: 'Garage', icon: Car },
    { id: 'telecom', label: 'Télécom', icon: Smartphone },
];

const LeadFormWizard = () => {
    const [currentStep, setCurrentStep] = useState(1);
    const [isSubmitted, setIsSubmitted] = useState(false);
    const [requestId, setRequestId] = useState('');

    const [formData, setFormData] = useState({
        sector: '',
        description: '',
        timeSlot: '',
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        address: '',
        consentTelemarketing: false,
        consentPrivacy: false,
    });

    const nextStep = () => setCurrentStep(prev => Math.min(prev + 1, steps.length));
    const prevStep = () => setCurrentStep(prev => Math.max(prev - 1, 1));

    const handleSubmit = async () => {
        try {
            const leadData = {
                name: `${formData.firstName} ${formData.lastName}`,
                email: formData.email,
                phone: formData.phone,
                address: formData.address,
                sector: formData.sector,
                need: formData.description,
                time_slot: formData.timeSlot,
                budget: "Non spécifié"
            };

            const response = await api.leads.create(leadData);
            setRequestId("RQM-" + (response.leadId || Math.floor(100000000 + Math.random() * 900000000)));
            setIsSubmitted(true);
        } catch (error) {
            console.error('Error submitting lead:', error);
            alert("Erreur lors de l'envoi de votre demande. Veuillez réessayer.");
        }
    };

    const resetForm = () => {
        setFormData({
            sector: '',
            description: '',
            timeSlot: '',
            firstName: '',
            lastName: '',
            email: '',
            phone: '',
            address: '',
            consentTelemarketing: false,
            consentPrivacy: false,
        });
        setCurrentStep(1);
        setIsSubmitted(false);
    };

    const progress = (currentStep / steps.length) * 100;

    // Validation
    const isStepValid = () => {
        switch (currentStep) {
            case 1: return !!formData.sector;
            case 2: return !!formData.description && formData.description.length > 5;
            case 3: return !!formData.timeSlot;
            case 4: return !!formData.firstName && !!formData.lastName && !!formData.email && !!formData.phone && !!formData.address;
            case 5: return formData.consentTelemarketing && formData.consentPrivacy;
            default: return false;
        }
    };

    if (isSubmitted) {
        return (
            <div className="w-full max-w-2xl mx-auto p-4">
                <Card className="min-h-[600px] flex flex-col items-center justify-center text-center p-8 bg-white/90">
                    <motion.div
                        initial={{ scale: 0 }}
                        animate={{ scale: 1 }}
                        className="w-20 h-20 bg-accent-50 rounded-full flex items-center justify-center text-accent-500 mb-6 border-4 border-accent-100 shadow-glow"
                    >
                        <Check size={40} strokeWidth={3} />
                    </motion.div>

                    <h2 className="text-3xl font-bold text-navy-900 mb-2">Votre demande a été enregistrée !</h2>
                    <p className="text-slate-600 mb-8 max-w-md">
                        Un ou plusieurs professionnels vous contacteront dans les prochaines heures.
                    </p>

                    <div className="bg-slate-50 w-full max-w-md rounded-xl p-6 text-left mb-8 border border-slate-100">
                        <h3 className="font-bold text-navy-900 mb-4 text-lg">Récapitulatif de votre demande</h3>
                        <div className="space-y-2 text-sm">
                            <p><span className="font-semibold text-navy-800">Numéro de demande :</span> {requestId}</p>
                            <p><span className="font-semibold text-navy-800">Date :</span> {new Date().toLocaleDateString('fr-FR')} à {new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}</p>
                            <p><span className="font-semibold text-navy-800">Créneau souhaité :</span> {formData.timeSlot}</p>
                        </div>
                        <p className="text-xs text-slate-400 mt-4 pt-4 border-t border-slate-200">
                            Un email de confirmation avec la preuve de votre consentement vous a été envoyé.
                        </p>
                    </div>

                    <Button size="lg" onClick={resetForm} className="w-full max-w-xs bg-navy-950 hover:bg-navy-900 shadow-premium">
                        Faire une nouvelle demande
                    </Button>
                </Card>
            </div>
        );
    }

    return (
        <div className="w-full max-w-2xl mx-auto p-4">
            <Card className="min-h-[600px] flex flex-col justify-between relative overflow-hidden bg-white/95">

                {/* Header */}
                <div className="mb-8">
                    <h2 className="text-2xl font-bold text-navy-900 mb-2">
                        {currentStep === 5 ? "Confirmation et consentement" : "Votre demande de rappel"}
                    </h2>
                    <ProgressBar progress={progress} steps={steps.length} currentStep={currentStep} />
                </div>

                {/* Content */}
                <div className="flex-1 w-full relative">
                    <AnimatePresence mode="wait">
                        <motion.div
                            key={currentStep}
                            initial={{ x: 20, opacity: 0 }}
                            animate={{ x: 0, opacity: 1 }}
                            exit={{ x: -20, opacity: 0 }}
                            transition={{ duration: 0.3 }}
                            className="w-full h-full flex flex-col"
                        >
                            {currentStep === 1 && (
                                <div className="grid grid-cols-2 gap-4 h-full content-center">
                                    {sectors.map((s) => (
                                        <button
                                            key={s.id}
                                            onClick={() => setFormData({ ...formData, sector: s.id })}
                                            className={cn(
                                                "flex flex-col items-center justify-center p-6 rounded-2xl border-2 transition-all duration-300 gap-4",
                                                formData.sector === s.id
                                                    ? "border-accent-500 bg-accent-50 text-accent-700 shadow-md transform scale-105"
                                                    : "border-slate-100 hover:border-accent-200 hover:bg-slate-50 text-slate-500"
                                            )}
                                        >
                                            <s.icon size={40} className={formData.sector === s.id ? "text-accent-500" : "text-slate-400"} />
                                            <span className="font-semibold text-lg">{s.label}</span>
                                        </button>
                                    ))}
                                </div>
                            )}

                            {currentStep === 2 && (
                                <div className="space-y-6 flex flex-col justify-center h-full">
                                    <div className="text-center mb-4">
                                        <h3 className="text-xl font-semibold text-navy-800">Décrivez votre besoin</h3>
                                        <p className="text-slate-500">Dites-nous en plus pour que l'expert puisse préparer l'entretien.</p>
                                    </div>
                                    <div className="relative">
                                        <textarea
                                            className="w-full h-48 p-4 rounded-xl border border-slate-200 focus:border-accent-500 focus:ring-0 resize-none text-navy-900 placeholder:text-slate-400 shadow-sm font-medium"
                                            placeholder="Ex: Je souhaite refaire l'isolation de mes combles..."
                                            value={formData.description}
                                            onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                                        />
                                        <div className="absolute bottom-4 right-4 text-xs text-slate-400">
                                            {formData.description.length} caractères
                                        </div>
                                    </div>
                                </div>
                            )}

                            {currentStep === 3 && (
                                <div className="space-y-6 flex flex-col justify-center h-full">
                                    <div className="text-center mb-4">
                                        <h3 className="text-xl font-semibold text-navy-800">Vos disponibilités</h3>
                                        <p className="text-slate-500">Quand préférez-vous être rappelé ?</p>
                                    </div>
                                    <div className="grid grid-cols-1 gap-3">
                                        {timeSlots.map((slot) => (
                                            <button
                                                key={slot.id}
                                                onClick={() => setFormData({ ...formData, timeSlot: slot.label })}
                                                className={cn(
                                                    "w-full p-4 rounded-xl border text-left transition-all duration-200 flex items-center justify-between group",
                                                    formData.timeSlot === slot.label
                                                        ? "border-accent-500 bg-accent-50 text-accent-700 shadow-sm"
                                                        : "border-slate-200 hover:border-accent-300 hover:bg-slate-50"
                                                )}
                                            >
                                                <span className="font-medium">{slot.label}</span>
                                                <div className={cn(
                                                    "w-5 h-5 rounded-full border flex items-center justify-center transition-colors",
                                                    formData.timeSlot === slot.label
                                                        ? "border-accent-500 bg-accent-500"
                                                        : "border-slate-300 group-hover:border-accent-400"
                                                )}>
                                                    {formData.timeSlot === slot.label && <Check size={12} className="text-white" />}
                                                </div>
                                            </button>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {currentStep === 4 && (
                                <div className="space-y-5 flex flex-col justify-center h-full">
                                    <div className="text-center mb-4">
                                        <h3 className="text-xl font-semibold text-navy-800">Vos Coordonnées</h3>
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <Input
                                            label="Nom"
                                            placeholder="Dupont"
                                            value={formData.lastName}
                                            onChange={(e) => setFormData({ ...formData, lastName: e.target.value })}
                                        />
                                        <Input
                                            label="Prénom"
                                            placeholder="Jean"
                                            value={formData.firstName}
                                            onChange={(e) => setFormData({ ...formData, firstName: e.target.value })}
                                        />
                                    </div>
                                    <Input
                                        label="Email"
                                        type="email"
                                        placeholder="jean.dupont@email.com"
                                        value={formData.email}
                                        onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                                    />
                                    <Input
                                        label="Téléphone"
                                        type="tel"
                                        placeholder="06 12 34 56 78"
                                        value={formData.phone}
                                        onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                                    />
                                    <Input
                                        label="Adresse complète"
                                        placeholder="10 rue de la Paix, 75001 Paris"
                                        value={formData.address}
                                        onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                                    />
                                </div>
                            )}

                            {currentStep === 5 && (
                                <div className="space-y-6 text-sm text-navy-900">
                                    {/* Yellow Info Box */}
                                    <div className="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg">
                                        <div className="flex items-center gap-2 mb-2">
                                            <FileText size={18} className="text-amber-700" />
                                            <span className="font-bold text-amber-900">Important - Cadre légal</span>
                                        </div>
                                        <p className="text-amber-800 leading-relaxed text-xs">
                                            Conformément à la loi française n° 2025-594 du 30 juin 2025 et au RGPD, vous êtes sur le point de <span className="font-bold">donner votre consentement explicite</span> pour être contacté par des professionnels.
                                        </p>
                                    </div>

                                    {/* Consent Box */}
                                    <div className="border border-amber-200 rounded-xl p-5 bg-amber-50/30">
                                        <div className="flex items-center gap-2 mb-3">
                                            <Lock size={16} className="text-navy-700" />
                                            <h4 className="font-bold text-navy-900">Votre consentement est requis</h4>
                                        </div>
                                        <p className="font-semibold mb-2 text-xs">Vous comprenez et acceptez que :</p>
                                        <ul className="list-disc pl-5 space-y-1 text-slate-700 text-xs mb-4 marker:text-navy-400">
                                            <li>Votre demande sera transmise à des prestataires professionnels sélectionnés</li>
                                            <li>Ces prestataires vous contacteront par téléphone sur le numéro fourni</li>
                                            <li>Vous autorisez explicitement ces appels suite à votre demande volontaire</li>
                                            <li>Vos données seront conservées de manière sécurisée pendant 36 mois</li>
                                            <li>Vous pouvez retirer votre consentement à tout moment via contact@rappelez-moi.fr</li>
                                        </ul>

                                        <div className="space-y-3">
                                            <label className="flex items-start gap-3 cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    checked={formData.consentTelemarketing}
                                                    onChange={(e) => setFormData({ ...formData, consentTelemarketing: e.target.checked })}
                                                    className="mt-0.5 rounded border-slate-300 text-accent-500 focus:ring-accent-500"
                                                />
                                                <span className="text-xs text-navy-800">
                                                    <strong>J'accepte explicitement</strong> que les informations fournies soient utilisées pour être mis en relation avec des professionnels qualifiés qui me contacteront par téléphone selon mes disponibilités. Je comprends que je fais une <strong>demande volontaire de rappel</strong> et que cela ne constitue pas du démarchage non sollicité.
                                                </span>
                                            </label>

                                            <label className="flex items-start gap-3 cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    checked={formData.consentPrivacy}
                                                    onChange={(e) => setFormData({ ...formData, consentPrivacy: e.target.checked })}
                                                    className="mt-0.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <span className="text-xs text-navy-800">
                                                    J'ai pris connaissance de la <a href="#" className="text-blue-600 underline">Politique de confidentialité</a> et j'accepte le traitement de mes données personnelles.
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    {/* GDPR/Rights Box */}
                                    <div className="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg">
                                        <div className="flex items-center gap-2 mb-1">
                                            <Shield size={16} className="text-amber-700" />
                                            <span className="font-bold text-amber-900 text-xs">Vos droits RGPD</span>
                                        </div>
                                        <p className="text-amber-800 text-xs">
                                            Droit d'accès, de rectification, d'effacement, de limitation, d'opposition et de portabilité. Contact : dpo@rappelez-moi.fr
                                        </p>
                                    </div>

                                </div>
                            )}
                        </motion.div>
                    </AnimatePresence>
                </div>

                {/* Footer Navigation */}
                <div className="flex justify-between items-center mt-6 pt-6 border-t border-slate-100">
                    <Button
                        variant="ghost"
                        onClick={prevStep}
                        disabled={currentStep === 1}
                        className={cn("text-navy-900 hover:bg-slate-100 pl-0 hover:pl-2 transition-all", currentStep === 1 ? "opacity-0 pointer-events-none" : "opacity-100")}
                    >
                        <ArrowLeft className="mr-2 w-4 h-4" /> Retour
                    </Button>

                    <Button
                        onClick={currentStep === steps.length ? handleSubmit : nextStep}
                        disabled={!isStepValid()}
                        className={cn(
                            "transition-all duration-300 min-w-[140px] font-bold py-6 rounded-xl",
                            currentStep === steps.length ? "bg-accent-500 hover:bg-accent-600 text-white shadow-glow" : "bg-navy-900 hover:bg-navy-800 text-white shadow-premium",
                            isStepValid() ? "translate-x-0" : "opacity-50 grayscale"
                        )}
                        size="lg"
                    >
                        {currentStep === steps.length ? (
                            <>
                                <Check className="mr-2 w-4 h-4" /> Confirmer ma demande de rappel
                            </>
                        ) : (
                            <>
                                {currentStep === 1 ? "Commencer" : "Étape suivante"} <ArrowRight className="ml-2 w-4 h-4" />
                            </>
                        )}
                    </Button>
                </div>
            </Card>
        </div>
    );
};

export { LeadFormWizard };
