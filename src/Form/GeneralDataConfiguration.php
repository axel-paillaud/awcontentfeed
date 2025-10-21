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

declare(strict_types=1);

namespace Axelweb\AwContentFeed\Form;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

final class GeneralDataConfiguration implements DataConfigurationInterface
{
    public const AWCONTENTFEED_TEST_FIELD = 'AWCONTENTFEED_TEST_FIELD';

    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): array
    {
        return [
            'test_field' => $this->configuration->get(static::AWCONTENTFEED_TEST_FIELD) ?: '',
        ];
    }

    public function updateConfiguration(array $configuration): array
    {
        $errors = [];

        if (!$this->validateConfiguration($configuration)) {
            $errors[] = 'Invalid configuration payload.';
            return $errors;
        }

        $testField = $configuration['test_field'] ?? '';

        $this->configuration->set(static::AWCONTENTFEED_TEST_FIELD, $testField);

        return $errors;
    }

    public function validateConfiguration(array $configuration): bool
    {
        return isset($configuration['test_field']);
    }
}
