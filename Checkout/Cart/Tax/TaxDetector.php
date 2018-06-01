<?php
declare(strict_types=1);
/**
 * Shopware\Core 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware\Core" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Core\Checkout\Cart\Tax;

use Shopware\Core\Checkout\CustomerContext;
use Shopware\Core\Checkout\Cart\Price\Struct\CartPrice;

class TaxDetector
{
    public function useGross(CustomerContext $context): bool
    {
        return $context->getCurrentCustomerGroup()->getDisplayGross();
    }

    public function isNetDelivery(CustomerContext $context): bool
    {
        return $context->getShippingLocation()->getCountry()->getTaxFree();
    }

    public function getTaxState(CustomerContext $context): string
    {
        if ($this->isNetDelivery($context)) {
            return CartPrice::TAX_STATE_FREE;
        }

        if ($this->useGross($context)) {
            return CartPrice::TAX_STATE_GROSS;
        }

        return CartPrice::TAX_STATE_NET;
    }
}
