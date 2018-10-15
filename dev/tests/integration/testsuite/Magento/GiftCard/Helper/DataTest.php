<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCard\Helper;

use Magento\TestFramework\Helper\Bootstrap;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Data
     */
    private $helper;

    protected function setUp()
    {
        $this->helper = Bootstrap::getObjectManager()->create(Data::class);
    }

    public function testGetEmailGeneratedItemsBlock()
    {
        $result = $this->helper->getEmailGeneratedItemsBlock()
            ->getUrl('magento_giftcardaccount/customer', ['giftcard' => '0V6YN8ZUBIZF']);

        $this->assertContains('/giftcard/customer/index/giftcard/0V6YN8ZUBIZF/', $result);
    }
}
