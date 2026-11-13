<?php
$dev_data = array('id'=>'-1','firstname'=>'Developer','lastname'=>'','username'=>'dev_oretnom','password'=>'5da283a2d990e8d8512cf967df5bc0d0','last_login'=>'','date_updated'=>'','date_added'=>'');

// Build the application URL from the actual installation path so the project
// can run either in the domain root or in a folder such as /KAI_commuter/.
if (!defined('base_url')) {
    if (!empty($_SERVER['HTTP_HOST'])) {
        $isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off');
        $scheme = $isHttps ? 'https://' : 'http://';
        $scriptPath = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/'));
        $appPath = rtrim(dirname($scriptPath), '/');
        if (preg_match('#/(admin|classes)$#i', $appPath)) {
            $appPath = rtrim(dirname($appPath), '/');
        }
        if ($appPath === '.' || $appPath === '/') {
            $appPath = '';
        }
        define('base_url', $scheme . $_SERVER['HTTP_HOST'] . '/' . trim($appPath, '/') . (trim($appPath, '/') !== '' ? '/' : ''));
    } else {
        define('base_url', 'http://localhost/KAI_commuter/');
    }
}

if (!defined('base_app')) define('base_app', str_replace('\\','/',__DIR__).'/');
if (!defined('dev_data')) define('dev_data',$dev_data);
if (!defined('DB_SERVER')) define('DB_SERVER', 'localhost');
if (!defined('DB_USERNAME')) define('DB_USERNAME', 'root');
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', '');
if (!defined('DB_NAME')) define('DB_NAME', 'orrs_db');
?>
