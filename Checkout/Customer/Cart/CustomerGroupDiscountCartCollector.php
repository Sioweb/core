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

namespace Shopware\Core\Checkout\Customer\Cart;

use Shopware\Core\Checkout\CustomerContext;
use Shopware\Core\Checkout\Cart\Cart\CartCollectorInterface;
use Shopware\Core\Checkout\Cart\Cart\Struct\Cart;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroupDiscount\Collection\CustomerGroupDiscountBasicCollection;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroupDiscount\CustomerGroupDiscountRepository;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroupDiscount\Struct\CustomerGroupDiscountBasicStruct;
use Shopware\Core\Framework\ORM\Search\Criteria;
use Shopware\Core\Framework\ORM\Search\Query\TermQuery;
use Shopware\Core\Framework\Struct\StructCollection;

class CustomerGroupDiscountCartCollector implements CartCollectorInterface
{
    /**
     * @var CustomerGroupDiscountRepository
     */
    private $customerGroupDiscountRepository;

    public function __construct(CustomerGroupDiscountRepository $customerGroupDiscountRepository)
    {
        $this->customerGroupDiscountRepository = $customerGroupDiscountRepository;
    }

    public function prepare(
        StructCollection $fetchDefinition,
        Cart $cart,
        CustomerContext $context
    ): void {
    }

    public function fetch(
        StructCollection $dataCollection,
        StructCollection $fetchCollection,
        CustomerContext $context
    ): void {
        $criteria = new Criteria();
        $criteria->addFilter(
            new TermQuery(
                'customer_group_discount.customerGroupId',
                $context->getCurrentCustomerGroup()->getId()
            )
        );
        $discounts = $this->customerGroupDiscountRepository->search($criteria, $context->getContext());

        $discounts->sort(function (CustomerGroupDiscountBasicStruct $a, CustomerGroupDiscountBasicStruct $b) {
            if ($a->getMinimumCartAmount() !== $b->getMinimumCartAmount()) {
                return -1;
            }

            return $a->getMinimumCartAmount() > $b->getMinimumCartAmount();
        });

        $dataCollection->add(new CustomerGroupDiscountBasicCollection($discounts->getElements()),
            CustomerGroupDiscountProcessor::class
        );
    }
}
