<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerCustomAttributes\Test\Handler\CustomerCustomAttribute;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class Curl
 * Curl handler for creating custom CustomerAttribute
 */
class Curl extends AbstractCurl implements CustomerCustomAttributeInterface
{
    /**
     * Url for saving data
     *
     * @var string
     */
    protected $saveUrl = 'admin/customer_attribute/save/back/edit/active_tab/general';

    /**
     * Mapping values for data
     *
     * @var array
     */
    protected $mappingData = [
        'frontend_input' => [
            'Text Field' => 'text',
            'Text Area' => 'textarea',
            'Multiple Line' => 'multiline',
            'Date' => 'date',
            'Dropdown' => 'select',
            'Multiple Select' => 'multiselect',
            'Yes/No' => 'boolean',
            'File (attachment)' => 'file',
            'Image File' => 'image',
        ],
    ];

    /**
     * POST request for creating Custom CustomerAttribute
     *
     * @param FixtureInterface $fixture
     * @return array|mixed
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . $this->saveUrl;
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url, $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("CustomerAttribute creating by curl handler was not successful! Response: $response");
        }
        preg_match('`\/attribute_id\/(\d*?)\/`', $response, $match);

        return ['attribute_id' => empty($match[1]) ? null : $match[1]];
    }

    /**
     * Prepare data from text to values
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData($fixture)
    {
        $data = $this->replaceMappingData($fixture->getData());
        $frontendLabels[] = $data['frontend_label'];
        if (isset($data['manage_title'])) {
            $frontendLabels[] = $data['manage_title'];
        }
        $data['frontend_label'] = $frontendLabels;

        return $data;
    }
}
