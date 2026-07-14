<?php
/**
 * 2019-2026 MEG Venture
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 *  @author    MEG Venture
 *  @copyright 2019-2026 MEG Venture
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Snowflakesmeg extends Module implements WidgetInterface
{
    const MIN_SIZE = 0.5;
    const MAX_SIZE = 5;
    const MIN_DENSITY = 4;
    const MAX_DENSITY = 48;

    /** @var array Fall duration in seconds for each snowfall intensity. */
    const SPEEDS = array('calm' => 16, 'normal' => 10, 'blizzard' => 6);

    const THEMES = array('classic', 'christmas');

    private $html = '';

    /** @var bool Guard so themes hooking the module in several places render the snow only once. */
    private $rendered = false;

    public function __construct()
    {
        $this->name = 'snowflakesmeg';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'MEG Venture';
        $this->need_instance = 0;
        $this->module_key = 'a67eacc637922bc344faab514f05f59a';
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        parent::__construct();

        $this->displayName = $this->l('Snow Effects - Christmas & Winter Atmosphere');
        $this->description = $this->l('Delight your visitors with a gentle snowfall or a festive Christmas mix, in pure CSS: no JavaScript, no external files, no impact on page speed. Set the season dates once and the snow starts and stops by itself.');
        $this->confirmUninstall = $this->l('The snow effect and its settings will be removed from your shop. Do you want to continue?');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        foreach (self::defaultSettings() as $key => $value) {
            Configuration::updateValue($key, $value);
        }

        return $this->registerHook('displayBeforeBodyClosingTag')
            && $this->registerHook('actionFrontControllerSetMedia');
    }

    public function uninstall()
    {
        $keys = array_keys(self::defaultSettings());
        // Leftovers from 1.x releases.
        $keys[] = 'SNOWFLAKES';
        $keys[] = 'sizesnowflakes';

        foreach ($keys as $key) {
            Configuration::deleteByName($key);
        }

        return parent::uninstall();
    }

    /**
     * Every setting of the module with its fresh-install value. Shared with
     * the 2.0.0 upgrade script, which overrides what 1.x lets it migrate.
     */
    public static function defaultSettings()
    {
        return array(
            'SNOWFLAKESMEG_ENABLED' => 1,
            'SNOWFLAKESMEG_THEME' => 'classic',
            'SNOWFLAKESMEG_SIZE' => 1.5,
            'SNOWFLAKESMEG_DENSITY' => 12,
            'SNOWFLAKESMEG_SPEED' => 'normal',
            'SNOWFLAKESMEG_COLOR' => '#ffffff',
            'SNOWFLAKESMEG_FROM' => '',
            'SNOWFLAKESMEG_TO' => '',
            'SNOWFLAKESMEG_MOBILE' => 1,
        );
    }

    /* -------------------------------------------------------------------- */
    /* Configuration page                                                     */
    /* -------------------------------------------------------------------- */

    public function getContent()
    {
        $this->html = '';

        if (Tools::isSubmit('submitSnowflakesmeg')) {
            $this->processSettingsForm();
        }
        if ((int) Tools::getValue('sfm_conf') === 1) {
            $this->html .= $this->displayConfirmation($this->l('Settings updated successfully. Reload your shop to admire the result.'));
        }

        $this->context->smarty->assign($this->getStatusVariables() + array(
            'sfm_preview_flakes' => $this->buildFlakes(10, Configuration::get('SNOWFLAKESMEG_THEME')),
            'sfm_size' => $this->getFlakeSize(),
            'sfm_color' => $this->getFlakeColor(),
        ));

        return $this->html
            . $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl')
            . $this->renderForm();
    }

    /**
     * Tells the configuration page what the visitor currently sees, so the
     * shop owner never has to guess whether the snow is live.
     */
    private function getStatusVariables()
    {
        $today = date('Y-m-d');
        $from = (string) Configuration::get('SNOWFLAKESMEG_FROM');
        $to = (string) Configuration::get('SNOWFLAKESMEG_TO');

        if (!(int) Configuration::get('SNOWFLAKESMEG_ENABLED')) {
            $status = 'disabled';
        } elseif ($from !== '' && $today < $from) {
            $status = 'waiting';
        } elseif ($to !== '' && $today > $to) {
            $status = 'ended';
        } else {
            $status = 'live';
        }

        return array(
            'sfm_status' => $status,
            'sfm_from' => $from,
            'sfm_to' => $to,
        );
    }

    private function getConfigureUrl($params = '')
    {
        return $this->context->link->getAdminLink('AdminModules', true)
            . '&configure=' . $this->name . $params;
    }

    private function processSettingsForm()
    {
        $errors = array();

        $theme = Tools::getValue('SNOWFLAKESMEG_THEME');
        if (!in_array($theme, self::THEMES, true)) {
            $errors[] = $this->l('Please choose a valid snow theme.');
        }

        // "1,5" is as welcome as "1.5".
        $size = str_replace(',', '.', trim((string) Tools::getValue('SNOWFLAKESMEG_SIZE')));
        if (!is_numeric($size) || $size < self::MIN_SIZE || $size > self::MAX_SIZE) {
            $errors[] = sprintf(
                $this->l('Snowflake size must be a number between %s and %s. For example: 1.5'),
                self::MIN_SIZE,
                self::MAX_SIZE
            );
        }

        $density = Tools::getValue('SNOWFLAKESMEG_DENSITY');
        if (!Validate::isUnsignedInt($density) || $density < self::MIN_DENSITY || $density > self::MAX_DENSITY) {
            $errors[] = sprintf(
                $this->l('The number of snowflakes must be a whole number between %d and %d.'),
                self::MIN_DENSITY,
                self::MAX_DENSITY
            );
        }

        $speed = Tools::getValue('SNOWFLAKESMEG_SPEED');
        if (!isset(self::SPEEDS[$speed])) {
            $errors[] = $this->l('Please choose a valid snowfall intensity.');
        }

        $color = trim((string) Tools::getValue('SNOWFLAKESMEG_COLOR'));
        if ($color !== '' && !Validate::isColor($color)) {
            $errors[] = $this->l('The snowflake color must be a valid color, for example #ffffff.');
        }

        $from = trim((string) Tools::getValue('SNOWFLAKESMEG_FROM'));
        $to = trim((string) Tools::getValue('SNOWFLAKESMEG_TO'));
        if ($from !== '' && !Validate::isDate($from)) {
            $errors[] = $this->l('The first day of snow is not a valid date. Use the format YYYY-MM-DD.');
        }
        if ($to !== '' && !Validate::isDate($to)) {
            $errors[] = $this->l('The last day of snow is not a valid date. Use the format YYYY-MM-DD.');
        }
        if (!$errors && $from !== '' && $to !== '' && $from > $to) {
            $errors[] = $this->l('The first day of snow must be before the last day.');
        }

        if ($errors) {
            foreach ($errors as $error) {
                $this->html .= $this->displayError($error);
            }

            return;
        }

        Configuration::updateValue('SNOWFLAKESMEG_ENABLED', (int) (bool) Tools::getValue('SNOWFLAKESMEG_ENABLED'));
        Configuration::updateValue('SNOWFLAKESMEG_THEME', $theme);
        Configuration::updateValue('SNOWFLAKESMEG_SIZE', (float) $size);
        Configuration::updateValue('SNOWFLAKESMEG_DENSITY', (int) $density);
        Configuration::updateValue('SNOWFLAKESMEG_SPEED', $speed);
        Configuration::updateValue('SNOWFLAKESMEG_COLOR', $color === '' ? '#ffffff' : $color);
        Configuration::updateValue('SNOWFLAKESMEG_FROM', $from);
        Configuration::updateValue('SNOWFLAKESMEG_TO', $to);
        Configuration::updateValue('SNOWFLAKESMEG_MOBILE', (int) (bool) Tools::getValue('SNOWFLAKESMEG_MOBILE'));

        Tools::redirectAdmin($this->getConfigureUrl('&sfm_conf=1'));
    }

    private function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSnowflakesmeg';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        // A failed submit re-renders the form with the rejected values, so
        // nothing typed by the shop owner is lost.
        $fieldsValue = array();
        foreach (array_keys(self::defaultSettings()) as $key) {
            $fieldsValue[$key] = Tools::getValue($key, Configuration::get($key));
        }

        $helper->tpl_vars = array(
            'fields_value' => $fieldsValue,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getSettingsForm()));
    }

    private function getSettingsForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Snow settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Let it snow'),
                        'name' => 'SNOWFLAKESMEG_ENABLED',
                        'is_bool' => true,
                        'desc' => $this->l('Master switch. When enabled, snow falls on every page of your shop, within the season dates below if you set any.'),
                        'values' => array(
                            array('id' => 'enabled_on', 'value' => 1, 'label' => $this->l('Enabled')),
                            array('id' => 'enabled_off', 'value' => 0, 'label' => $this->l('Disabled')),
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Theme'),
                        'name' => 'SNOWFLAKESMEG_THEME',
                        'desc' => $this->l('The Christmas mix scatters bells, music notes, trees, snowmen and gifts between the snowflakes.'),
                        'options' => array(
                            'query' => array(
                                array('id' => 'classic', 'name' => $this->l('Classic snowfall')),
                                array('id' => 'christmas', 'name' => $this->l('Christmas mix (snow + festive ornaments)')),
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Snowfall intensity'),
                        'name' => 'SNOWFLAKESMEG_SPEED',
                        'desc' => $this->l('How fast the flakes fall.'),
                        'options' => array(
                            'query' => array(
                                array('id' => 'calm', 'name' => $this->l('Calm - slow, dreamy flakes')),
                                array('id' => 'normal', 'name' => $this->l('Normal')),
                                array('id' => 'blizzard', 'name' => $this->l('Blizzard - fast and lively')),
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Snowflake size'),
                        'name' => 'SNOWFLAKESMEG_SIZE',
                        'desc' => sprintf(
                            $this->l('Relative to your theme text size, between %s and %s. 1.5 fits most shops; every flake also varies a little around this value.'),
                            self::MIN_SIZE,
                            self::MAX_SIZE
                        ),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Number of snowflakes'),
                        'name' => 'SNOWFLAKESMEG_DENSITY',
                        'desc' => sprintf(
                            $this->l('Between %d and %d. 12 is a good balance; higher values feel like heavy snow.'),
                            self::MIN_DENSITY,
                            self::MAX_DENSITY
                        ),
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Snowflake color'),
                        'name' => 'SNOWFLAKESMEG_COLOR',
                        'desc' => $this->l('The Christmas ornaments keep their own colors. White comes with a soft shadow, so it stays visible on light themes too.'),
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('First day of snow'),
                        'name' => 'SNOWFLAKESMEG_FROM',
                        'desc' => $this->l('Optional. The snow appears automatically on this day. Leave empty to start right away.'),
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('Last day of snow'),
                        'name' => 'SNOWFLAKESMEG_TO',
                        'desc' => $this->l('Optional. The snow disappears automatically after this day - no need to remember to switch it off in January.'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show on phones'),
                        'name' => 'SNOWFLAKESMEG_MOBILE',
                        'is_bool' => true,
                        'desc' => $this->l('Switch off to show the snow only on screens wider than 768 pixels.'),
                        'values' => array(
                            array('id' => 'mobile_on', 'value' => 1, 'label' => $this->l('Enabled')),
                            array('id' => 'mobile_off', 'value' => 0, 'label' => $this->l('Disabled')),
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /* -------------------------------------------------------------------- */
    /* Front office                                                           */
    /* -------------------------------------------------------------------- */

    /**
     * The snow is shown when the module is enabled and today falls within the
     * optional season dates.
     */
    private function shouldDisplay()
    {
        if (!(int) Configuration::get('SNOWFLAKESMEG_ENABLED')) {
            return false;
        }

        $today = date('Y-m-d');
        $from = (string) Configuration::get('SNOWFLAKESMEG_FROM');
        if ($from !== '' && $today < $from) {
            return false;
        }
        $to = (string) Configuration::get('SNOWFLAKESMEG_TO');

        return !($to !== '' && $today > $to);
    }

    public function hookActionFrontControllerSetMedia()
    {
        if (!$this->shouldDisplay()) {
            return;
        }

        $this->context->controller->registerStylesheet(
            'modules-snowflakesmeg',
            'modules/' . $this->name . '/views/css/snowflakes.css',
            array('media' => 'all', 'priority' => 150)
        );
    }

    public function hookDisplayBeforeBodyClosingTag()
    {
        return $this->renderWidget('displayBeforeBodyClosingTag', array());
    }

    /**
     * Renders the snow on any display hook the module is attached to, and
     * through {widget name='snowflakesmeg'} in any theme template.
     */
    public function renderWidget($hookName, array $configuration)
    {
        if ($this->rendered || !$this->shouldDisplay()) {
            return '';
        }
        $this->rendered = true;

        $this->context->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->fetch('module:snowflakesmeg/views/templates/front/snowflakes.tpl');
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        $density = (int) Configuration::get('SNOWFLAKESMEG_DENSITY');

        return array(
            'sfm_flakes' => $this->buildFlakes(
                min(max($density ?: 12, self::MIN_DENSITY), self::MAX_DENSITY),
                Configuration::get('SNOWFLAKESMEG_THEME')
            ),
            'sfm_size' => $this->getFlakeSize(),
            'sfm_color' => $this->getFlakeColor(),
            'sfm_mobile' => (int) Configuration::get('SNOWFLAKESMEG_MOBILE'),
        );
    }

    /**
     * Precomputes one randomized set of flakes: glyph, horizontal position and
     * animation timings differ on every page load, so the snowfall never looks
     * like a repeating pattern. All numbers are pre-formatted with a decimal
     * point, so a locale using decimal commas cannot break the CSS.
     */
    private function buildFlakes($count, $theme)
    {
        $snow = array('❅', '❆', '❄');
        $ornaments = array('🔔', '🎵', '🎄', '⛄', '🎁');
        $baseFall = $this->getFallDuration();

        $flakes = array();
        for ($i = 0; $i < $count; $i++) {
            // The Christmas mix slips roughly one ornament in every three flakes.
            $isSnow = $theme !== 'christmas' || mt_rand(0, 2) > 0;
            $glyphs = $isSnow ? $snow : $ornaments;
            $fall = $baseFall * mt_rand(80, 140) / 100;
            $sway = mt_rand(25, 45) / 10;

            $flakes[] = array(
                'glyph' => $glyphs[mt_rand(0, count($glyphs) - 1)],
                'is_snow' => $isSnow,
                'left' => mt_rand(0, 97),
                'scale' => self::cssNumber(mt_rand(70, 140) / 100),
                'fall' => self::cssNumber($fall),
                'sway' => self::cssNumber($sway),
                // Negative delays start every flake mid-animation, so it is
                // already snowing the moment the page appears.
                'fall_delay' => self::cssNumber(mt_rand(0, (int) ($fall * 10)) / 10),
                'sway_delay' => self::cssNumber(mt_rand(0, (int) ($sway * 10)) / 10),
            );
        }

        return $flakes;
    }

    private function getFallDuration()
    {
        $speed = Configuration::get('SNOWFLAKESMEG_SPEED');

        return isset(self::SPEEDS[$speed]) ? self::SPEEDS[$speed] : self::SPEEDS['normal'];
    }

    private function getFlakeSize()
    {
        $size = (float) Configuration::get('SNOWFLAKESMEG_SIZE');

        return self::cssNumber(min(max($size ?: 1.5, self::MIN_SIZE), self::MAX_SIZE));
    }

    private function getFlakeColor()
    {
        $color = (string) Configuration::get('SNOWFLAKESMEG_COLOR');

        return Validate::isColor($color) && $color !== '' ? $color : '#ffffff';
    }

    private static function cssNumber($value)
    {
        return number_format((float) $value, 2, '.', '');
    }
}
