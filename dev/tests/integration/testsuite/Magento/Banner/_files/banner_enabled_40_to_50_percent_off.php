<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_40_percent_off.php';
require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_50_percent_off.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$registry = $objectManager->get('Magento\Framework\Registry');

/** @var \Magento\SalesRule\Model\Rule $ruleFrom */
$ruleFrom = $objectManager->create('Magento\SalesRule\Model\Rule');
$ruleFromId = $registry->registry('Magento/SalesRule/_files/cart_rule_40_percent_off');
$ruleFrom->load($ruleFromId);

/** @var \Magento\SalesRule\Model\Rule $ruleTo */
$ruleTo = $objectManager->create('Magento\SalesRule\Model\Rule');
$ruleToId = $registry->registry('Magento/SalesRule/_files/cart_rule_50_percent_off');
$ruleTo->load($ruleToId);

/** @var \Magento\Banner\Model\Banner $banner */
$banner = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Banner\Model\Banner');
$banner->setData(
    [
        'name' => 'Get from 40% to 50% Off on Large Orders',
        'is_enabled' => \Magento\Banner\Model\Banner::STATUS_ENABLED,
        'types' => [], /*Any Banner Type*/
        'store_contents' => ['<img src="http://example.com/banner_40_to_50_percent_off.png" />'],
        'banner_sales_rules' => [$ruleFrom->getId(), $ruleTo->getId()],
    ]
);
$banner->save();
