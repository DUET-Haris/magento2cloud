<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_40_percent_off.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\SalesRule\Model\Rule $rule */
$rule = $objectManager->create('Magento\SalesRule\Model\Rule');
$ruleId = $objectManager->get('Magento\Framework\Registry')
    ->registry('Magento/SalesRule/_files/cart_rule_40_percent_off');
$rule->load($ruleId);

/** @var \Magento\Banner\Model\Banner $banner */
$banner = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Banner\Model\Banner');
$banner->setData(
    [
        'name' => 'Get 40% Off on Large Orders',
        'is_enabled' => \Magento\Banner\Model\Banner::STATUS_DISABLED,
        'types' => [], /*Any Banner Type*/
        'store_contents' => ['<img src="http://example.com/banner_40_percent_off.png" />'],
        'banner_sales_rules' => [$rule->getId()],
    ]
);
$banner->save();
