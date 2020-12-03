<?php

namespace Eleadtech\ProductSlider\Block;
use Magento\Framework\View\Element\Template\Context;

class Slider extends \Magento\Framework\View\Element\Template
{
    protected $helper;
    protected $serializer;

    public function __construct(
        Context $context,
        \Eleadtech\ProductSlider\Helper\Data $helper,
        \Magento\Framework\Serialize\Serializer\Json $serializer

    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->serializer = $serializer;
    }
    public function getProductDetailUrls(){
        $productUrls = [];
        $blockList = false;
        if($this->helper->getFullAction() == "catalog_category_view"){
            $blockList = $this->getLayout()->getBlock("category.products.list");
        }
        if($this->helper->getFullAction() == "catalogsearch_result_index"){
            $blockList = $this->getLayout()->getBlock("search_result_list");
        }
        if($blockList){
            $products = $blockList->getLoadedProductCollection();
            foreach ($products as $product){
                $productUrls[] = $product->getProductUrl();
            }
        }

        return $productUrls;
    }
    public function canShow(){
        return $this->helper->isEnabled();
    }
    public function getSlideFormularList(){
        $screenFormula = $this->helper->getSlideFormularList();
        if(!empty($screenFormula)){
            return $this->serializer->serialize($screenFormula);
        }
        return "";
    }
    public function useTemplateToLoading(){
        return $this->helper->useTemplateToLoading();
    }
}

