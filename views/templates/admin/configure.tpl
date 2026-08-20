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

<div class="panel">
    <div class="panel-heading">
        <i class="icon-info-circle"></i> {l s='How it works' mod='snowflakesmeg'}
    </div>
    <div class="panel-body" style="padding: 15px;">
        <p>{l s='This module adds a snowfall effect to every page of your shop, in pure CSS: no JavaScript, no external files, no measurable impact on page speed.' mod='snowflakesmeg'}</p>
        <ol style="margin-bottom: 10px;">
            <li>{l s='Turn the snow on with the "Let it snow" switch below.' mod='snowflakesmeg'}</li>
            <li>{l s='Pick a theme: classic white snowfall, or a Christmas mix with bells, trees, snowmen and gifts.' mod='snowflakesmeg'}</li>
            <li>{l s='Adjust the size, amount, speed and color of the flakes to match your design.' mod='snowflakesmeg'}</li>
            <li>{l s='Optional: set the first and last day of snow, and the effect starts and stops by itself - perfect for the holiday season.' mod='snowflakesmeg'}</li>
        </ol>
        <p style="margin-bottom: 0;">
            <i class="icon-lightbulb"></i>
            {l s='The snow never blocks clicks or scrolling, and it hides itself for visitors whose device asks for reduced motion.' mod='snowflakesmeg'}
        </p>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <i class="icon-eye"></i> {l s='Current status and preview' mod='snowflakesmeg'}
    </div>
    <div class="panel-body" style="padding: 15px;">
        {if $sfm_status == 'live'}
            <div class="alert alert-success" style="margin-bottom: 15px;">
                {l s='Snow is falling on your storefront right now.' mod='snowflakesmeg'}
                {if $sfm_to}
                    {l s='It will stop automatically after %s.' sprintf=[$sfm_to|escape:'html':'UTF-8'] mod='snowflakesmeg'}
                {/if}
            </div>
        {elseif $sfm_status == 'waiting'}
            <div class="alert alert-info" style="margin-bottom: 15px;">
                {l s='Snow is enabled and scheduled: it will appear automatically on %s.' sprintf=[$sfm_from|escape:'html':'UTF-8'] mod='snowflakesmeg'}
            </div>
        {elseif $sfm_status == 'ended'}
            <div class="alert alert-warning" style="margin-bottom: 15px;">
                {l s='The snow season ended on %s, so visitors no longer see it. Move or clear the last day of snow to bring it back.' sprintf=[$sfm_to|escape:'html':'UTF-8'] mod='snowflakesmeg'}
            </div>
        {else}
            <div class="alert alert-info" style="margin-bottom: 15px;">
                {l s='Snow is currently switched off. Enable it below to let it snow.' mod='snowflakesmeg'}
            </div>
        {/if}

        <div class="sfm-preview" aria-hidden="true"
            style="font-size: {$sfm_size|escape:'html':'UTF-8'}em; --sfm-color: {$sfm_color|escape:'html':'UTF-8'};">
            {foreach $sfm_preview_flakes as $flake}
                <span class="sfm-preview__flake{if $flake.is_snow} sfm-preview__flake--snow{/if}"
                    style="left: {$flake.left|escape:'html':'UTF-8'}%; font-size: {$flake.scale|escape:'html':'UTF-8'}em; animation-duration: {$flake.fall|escape:'html':'UTF-8'}s; animation-delay: -{$flake.fall_delay|escape:'html':'UTF-8'}s;">{$flake.glyph|escape:'html':'UTF-8'}</span>
            {/foreach}
        </div>
        <p class="help-block" style="margin-bottom: 0;">
            {l s='The preview uses your saved settings. Save the form below to refresh it.' mod='snowflakesmeg'}
        </p>
    </div>
</div>

<style>
    .sfm-preview {
        position: relative;
        height: 200px;
        overflow: hidden;
        border-radius: 4px;
        background: linear-gradient(180deg, #1d2b45 0%, #3a5578 100%);
        pointer-events: none;
    }

    .sfm-preview__flake {
        position: absolute;
        top: -1.5em;
        line-height: 1;
        color: var(--sfm-color, #fff);
        animation: sfm-preview-fall 10s linear infinite;
    }

    .sfm-preview__flake--snow {
        text-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    }

    @keyframes sfm-preview-fall {
        to {
            transform: translateY(260px);
        }
    }
</style>
