<?php

namespace Eleadtech\ProductSlider\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Eleadtech\Bcore\Helper\Data as BcoreData;

class Data extends BcoreData
{
    protected $logFile = "var/log/productslider.log";
    const ENABLED = "enabled";
    const SCREEN_FORMULA = "screen_formula";
    const SHOW_DESCRIPTION = "show_description";
    const USE_TEMPLATE_TO_LOADING = "use_template_to_loading";

    public function writeLog($message)
    {
        parent::writeLog($message);
    }
    public function createGeneralConfigurationPath($name){
        return "productslider/general/".$name;
    }
    public function createDeveloperConfigurationPath($name){
        return "productslider/developer/".$name;
    }
    public function isEnabled(){
        return $this->getConfigValue($this->createGeneralConfigurationPath(self::ENABLED));
    }
    public function getScreenFormula(){
        $format = $this->getConfigValue($this->createGeneralConfigurationPath(self::SCREEN_FORMULA));
        return $this->parseFormulaScreenFormat($format);
    }
    public function showDescription(){
        return $this->getConfigValue($this->createGeneralConfigurationPath(self::SHOW_DESCRIPTION));;
    }
    public function useTemplateToLoading(){
        return $this->getConfigValue($this->createDeveloperConfigurationPath(self::USE_TEMPLATE_TO_LOADING));
    }
    public function validateScreenFormat($format){
        return $this->parseFormulaScreenFormat($format);
    }
    protected function parseFormulaScreenFormat($format){
        if(empty(trim($format))){
            return false;
        }
        $data = [];
        $format = explode("||",$format);
        if(!is_array($format)){
            $format[0] = $format;
        }
        $min = 0;
        foreach ($format as $key => $item){
            $item = explode("-",trim($item));
            if(count($item) == 2){
                if($item[0] < 768){
                    return false;
                }
                foreach ($item as $keyItem=>$element){
                    if(!is_numeric($element)){
                        return false;
                    }
                }
                $data[$key] = $item;
                if($min < $item[0]){
                    $min = $item[0];
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }
        return $data;
    }

    public function getSlideFormularList(){
        $screenFormular = $this->getScreenFormula();
        $data = [];
        foreach ($screenFormular as $key =>$formula){
            $data[$formula[0]] = round(@$formula[1]);
        }
        return $data;
    }
}
