import React from 'react';
import { motion } from 'framer-motion';
import { cn } from '../../lib/utils';
import { Loader2 } from 'lucide-react';

const Button = React.forwardRef(({
    className,
    variant = 'primary',
    size = 'md',
    isLoading = false,
    showShine = false,
    children,
    ...props
}, ref) => {

    const variants = {
        primary: "bg-accent-500 text-white hover:bg-accent-600 shadow-glow shadow-accent-500/20",
        secondary: "bg-navy-900 text-white hover:bg-navy-800 shadow-premium",
        glass: "bg-white/10 backdrop-blur-md border border-white/20 text-navy-900 hover:bg-white/20",
        outline: "border-2 border-navy-900 text-navy-900 hover:bg-navy-50",
        ghost: "text-navy-600 hover:bg-navy-50",
    };

    const sizes = {
        sm: "h-9 px-4 text-sm",
        md: "h-11 px-6 text-base",
        lg: "h-14 px-8 text-lg font-semibold",
    };

    return (
        <motion.button
            ref={ref}
            whileHover={{ scale: 1.02, y: -1 }}
            whileTap={{ scale: 0.98 }}
            className={cn(
                "relative inline-flex items-center justify-center rounded-2xl font-medium transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 disabled:pointer-events-none disabled:opacity-50 overflow-hidden",
                variants[variant],
                sizes[size],
                className
            )}
            disabled={isLoading}
            {...props}
        >
            {showShine && (
                <span className="absolute inset-0 w-full h-full">
                    <span className="shimmer-effect absolute inset-0 transform -skew-x-12" />
                </span>
            )}
            <span className="relative flex items-center justify-center gap-2">
                {isLoading && <Loader2 className="h-4 w-4 animate-spin" />}
                {children}
            </span>
        </motion.button>
    );
});

Button.displayName = "Button";

export { Button };
