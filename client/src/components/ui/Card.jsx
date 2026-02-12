import React from 'react';
import { motion } from 'framer-motion';
import { cn } from '../../lib/utils';

const Card = React.forwardRef(({ className, children, ...props }, ref) => {
    return (
        <motion.div
            ref={ref}
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            whileHover={{ y: -5, transition: { duration: 0.2 } }}
            className={cn(
                "rounded-[2rem] bg-white/40 backdrop-blur-2xl border border-white/60 shadow-premium p-8 transition-all duration-300",
                className
            )}
            {...props}
        >
            {children}
        </motion.div>
    );
});

Card.displayName = "Card";

export { Card };
