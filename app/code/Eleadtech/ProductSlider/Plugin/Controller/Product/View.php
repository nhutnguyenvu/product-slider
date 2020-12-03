<?php
namespace Eleadtech\ProductSlider\Plugin\Controller\Product;

use Ess\M2ePro\Model\Exception;

class View
{
    protected $helper;
    protected $jsonHelper;
    public function __construct(
        \Eleadtech\ProductSlider\Helper\Data $helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ){
        $this->helper = $helper;
        $this->jsonHelper = $jsonHelper;
    }
    public function afterExecute(\Magento\Catalog\Controller\Product\View $action,$page){

        $isAjax =  $action->getRequest()->isXmlHttpRequest();
        if ($this->helper->isEnabled() && $action->getRequest()->getParam('isAjax')  && $isAjax ) {
            try{
                if($this->helper->showDescription()){
                    $page->getLayout()->unsetElement("reviews.tab");
                }
                else{
                    $page->getLayout()->unsetElement("product.info.details");
                    $page->getLayout()->unsetElement("reviews.tab");
                }
                $page->getLayout()->unsetElement("content.aside");
                $page->getLayout()->getBlock("product.info.media.image")->setTemplate("Eleadtech_ProductSlider::catalog/product/view/gallery.phtml");
                $content = $page->getLayout()->renderNonCachedElement("content");

                //$content = '<main id="maincontent" class="page-main"> <div class="columns"> <div class="column main">' . $content . "</div></div></div>";
                $content = "<div class='product_info'>" . $content ."</div>";
                if(!empty($content)){
                    $result = ['data' => $content,"error" => false];
                }
                else{
                    $result = ['data' => "","error" => true,"message"=>__("cannot load product content")];
                }
            }
            catch (\Exception $ex){
                $result = ['data' => "","error" => true,"message"=>__("Please check log to see error")];
                $this->helper->writeLog($ex->getMessage());
            }
            $action->getResponse()->representJson($this->jsonHelper->jsonEncode($result));
            return;
        }
        return $page;
    }
}
