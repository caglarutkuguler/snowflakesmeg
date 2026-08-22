<?php
/**
 * @author    MEG Venture <info@megventure.com>
 * @copyright 2019-2026 MEG Venture & Consulting Ltd.
 * @license   https://opensource.org/licenses/MIT MIT License
 *
 * Guards the 2.0.3 configuration-key change.
 *
 * Runs without PrestaShop: Configuration, Tools and Module are stubbed, so the
 * whole file is `php tests/ConfigurationKeyTest.php` and nothing else.
 *
 * What it is here to catch: uninstall() and the 2.0.0 upgrade step used to
 * delete the bare shop-wide configuration names 'SNOWFLAKES' and
 * 'sizesnowflakes' while cleaning up after 1.x. Any other module storing a
 * setting under one of those names lost it. Both now leave the bare rows
 * alone; the upgrade still migrates their values to the prefixed names.
 */

namespace PrestaShop\PrestaShop\Core\Module {
    interface WidgetInterface
    {
        public function renderWidget($hookName, array $configuration);
        public function getWidgetVariables($hookName, array $configuration);
    }
}

namespace {

define('_PS_VERSION_', '8.1.0');
define('_PS_MODULE_DIR_', __DIR__ . '/nonexistent-modules/');

class Configuration
{
    public static $store = [];
    public static function get($k, $l = null, $s = null, $sh = null, $default = false)
    {
        return array_key_exists($k, self::$store) ? self::$store[$k] : false;
    }
    public static function updateValue($k, $v)
    {
        self::$store[$k] = is_bool($v) ? ($v ? '1' : '0') : (string) $v;
        return true;
    }
    public static function deleteByName($k)
    {
        unset(self::$store[$k]);
        return true;
    }
    public static function updateGlobalValue($k, $v)
    {
        return self::updateValue($k, $v);
    }
    public static function hasKey($k, $l = null)
    {
        return array_key_exists($k, self::$store);
    }
}

class Tools
{
    public static function strtoupper($s) { return strtoupper($s); }
    public static function getValue($k, $d = false) { return $d; }
    public static function isSubmit($k) { return false; }
}

class Validate
{
    public static function isColor($v) { return true; }
    public static function isInt($v) { return true; }
}

class Media { public static function addJsDef($a) {} }

abstract class Module
{
    public $name, $tab, $version, $author, $need_instance, $module_key,
           $ps_versions_compliancy, $bootstrap, $displayName, $description,
           $confirmUninstall, $_path, $context, $id, $local_path;
    public function __construct() {}
    public function l($s, $c = null) { return $s; }
    public function registerHook($h) { return true; }
    public function unregisterHook($h) { return true; }
    public function isRegisteredInHook($h) { return true; }
    public function display($f, $t) { return ''; }
    public function displayConfirmation($s) { return ''; }
    public function displayError($s) { return ''; }
    public function install() { return true; }
    public function uninstall() { return true; }
}

require dirname(__DIR__) . '/snowflakesmeg.php';
require dirname(__DIR__) . '/upgrade/upgrade-2.0.0.php';

$fail = 0;
function ok($cond, $label) {
    global $fail;
    if ($cond) { echo "  ok   $label\n"; } else { echo "  FAIL $label\n"; $fail++; }
}

echo "1) Kurulum yabanci ciplak anahtarlara dokunuyor mu\n";
Configuration::$store = [
    'SNOWFLAKES' => 'baska-modulun-degeri',
    'sizesnowflakes' => 'baska-modulun-boyutu',
];
$m = new Snowflakesmeg();
$m->install();
ok(Configuration::$store['SNOWFLAKES'] === 'baska-modulun-degeri', "kurulum ciplak 'SNOWFLAKES' satirini ezmedi");
ok(Configuration::$store['sizesnowflakes'] === 'baska-modulun-boyutu', "kurulum ciplak 'sizesnowflakes' satirina dokunmadi");
ok(Configuration::$store['SNOWFLAKESMEG_ENABLED'] === '1', 'SNOWFLAKESMEG_ENABLED varsayilani yazildi');
ok(count(array_filter(array_keys(Configuration::$store), function ($k) { return strpos($k, 'SNOWFLAKESMEG_') === 0; })) === 10,
   '10 onekli ayar yazildi (9 ayar + review nudge zaman damgasi)');

echo "\n2) Kaldirma baska modulun anahtarina dokunuyor mu\n";
$m->uninstall();
ok(Configuration::$store['SNOWFLAKES'] === 'baska-modulun-degeri', "uninstall ciplak 'SNOWFLAKES' satirini SILMEDI");
ok(Configuration::$store['sizesnowflakes'] === 'baska-modulun-boyutu', "uninstall ciplak 'sizesnowflakes' satirini SILMEDI");
ok(count(array_filter(array_keys(Configuration::$store), function ($k) { return strpos($k, 'SNOWFLAKESMEG_') === 0; })) === 0,
   'uninstall butun SNOWFLAKESMEG_ anahtarlarini sildi');

echo "\n3) 1.x -> 2.0.0 gecisi hala deger tasiyor mu (silmeden)\n";
Configuration::$store = ['SNOWFLAKES' => '2', 'sizesnowflakes' => '2,5'];
upgrade_module_2_0_0($m);
ok(Configuration::$store['SNOWFLAKESMEG_ENABLED'] === '1', 'eski SNOWFLAKES=2 -> etkin');
ok(Configuration::$store['SNOWFLAKESMEG_THEME'] === 'christmas', 'eski SNOWFLAKES=2 -> christmas temasi');
ok(Configuration::$store['SNOWFLAKESMEG_SIZE'] === '2.5', 'eski sizesnowflakes 2,5 -> 2.5 tasindi');
ok(Configuration::$store['SNOWFLAKES'] === '2', "eski ciplak 'SNOWFLAKES' satiri SILINMEDI (baska modulun olabilir)");
ok(Configuration::$store['sizesnowflakes'] === '2,5', "eski ciplak 'sizesnowflakes' satiri SILINMEDI");

echo "\n4) Upgrade ikinci kez calisirsa patlamiyor mu\n";
upgrade_module_2_0_0($m);
ok(Configuration::$store['SNOWFLAKESMEG_THEME'] === 'christmas', 'ikinci calistirma sonucu degistirmedi');

echo "\n5) Kaynakta ciplak anahtar silme kaldi mi\n";
$sources = [
    dirname(__DIR__) . '/snowflakesmeg.php',
    dirname(__DIR__) . '/upgrade/upgrade-2.0.0.php',
];
$pattern = "/deleteByName\\(\\s*'(SNOWFLAKES|sizesnowflakes)'/";
foreach ($sources as $src) {
    ok(!preg_match($pattern, (string) file_get_contents($src)), basename($src) . ' ciplak anahtar silmiyor');
}

echo "\n" . ($fail === 0 ? "OK - hepsi gecti\n" : "$fail test BASARISIZ\n");
exit($fail === 0 ? 0 : 1);

}
