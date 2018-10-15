<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftWrapping\Test\Block\Cart\Item;

use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\Mtf\Block\Block;

/**
 * Gift options for items block checkout cart frontend page
 */
class GiftOptions extends Block
{
    /**
     * Allow Gift Options for individual items
     *
     * @var string
     */
    protected $allowGiftOptionsForItems = '.action-gift';

    /**
     * Gift Wrapping Design Options
     *
     * @var string
     */
    protected $giftWrappingOptions = '.gift-wrapping-item > span';

    /**
     * Gift Wrapping Name
     *
     * @var string
     */
    protected $giftWrappingName = '.gift-wrapping-name';

    /**
     * Get Gift Wrappings Available on Checkout Cart
     *
     * @return array
     */
    public function getGiftWrappingsAvailable()
    {
        $this->_rootElement->find($this->allowGiftOptionsForItems)->click();
        $giftWrappings = $this->_rootElement->getElements($this->giftWrappingOptions);
        $getGiftWrappingsAvailable = [];
        foreach ($giftWrappings as $giftWrapping) {
            $giftWrapping->click();
            $getGiftWrappingsAvailable[] = $this->_rootElement->find($this->giftWrappingName)->getText();
        }

        return $getGiftWrappingsAvailable;
    }
}
