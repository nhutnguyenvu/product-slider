<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eleadtech\ProductSlider\Model\Config\Backend;

use Magento\Framework\App\ObjectManager;

/**
 * @api
 * @since 100.0.2
 */
class ScreenFormula extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Framework\View\Asset\MergeService
     */
    protected $_mergeService;

    /**
     * @var UrlValidator
     */
    protected $helper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\View\Asset\MergeService $mergeService
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Eleadtech\ProductSlider\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Validate a base URL field value
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        try {
            $this->validate($value);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $msg = __('%1', $e->getMessage());
            $error = new \Magento\Framework\Exception\LocalizedException($msg, $e);
            throw $error;
        }
    }
    public function validate($value){

        if($this->helper->validateScreenFormat($value)){
            return true;
        }
        $msg = __('Invalid Screen Formula');
        $error = new \Magento\Framework\Exception\LocalizedException($msg);
        throw $error;
    }
}
