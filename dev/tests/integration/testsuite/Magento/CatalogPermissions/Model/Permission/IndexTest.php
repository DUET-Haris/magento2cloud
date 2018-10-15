<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Permission;

/**
 * Class IndexTest
 * @package Magento\CatalogPermissions\Model\Permission
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Model\Permission\Index
     */
    protected $index;

    /**
     * @var \Magento\Framework\Indexer\IndexerInterface
     */
    protected $indexer;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    protected function setUp()
    {
        $this->index = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CatalogPermissions\Model\Permission\Index'
        );
        $this->indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\Indexer\IndexerInterface'
        );
        $this->indexer->load(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID);
        $this->product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     */
    public function testGetIndexForCategory()
    {
        $fixturePermission = [
            'category_id' => 6,
            'website_id' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Store\Model\StoreManagerInterface'
            )->getWebsite()->getId(),
            'customer_group_id' => 1,
            'grant_catalog_category_view' => \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            'grant_catalog_product_price' => \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            'grant_checkout_items' => \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
        ];

        $permissions = $this->index->getIndexForCategory(6, 1, 1);
        $this->assertEquals([], $permissions);

        $this->indexer->reindexRow(6);
        $permissions = $this->index->getIndexForCategory(6, 1, 1);

        $this->assertArrayHasKey(6, $permissions);
        $this->assertCount(1, $permissions);
        foreach ($fixturePermission as $key => $permissionData) {
            $this->assertArrayHasKey($key, $permissions[6]);
            $this->assertEquals($permissionData, $permissions[6][$key]);
        }
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 1
     */
    public function testAddIndexToCategoryCollectionWithDefaultAllow()
    {
        /** @var \Magento\Customer\Model\Session $session */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Customer\Model\Session');

        $session->setCustomerGroupId(0);
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
        $categoryCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\ResourceModel\Category\Collection'
        );
        $categoryCollection->addIsActiveFilter();
        $categoryCollection->load();
        $this->assertCount(11, $categoryCollection->getItems());
        $this->assertInstanceOf('Magento\Catalog\Model\Category', $categoryCollection->getItemById(6));

        $this->indexer->reindexAll();

        $session->setCustomerGroupId(1);
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
        $categoryCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\ResourceModel\Category\Collection'
        );
        $categoryCollection->addIsActiveFilter();
        $categoryCollection->load();
        $this->assertCount(10, $categoryCollection->getItems());
        $this->assertEquals(null, $categoryCollection->getItemById(6));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 0
     */
    public function testAddIndexToCategoryCollectionWithDefaultDeny()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
        $categoryCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\ResourceModel\Category\Collection'
        );
        $categoryCollection->addIsActiveFilter();
        $categoryCollection->load();
        $this->assertCount(0, $categoryCollection->getItems());

        $this->indexer->reindexAll();

        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
        $categoryCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\ResourceModel\Category\Collection'
        );
        $categoryCollection->addIsActiveFilter();
        $categoryCollection->load();
        $this->assertCount(1, $categoryCollection->getItems());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 0
     */
    public function testGetRestrictedCategoryIdsWithDefaultDeny()
    {
        $websiteId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Store\Model\StoreManagerInterface'
        )->getWebsite()->getId();

        $this->assertCount(13, $this->index->getRestrictedCategoryIds(0, $websiteId));
        $this->assertCount(13, $this->index->getRestrictedCategoryIds(1, $websiteId));

        $this->indexer->reindexAll();

        $this->assertCount(12, $this->index->getRestrictedCategoryIds(0, $websiteId));
        $this->assertCount(12, $this->index->getRestrictedCategoryIds(1, $websiteId));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 1
     */
    public function testGetRestrictedCategoryIdsWithDefaultAllow()
    {
        $websiteId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Store\Model\StoreManagerInterface'
        )->getWebsite()->getId();

        $this->assertCount(0, $this->index->getRestrictedCategoryIds(0, $websiteId));
        $this->assertCount(0, $this->index->getRestrictedCategoryIds(1, $websiteId));

        $this->indexer->reindexAll();
        $this->assertCount(0, $this->index->getRestrictedCategoryIds(0, $websiteId));
        $this->assertCount(1, $this->index->getRestrictedCategoryIds(1, $websiteId));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 1
     */
    public function testAddIndexToProductCollectionWithDefaultAllow()
    {
        /** @var \Magento\Customer\Model\Session $session */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Customer\Model\Session');

        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Category'
        );
        $category->load(6);

        $session->setCustomerGroupId(0);
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\ResourceModel\Product\Collection'
        );
        $productCollection->addCategoryFilter($category);
        $productCollection->load();
        $this->assertCount(1, $productCollection->getItems());
        $productId = $this->product->getIdBySku('12345-1');
        $this->assertInstanceOf('Magento\Catalog\Model\Product', $productCollection->getItemById($productId));

        $this->indexer->reindexAll();

        $session->setCustomerGroupId(1);
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $categoryCollection */
        $productCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\ResourceModel\Product\Collection'
        );
        $productCollection->addCategoryFilter($category);
        $productCollection->load();
        $this->assertCount(0, $productCollection->getItems());
        $this->assertEquals(null, $productCollection->getItemById($productId));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 0
     */
    public function testAddIndexToProductCollectionWithDefaultDeny()
    {
        $productId = $this->product->getIdBySku('12345-1');
        /** @var \Magento\Customer\Model\Session $session */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Customer\Model\Session');

        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Category'
        );
        $category->load(6);

        $session->setCustomerGroupId(0);
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $categoryCollection */
        $productCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\ResourceModel\Product\Collection'
        );
        $productCollection->addCategoryFilter($category);
        $productCollection->load();
        $this->assertCount(0, $productCollection->getItems());
        $this->assertEquals(null, $productCollection->getItemById($productId));

        $this->indexer->reindexAll();

        $session->setCustomerGroupId(1);
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $categoryCollection */
        $productCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\ResourceModel\Product\Collection'
        );
        $productCollection->addCategoryFilter($category);
        $productCollection->load();
        $this->assertCount(0, $productCollection->getItems());
        $this->assertEquals(null, $productCollection->getItemById($productId));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 1
     */
    public function testAddIndexToProductWithCategoryAndDefaultAllow()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Category'
        );
        $category->load(6);

        /** @var \Magento\Framework\Registry $registry */
        $registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
        $registry->register('current_category', $category);

        $this->product->load('12345-1', 'sku');

        $this->index->addIndexToProduct($this->product, 0);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $this->product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $this->product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $this->product->getData());

        $this->index->addIndexToProduct($this->product, 1);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $this->product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $this->product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $this->product->getData());

        $this->indexer->reindexAll();

        $this->index->addIndexToProduct($this->product, 0);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $this->product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $this->product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $this->product->getData());

        $this->index->addIndexToProduct($this->product, 1);
        $this->assertArrayHasKey('grant_catalog_category_view', $this->product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $this->product->getData('grant_catalog_category_view')
        );
        $this->assertArrayHasKey('grant_catalog_product_price', $this->product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $this->product->getData('grant_catalog_product_price')
        );
        $this->assertArrayHasKey('grant_checkout_items', $this->product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $this->product->getData('grant_checkout_items')
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 1
     */
    public function testAddIndexToProductStandaloneWithDefaultAllow()
    {
        /** @var $permission \Magento\CatalogPermissions\Model\Permission */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $productRepository = $objectManager->create(
            'Magento\Catalog\Api\ProductRepositoryInterface'
        );

        $this->product = $productRepository->get('12345-1');

        $this->index->addIndexToProduct($this->product, 0);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $this->product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $this->product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $this->product->getData());

        $this->index->addIndexToProduct($this->product, 1);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $this->product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $this->product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $this->product->getData());

        $this->indexer->reindexAll();

        $this->index->addIndexToProduct($this->product, 0);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $this->product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $this->product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $this->product->getData());

        $this->index->addIndexToProduct($this->product, 1);
        $this->assertArrayHasKey('grant_catalog_category_view', $this->product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $this->product->getData('grant_catalog_category_view')
        );
        $this->assertArrayHasKey('grant_catalog_product_price', $this->product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $this->product->getData('grant_catalog_product_price')
        );
        $this->assertArrayHasKey('grant_checkout_items', $this->product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $this->product->getData('grant_checkout_items')
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 0
     */
    public function testAddIndexToProductStandaloneWithDefaultDeny()
    {
        /** @var $permission \Magento\CatalogPermissions\Model\Permission */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $productRepository = $objectManager->create(
            'Magento\Catalog\Api\ProductRepositoryInterface'
        );

        $this->product = $productRepository->get('12345-1');

        $this->index->addIndexToProduct($this->product, 0);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $this->product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $this->product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $this->product->getData());

        $this->index->addIndexToProduct($this->product, 1);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $this->product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $this->product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $this->product->getData());

        $this->indexer->reindexAll();

        $this->index->addIndexToProduct($this->product, 0);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $this->product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $this->product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $this->product->getData());

        $this->index->addIndexToProduct($this->product, 1);
        $this->assertArrayHasKey('grant_catalog_category_view', $this->product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $this->product->getData('grant_catalog_category_view')
        );
        $this->assertArrayHasKey('grant_catalog_product_price', $this->product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $this->product->getData('grant_catalog_product_price')
        );
        $this->assertArrayHasKey('grant_checkout_items', $this->product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $this->product->getData('grant_checkout_items')
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 1
     */
    public function testGetIndexForProductWithDefaultAllow()
    {
        $productId = $this->product->getIdBySku('12345-1');
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Store\Model\StoreManagerInterface'
        )->getStore()->getId();

        $deny = \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY;

        $permissions = $this->index->getIndexForProduct($productId, 0, $storeId);
        $this->assertCount(0, $permissions);
        $permissions = $this->index->getIndexForProduct($productId, 1, $storeId);
        $this->assertCount(0, $permissions);

        $this->indexer->reindexAll();

        $permissions = $this->index->getIndexForProduct($productId, 0, $storeId);
        $this->assertCount(0, $permissions);

        $permissions = $this->index->getIndexForProduct($productId, 1, $storeId);
        $this->assertCount(1, $permissions);
        $this->assertArrayHasKey('grant_catalog_category_view', $permissions[$productId]);
        $this->assertEquals($deny, $permissions[$productId]['grant_catalog_category_view']);
        $this->assertArrayHasKey('grant_catalog_product_price', $permissions[$productId]);
        $this->assertEquals($deny, $permissions[$productId]['grant_catalog_product_price']);
        $this->assertArrayHasKey('grant_checkout_items', $permissions[$productId]);
        $this->assertEquals($deny, $permissions[$productId]['grant_checkout_items']);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 0
     */
    public function testGetIndexForProductWithDefaultDeny()
    {
        $productId = $this->product->getIdBySku('12345-1');
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Store\Model\StoreManagerInterface'
        )->getStore()->getId();

        $deny = \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY;

        $permissions = $this->index->getIndexForProduct($productId, 0, $storeId);
        $this->assertCount(0, $permissions);
        $permissions = $this->index->getIndexForProduct($productId, 1, $storeId);
        $this->assertCount(0, $permissions);

        $this->indexer->reindexAll();

        $permissions = $this->index->getIndexForProduct($productId, 0, $storeId);
        $this->assertCount(0, $permissions);

        $permissions = $this->index->getIndexForProduct($productId, 1, $storeId);
        $this->assertCount(1, $permissions);
        $this->assertArrayHasKey('grant_catalog_category_view', $permissions[$productId]);
        $this->assertEquals($deny, $permissions[$productId]['grant_catalog_category_view']);
        $this->assertArrayHasKey('grant_catalog_product_price', $permissions[$productId]);
        $this->assertEquals($deny, $permissions[$productId]['grant_catalog_product_price']);
        $this->assertArrayHasKey('grant_checkout_items', $permissions[$productId]);
        $this->assertEquals($deny, $permissions[$productId]['grant_checkout_items']);
    }
}
