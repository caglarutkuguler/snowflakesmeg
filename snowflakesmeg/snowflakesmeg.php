<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2021 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Snowflakesmeg extends Module
{
    protected $config_form = false;
    private $confirmation;
    private $_html = '';
    private $post_errors = array();

    public function __construct()
    {
        $this->name = 'snowflakesmeg';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'MEG Venture';
        $this->need_instance = 0;
        $this->module_key = 'a67eacc637922bc344faab514f05f59a';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Snowflakes');
        $this->description = $this->l('Create snowflakes effect on your front office.');
    }

    public function install()
    {
        Configuration::updateValue('SNOWFLAKES', 1);
        Configuration::updateValue('sizesnowflakes', 1.5);

        return parent::install() &&
        $this->registerHook('header') &&
        $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('SNOWFLAKES');
        Configuration::deleteByName('sizesnowflakes');

        return parent::uninstall();
    }

    private function _postValidation()
    {
        if (Tools::isSubmit('submitSnowflakesModule') == true) {
            if (!Tools::getValue('sizesnowflakes')) {
                $this->post_errors[] = $this->l('Changes are not saved. Snowflake size is required.');
            }

            if (!Validate::isFloat(Tools::getValue('sizesnowflakes'))) {
                $this->post_errors[] = $this->l('Changes are not saved. Snowflake size is should be a floating value.');
            }
        }
    }

    public function getContent()
    {
        $this->_html = null;
        if (Tools::isSubmit('submitSnowflakesModule') == true) {
            $this->_postValidation();
            if (!count($this->post_errors)) {
                $this->postProcess();
                $this->confirmation = $this->displayConfirmation($this->l('Settings updated successfully.'));
            } else {
                foreach ($this->post_errors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $this->_html . $this->confirmation . $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSnowflakesModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Type of Snowflakes'),
                        'name' => 'SNOWFLAKES',
                        'class' => 't',
                        'required' => true,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'disabled_snowflakes',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                            array(
                                'id' => 'normal_snowflakes',
                                'value' => 1,
                                'label' => $this->l('Normal Snowflakes') . '&nbsp;<i class="fa fa-snowflake-o" aria-hidden="true"></i>',
                            ),
                            array(
                                'id' => 'christmas_snowflakes',
                                'value' => 2,
                                'label' => $this->l('Christmas Theme Snowflakes') . '&nbsp;<i class="fa fa-snowflake-o" aria-hidden="true"></i>&nbsp;<i style="color: #cc2037;" class="fa fa-music" aria-hidden="true"></i>&nbsp;<i style="color: #ed9b40;" class="fa fa-bell-o" aria-hidden="true"></i>',
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Size of the Snowflakes'),
                        'name' => 'sizesnowflakes',
                        'size' => 20,
                        'required' => true,
                        'hint' => $this->l('Default: 1.5, values between 0.5 and 3 are recommended'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'SNOWFLAKES' => Configuration::get('SNOWFLAKES'),
            'sizesnowflakes' => Configuration::get('sizesnowflakes'),
        );
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookHeader()
    {
        $this->smarty->assign(array(
            'sizesnowflakes' => Configuration::get('sizesnowflakes'),
        ));

        if (Configuration::get('SNOWFLAKES') == 1) {
            return $this->display(__FILE__, 'views/templates/front/normal_snowflakes.tpl');
        } elseif (Configuration::get('SNOWFLAKES') == 2) {
            return $this->display(__FILE__, 'views/templates/front/christmas_snowflakes.tpl');
        }
    }

    public function hookDisplayHeader()
    {
        return $this->hookHeader();
    }
}
