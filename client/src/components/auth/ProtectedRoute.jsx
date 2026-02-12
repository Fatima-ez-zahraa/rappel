import React from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../context/useAuth';

const ProtectedRoute = ({ children, requireVerified = false, requireSubscription = false, requireAdmin = false }) => {
    const { isAuthenticated, isEmailVerified, hasSubscription, isAdmin } = useAuth();
    const location = useLocation();

    // 1. Must be logged in
    if (!isAuthenticated) {
        return <Navigate to="/pro/login" state={{ from: location }} replace />;
    }

    // 2. Must be verified (if required)
    if (requireVerified && !isEmailVerified) {
        return <Navigate to="/pro/verify" replace />;
    }

    // 3. Must be subscribed (if required)
    if (requireSubscription && !hasSubscription) {
        return <Navigate to="/pro/pricing" replace />;
    }

    // 4. Must be admin (if required)
    if (requireAdmin && !isAdmin) {
        return <Navigate to="/pro/dashboard" replace />;
    }

    return children;
};

export default ProtectedRoute;
