import React, { useState, useEffect } from 'react';
import Sidebar from './Sidebar';
import { Menu } from 'lucide-react';
import { AnimatePresence, motion } from 'framer-motion';
import { Logo } from '../ui/Logo';
import { useNavigate } from 'react-router-dom';

const DashboardLayout = ({ children }) => {
    const navigate = useNavigate();
    const [isSidebarOpen, setIsSidebarOpen] = useState(true);
    const [isMobile, setIsMobile] = useState(false);

    // Handle resize for mobile check
    useEffect(() => {
        const handleResize = () => {
            const mobile = window.innerWidth < 1024; // lg breakpoint
            setIsMobile(mobile);
            if (mobile) setIsSidebarOpen(false);
            else setIsSidebarOpen(true);
        };

        handleResize(); // Init
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    return (
        <div className="min-h-screen bg-transparent flex">
            {/* Mobile Overlay */}
            <AnimatePresence>
                {isMobile && isSidebarOpen && (
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        onClick={() => setIsSidebarOpen(false)}
                        className="fixed inset-0 bg-navy-900/20 backdrop-blur-sm z-30 lg:hidden"
                    />
                )}
            </AnimatePresence>

            <Sidebar
                isOpen={isSidebarOpen}
                setIsOpen={setIsSidebarOpen}
                isMobile={isMobile}
            />

            <main
                className={`flex-1 transition-all duration-300 ease-in-out min-h-screen
                            ${isMobile ? 'pl-0' : (isSidebarOpen ? 'pl-[260px]' : 'pl-[80px]')}`}
            >
                {/* Mobile Header */}
                {isMobile && (
                    <div className="sticky top-0 z-20 h-16 bg-white/70 backdrop-blur-md border-b border-white/50 flex items-center justify-between px-4 shadow-sm">
                        <div className="flex items-center gap-3">
                            <button
                                onClick={() => setIsSidebarOpen(true)}
                                className="p-2 -ml-2 text-navy-600 hover:bg-navy-50 rounded-lg transition-colors"
                            >
                                <Menu size={24} />
                            </button>
                            <Logo
                                className="h-9 cursor-pointer"
                                onClick={() => navigate('/')}
                            />
                        </div>
                        <div className="w-8 h-8 rounded-full bg-gradient-to-r from-brand-500 to-indigo-600" />
                    </div>
                )}

                <div className="p-4 lg:p-8 max-w-[1600px] mx-auto animate-fade-in">
                    {children}
                </div>
            </main>
        </div>
    );
};

export default DashboardLayout;
