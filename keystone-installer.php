#!/usr/bin/env php
<?php

/**
* curl -sSL https://keystone-cms.com/keystone-installer | php
* 
* wget https://keystone-cms.com/keystone-installer
* php keystone-installer
*/ 

declare(strict_types=1);

/**
 * Keystone CMS Installer (v1)
 *
 * Responsibilities:
 * - Download latest stable release
 * - Verify signature
 * - Extract to releases/<version>
 * - Create current symlink
 * - Create shared directories
 * - Create required symlinks
 * - Run composer install
 *
 * Web installer will handle configuration & database.
 */

const INSTALLER_VERSION = '1.0.0';
const UPDATE_LATEST_URL = 'https://github.com/keystonecms/keystone/releases/latest/download/latest.json';
const INSTALL_PROFILES = [
    'default' => ['pages', 'auth', 'shoppingcart'],
    'blog'    => ['pages', 'auth', 'media', 'seo'],
    'empty'   => [],
];

function fail(string $message): void {
    fwrite(STDERR, "✖ $message\n");
    exit(1);
}

function info(string $message): void {
    echo "• $message\n";
}

function success(string $message): void {
    echo "✔ $message\n";
}

$options = getopt('', [
    'root::',
    'with::',
    'force',
]);


$profile = $options['profile'] ?? 'default';
$plugins = INSTALL_PROFILES[$profile] ?? INSTALL_PROFILES['default'];

$webRoot      = $options['root'] ?? 'public_html';
$defaultPlugins = isset($options['with'])
    ? array_map('trim', explode(',', $options['with']))
    : $plugins;
$force = array_key_exists('force', $options);

echo "Keystone CMS Installer v" . INSTALLER_VERSION . PHP_EOL;
echo str_repeat('-', 40) . PHP_EOL;

/* ------------------------------------------------------------
 | 1. Environment checks
 * ------------------------------------------------------------ */

info('Checking PHP version');

if (PHP_VERSION_ID < 80200) {
    fail('PHP 8.2 or higher is required');
}

info('Checking required PHP extensions');

foreach (['curl', 'openssl', 'zip'] as $ext) {
    if (!extension_loaded($ext)) {
        fail("Required PHP extension missing: $ext");
    }
}

info('Checking composer availability');

exec('composer --version 2>/dev/null', $out, $code);
if ($code !== 0) {
    fail('Composer not found. Install Composer first: https://getcomposer.org');
}

/* ------------------------------------------------------------
 | 2. Determine install paths
 * ------------------------------------------------------------ */

$root = getcwd();

info("Installing in: $root");

$releasesDir = $root . '/releases';
$sharedDir   = $root . '/shared';
$currentLink = $root . '/current';

@mkdir($releasesDir, 0775, true);
@mkdir($sharedDir, 0775, true);

/* ------------------------------------------------------------
 | 3. Fetch latest release metadata
 * ------------------------------------------------------------ */

info('Fetching latest release metadata');

$latestJson = file_get_contents(UPDATE_LATEST_URL);
if ($latestJson === false) {
    fail('Unable to download latest.json');
}

$meta = json_decode($latestJson, true);
if (!$meta || empty($meta['version'])) {
    fail('Invalid latest.json');
}

$version   = ltrim($meta['version'], 'v');
$zipUrl    = $meta['zip'] ?? null;
$sigUrl    = $meta['signature'] ?? null;

if (!$zipUrl || !$sigUrl) {
    fail('latest.json missing zip or signature');
}

success("Latest version: $version");

/* ------------------------------------------------------------
 | 4. Download release
 * ------------------------------------------------------------ */

$tmpDir = sys_get_temp_dir() . '/keystone_installer_' . uniqid();
mkdir($tmpDir);

$zipPath = "$tmpDir/keystone-$version.zip";
$sigPath = "$tmpDir/keystone-$version.zip.sig";

info('Downloading release package');
file_put_contents($zipPath, file_get_contents($zipUrl));

info('Downloading signature');
file_put_contents($sigPath, file_get_contents($sigUrl));

if (!file_exists($zipPath) || filesize($zipPath) === 0) {
    fail('Downloaded ZIP is invalid');
}

/* ------------------------------------------------------------
 | 5. Download and Verify signature
 * ------------------------------------------------------------ */

info('Downloading public key');

$pubKeyUrl = 'https://raw.githubusercontent.com/keystonecms/keystone/master/resources/keys/keystone-cms-update.pub';

$publicKeyPem = file_get_contents($pubKeyUrl);

if ($publicKeyPem === false) {
    fail('Unable to download public key');
}

info('Verifying release signature');


if (!str_contains($publicKeyPem, 'BEGIN PUBLIC KEY')) {
    fail('Downloaded public key is invalid');
}

$publicKey = openssl_pkey_get_public($publicKeyPem);

if ($publicKey === false) {
    fail('Public key is not valid');
}

$data      = file_get_contents($zipPath);
$signature = file_get_contents($sigPath);

$ok = openssl_verify(
    $data,
    $signature,
    $publicKey,
    OPENSSL_ALGO_SHA256
);

