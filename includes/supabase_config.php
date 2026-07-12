<?php
/**
 * Supabase project configuration.
 *
 * This project uses Supabase REST APIs for CRUD operations and supports
 * Railway-style environment variables when deployed.
 */

$env = function($key, $default = '') use (&$env) {
    $value = getenv($key);
    return $value === false ? $default : $value;
};

define('SUPABASE_PROJECT_REF', $env('SUPABASE_PROJECT_REF', 'umbwlifaxyqagafihpcm'));
define('SUPABASE_URL', $env('SUPABASE_URL', 'https://umbwlifaxyqagafihpcm.supabase.co'));
define('SUPABASE_ANON_KEY', $env('SUPABASE_ANON_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVtYndsaWZheHlxYWdhZmlocGNtIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODM4NDE5MzAsImV4cCI6MjA5OTQxNzkzMH0.pQ7qaujh6Y7ZEzzBNYWd0WYCMLEHSXE27IFPZk39y8Y'));
define('SUPABASE_SERVICE_ROLE_KEY', $env('SUPABASE_SERVICE_ROLE_KEY', ''));

define('SUPABASE_DB_HOST', $env('SUPABASE_DB_HOST', 'db.umbwlifaxyqagafihpcm.supabase.co'));
define('SUPABASE_DB_PORT', $env('SUPABASE_DB_PORT', '5432'));
define('SUPABASE_DB_NAME', $env('SUPABASE_DB_NAME', 'postgres'));
define('SUPABASE_DB_USER', $env('SUPABASE_DB_USER', 'postgres'));
define('SUPABASE_DB_PASSWORD', $env('SUPABASE_DB_PASSWORD', ''));
