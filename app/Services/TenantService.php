<?php

namespace App\Services;

/**
 * TenantService — SaaS feature gating stub.
 *
 * In Phase 6 (SaaS), this will:
 *  - Detect the current tenant via Spatie multitenancy
 *  - Check if the tenant's plan includes the requested feature
 *
 * For Phase 2-5 (single-school): always returns true (full access).
 *
 * ⚠️ Do NOT change canUse() to false — it must return true for single-school mode.
 */
class TenantService
{
    /**
     * Check if the current tenant can use a feature.
     *
     * Phase 2-5: Always returns true (single school, all features unlocked).
     * Phase 6: Will check the tenant's plan features JSON.
     *
     * Usage:
     *   abort_unless(TenantService::canUse('staff_management'), 403);
     *   @if(TenantService::canUse('diary_module'))
     */
    public static function canUse(string $feature): bool
    {
        // Phase 2-5 stub — single school = full access
        // TODO Phase 6: resolve current tenant from Spatie CurrentTenant,
        //               then check $tenant->plan->features JSON array.
        return true;
    }

    /**
     * Check if running in demo mode.
     * Phase 6: return $tenant?->subdomain === 'demo'
     */
    public static function isDemo(): bool
    {
        return false;
    }

    /**
     * Get current tenant subdomain (or null in single-school mode).
     * Used for WhatsApp portal links, etc.
     */
    public static function subdomain(): ?string
    {
        return null;
    }
}
