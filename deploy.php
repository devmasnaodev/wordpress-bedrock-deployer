<?php

namespace Deployer;

// Carrega o autoloader do Composer
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    $paths = [
        $_SERVER['HOME'] . '/.composer/vendor/autoload.php',
        '/home/runner/.composer/vendor/autoload.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            break;
        }
    }
}

// Carrega variáveis do arquivo .env se existir
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

use function Env\env;

\Env\Env::$options = 31;

require 'recipe/common.php';

// Configurações básicas
set('application', 'WordPress Bedrock');
set('user', 'www-data');
set('keep_releases', 5);

// Desabilitar git (modo CI)
set('repository', '.');
set('git_strategy', false);

// Task customizada para upload (substitui a padrão)
task('deploy:update_code', function () {
    // Garantir que o diretório release existe
    run('mkdir -p {{release_path}}');
    
    // Upload usando rsync com exclusões
    $exclusions = [
        '.git*',
        '.ddev*', 
        '.vscode*',
        '.github*',
        '.tag.*',
        '.env.*',
        '.cz.toml',
        'README.md',
        'LICENSE.md',
        'deploy*.php',
        'composer.lock',
        'phpcs.xml',
        'pint.json',
    ];
    
    $rsyncOptions = array_map(function($item) {
        return '--exclude=' . $item;
    }, $exclusions);
    
    upload('.', '{{release_path}}', [
        'options' => $rsyncOptions
    ]);
});

// Configurações WordPress
set('shared_files', ['.env']);
set('shared_dirs', ['web/app/uploads']);
set('writable_dirs', ['web/app/uploads']);
set('writable_mode', 'chmod');

// Host - Production
$server_ip = env('SERVER_IP') ?: getenv('SERVER_IP');
$server_port = (int) (env('SERVER_PORT') ?: getenv('SERVER_PORT'));

if (empty($server_ip) || empty($server_port)) {
    throw new \Exception("SERVER_IP and SERVER_PORT environment variables are required");
}

host('production')
    ->setHostname($server_ip)
    ->setPort($server_port)
    ->set('remote_user', 'www-data')
    ->set('deploy_path', '/var/www/deployer.devmasnaodev.com/htdocs')
    ->set('branch', 'main');

// Host - Staging
$staging_ip = env('STAGING_IP') ?: getenv('STAGING_IP');
$staging_port = (int) (env('STAGING_PORT') ?: getenv('STAGING_PORT'));

if (!empty($staging_ip) && !empty($staging_port)) {
    host('staging')
        ->setHostname($staging_ip)
        ->setPort($staging_port)
        ->set('remote_user', 'www-data')
        ->set('deploy_path', '/var/www/deployer.devmasnaodev.com/htdocs')
        ->set('branch', 'develop');
}

// WordPress tasks
task('wordpress:update-db', function () {
    run('cd {{deploy_path}}/current && wp core update-db || true');
});

task('wordpress:cache:flush', function () {
    run('cd {{deploy_path}}/current && wp cache flush || true');
    run('cd {{deploy_path}}/current && wp redis enable || true');
});

// Hooks
after('deploy:symlink', 'wordpress:update-db');
after('wordpress:update-db', 'wordpress:cache:flush');
after('deploy:failed', 'deploy:unlock');

desc('Deploy WordPress (Bedrock) via CI upload');