import React from 'react';
import { motion } from 'framer-motion';

const BackgroundBlobs = () => {
    return (
        <div className="fixed inset-0 overflow-hidden pointer-events-none -z-10 bg-transparent">
            {/* Emerald Blob */}
            <motion.div
                animate={{
                    x: [0, 100, 0],
                    y: [0, 50, 0],
                    scale: [1, 1.2, 1],
                }}
                transition={{
                    duration: 20,
                    repeat: Infinity,
                    ease: "easeInOut"
                }}
                className="absolute top-[-10%] left-[-10%] w-[50%] h-[60%] rounded-full bg-accent-500/10 blur-[120px]"
            />

            {/* Navy Blob */}
            <motion.div
                animate={{
                    x: [0, -80, 0],
                    y: [0, 100, 0],
                    scale: [1, 1.1, 1],
                }}
                transition={{
                    duration: 25,
                    repeat: Infinity,
                    ease: "easeInOut",
                    delay: 2
                }}
                className="absolute bottom-[-10%] right-[-10%] w-[60%] h-[70%] rounded-full bg-brand-900/10 blur-[120px]"
            />

            {/* Center subtle glow */}
            <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[radial-gradient(circle_at_center,_rgba(16,185,129,0.02),transparent_70%)]" />
        </div>
    );
};

export default BackgroundBlobs;
