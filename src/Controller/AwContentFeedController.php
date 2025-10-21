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

namespace Axelweb\AwContentFeed\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AwContentFeedController extends FrameworkBundleAdminController
{
    public function index(Request $request): Response
    {
        $repository = $this->get('axelweb.awcontentfeed.repository.content_feed_item');
        $items = $repository->findAll();

        // Content Feed Item form via service
        $formDataHandler = $this->get('axelweb.awcontentfeed.form.content_feed_item_form_data_handler');
        $form = $formDataHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            // Fetch metadata before saving
            $metadataFetcher = $this->get('axelweb.awcontentfeed.service.metadata_fetcher');
            $metadata = $metadataFetcher->fetch($formData['url'], $formData['type']);

            // Merge metadata with form data
            $formData = array_merge($formData, $metadata);

            $errors = $formDataHandler->save($formData);

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Content feed item saved successfully.', 'Modules.Awcontentfeed.Admin'));

                return $this->redirectToRoute('awcontentfeed_configuration');
            }

            $this->flashErrors($errors);
        }

        return $this->render('@Modules/awcontentfeed/views/templates/admin/configuration.html.twig', [
            'itemForm' => $form->createView(),
            'items' => $items,
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $repository = $this->get('axelweb.awcontentfeed.repository.content_feed_item');
        $item = $repository->findById($id);

        if (!$item) {
            $this->addFlash('error', $this->trans('Content feed item not found.', 'Modules.Awcontentfeed.Admin'));

            return $this->redirectToRoute('awcontentfeed_configuration');
        }

        $items = $repository->findAll();

        // Create form data provider with edit ID
        $dataProvider = $this->get('axelweb.awcontentfeed.form.content_feed_item_form_data_provider');
        $dataProvider->setEditId($id);

        // Create form handler with the updated data provider
        $formDataHandler = new \PrestaShop\PrestaShop\Core\Form\Handler(
            $this->get('form.factory'),
            $this->get('prestashop.core.hook.dispatcher'),
            $dataProvider,
            'Axelweb\AwContentFeed\Form\ContentFeedItemFormType',
            'AwContentFeed'
        );

        $form = $formDataHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            // Fetch metadata before saving
            $metadataFetcher = $this->get('axelweb.awcontentfeed.service.metadata_fetcher');
            $metadata = $metadataFetcher->fetch($formData['url'], $formData['type']);

            // Merge metadata with form data
            $formData = array_merge($formData, $metadata);

            $errors = $formDataHandler->save($formData);

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Content feed item updated successfully.', 'Modules.Awcontentfeed.Admin'));

                return $this->redirectToRoute('awcontentfeed_configuration');
            }

            $this->flashErrors($errors);
        }

        return $this->render('@Modules/awcontentfeed/views/templates/admin/configuration.html.twig', [
            'itemForm' => $form->createView(),
            'items' => $items,
            'editId' => $id,
        ]);
    }

    public function delete(Request $request, int $id): Response
    {
        $repository = $this->get('axelweb.awcontentfeed.repository.content_feed_item');
        $item = $repository->findById($id);

        if (!$item) {
            $this->addFlash('error', $this->trans('Content feed item not found.', 'Modules.Awcontentfeed.Admin'));
        } else {
            $success = $repository->delete($id);
            if ($success) {
                $this->addFlash('success', $this->trans('Content feed item deleted successfully.', 'Modules.Awcontentfeed.Admin'));
            } else {
                $this->addFlash('error', $this->trans('An error occurred while deleting the content feed item.', 'Modules.Awcontentfeed.Admin'));
            }
        }

        return $this->redirectToRoute('awcontentfeed_configuration');
    }

    public function toggle(Request $request, int $id): Response
    {
        $repository = $this->get('axelweb.awcontentfeed.repository.content_feed_item');
        $item = $repository->findById($id);

        if (!$item) {
            $this->addFlash('error', $this->trans('Content feed item not found.', 'Modules.Awcontentfeed.Admin'));
        } else {
            $success = $repository->toggleActive($id);

            if ($success) {
                $newStatus = $item['active'] ? 0 : 1;
                if ($newStatus) {
                    $this->addFlash('success', $this->trans('Content feed item activated successfully.', 'Modules.Awcontentfeed.Admin'));
                } else {
                    $this->addFlash('success', $this->trans('Content feed item deactivated successfully.', 'Modules.Awcontentfeed.Admin'));
                }
            } else {
                $this->addFlash('error', $this->trans('An error occurred while updating the content feed item.', 'Modules.Awcontentfeed.Admin'));
            }
        }

        return $this->redirectToRoute('awcontentfeed_configuration');
    }

    public function refresh(Request $request, int $id): Response
    {
        $repository = $this->get('axelweb.awcontentfeed.repository.content_feed_item');
        $item = $repository->findById($id);

        if (!$item) {
            $this->addFlash('error', $this->trans('Content feed item not found.', 'Modules.Awcontentfeed.Admin'));
        } else {
            // Fetch fresh metadata
            $metadataFetcher = $this->get('axelweb.awcontentfeed.service.metadata_fetcher');
            $metadata = $metadataFetcher->fetch($item['url'], $item['type']);

            // Update item with fresh metadata
            $success = $repository->update($id, $metadata);

            if ($success) {
                $this->addFlash('success', $this->trans('Metadata refreshed successfully.', 'Modules.Awcontentfeed.Admin'));
            } else {
                $this->addFlash('error', $this->trans('An error occurred while refreshing metadata.', 'Modules.Awcontentfeed.Admin'));
            }
        }

        return $this->redirectToRoute('awcontentfeed_configuration');
    }
}
