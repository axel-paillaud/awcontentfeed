{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Axelweb <contact@axelweb.fr>
 * @copyright 2025 Axelweb
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}

<section id="awcontentfeed" class="awcontentfeed py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h3 text-center mb-4">
                    {l s='Our Latest Content' d='Modules.Awcontentfeed.Shop'}
                </h2>
            </div>
        </div>

        <div class="row g-4">
            {foreach from=$items item=item}
                <div class="col-12 col-md-6 col-xl-4">
                    <a href="{$item.url|escape:'html':'UTF-8'}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="awcontentfeed-card-link">
                        <div class="card h-100 awcontentfeed-card">
                            {if $item.thumbnail}
                                <img src="{$item.thumbnail|escape:'html':'UTF-8'}"
                                     class="card-img-top"
                                     alt="{$item.title|escape:'html':'UTF-8'}"
                                     loading="lazy">
                            {else}
                                <div class="awcontentfeed-placeholder">
                                    {if $item.type == 'youtube'}
                                        <i class="material-icons">play_circle_filled</i>
                                    {else}
                                        <i class="material-icons">article</i>
                                    {/if}
                                </div>
                            {/if}

                            <div class="card-body d-flex flex-column">
                                <div class="awcontentfeed-type mb-2">
                                    {if $item.type == 'youtube'}
                                        <span class="badge bg-danger">
                                            <i class="material-icons">play_circle_filled</i>
                                            {l s='Video' d='Modules.Awcontentfeed.Shop'}
                                        </span>
                                    {else}
                                        <span class="badge bg-primary">
                                            <i class="material-icons">article</i>
                                            {l s='Article' d='Modules.Awcontentfeed.Shop'}
                                        </span>
                                    {/if}
                                </div>

                                {if $item.title}
                                    <h3 class="card-title h5">
                                        {$item.title|escape:'html':'UTF-8'}
                                    </h3>
                                {/if}

                                {if $item.description}
                                    <p class="card-text text-muted flex-grow-1">
                                        {$item.description|truncate:120:'...'|escape:'html':'UTF-8'}
                                    </p>
                                {/if}

                                <span class="btn btn-primary mt-auto">
                                    {if $item.type == 'youtube'}
                                        {l s='Watch Video' d='Modules.Awcontentfeed.Shop'}
                                    {else}
                                        {l s='Read Article' d='Modules.Awcontentfeed.Shop'}
                                    {/if}
                                    <i class="material-icons">arrow_forward</i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            {/foreach}
        </div>
    </div>
</section>
