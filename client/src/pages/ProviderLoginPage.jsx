import React, { useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../context/useAuth';
import { Button } from '../components/ui/Button';
import { Input } from '../components/ui/Input';
import { Card } from '../components/ui/Card';
import { LogIn, Mail, Lock, Loader2, ArrowLeft } from 'lucide-react';
import { motion } from 'framer-motion';
import { Logo } from '../components/ui/Logo';

const ProviderLoginPage = () => {
    const navigate = useNavigate();
    const location = useLocation();
    const { login } = useAuth();

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    const from = location.state?.from?.pathname || "/pro/dashboard";

    const handleLogin = async (e) => {
        e.preventDefault();
        if (!email || !password) {
            setError("Veuillez remplir tous les champs.");
            return;
        }

        setLoading(true);
        setError('');

        try {
            await login(email, password);
            navigate(from, { replace: true });
        } catch (err) {
            setError(err.message || "Identifiants invalides.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-transparent flex items-center justify-center p-6 font-sans relative overflow-hidden">
            <button
                onClick={() => navigate('/pro')}
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
                <Card className="p-10 space-y-10 border-white/60 bg-white/40 backdrop-blur-3xl shadow-premium rounded-[2.5rem]">
                    <div className="text-center space-y-6">
                        <Logo className="justify-center scale-110 mb-8" />
                        <div className="space-y-2">
                            <h1 className="text-4xl font-display font-bold text-navy-950 tracking-tight">Accès Expert</h1>
                            <p className="text-navy-500 font-medium">Gérez vos opportunités et votre croissance.</p>
                        </div>
                    </div>

                    <form onSubmit={handleLogin} className="space-y-8">
                        <div className="space-y-5">
                            <Input
                                label="Identifiant Professionnel"
                                type="email"
                                placeholder="nom@expert.com"
                                icon={<Mail size={20} className="text-navy-400" />}
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                className="h-14 rounded-2xl border-navy-100 focus:ring-accent-500"
                                required
                            />
                            <Input
                                label="Clé de Sécurité"
                                type="password"
                                placeholder="••••••••"
                                icon={<Lock size={20} className="text-navy-400" />}
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                className="h-14 rounded-2xl border-navy-100 focus:ring-accent-500"
                                required
                            />
                        </div>

                        {error && (
                            <motion.div
                                initial={{ opacity: 0, x: -10 }}
                                animate={{ opacity: 1, x: 0 }}
                                className="p-4 bg-red-50 text-red-600 text-sm rounded-2xl border border-red-100 font-bold flex items-center gap-3"
                            >
                                <span className="w-1.5 h-1.5 rounded-full bg-red-600" />
                                {error}
                            </motion.div>
                        )}

                        <Button
                            type="submit"
                            size="lg"
                            showShine
                            className="w-full h-14 rounded-2xl shadow-premium text-lg font-bold"
                            disabled={loading}
                        >
                            {loading ? <Loader2 className="animate-spin" /> : "Identifier la session"}
                        </Button>
                    </form>

                    <div className="text-center pt-8 border-t border-navy-100/50">
                        <p className="text-sm font-medium text-navy-400">
                            Nouveau partenaire ?{' '}
                            <button
                                onClick={() => navigate('/pro/signup')}
                                className="text-accent-600 font-bold hover:text-accent-700 transition-colors ml-1"
                            >
                                Créer un profil expert
                            </button>
                        </p>
                    </div>
                </Card>
            </motion.div>
        </div>
    );
};

export default ProviderLoginPage;
