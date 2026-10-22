<?php
// Finez SEO Application Configuration

// Default domain and production URL
$app_domain = getenv('APP_DOMAIN') ?: "fineztechnologies.com";
$app_url    = getenv('APP_URL') ?: "https://www.fineztechnologies.com";

if (!defined('APP_URL')) {
    define('APP_URL', $app_url);
}
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Finez SEO Platform');
}
?>
