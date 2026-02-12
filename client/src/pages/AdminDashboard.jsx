import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Card } from '../components/ui/Card';
import { Button } from '../components/ui/Button';
import {
    Search,
    Users,
    FileText,
    Settings,
    Shield,
    CheckCircle,
    XCircle,
    Loader2,
    Mail,
    Phone,
    MapPin,
    Calendar,
    Zap
} from 'lucide-react';
import { api } from '../lib/api';
import { cn } from '../lib/utils';

const AdminDashboard = () => {
    const [activeTab, setActiveTab] = useState('leads');
    const [leads, setLeads] = useState([]);
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');

    useEffect(() => {
        fetchData();
    }, [activeTab]);

    const fetchData = async () => {
        setLoading(true);
        try {
            if (activeTab === 'leads') {
                const data = await api.admin.fetchLeads();
                setLeads(data);
            } else {
                const data = await api.admin.fetchUsers();
                setUsers(data);
            }
        } catch (error) {
            console.error('Error fetching admin data:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleRoleUpdate = async (userId, currentRole) => {
        const newRole = currentRole === 'admin' ? 'provider' : 'admin';
        try {
            await api.admin.updateUserRole(userId, newRole);
            fetchData();
        } catch (error) {
            alert('Error updating role');
        }
    };

    return (
        <div className="space-y-10 pb-20">
            {/* Header / Stats Summary */}
            <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div>
                    <h1 className="text-4xl font-display font-bold text-navy-950 tracking-tight">Poste de Commandement</h1>
                    <p className="text-navy-800 font-bold mt-1">Supervision globale des flux et des utilisateurs.</p>
                </div>

                <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 w-full lg:w-auto">
                    {[
                        { label: 'Flux Total', value: leads.length, icon: FileText, color: 'text-brand-600' },
                        { label: 'Utilisateurs', value: users.length, icon: Users, color: 'text-accent-600' },
                        { label: 'Status OK', value: '98%', icon: Shield, color: 'text-emerald-600' },
                        { label: 'Uptime', value: '99.9%', icon: Zap, color: 'text-amber-500' },
                    ].map((stat, i) => (
                        <Card key={i} className="p-4 flex flex-col gap-1 min-w-[140px] items-center text-center bg-white/50 border-white/60">
                            <stat.icon size={16} className={stat.color} />
                            <div className="text-xl font-bold text-navy-950">{stat.value}</div>
                            <div className="text-[10px] font-black text-navy-800 uppercase tracking-widest">{stat.label}</div>
                        </Card>
                    ))}
                </div>
            </div>

            {/* Main Interface */}
            <div className="space-y-6">
                <div className="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div className="flex bg-navy-100/50 p-1.5 rounded-2xl w-full md:w-auto shadow-inner-light backdrop-blur-md">
                        <button
                            onClick={() => setActiveTab('leads')}
                            className={cn(
                                "px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-300 flex items-center gap-2",
                                activeTab === 'leads' ? "bg-white text-navy-950 shadow-premium" : "text-navy-700 hover:text-navy-950"
                            )}
                        >
                            <FileText size={18} /> Leads Globaux
                        </button>
                        <button
                            onClick={() => setActiveTab('users')}
                            className={cn(
                                "px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-300 flex items-center gap-2",
                                activeTab === 'users' ? "bg-white text-navy-950 shadow-premium" : "text-navy-700 hover:text-navy-950"
                            )}
                        >
                            <Users size={18} /> Base Utilisateurs
                        </button>
                    </div>

                    <div className="relative w-full md:w-80 group">
                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-navy-300 transition-colors group-focus-within:text-accent-500" size={18} />
                        <input
                            type="text"
                            placeholder="Filtrer les données..."
                            className="w-full pl-11 pr-4 py-3 bg-white/50 border border-navy-100 rounded-2xl text-sm focus:outline-none focus:ring-4 focus:ring-accent-500/10 focus:border-accent-500 transition-all font-medium"
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                        />
                    </div>
                </div>

                {/* Content Area */}
                <div className="relative min-h-[400px]">
                    {loading ? (
                        <div className="absolute inset-0 flex flex-col items-center justify-center bg-white/20 backdrop-blur-sm rounded-3xl z-10 transition-all">
                            <div className="w-12 h-12 border-4 border-navy-100 border-t-accent-500 rounded-full animate-spin mb-4" />
                            <p className="text-navy-700 font-bold tracking-widest text-[10px] uppercase">Interrogation Système...</p>
                        </div>
                    ) : null}

                    <div className={cn("transition-all duration-500", loading ? "opacity-30 pointer-events-none scale-[0.99]" : "opacity-100")}>
                        {activeTab === 'leads' ? (
                            <LeadsTable leads={leads.filter(l => l.name?.toLowerCase().includes(searchTerm.toLowerCase()) || l.email?.toLowerCase().includes(searchTerm.toLowerCase()))} />
                        ) : (
                            <UsersTable users={users.filter(u => u.email?.toLowerCase().includes(searchTerm.toLowerCase()))} onRoleUpdate={handleRoleUpdate} />
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

const LeadsTable = ({ leads }) => (
    <Card className="p-0 overflow-hidden border-navy-100/50 bg-white shadow-premium rounded-[2rem]">
        <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
                <thead>
                    <tr className="border-b border-navy-50">
                        <th className="px-8 py-6 font-bold text-navy-800 uppercase tracking-widest text-[10px]">Propriétaire du Lead</th>
                        <th className="px-8 py-6 font-bold text-navy-800 uppercase tracking-widest text-[10px]">Besoin Identifié</th>
                        <th className="px-8 py-6 font-bold text-navy-800 uppercase tracking-widest text-[10px]">Allocation</th>
                        <th className="px-8 py-6 font-bold text-navy-800 uppercase tracking-widest text-[10px]">Date d'entrée</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-navy-50">
                    {leads.map((lead) => (
                        <tr key={lead.id} className="hover:bg-navy-50/30 transition-colors group">
                            <td className="px-8 py-6">
                                <div className="flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 font-bold uppercase transition-transform group-hover:scale-110">
                                        {lead.name?.charAt(0)}
                                    </div>
                                    <div>
                                        <div className="font-bold text-navy-950 text-base">{lead.name}</div>
                                        <div className="text-navy-800 font-bold">{lead.email}</div>
                                    </div>
                                </div>
                            </td>
                            <td className="px-8 py-6">
                                <span className="px-3 py-1 rounded-full bg-accent-50 text-accent-700 font-bold text-xs">
                                    {lead.need}
                                </span>
                                <div className="text-navy-800 font-bold mt-1 text-xs px-1">Budget: {lead.budget}</div>
                            </td>
                            <td className="px-8 py-6">
                                {lead.assigned_provider ? (
                                    <div className="flex items-center gap-2 text-emerald-600 font-bold">
                                        <CheckCircle size={14} />
                                        <span>{lead.assigned_provider}</span>
                                    </div>
                                ) : (
                                    <div className="flex items-center gap-2 text-amber-500 font-bold animate-pulse">
                                        <div className="w-2 h-2 rounded-full bg-current" />
                                        <span>En attente</span>
                                    </div>
                                )}
                            </td>
                            <td className="px-8 py-6 text-navy-800 font-black">
                                {new Date(lead.created_at).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' })}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
        {leads.length === 0 && (
            <div className="p-20 text-center">
                <div className="inline-flex p-6 rounded-full bg-navy-50 text-navy-400 mb-4 items-center justify-center">
                    <FileText size={48} />
                </div>
                <div className="text-navy-950 font-bold text-xl">Aucune donnée de lead active</div>
                <p className="text-navy-800 font-bold max-w-xs mx-auto mt-2">Le système est prêt mais aucun lead n'a été enregistré pour le moment.</p>
            </div>
        )}
    </Card>
);

const UsersTable = ({ users, onRoleUpdate }) => (
    <Card className="p-0 overflow-hidden border-navy-100/50 bg-white shadow-premium rounded-[2rem]">
        <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
                <thead>
                    <tr className="border-b border-navy-50">
                        <th className="px-8 py-6 font-bold text-navy-800 uppercase tracking-widest text-[10px]">Identité</th>
                        <th className="px-8 py-6 font-bold text-navy-800 uppercase tracking-widest text-[10px]">Privilèges</th>
                        <th className="px-8 py-6 font-bold text-navy-800 uppercase tracking-widest text-[10px]">Abonnement</th>
                        <th className="px-8 py-6 font-bold text-navy-800 uppercase tracking-widest text-[10px]">Configuration</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-navy-50">
                    {users.map((user) => (
                        <tr key={user.id} className="hover:bg-navy-50/30 transition-colors group">
                            <td className="px-8 py-6">
                                <span className="font-bold text-navy-950 text-base">{user.email}</span>
                            </td>
                            <td className="px-8 py-6">
                                <span className={cn(
                                    "inline-flex items-center px-4 py-1.5 rounded-lg text-[10px] font-bold tracking-widest uppercase",
                                    user.role === 'admin' ? "bg-navy-950 text-white shadow-premium" : "bg-navy-100 text-navy-600"
                                )}>
                                    {user.role === 'admin' ? 'ROOT ACCESS' : 'PROVIDER'}
                                </span>
                            </td>
                            <td className="px-8 py-6">
                                <span className={cn(
                                    "flex items-center gap-2 font-bold",
                                    user.subscription_status === 'active' ? "text-accent-600" : "text-navy-500"
                                )}>
                                    <div className={cn("w-2 h-2 rounded-full", user.subscription_status === 'active' ? "bg-accent-500" : "bg-navy-200")} />
                                    {user.subscription_status === 'active' ? 'Opérationnel' : 'Suspendu'}
                                </span>
                            </td>
                            <td className="px-8 py-6">
                                <Button
                                    size="sm"
                                    variant="ghost"
                                    onClick={() => onRoleUpdate(user.id, user.role)}
                                    className="rounded-xl border border-navy-100 text-navy-600 hover:bg-navy-950 hover:text-white transition-all transform hover:scale-105"
                                >
                                    <Settings size={14} className="mr-2" />
                                    Changer de Rôle
                                </Button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    </Card>
);

export default AdminDashboard;
