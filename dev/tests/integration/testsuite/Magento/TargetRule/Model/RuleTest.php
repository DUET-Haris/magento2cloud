<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Model;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Rule
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\TargetRule\Model\Rule'
        );
    }

    public function testValidateDataOnEmpty()
    {
        $data = new \Magento\Framework\DataObject();
        $this->assertTrue($this->_model->validateData($data), 'True for empty object');
    }

    public function testValidateDataOnValid()
    {
        $data = new \Magento\Framework\DataObject();
        $data->setRule(
            ['actions' => ['test' => ['type' => 'Magento\TargetRule\Model\Actions\Condition\Combine']]]
        );

        $this->assertTrue($this->_model->validateData($data), 'True for right data');
    }

    /**
     * @dataProvider invalidCodesDataProvider
     * @param string $code
     */
    public function testValidateDataOnInvalidCode($code)
    {
        $data = new \Magento\Framework\DataObject();
        $data->setRule(
            [
                'actions' => [
                    'test' => [
                        'type' => 'Magento\TargetRule\Model\Actions\Condition\Combine',
                        'attribute' => $code,
                    ],
                ],
            ]
        );
        $this->assertCount(1, $this->_model->validateData($data), 'Error for invalid attribute code');
    }

    /**
     * @return array
     */
    public static function invalidCodesDataProvider()
    {
        return [[''], ['_'], ['123'], ['!'], [str_repeat('2', 256)]];
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testValidateDataOnInvalidType()
    {
        $data = new \Magento\Framework\DataObject();
        $data->setRule(['actions' => ['test' => ['type' => 'Magento\TargetRule\Invalid']]]);
        $this->_model->validateData($data);
    }
}
