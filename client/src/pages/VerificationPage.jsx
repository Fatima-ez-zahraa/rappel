import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { Mail, CheckCircle2, ArrowLeft, Loader2 } from 'lucide-react';
import { useAuth } from '../context/useAuth';
import { Button } from '../components/ui/Button';
import { Input } from '../components/ui/Input';
import { Card } from '../components/ui/Card';
import { api } from '../lib/api';
import { Logo } from '../components/ui/Logo';

const VerificationPage = () => {
    const navigate = useNavigate();
    const { refreshUser } = useAuth();
    const [code, setCode] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [resendLoading, setResendLoading] = useState(false);

    const handleVerify = async () => {
        if (code.length !== 6) {
            setError("Le code doit contenir 6 chiffres.");
            return;
        }

        setLoading(true);
        setError('');

        try {
            await api.auth.verify(code);
            await refreshUser();
            navigate('/pro/dashboard');
        } catch (err) {
            setError(err.message || "Code invalide. Veuillez réessayer.");
        } finally {
            setLoading(false);
        }
    };

    const handleResend = async () => {
        const userEmail = localStorage.getItem('rappel_user_email');

        if (!userEmail) {
            setError("Email non trouvé. Veuillez vous réinscrire.");
            return;
        }

        setResendLoading(true);
        setError('');

        try {
            const res = await fetch(`${import.meta.env.VITE_API_URL}/auth/resend-activation`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: userEmail })
            });
            const data = await res.json();

            if (!res.ok) throw new Error(data.error);

            setError(''); // Clear any previous errors
            // Optionally show success message
            alert('Un nouveau code a été envoyé à votre email!');
        } catch (err) {
            setError(err.message || "Erreur lors du renvoi du code.");
        } finally {
            setResendLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-transparent flex items-center justify-center p-6 font-sans relative overflow-hidden">
            <button
                onClick={() => navigate('/pro/signup')}
                className="absolute top-8 left-8 flex items-center gap-2 text-navy-500 hover:text-navy-900 font-bold transition-all z-20 group"
            >
                <ArrowLeft size={20} className="group-hover:-translate-x-1 transition-transform" />
                Retour
            </button>

            {/* Background elements */}
            <div className="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_50%_0%,_rgba(124,203,99,0.08),transparent_50%)]" />

            <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5 }}
                className="w-full max-w-lg z-10"
            >
                <Card className="p-10 text-center space-y-8 border-white/60 bg-white/40 backdrop-blur-3xl shadow-premium rounded-[2.5rem]">
                    <Logo className="justify-center scale-110 mb-4" />
                    <div className="w-20 h-20 bg-navy-950 rounded-3xl flex items-center justify-center mx-auto text-white shadow-glow">
                        <Mail size={36} />
                    </div>

                    <div className="space-y-3">
                        <h1 className="text-3xl font-display font-bold text-navy-950 tracking-tight">Activez votre compte</h1>
                        <p className="text-navy-500 font-medium px-4">
                            Un code de vérification unique a été envoyé à votre adresse email professionnelle.
                        </p>
                    </div>

                    <div className="space-y-6 pt-4">
                        <div className="relative">
                            <Input
                                className="text-center text-3xl tracking-[0.5em] font-mono h-16 rounded-2xl border-navy-100 bg-white/50 focus:bg-white"
                                placeholder="000000"
                                maxLength={6}
                                value={code}
                                onChange={(e) => {
                                    const val = e.target.value.replace(/\D/g, '');
                                    if (val.length <= 6) setCode(val);
                                    if (error) setError('');
                                }}
                            />
                        </div>

                        {error && (
                            <motion.div
                                initial={{ opacity: 0, x: -10 }}
                                animate={{ opacity: 1, x: 0 }}
                                className="p-4 bg-red-50 text-red-600 text-sm rounded-2xl border border-red-100 font-bold flex items-center gap-3 justify-center"
                            >
                                <span className="w-1.5 h-1.5 rounded-full bg-red-600" />
                                {error}
                            </motion.div>
                        )}

                        <Button
                            size="lg"
                            showShine
                            className="w-full h-14 bg-navy-900 hover:bg-navy-800 rounded-2xl shadow-premium text-lg font-bold"
                            onClick={handleVerify}
                            disabled={loading || code.length !== 6}
                        >
                            {loading ? <Loader2 className="animate-spin" /> : "Vérifier mon identité"}
                        </Button>
                    </div>

                    <p className="text-sm font-medium text-navy-400">
                        Problème de réception ?{' '}
                        <button
                            onClick={handleResend}
                            disabled={resendLoading}
                            className="text-accent-600 font-bold hover:text-accent-700 transition-colors ml-1 disabled:opacity-50"
                        >
                            {resendLoading ? 'Envoi...' : 'Renvoyer le code'}
                        </button>
                    </p>
                </Card>
            </motion.div>
        </div>
    );
};

export default VerificationPage;
