<?php

namespace Divido\DividoFinancing\Model;

class Payment extends \Magento\Payment\Model\Method\AbstractMethod
{
    const METHOD_CODE = 'divido_financing';

    protected $_code = self::METHOD_CODE;
    protected $_isOffline = true;

    private $dividoHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger,
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param \Divido\DividoFinancing\Helper\Data $dividoHelper
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct (
        \Divido\DividoFinancing\Helper\Data $dividoHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );

        $this->_paymentData = $paymentData;
        $this->_scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->initializeData($data);
        $this->dividoHelper = $dividoHelper;
    }

    public function isAvailable (\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if (! is_null($quote)) {
            $cartThreshhold = $this->_scopeConfig->getValue('payment/divido_financing/cart_threshold',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            if (is_numeric($cartThreshhold)  && $quote->getBaseGrandTotal() < $cartThreshhold) {
                return false;
            }

            $plans = $this->dividoHelper->getQuotePlans($quote);
            if (! $plans) {
                return false;
            }
        }

        return parent::isAvailable($quote);
    }

    public function canUseForCurrency ($currencyCode)
    {
        return $currencyCode === 'GBP';
    }

    public function canUseForCountry ($country) 
    {
        $parentOk = parent::canUseForCountry($country);
        if (! $parentOk) {
            return false;
        }

        return $country === 'GB';
    }

}
