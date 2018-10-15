<?php
/**
 * Save quote_with_giftcard_saved_rollback fixture
 *
 * The quote is not saved inside the original fixture. It is later saved inside child fixtures, but along with some
 * additional data which may break some tests.
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

global $fixtureBaseDir;

require $fixtureBaseDir . '/Magento/Checkout/_files/quote_with_address_saved_rollback.php';
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$giftCardAccount = $objectManager->create('Magento\GiftCardAccount\Model\Giftcardaccount');
$giftCardAccount->loadByCode('giftcardaccount_fixture')->delete();
