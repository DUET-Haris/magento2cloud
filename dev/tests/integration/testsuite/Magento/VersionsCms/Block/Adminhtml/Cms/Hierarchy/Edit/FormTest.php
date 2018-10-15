<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit;

/**
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\View\LayoutInterface */
    protected $_layout = null;

    /** @var \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        $this->_block = $this->_layout->createBlock('Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form');
    }

    public function testGetGridJsObject()
    {
        $parentName = 'parent';
        $mockClass = $this->getMockClass(
            'Magento\Catalog\Block\Product\AbstractProduct',
            ['_prepareLayout'],
            [
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
                    'Magento\Framework\View\Element\Template\Context'
                )
            ]
        );
        $this->_layout->createBlock($mockClass, $parentName);
        $this->_layout->setChild($parentName, $this->_block->getNameInLayout(), '');

        $pageGrid = $this->_layout->addBlock(
            'Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form\Grid',
            'cms_page_grid',
            $parentName
        );
        $this->assertEquals($pageGrid->getJsObjectName(), $this->_block->getGridJsObject());
    }

    /**
     * @param int $isMetadataEnabled
     * @param bool $result
     *
     * @dataProvider prepareFormDataProvider
     */
    public function testPrepareForm($isMetadataEnabled, $result)
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $cmsHierarchyMock = $this->getMockBuilder('\Magento\VersionsCms\Helper\Hierarchy')
            ->setMethods(['isMetadataEnabled'])
            ->disableOriginalConstructor()
            ->getMock();
        $cmsHierarchyMock->expects($this->any())
            ->method('isMetadataEnabled')
            ->will($this->returnValue($isMetadataEnabled));
        $block = $objectManager->create('Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form',
            ['cmsHierarchy' =>$cmsHierarchyMock]
        );
        $prepareFormMethod = new \ReflectionMethod(
            'Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form',
            '_prepareForm'
        );
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);
        $form = $block->getForm();
        $this->assertEquals($result, is_null($form->getElement('top_menu_fieldset')));
    }

    /**
     * Data provider for testPrepareForm
     *
     * @return array
     */
    public function prepareFormDataProvider()
    {
        return [
            [1, false],
            [0, false]
        ];
    }
}
