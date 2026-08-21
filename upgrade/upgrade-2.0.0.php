<?php
/**
 * @author    MEG Venture <info@megventure.com>
 * @copyright 2019-2026 MEG Venture & Consulting Ltd.
 * @license   https://opensource.org/licenses/MIT MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * 2.0.0: migrates the two unprefixed 1.x settings to the new prefixed ones,
 * replaces the duplicated header hooks (which made it snow twice) with the
 * canonical 1.7 hooks, and removes the 1.x files that no longer exist.
 *
 * @param Snowflakesmeg $module
 *
 * @return bool
 */
function upgrade_module_2_0_0($module)
{
    $settings = Snowflakesmeg::defaultSettings();

    // 1.x stored one three-state setting: 0 off, 1 classic, 2 christmas.
    if (Configuration::hasKey('SNOWFLAKES')) {
        $legacyMode = (int) Configuration::get('SNOWFLAKES');
        $settings['SNOWFLAKESMEG_ENABLED'] = (int) ($legacyMode > 0);
        $settings['SNOWFLAKESMEG_THEME'] = $legacyMode === 2 ? 'christmas' : 'classic';
    }

    $legacySize = (float) str_replace(',', '.', (string) Configuration::get('sizesnowflakes'));
    if ($legacySize >= Snowflakesmeg::MIN_SIZE && $legacySize <= Snowflakesmeg::MAX_SIZE) {
        $settings['SNOWFLAKESMEG_SIZE'] = $legacySize;
    }

    foreach ($settings as $key => $value) {
        Configuration::updateValue($key, $value);
    }

    // The bare 1.x rows are read but never deleted: configuration is shop-wide
    // and another module may own a row by the same name.

    // 1.x registered both displayHeader and its legacy alias, so the snow
    // rendered twice on PrestaShop 1.7.
    $module->unregisterHook('header');
    $module->unregisterHook('displayHeader');

    // Module upgrades unpack over the previous folder: sweep out the 1.x
    // files that 2.0.0 no longer ships.
    $stale = array(
        'logo.gif',
        'translations/de.php',
        'translations/es.php',
        'translations/fr.php',
        'translations/it.php',
        'translations/nl.php',
        'views/templates/front/normal_snowflakes.tpl',
        'views/templates/front/christmas_snowflakes.tpl',
    );
    foreach ($stale as $file) {
        $path = _PS_MODULE_DIR_ . $module->name . '/' . $file;
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    // Never let a live registerHook() call decide whether this upgrade step
    // - and therefore the whole module - succeeds or gets disabled: check
    // idempotently, attempt it, and always report success either way.
    foreach (array('displayBeforeBodyClosingTag', 'actionFrontControllerSetMedia') as $hook) {
        if (!$module->isRegisteredInHook($hook)) {
            $module->registerHook($hook);
        }
    }

    return true;
}
