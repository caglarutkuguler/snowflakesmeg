{*
* 2019-2026 MEG Venture
*
* NOTICE OF LICENSE
*
* This source file is subject to the MIT License
* that is bundled with this package in the file LICENSE.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/MIT
*
*  @author    MEG Venture
*  @copyright 2019-2026 MEG Venture & Consulting Ltd.
*  @license   https://opensource.org/licenses/MIT MIT License
*}

{if $sfm_flakes}
    <div class="snowflakesmeg{if !$sfm_mobile} snowflakesmeg--desktop-only{/if}" aria-hidden="true"
        style="font-size: {$sfm_size|escape:'html':'UTF-8'}em; --sfm-color: {$sfm_color|escape:'html':'UTF-8'};">
        {foreach $sfm_flakes as $flake}
            <span class="snowflakesmeg__flake{if $flake.is_snow} snowflakesmeg__flake--snow{/if}"
                style="left: {$flake.left|escape:'html':'UTF-8'}%; font-size: {$flake.scale|escape:'html':'UTF-8'}em; animation-duration: {$flake.fall|escape:'html':'UTF-8'}s; animation-delay: -{$flake.fall_delay|escape:'html':'UTF-8'}s;"><span
                    class="snowflakesmeg__sway"
                    style="animation-duration: {$flake.sway|escape:'html':'UTF-8'}s; animation-delay: -{$flake.sway_delay|escape:'html':'UTF-8'}s;">{$flake.glyph|escape:'html':'UTF-8'}</span></span>
        {/foreach}
    </div>
{/if}
