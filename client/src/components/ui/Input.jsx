import React from 'react';
import { cn } from '../../lib/utils';
import { motion } from 'framer-motion';

const Input = React.forwardRef(({ className, label, error, ...props }, ref) => {
    return (
        <div className="space-y-1">
            {label && (
                <label className="text-sm font-medium text-navy-800 ml-1">
                    {label}
                </label>
            )}
            <motion.div
                initial={false}
                animate={{ scale: 1 }}
                whileTap={{ scale: 0.99 }}
                className="relative group"
            >
                <div className="absolute -inset-0.5 bg-gradient-to-r from-accent-500 to-brand-900 rounded-xl opacity-0 group-focus-within:opacity-100 transition duration-500 blur-sm mix-blend-multiply" />
                <input
                    className={cn(
                        "relative w-full h-12 px-4 rounded-xl bg-white border border-slate-200 text-navy-900 placeholder:text-slate-400 focus:outline-none focus:ring-0 focus:border-transparent transition-all shadow-sm",
                        error && "border-red-500 focus:border-red-500",
                        className
                    )}
                    ref={ref}
                    {...props}
                />
            </motion.div>
            {error && (
                <p className="text-xs text-red-500 ml-1 mt-1">{error}</p>
            )}
        </div>
    );
});

Input.displayName = "Input";

export { Input };
