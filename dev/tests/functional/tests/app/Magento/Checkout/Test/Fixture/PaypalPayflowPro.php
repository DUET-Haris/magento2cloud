<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Checkout\Test\Fixture;

use Magento\Mtf\Factory\Factory;

/**
 * Class PaypalPayflowPro
 * PayPal Payflow Pro Method
 * Guest checkout using PayPal Payments Pro method and offline shipping method
 *
 */
class PaypalPayflowPro extends Checkout
{
    /**
     * Prepare data for guest checkout with PayPal Payflow Pro Method
     */
    protected function _initData()
    {
        //Verification data
        $this->_data = [
            'totals' => [
                'grand_total' => '156.81',
            ],
        ];
    }

    /**
     * Setup fixture
     */
    public function persist()
    {
        //Configuration
        $this->_persistConfiguration([
            'flat_rate',
            'paypal_disabled_all_methods',
            'paypal_payflow_pro',
            'display_price',
            'display_shopping_cart',
        ]);

        //Tax
        Factory::getApp()->magentoTaxRemoveTaxRule();
        $objectManager = Factory::getObjectManager();
        $taxRule = $objectManager->create('Magento\Tax\Test\Fixture\TaxRule', ['dataset' => 'custom_rule']);
        $taxRule->persist();

        //Products
        $simple = $objectManager->create(
            'Magento\Catalog\Test\Fixture\CatalogProductSimple',
            ['dataset' => 'simple_10_dollar']
        );
        $simple->persist();
        $configurable = $objectManager->create(
            'Magento\ConfigurableProduct\Test\Fixture\ConfigurableProduct',
            ['dataset' => 'two_options_by_one_dollar']
        );
        $configurable->persist();
        $bundle = $objectManager->create(
            'Magento\Bundle\Test\Fixture\BundleProduct',
            [
                'dataset' => 'fixed_100_dollar_with_required_options'
            ]
        );
        $bundle->persist();

        $this->products = [
            $simple,
            $configurable,
            $bundle,
        ];

        //Checkout data
        $this->billingAddress = $objectManager->create(
            'Magento\Customer\Test\Fixture\Address',
            ['dataset' => 'US_address_1']
        );

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('flat_rate');

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('paypal_payflow_pro');

        $this->creditCard = $objectManager->create(
            'Magento\Payment\Test\Fixture\CreditCard',
            ['dataset' => 'visa_default']
        );

        $this->customer = Factory::getFixtureFactory()->getMagentoPaypalCustomer();
        $this->customer->switchData('customer_US');
    }
}
