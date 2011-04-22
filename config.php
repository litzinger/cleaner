<?php
if (! defined('CLEANER_VERSION'))
{
    define('CLEANER_VERSION', '1.0');
    define('CLEANER_NAME', 'Cleaner');
    define('CLEANER_DESC', 'Sanitizes your POST data, and template output.');
    define('CLEANER_DOCS_URL', 'http://boldminded.com/add-ons/cleaner');
    define('CLEANER_AUTHOR', 'Brian Litzinger');
}

$config['name'] = CLEANER_NAME;
$config['version'] = CLEANER_VERSION;
$config['description'] = CLEANER_DESC;
$config['docs_url'] = CLEANER_DOCS_URL;
$config['nsm_addon_updater']['versions_xml'] = 'http://boldminded.com/assets/add-ons/versions/cleaner.xml';