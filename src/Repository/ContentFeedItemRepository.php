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

namespace Axelweb\AwContentFeed\Repository;

class ContentFeedItemRepository
{
    /**
     * Find all content feed items ordered by position
     *
     * @return array
     */
    public function findAll(): array
    {
        $query = new \DbQuery();
        $query->select('*')
            ->from('awcontentfeed_item')
            ->orderBy('position ASC, date_add DESC');

        $results = \Db::getInstance()->executeS($query);

        return $results ?: [];
    }

    /**
     * Find a content feed item by ID
     *
     * @param int $id
     *
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $query = new \DbQuery();
        $query->select('*')
            ->from('awcontentfeed_item')
            ->where('id_awcontentfeed_item = ' . (int) $id);

        $result = \Db::getInstance()->getRow($query);

        return $result ?: null;
    }

    /**
     * Create a new content feed item
     *
     * @param array $data
     *
     * @return bool|int Returns the inserted ID on success, false on failure
     */
    public function create(array $data): bool
    {
        $now = date('Y-m-d H:i:s');

        $insertData = [
            'type' => pSQL($data['type']),
            'url' => pSQL($data['url']),
            'title' => isset($data['title']) ? pSQL($data['title']) : null,
            'description' => isset($data['description']) ? pSQL($data['description']) : null,
            'thumbnail' => isset($data['thumbnail']) ? pSQL($data['thumbnail']) : null,
            'position' => isset($data['position']) ? (int) $data['position'] : 0,
            'active' => isset($data['active']) ? (int) $data['active'] : 1,
            'date_add' => $now,
            'date_upd' => $now,
        ];

        return \Db::getInstance()->insert('awcontentfeed_item', $insertData);
    }

    /**
     * Update an existing content feed item
     *
     * @param int $id
     * @param array $data
     *
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $updateData = [
            'date_upd' => date('Y-m-d H:i:s'),
        ];

        if (isset($data['type'])) {
            $updateData['type'] = pSQL($data['type']);
        }
        if (isset($data['url'])) {
            $updateData['url'] = pSQL($data['url']);
        }
        if (isset($data['title'])) {
            $updateData['title'] = pSQL($data['title']);
        }
        if (isset($data['description'])) {
            $updateData['description'] = pSQL($data['description']);
        }
        if (isset($data['thumbnail'])) {
            $updateData['thumbnail'] = pSQL($data['thumbnail']);
        }
        if (isset($data['position'])) {
            $updateData['position'] = (int) $data['position'];
        }
        if (isset($data['active'])) {
            $updateData['active'] = (int) $data['active'];
        }

        return \Db::getInstance()->update(
            'awcontentfeed_item',
            $updateData,
            'id_awcontentfeed_item = ' . (int) $id
        );
    }

    /**
     * Delete a content feed item by ID
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        return \Db::getInstance()->delete(
            'awcontentfeed_item',
            'id_awcontentfeed_item = ' . (int) $id
        );
    }

    /**
     * Toggle active status of a content feed item
     *
     * @param int $id
     *
     * @return bool
     */
    public function toggleActive(int $id): bool
    {
        $item = $this->findById($id);

        if (!$item) {
            return false;
        }

        $newStatus = $item['active'] ? 0 : 1;

        return $this->update($id, ['active' => $newStatus]);
    }

    /**
     * Get the next position value
     *
     * @return int
     */
    public function getNextPosition(): int
    {
        $query = new \DbQuery();
        $query->select('MAX(position)')
            ->from('awcontentfeed_item');

        $maxPosition = (int) \Db::getInstance()->getValue($query);

        return $maxPosition + 1;
    }
}
