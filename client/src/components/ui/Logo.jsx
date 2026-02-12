import React from 'react';
import { cn } from '../../lib/utils';

/**
 * Logo Component
 * Renders the brand logo from public/logo.png
 * 
 * @param {string} className - Additional classes for the container
 * @param {boolean} light - If true, applies an invert filter for dark backgrounds
 * @param {string} variant - Support for different variants (e.g., 'short')
 * @param {boolean} iconOnly - If true, can be used to show only the mark if applicable
 * @param {function} onClick - Click handler
 */
export const Logo = ({ className, light, variant, iconOnly, onClick }) => {
    return (
        <div
            className={cn(
                "flex items-center justify-center transition-all duration-300",
                onClick && "cursor-pointer hover:opacity-80",
                className
            )}
            onClick={onClick}
        >
            <img
                src="/logo.png"
                alt="Rappelez-moi"
                className={cn(
                    "max-h-full w-auto object-contain transition-all duration-300",
                    light && "brightness-0 invert",
                    // If iconOnly or special variant is requested, we can adjust sizing or apply specific styles
                    iconOnly && "scale-110"
                )}
            />
        </div>
    );
};
