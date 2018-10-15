<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Api;

// @codingStandardsIgnoreFile

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

class MessageGuestItemRepositoryTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'giftMessageItemRepositoryV1';
    const RESOURCE_PATH = '/V1/guest-carts/';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @magentoApiDataFixture Magento/GiftMessage/_files/quote_with_item_message.php
     * @magentoApiDataFixture Magento/GiftWrapping/_files/wrapping.php
     */
    public function testSave()
    {
        /** @var \Magento\GiftWrapping\Model\Wrapping $wrapping */
        $wrapping = $this->objectManager->create('Magento\GiftWrapping\Model\Wrapping');
        $wrapping->load('image.png', 'image');

        // sales/gift_options/allow_items must be set to 1 in system configuration
        // @todo remove next statement when \Magento\TestFramework\TestCase\WebapiAbstract::_updateAppConfig is fixed
        $this->markTestIncomplete('This test relies on system configuration state.');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->objectManager->create('Magento\Quote\Model\Quote');
        $quote->load('test_order_item_with_message', 'reserved_order_id');
        $product = $this->objectManager->create('Magento\Catalog\Model\Product');
        $product->load($product->getIdBySku('simple_with_message'));
        $itemId = $quote->getItemByProduct($product)->getId();
        $cartId = $quote->getId();
        /** @var \Magento\Quote\Model\QuoteIdMask $quoteIdMask */
        $quoteIdMask = Bootstrap::getObjectManager()
            ->create('Magento\Quote\Model\QuoteIdMaskFactory')
            ->create();
        $quoteIdMask->load($cartId, 'quote_id');
        //Use masked cart Id
        $cartId = $quoteIdMask->getMaskedId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/gift-message/' .  $itemId,
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];

        $requestData = [
            'cartId' => $cartId,
            'itemId' => $itemId,
            'giftMessage' => [
                'recipient' => 'John Doe',
                'sender' => 'Jane Roe',
                'message' => 'Gift Message Text New',
                'extension_attributes' => [
                    'wrapping_id' => $wrapping->getId(),
                ],
            ],
        ];
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
        /** @var \Magento\Quote\Model\Quote\Item $itemNew */
        $itemNew = $this->objectManager->create('Magento\Quote\Model\Quote\Item')->load($itemId);
        $this->assertEquals($wrapping->getId(), $itemNew->getGwId());
    }
}
