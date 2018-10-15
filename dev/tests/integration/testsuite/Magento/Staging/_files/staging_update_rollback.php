<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @var $resourceModel Magento\CatalogRule\Model\ResourceModel\Rule
 */
$resourceModel = Bootstrap::getObjectManager()->create('\Magento\Staging\Model\ResourceModel\Update');
$entityTable = $resourceModel->getMainTable();

/**
 * @var $resource Magento\Framework\App\ResourceConnection
 */
$resource = Bootstrap::getObjectManager()->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();

$connection->query("DELETE FROM {$entityTable};");
