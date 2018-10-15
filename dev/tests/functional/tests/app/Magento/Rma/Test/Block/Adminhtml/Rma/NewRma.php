<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma;

use Magento\Rma\Test\Fixture\Rma;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Rma\Test\Fixture\Rma\OrderId;
use Magento\Mtf\Client\Element\SimpleElement;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Rma new page tabs.
 */
class NewRma extends FormTabs
{
    /**
     * Fill form with tabs.
     *
     * @param FixtureInterface $fixture
     * @param SimpleElement|null $element
     * @return FormTabs
     */
    public function fill(FixtureInterface $fixture, SimpleElement $element = null)
    {
        $tabs = $this->getFixtureFieldsByContainers($fixture);
        if (isset($tabs['items']['items']['value'])) {
            $orderItems = $this->getOrderItems($fixture);
            $tabs['items']['items']['value'] = $this->prepareItems($orderItems, $tabs['items']['items']['value']);
        }

        return $this->fillTabs($tabs, $element);
    }

    /**
     * Get order items from rma fixture.
     *
     * @param InjectableFixture $fixture
     * @return array
     */
    protected function getOrderItems(InjectableFixture $fixture)
    {
        /** @var OrderId $sourceOrderId */
        $sourceOrderId = $fixture->getDataFieldConfig('order_id')['source'];
        return $sourceOrderId->getOrder()->getEntityId()['products'];
    }

    /**
     * Prepare items data.
     *
     * @param array $orderItems
     * @param array $items
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function prepareItems(array $orderItems, array $items)
    {
        foreach ($items as $productKey => $productData) {
            $key = str_replace('product_key_', '', $productKey);
            $items[$productKey]['product'] = $orderItems[$key];
        }
        return $items;
    }
}
