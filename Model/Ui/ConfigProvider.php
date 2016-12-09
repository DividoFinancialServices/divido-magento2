<?php
namespace Divido\DividoFinancing\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'divido_financing';

    private $cart,
        $helper;

    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Divido\DividoFinancing\Helper\Data $helper
    ) {
    
        $this->helper = $helper;
        $this->cart  = $cart;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $quote = $this->cart->getQuote();
        $plans = $this->helper->getQuotePlans($quote);
        $plans = $this->helper->plans2list($plans);

        return [
            'payment' => [
                self::CODE => [
                    'cart_plans' => $plans,
                ]
            ]
        ];
    }
}
