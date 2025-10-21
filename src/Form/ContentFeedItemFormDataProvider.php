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

use Axelweb\AwContentFeed\Repository\ContentFeedItemRepository;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class ContentFeedItemFormDataProvider implements FormDataProviderInterface
{
    private $repository;
    private $editId;

    public function __construct(ContentFeedItemRepository $repository, ?int $editId = null)
    {
        $this->repository = $repository;
        $this->editId = $editId;
    }

    public function getData(): array
    {
        // If editing, load existing data
        if ($this->editId) {
            $item = $this->repository->findById($this->editId);
            
            if ($item) {
                return [
                    'type' => $item['type'],
                    'url' => $item['url'],
                    'active' => (bool) $item['active'],
                ];
            }
        }

        // Default values for new item
        return [
            'type' => null,
            'url' => '',
            'active' => true,
        ];
    }

    public function setData(array $data): array
    {
        $errors = [];

        // Validate required fields
        if (empty($data['type']) || !in_array($data['type'], ['youtube', 'wordpress'])) {
            $errors[] = 'Invalid content type.';
        }

        if (empty($data['url'])) {
            $errors[] = 'URL is required.';
        }

        if (!empty($errors)) {
            return $errors;
        }

        // Prepare data for save
        $saveData = [
            'type' => $data['type'],
            'url' => $data['url'],
            'active' => isset($data['active']) ? (int) $data['active'] : 0,
        ];

        // Update or create
        if ($this->editId) {
            $success = $this->repository->update($this->editId, $saveData);
        } else {
            // Set position for new item
            $saveData['position'] = $this->repository->getNextPosition();
            $success = $this->repository->create($saveData);
        }

        if (!$success) {
            $errors[] = 'An error occurred while saving the content feed item.';
        }

        return $errors;
    }

    /**
     * Set the ID of the item to edit
     *
     * @param int|null $editId
     */
    public function setEditId(?int $editId): void
    {
        $this->editId = $editId;
    }
}
