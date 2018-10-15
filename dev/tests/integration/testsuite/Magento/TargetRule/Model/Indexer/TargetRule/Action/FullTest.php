<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Action;

class FullTest extends \Magento\TestFramework\Indexer\TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor
     */
    protected $_processor;

    /**
     * @var \Magento\TargetRule\Model\Rule
     */
    protected $_rule;

    protected function setUp()
    {
        $this->_processor = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor'
        );
        $this->_rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\TargetRule\Model\Rule'
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/controllers/_files/products.php
     * @magentoDataFixture Magento/TargetRule/_files/related.php
     */
    public function testReindexAll()
    {
        $this->_processor->getIndexer()->setScheduled(false);
        $this->assertFalse($this->_processor->getIndexer()->isScheduled());

        $this->_processor->reindexAll();
        $this->_rule->load(1);
        $this->assertEquals(2, count($this->_rule->getMatchingProductIds()));
    }
}
