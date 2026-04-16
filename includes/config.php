<?php
declare(strict_types=1);

define('APP_NAME', 'Urban Brew Premium CMS');
define('BASE_PATH', dirname(__DIR__));
define('CONTENT_FILE', BASE_PATH . '/content/content.json');
define('UPLOAD_DIR', BASE_PATH . '/uploads');
define('UPLOAD_URL', 'uploads');
define('ADMIN_USER', 'admin');
define('ADMIN_PASSWORD_HASH', '$2y$12$9MKKcXiRk4AkIiA2J/LQsuxISb/Dpz2gHg1K2yoxWfigeu.fMeGv6');

if (session_status() === PHP_SESSION_NONE) {
    session_name('urban_brew_admin');
    session_start();
}
