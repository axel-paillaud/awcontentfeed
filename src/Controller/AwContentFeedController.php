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
        // General configuration form via service
        $generalFormDataHandler = $this->get('axelweb.awcontentfeed.form.general_form_data_handler');
        $generalForm = $generalFormDataHandler->getForm();
        $generalForm->handleRequest($request);

        if ($generalForm->isSubmitted() && $generalForm->isValid()) {
            $errors = $generalFormDataHandler->save($generalForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Configuration saved successfully.', 'Modules.Awcontentfeed.Admin'));
                return $this->redirectToRoute('awcontentfeed_configuration');
            }

            $this->flashErrors($errors);
        }

        return $this->render('@Modules/awcontentfeed/views/templates/admin/configuration.html.twig', [
            'generalForm' => $generalForm->createView(),
        ]);
    }
}
