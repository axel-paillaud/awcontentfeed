<?php
/**
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
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use Axelweb\AwContentFeed\Repository\ContentFeedItemRepository;

class AwContentFeed extends Module implements PrestaShop\PrestaShop\Core\Module\WidgetInterface
{
    public function __construct()
    {
        $this->name = 'awcontentfeed';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Axelweb';
        $this->need_instance = 1;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Content Feed', [], 'Modules.Awcontentfeed.Admin');
        $this->description = $this->trans('Display YouTube videos and WordPress articles in your store.', [], 'Modules.Awcontentfeed.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall this module?', [], 'Modules.Awcontentfeed.Admin');

        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    public function install(): bool
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $installed = parent::install()
            && $this->installDb()
            && $this->registerHook('displayHome')
            && $this->registerHook('actionFrontControllerSetMedia');

        if ($installed) {
            Tools::clearSf2Cache();
        }

        return $installed;
    }

    public function uninstall(): bool
    {
        return parent::uninstall()
            && $this->uninstallDb();
    }

    protected function installDb(): bool
    {
        $file = __DIR__ . '/sql/install.php';

        return is_file($file) ? (bool) require $file : false;
    }

    protected function uninstallDb(): bool
    {
        $file = __DIR__ . '/sql/uninstall.php';

        return is_file($file) ? (bool) require $file : false;
    }

    public function getContent(): void
    {
        $route = $this->get('router')->generate('awcontentfeed_configuration');
        Tools::redirectAdmin($route);
    }

    /**
     * Hook to register CSS on front-office pages
     */
    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-awcontentfeed-style',
            'modules/' . $this->name . '/views/css/awcontentfeed.css',
            [
                'media' => 'all',
                'priority' => 200,
            ]
        );
    }

    /**
     * Render the widget
     *
     * @param string|null $hookName Hook name
     * @param array $configuration Widget configuration
     *
     * @return string Rendered widget HTML
     */
    public function renderWidget($hookName = null, array $configuration = []): string
    {
        // Get widget variables
        $variables = $this->getWidgetVariables($hookName, $configuration);

        // If no items, don't render anything
        if (empty($variables['items'])) {
            return '';
        }

        // Assign variables to Smarty
        $this->smarty->assign($variables);

        // Return rendered template
        return $this->fetch('module:' . $this->name . '/views/templates/hook/contentfeed.tpl');
    }

    /**
     * Get widget variables
     *
     * @param string|null $hookName Hook name
     * @param array $configuration Widget configuration
     *
     * @return array Widget variables
     */
    public function getWidgetVariables($hookName = null, array $configuration = []): array
    {
        $repository = new ContentFeedItemRepository();

        // Get all active items
        $allItems = $repository->findAll();

        // Filter only active items
        $activeItems = array_filter($allItems, function ($item) {
            return (bool) $item['active'];
        });

        return [
            'items' => array_values($activeItems),
        ];
    }
}
