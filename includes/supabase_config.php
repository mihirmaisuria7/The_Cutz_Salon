<?php
/**
 * Supabase project configuration.
 *
 * Dashboard: https://supabase.com/dashboard/project/umbwlifaxyqagafihpcm
 *
 * IMPORTANT: The anon key is for Supabase REST/Realtime APIs.
 * PHP connects directly to the PostgreSQL database — you must set
 * SUPABASE_DB_PASSWORD from: Dashboard → Project Settings → Database → Database password
 */

define('SUPABASE_PROJECT_REF', 'umbwlifaxyqagafihpcm');
define('SUPABASE_URL', 'https://umbwlifaxyqagafihpcm.supabase.co');
define('SUPABASE_ANON_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVtYndsaWZheHlxYWdhZmlocGNtIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODM4NDE5MzAsImV4cCI6MjA5OTQxNzkzMH0.pQ7qaujh6Y7ZEzzBNYWd0WYCMLEHSXE27IFPZk39y8Y');

// Direct PostgreSQL connection (required for this PHP app)
define('SUPABASE_DB_HOST', 'db.umbwlifaxyqagafihpcm.supabase.co');
define('SUPABASE_DB_PORT', '5432');
define('SUPABASE_DB_NAME', 'postgres');
define('SUPABASE_DB_USER', 'postgres');

// Set your database password here (from Supabase Dashboard → Settings → Database)
define('SUPABASE_DB_PASSWORD', '');
