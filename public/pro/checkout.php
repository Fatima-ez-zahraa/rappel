<?php
/**
 * checkout.php — Handles plan selection from the public landing page.
 * Redirects authenticated providers to pricing.php with a plan pre-selected,
 * or sends unauthenticated visitors to signup with a plan intent.
 */
require_once __DIR__ . '/../includes/auth.php';

$plan = strtolower(trim($_GET['plan'] ?? ''));
$validPlans = ['croissance', 'acceleration', 'flexibilite'];

if (!in_array($plan, $validPlans)) {
    header('Location: /rappel/public/pro/');
    exit;
}

if (isLoggedIn() && isVerified()) {
    // Already logged in: go directly to pricing page (which will trigger Stripe)
    header('Location: /rappel/public/pro/pricing.php?select=' . urlencode($plan));
    exit;
}

// Not logged in: redirect to signup with a hint so they land on pricing after registration
header('Location: /rappel/public/pro/signup.php?intent=' . urlencode($plan));
exit;
