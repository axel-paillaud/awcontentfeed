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

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        // Handle toggle active status
        document.querySelectorAll('[data-action="toggle"]').forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const formId = this.getAttribute('data-form-id');
                const form = document.getElementById(formId);
                if (form) {
                    form.submit();
                }
            });
        });

        // Handle refresh metadata
        document.querySelectorAll('[data-action="refresh"]').forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const formId = this.getAttribute('data-form-id');
                const form = document.getElementById(formId);
                if (form) {
                    form.submit();
                }
            });
        });

        // Handle delete with confirmation
        document.querySelectorAll('[data-action="delete"]').forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const confirmMessage = this.getAttribute('data-confirm-message');
                const formId = this.getAttribute('data-form-id');

                if (confirm(confirmMessage)) {
                    const form = document.getElementById(formId);
                    if (form) {
                        form.submit();
                    }
                }
            });
        });

    });

})();