if ($ok !== 1) {
    fail('Signature verification failed');
}

success('Signature valid');

/* ------------------------------------------------------------
 | 6. Extract release
 * ------------------------------------------------------------ */

$releaseDir = "$releasesDir/$version";

if (is_dir($releaseDir)) {
    fail("Release $version already exists");
}

info("Extracting release to $releaseDir");

$zip = new ZipArchive();
$zip->open($zipPath);
$zip->extractTo($releaseDir);
$zip->close();

/* ------------------------------------------------------------
 | 7. Create / update current symlink
 * ------------------------------------------------------------ */

info('Creating current symlink');

if (is_link($currentLink) || file_exists($currentLink)) {
    unlink($currentLink);
}

symlink("releases/$version", $currentLink);

/* ------------------------------------------------------------
 | 8. Create shared directories
 * ------------------------------------------------------------ */

info('Creating shared directories');

foreach (['storage', 'uploads', 'cache'] as $dir) {
    @mkdir("$sharedDir/$dir", 0775, true);
}

/* ------------------------------------------------------------
 | 9. Create symlinks into current
 * ------------------------------------------------------------ */

info('Creating application symlinks');

$links = [
    "$currentLink/storage"        => "../shared/storage",
    "$currentLink/public/uploads" => "../../shared/uploads",
];

foreach ($links as $link => $target) {
    if (!is_link($link)) {
        symlink($target, $link);
    }
}
/* ------------------------------------------------------------
 | 10. Fetching plugins to install
 * ------------------------------------------------------------ */

info('Fetching plugin catalog');

$catalogUrl = 'https://raw.githubusercontent.com/keystonecms/plugin-catalog/main/plugins.json';

$catalogJson = file_get_contents($catalogUrl);
if ($catalogJson === false) {
    fail('Unable to download plugin catalog');
}

$catalog = json_decode($catalogJson, true);
if (!is_array($catalog)) {
    fail('Invalid plugin catalog JSON');
}

if (!isset($catalog['plugins']) || !is_array($catalog['plugins'])) {
    fail('Plugin catalog has no plugins array');
}

info('Normalizing plugin catalog');

$catalogBySlug = [];

foreach ($catalog['plugins'] as $plugin) {
    if (!isset($plugin['slug'], $plugin['package'])) {
        continue;
    }

    $slug = trim(strtolower($plugin['slug']));
    $catalogBySlug[$slug] = $plugin;
}

info('Resolving plugin packages');

$defaultPlugins = array_map(
    fn ($p) => trim(strtolower($p)),
    $defaultPlugins
);

$packages = [];

foreach ($defaultPlugins as $plugin) {
    if (!isset($catalogBySlug[$plugin])) {
        fail(
            "Unknown plugin: $plugin\n" .
            "Available plugins: " . implode(', ', array_keys($catalogBySlug))
        );
    }

    $packages[] = $catalogBySlug[$plugin]['package'];
}

if ($packages) {
    info('Installing default plugins via Composer');

    $cmd = 'composer require ' . implode(' ', $packages) . ' --no-interaction';

    passthru($cmd, $code);

    if ($code !== 0) {
        fail('Failed to install plugins via Composer');
    }

    success('Default plugins installed');
}

info('Verifying installed plugins');

foreach ($packages as $package) {
    [$vendor, $name] = explode('/', $package, 2);
    $path = "$currentLink/vendor/$vendor/$name";

    if (!is_dir($path)) {
        fail("Plugin package missing after install: $package");
    }
}

/* ------------------------------------------------------------
 | 11. Run composer install
 * ------------------------------------------------------------ */

info('Running composer install');

chdir($currentLink);
passthru('composer install --no-dev --optimize-autoloader', $code);

if ($code !== 0) {
    fail('Composer install failed');
}

/* ------------------------------------------------------------
 | 12. Determine webroot and copy htaccess and index.php to it
 * ------------------------------------------------------------ */
$publicDir = "$currentLink/$webRoot";

info("Using web root: $publicDir");

if (!is_dir($publicDir)) {
    fail("Web root directory does not exist: $webRoot");
   }

info('Setting up entry files');

$indexSource = "$currentLink/resources/index.php.dist";
$indexTarget = "$publicDir/index.php";

if (!file_exists($indexTarget) || $force) {
    copy($indexSource, $indexTarget);
    success('index.php created');
} else {
    info('index.php already exists, skipping');
}

$htaccessSource = "$currentLink/resources/htaccess";
$htaccessTarget = "$publicDir/.htaccess";

if (!file_exists($htaccessTarget) || $force) {
    copy($htaccessSource, $htaccessTarget);
    success('.htaccess created');
}

/* ------------------------------------------------------------
 | Done
 * ------------------------------------------------------------ */

echo PHP_EOL;
success('Keystone CMS installed successfully');
echo PHP_EOL;
echo "Next steps:\n";
echo "1. Point your webserver to: $currentLink/$webRoot\n";
echo "2. Open your browser and go to: /installer\n";
echo PHP_EOL;

?>