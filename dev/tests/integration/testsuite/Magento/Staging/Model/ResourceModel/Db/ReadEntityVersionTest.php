<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model\ResourceModel\Db;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Staging\Model\VersionManager;

class ReadEntityVersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadEntityVersion
     */
    private $model;

    protected function setUp()
    {
        $this->model = Bootstrap::getObjectManager()
            ->create('Magento\Staging\Model\ResourceModel\Db\ReadEntityVersion');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Staging/_files/staging_entity.php
     */
    public function testGetNextVersionId()
    {
        $this->assertEquals(
            400,
            $this->model->getNextVersionId('Magento\CatalogRule\Api\Data\RuleInterface', 300)
        );
        $this->assertEquals(
            VersionManager::MAX_VERSION,
            $this->model->getNextVersionId('Magento\CatalogRule\Api\Data\RuleInterface', 600)
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Staging/_files/staging_entity.php
     * @magentoDataFixture Magento/Staging/_files/staging_update.php
     */
    public function testGetNextPermanentVersionId()
    {
        $this->assertEquals(
            400,
            $this->model->getNextPermanentVersionId('Magento\CatalogRule\Api\Data\RuleInterface', 100, 1)
        );
        $this->assertEquals(
            400,
            $this->model->getNextPermanentVersionId('Magento\CatalogRule\Api\Data\RuleInterface', 200, 1)
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Staging/_files/staging_entity.php
     */
    public function testGetPreviousVersionId()
    {
        $this->assertEquals(
            200,
            $this->model->getPreviousVersionId('Magento\CatalogRule\Api\Data\RuleInterface', 300)
        );
        $this->assertEquals(
            1,
            $this->model->getPreviousVersionId('Magento\CatalogRule\Api\Data\RuleInterface', 100)
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Staging/_files/staging_entity.php
     * @magentoDataFixture Magento/Staging/_files/staging_update.php
     */
    public function testGetRollbackVersionIds()
    {
        $this->assertEquals(
            [300],
            $this->model->getRollbackVersionIds('Magento\CatalogRule\Api\Data\RuleInterface', 1, 400, 1)
        );
        $this->assertEquals(
            [300, 600],
            $this->model->getRollbackVersionIds('Magento\CatalogRule\Api\Data\RuleInterface', 1, 700, 1)
        );
        $this->assertEquals(
            [],
            $this->model->getRollbackVersionIds('Magento\CatalogRule\Api\Data\RuleInterface', 1, 200, 1)
        );
    }
}
