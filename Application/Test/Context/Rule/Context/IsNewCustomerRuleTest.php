<?php declare(strict_types=1);
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

namespace Shopware\Core\Application\Test\Context\Rule\Context;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\CustomerContext;
use Shopware\Core\Checkout\Cart\Cart\Struct\CalculatedCart;
use Shopware\Core\Checkout\Customer\Struct\CustomerBasicStruct;
use Shopware\Core\Checkout\Rule\Specification\Context\IsNewCustomerRule;
use Shopware\Core\Checkout\Rule\Specification\Scope\CartRuleScope;

class IsNewCustomerRuleTest extends TestCase
{
    public function testIsNewCustomer(): void
    {
        $rule = new IsNewCustomerRule();

        $cart = $this->createMock(CalculatedCart::class);

        $customer = new CustomerBasicStruct();
        $customer->setFirstLogin(new \DateTime());

        $context = $this->createMock(CustomerContext::class);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertTrue(
            $rule->match(new CartRuleScope($cart, $context))->matches()
        );
    }

    public function testIsNotNewCustomer(): void
    {
        $rule = new IsNewCustomerRule();

        $cart = $this->createMock(CalculatedCart::class);

        $customer = new CustomerBasicStruct();
        $customer->setFirstLogin(
            (new \DateTime())->sub(
                new \DateInterval('P' . 10 . 'D')
            )
        );

        $context = $this->createMock(CustomerContext::class);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertFalse(
            $rule->match(new CartRuleScope($cart, $context))->matches()
        );
    }

    public function testWithFutureDate(): void
    {
        $rule = new IsNewCustomerRule();

        $cart = $this->createMock(CalculatedCart::class);

        $customer = new CustomerBasicStruct();
        $customer->setFirstLogin(
            (new \DateTime())->add(
                new \DateInterval('P' . 10 . 'D')
            )
        );

        $context = $this->createMock(CustomerContext::class);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertFalse(
            $rule->match(new CartRuleScope($cart, $context))->matches()
        );
    }

    public function testWithoutCustomer(): void
    {
        $rule = new IsNewCustomerRule();

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(CustomerContext::class);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue(null));

        $this->assertFalse(
            $rule->match(new CartRuleScope($cart, $context))->matches()
        );
    }
}
