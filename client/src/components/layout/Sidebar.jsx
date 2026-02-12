import React, { useState } from 'react';
import { NavLink, useNavigate, useLocation } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import {
    LayoutDashboard,
    FileText,
    Users,
    Settings,
    LogOut,
    ChevronLeft,
    ChevronRight,
    PieChart,
    Bell,
    Shield,
    CreditCard
} from 'lucide-react';
import { cn } from '../../lib/utils';
import { useAuth } from '../../context/useAuth';
import { Logo } from '../ui/Logo';

const Sidebar = ({ isOpen, setIsOpen, isMobile }) => {
    const navigate = useNavigate();
    const location = useLocation();
    const { user, logout } = useAuth();

    const menuItems = [
        { icon: LayoutDashboard, label: 'Tableau de bord', path: '/pro/dashboard' },
        ...(user?.subscription_status !== 'active' && user?.role !== 'admin' ? [{ icon: CreditCard, label: 'Plans & Tarifs', path: '/pro/pricing' }] : []),
        { icon: Users, label: 'Mes Leads', path: '/pro/leads' },
        ...(user?.role === 'admin' ? [{ icon: Shield, label: 'Admin', path: '/admin' }] : []),
        { icon: FileText, label: 'Devis', path: '/pro/quotes' },
        { icon: PieChart, label: 'Performances', path: '/pro/performance' },
        { icon: Settings, label: 'Paramètres', path: '/pro/settings' },
    ];

    const sidebarVariants = {
        open: { width: 260, transition: { type: 'spring', damping: 20 } },
        closed: { width: 80, transition: { type: 'spring', damping: 20 } },
    };

    const handleLogout = () => {
        logout();
        navigate('/pro');
    };

    return (
        <motion.aside
            initial={false}
            animate={isMobile ? (isOpen ? { x: 0 } : { x: -280 }) : (isOpen ? 'open' : 'closed')}
            variants={!isMobile ? sidebarVariants : {}}
            className={cn(
                "fixed top-0 left-0 h-screen z-40 flex flex-col",
                "bg-white/70 backdrop-blur-xl border-r border-white/50 shadow-glass",
                isMobile && "w-[280px]"
            )}
        >
            {/* Logo Section */}
            <div className="h-20 flex items-center px-6 border-b border-navy-100/50">
                <Logo iconOnly={!isOpen} variant="short" className="h-10" />
            </div>

            {/* Navigation */}
            <nav className="flex-1 py-6 px-3 space-y-1 overflow-y-auto">
                {menuItems.map((item) => {
                    const isActive = location.pathname === item.path;
                    return (
                        <NavLink
                            key={item.path}
                            to={item.path}
                            className={({ isActive }) => cn(
                                "flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 group relative overflow-hidden",
                                isActive
                                    ? "bg-brand-50 text-brand-700 shadow-sm"
                                    : "text-navy-800 hover:bg-neutral-50 hover:text-navy-950"
                            )}
                        >
                            {({ isActive }) => (
                                <>
                                    <item.icon size={22} className={cn("min-w-[22px]", isActive ? "text-brand-600" : "text-neutral-400 group-hover:text-neutral-700")} />
                                    <AnimatePresence mode="wait">
                                        {isOpen && (
                                            <motion.span
                                                initial={{ opacity: 0, x: -10 }}
                                                animate={{ opacity: 1, x: 0 }}
                                                exit={{ opacity: 0, x: -10 }}
                                                className="font-medium whitespace-nowrap"
                                            >
                                                {item.label}
                                            </motion.span>
                                        )}
                                    </AnimatePresence>
                                    {isActive && (
                                        <motion.div
                                            layoutId="activeTab"
                                            className="absolute left-0 top-0 bottom-0 w-1 bg-brand-600 rounded-r-full"
                                        />
                                    )}
                                </>
                            )}
                        </NavLink>
                    );
                })}
            </nav>

            {/* User Profile & Footer */}
            <div className="p-4 border-t border-navy-100/50">
                <div className={cn(
                    "flex items-center gap-3 p-3 rounded-xl bg-gradient-to-br from-navy-50 to-white border border-navy-100 transition-all",
                    isOpen ? "justify-start" : "justify-center"
                )}>
                    <div className="relative">
                        <div className="w-10 h-10 rounded-full bg-navy-900 text-white flex items-center justify-center font-bold shadow-md">
                            {user?.email?.charAt(0).toUpperCase() || 'U'}
                        </div>
                    </div>

                    <AnimatePresence mode="wait">
                        {isOpen && (
                            <motion.div
                                initial={{ opacity: 0, width: 0 }}
                                animate={{ opacity: 1, width: 'auto' }}
                                exit={{ opacity: 0, width: 0 }}
                                className="overflow-hidden"
                            >
                                <p className="text-sm font-bold text-navy-950 truncate">
                                    {user?.email?.split('@')[0] || 'Utilisateur'}
                                </p>
                                <p className="text-xs text-navy-700 font-semibold truncate lowercase">
                                    {user?.role || 'Compte'} {user?.subscription_status === 'active' ? 'Pro' : ''}
                                </p>
                            </motion.div>
                        )}
                    </AnimatePresence>

                    {isOpen && (
                        <button
                            onClick={handleLogout}
                            className="ml-auto text-navy-400 hover:text-red-500 transition-colors"
                        >
                            <LogOut size={18} />
                        </button>
                    )}
                </div>
            </div>

            {/* Toggle Button */}
            {!isMobile && (
                <button
                    onClick={() => setIsOpen(!isOpen)}
                    className="absolute -right-3 top-24 w-6 h-6 bg-white border border-navy-100 rounded-full shadow-md flex items-center justify-center text-navy-500 hover:text-brand-600 transition-colors z-50"
                >
                    {isOpen ? <ChevronLeft size={14} /> : <ChevronRight size={14} />}
                </button>
            )}
        </motion.aside>
    );
};

export default Sidebar;
