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

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ContentFeedItemFormType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => $this->trans('Content Type', 'Modules.Awcontentfeed.Admin'),
                'help' => $this->trans('Select the type of content (YouTube video or WordPress article)', 'Modules.Awcontentfeed.Admin'),
                'choices' => [
                    $this->trans('YouTube Video', 'Modules.Awcontentfeed.Admin') => 'youtube',
                    $this->trans('WordPress Article', 'Modules.Awcontentfeed.Admin') => 'wordpress',
                ],
                'required' => true,
                'placeholder' => $this->trans('Choose a type...', 'Modules.Awcontentfeed.Admin'),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice(['choices' => ['youtube', 'wordpress']]),
                ],
            ])
            ->add('url', UrlType::class, [
                'label' => $this->trans('URL', 'Modules.Awcontentfeed.Admin'),
                'help' => $this->trans('Enter the URL of the YouTube video or WordPress article', 'Modules.Awcontentfeed.Admin'),
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Url(),
                    new Assert\Length(['max' => 500]),
                ],
                'attr' => [
                    'placeholder' => 'https://www.youtube.com/watch?v=... or https://example.com/article',
                ],
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Active', 'Modules.Awcontentfeed.Admin'),
                'help' => $this->trans('Display this content on the front-office', 'Modules.Awcontentfeed.Admin'),
                'required' => false,
            ]);
    }
}
