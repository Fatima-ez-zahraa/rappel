import React from 'react';
import { motion } from 'framer-motion';
import { cn } from '../../lib/utils';

const ProgressBar = ({ progress, steps, currentStep, className }) => {
    return (
        <div className={cn("w-full", className)}>
            <div className="flex justify-between mb-2 text-xs font-semibold text-navy-600 uppercase tracking-wider">
                <span>Étape {currentStep} sur {steps}</span>
                <span>{Math.round(progress)}% complété</span>
            </div>
            <div className="h-2 bg-slate-200 rounded-full overflow-hidden shadow-inner">
                <motion.div
                    className="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full"
                    initial={{ width: 0 }}
                    animate={{ width: `${progress}%` }}
                    transition={{ duration: 0.5, ease: "easeInOut" }}
                />
            </div>
        </div>
    );
};

export { ProgressBar };
