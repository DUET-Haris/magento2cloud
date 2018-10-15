<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SalesArchive\Test\TestCase;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderCreditMemoNew;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. Enable "Orders Archiving" for all statuses in configuration
 * 2. Enable payment method "Check/Money Order"
 * 3. Enable shipping method Flat Rate
 * 4. Place order with product qty = 2
 * 5. Invoice order with 2 products
 * 6. Ship full order
 * 7. Move order to Archive
 *
 * Steps:
 * 1. Go to Admin > Sales > Archive > Orders
 * 2. Select order and create Credit Memo
 * 3. Fill data from dataset
 * 4. Click 'Submit' button
 * 5. Perform all assertions
 *
 * @group Sales_Archive_(CS)
 * @ZephyrId MAGETWO-29100
 */
class CreditMemoSalesArchiveEntityTest extends Injectable
{
    /* tags */
    const MVP = 'no';
    const DOMAIN = 'CS';
    /* end tags */

    /**
     * Orders page.
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * Archive orders page.
     *
     * @var ArchiveOrders
     */
    protected $archiveOrders;

    /**
     * Order view page.
     *
     * @var SalesOrderView
     */
    protected $salesOrderView;

    /**
     * Order new credit memo page.
     *
     * @var OrderCreditMemoNew
     */
    protected $orderCreditMemoNew;

    /**
     * Enable "Check/Money Order", "Flat Rate" and archiving for all statuses in configuration.
     *
     * @return void
     */
    public function __prepare()
    {
        $setupConfig = $this->objectManager->create(
            'Magento\Config\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'salesarchive_all_statuses, checkmo, flatrate']
        );
        $setupConfig->run();
    }

    /**
     * Injection data.
     *
     * @param OrderIndex $orderIndex
     * @param ArchiveOrders $archiveOrders
     * @param SalesOrderView $salesOrderView
     * @param OrderCreditMemoNew $orderCreditMemoNew
     * @return void
     */
    public function __inject(
        OrderIndex $orderIndex,
        ArchiveOrders $archiveOrders,
        SalesOrderView $salesOrderView,
        OrderCreditMemoNew $orderCreditMemoNew
    ) {
        $this->orderIndex = $orderIndex;
        $this->archiveOrders = $archiveOrders;
        $this->salesOrderView = $salesOrderView;
        $this->orderCreditMemoNew = $orderCreditMemoNew;
    }

    /**
     * Create Credit Memo SalesArchive Entity.
     *
     * @param OrderInjectable $order
     * @param array $data
     * @return array
     */
    public function test(OrderInjectable $order, array $data)
    {
        // Preconditions
        $order->persist();
        $this->objectManager->create('Magento\Sales\Test\TestStep\CreateInvoiceStep', ['order' => $order])->run();
        $this->objectManager->create('Magento\Sales\Test\TestStep\CreateShipmentStep', ['order' => $order])->run();
        $this->orderIndex->open();
        $orderId = $order->getId();
        $this->orderIndex->getSalesOrderGrid()->massaction([['id' => $orderId]], 'Move to Archive');

        // Steps
        $this->archiveOrders->open();
        $this->archiveOrders->getSalesArchiveOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        $this->salesOrderView->getPageActions()->orderCreditMemo();
        $this->orderCreditMemoNew->getFormBlock()->fillProductData($data, $order->getEntityId()['products']);
        $this->orderCreditMemoNew->getFormBlock()->updateQty();
        $this->orderCreditMemoNew->getFormBlock()->fillFormData($data);
        $this->orderCreditMemoNew->getFormBlock()->submit();

        $this->salesOrderView->getOrderForm()->openTab('creditmemos');
        $creditMemoIds = $this->salesOrderView->getOrderForm()->getTab('creditmemos')->getGridBlock()->getIds();

        return [
            'ids' => [
                'creditMemoIds' => $creditMemoIds,
            ],
            'orderId' => $orderId
        ];
    }
}
