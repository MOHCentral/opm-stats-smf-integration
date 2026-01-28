-- =============================================================================
-- MOHAA Stats Plugin - Portainer/Production Patch
-- 
-- Run this AFTER install.sql in production environments
-- Updates URLs and settings for production deployment
-- =============================================================================

-- Update API URL for production (internal Docker network)
-- Change this URL to match your production API endpoint
UPDATE smf_settings 
SET value = 'http://opm-stats-api:8084' 
WHERE variable = 'mohaa_stats_api_url';

-- If using external/public API URL instead, use this:
-- UPDATE smf_settings 
-- SET value = 'https://api.moh-central.net' 
-- WHERE variable = 'mohaa_stats_api_url';

-- Increase cache TTL for production (10 minutes instead of 5)
UPDATE smf_settings 
SET value = '600' 
WHERE variable = 'mohaa_stats_cache_ttl';

-- =============================================================================
-- PATCH COMPLETE!
-- Verify: SELECT * FROM smf_settings WHERE variable LIKE 'mohaa%';
-- =============================================================================
