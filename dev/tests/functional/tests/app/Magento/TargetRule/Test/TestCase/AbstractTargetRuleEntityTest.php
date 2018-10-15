<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\TargetRule\Test\Fixture\TargetRule;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleEdit;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleNew;
use Magento\Mtf\TestCase\Injectable;

/**
 * Parent class for TargetRule tests.
 */
abstract class AbstractTargetRuleEntityTest extends Injectable
{
    /**
     * Target rule grid page.
     *
     * @var TargetRuleIndex
     */
    protected $targetRuleIndex;

    /**
     * Target rule create page.
     *
     * @var TargetRuleNew
     */
    protected $targetRuleNew;

    /**
     * Target rule edit page.
     *
     * @var TargetRuleEdit
     */
    protected $targetRuleEdit;

    /**
     * Target rule entity name.
     *
     * @var string
     */
    protected $targetRuleName;

    /**
     * Injection data.
     *
     * @param TargetRuleIndex $targetRuleIndex
     * @param TargetRuleNew $targetRuleNew
     * @param TargetRuleEdit $targetRuleEdit
     * @return void
     */
    public function __inject(
        TargetRuleIndex $targetRuleIndex,
        TargetRuleNew $targetRuleNew,
        TargetRuleEdit $targetRuleEdit
    ) {
        $this->targetRuleIndex = $targetRuleIndex;
        $this->targetRuleNew = $targetRuleNew;
        $this->targetRuleEdit = $targetRuleEdit;
    }

    /**
     * Prepare data for tear down.
     *
     * @param TargetRule $targetRule
     * @param TargetRule $targetRuleInitial
     * @return void
     */
    public function prepareTearDown(TargetRule $targetRule, TargetRule $targetRuleInitial = null)
    {
        $this->targetRuleName = $targetRule->hasData('name') ? $targetRule->getName() : $targetRuleInitial->getName();
    }

    /**
     * Clear data after test.
     *
     * @return void
     */
    public function tearDown()
    {
        if (empty($this->targetRuleName)) {
            return;
        }
        $filter = ['name' => $this->targetRuleName];
        $this->targetRuleIndex->open();
        $this->targetRuleIndex->getTargetRuleGrid()->searchAndOpen($filter);
        $this->targetRuleEdit->getPageActions()->delete();
        $this->targetRuleEdit->getModalBlock()->acceptAlert();
        $this->targetRuleName = '';
    }

    /**
     * Get data for replace in variations.
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductSimple $promotedProduct
     * @param CustomerSegment|null $customerSegment
     * @return array
     */
    protected function getReplaceData(
        CatalogProductSimple $product,
        CatalogProductSimple $promotedProduct,
        CustomerSegment $customerSegment = null
    ) {
        $customerSegmentName = ($customerSegment && $customerSegment->hasData()) ? $customerSegment->getName() : '';
        $sourceCategory = $product->getDataFieldConfig('category_ids')['source'];
        $sourceRelatedCategory = $promotedProduct->getDataFieldConfig('category_ids')['source'];

        return [
            'rule_information' => [
                'customer_segment_ids' => [
                    '%customer_segment%' => $customerSegmentName,
                ],
            ],
            'products_to_match' => [
                'conditions_serialized' => [
                    '%category_1%' => $sourceCategory->getIds()[0],
                ],
            ],
            'products_to_display' => [
                'actions_serialized' => [
                    '%category_2%' => $sourceRelatedCategory->getIds()[0],
                ],
            ],
        ];
    }
}
