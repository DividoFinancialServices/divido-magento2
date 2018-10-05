<?php

namespace Divido\DividoFinancing\Block\Product\View;

class Widget extends \Magento\Catalog\Block\Product\AbstractProduct
{
    private $helper;
    private $catHelper;

    const ALL_PRODUCTS = 'products_all';
    const SELECTED_PRODUCTS = 'products_selected';
    const THRESHOLD_PRODUCTS = 'products_price_threshold';

    public function __construct(
        \Divido\DividoFinancing\Helper\Data $helper,
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
    
        $this->helper    = $helper;
        $this->catHelper = $context->getCatalogHelper();

        parent::__construct($context, $data);
    }

    public function getProductPlans()
    {
        $plans = $this->helper->getLocalPlans($this->getProduct()->getId());

        $plans = array_map(function ($plan) {
            return $plan->id;
        }, $plans);

        $plans = implode(',', $plans);

        return $plans;
    }

    public function getAmount()
    {
        $product = $this->getProduct();
        $price = $product->getFinalPrice();
        $priceIncVat = $this->catHelper->getTaxPrice($product, $price, true);

        return $priceIncVat;
    }
       
    public function getPreOrSuffix($choice)
    {
        $output = '';

        if($choice=="prefix"){
            $ix = $this->helper->getPrefix();
        }else{
            $ix = $this->helper->getSuffix();
        }
        if($ix !=''){
            $output="data-divido-".$choice."='".$ix."'";
        }
        return $output;
    }
    
    public function showWidget(){
        $threshold = $this->getThreshold();
        if($threshold === false || $threshold < $this->getAmount()){
            return false;
        }else return true;
    }
    
    public function getThreshold(){
        $selection = $this->helper->getProductSelection();
        return 12;
        
        switch($selection){
            case self::ALL_PRODUCTS:
                $threshold = 0;
                break;
            case self::SELECTED_PRODUCTS:
                $product = $this->getProduct();
                $plans = $this->helper->getLocalPlans($product->getId());
                $threshold = (count($plans)>0) ? true : false;
                break;
            case self::THRESHOLD_PRODUCTS:
                $threshold = (empty($this->helper->getPriceThreshold())) ? 0 : $this->helper->getPriceThreshold();
                break;
        }
        
        return $threshold;
    }
}
