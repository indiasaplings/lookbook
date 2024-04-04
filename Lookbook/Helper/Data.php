<?php
/**
 * @Author: nguyen
 * @Date:   2020-02-12 14:01:01
 * @Last Modified by:   nguyen
 * @Last Modified time: 2021-05-11 16:40:01
 */

namespace Magiccart\Lookbook\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $configModule;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->moduleManager = $moduleManager;
        $module = strtolower(str_replace('Magiccart_', '', $this->_getModuleName()));
        $this->configModule = $this->getConfig($module);
        $this->_storeManager = $storeManager;

    }

    public function getConfig($cfg='')
    {
        if($cfg) return $this->scopeConfig->getValue( $cfg, \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        return $this->scopeConfig;
    }

    public function getConfigModule($cfg='', $value=null)
    {
        $values = $this->configModule;
        if( !$cfg ) return $values;
        $config  = explode('/', $cfg);
        $end     = count($config) - 1;
        foreach ($config as $key => $vl) {
            if( isset($values[$vl]) ){
                if( $key == $end ) {
                    $value = $values[$vl];
                }else {
                    $values = $values[$vl];
                }
            } 

        }
        return $value;
    }

    public function isEnabledModule($moduleName)
    {
        return $this->moduleManager->isEnabled($moduleName);
    }

    public function getConfgGridSlider()
    {   
        $data = $this->getConfigModule('general');
        $breakpoints = $this->getResponsiveBreakpoints();
        $total = count($breakpoints);
        $data['slide'] = false;
        if($data['slide']){
           
            $data['vertical-Swiping'] = $data['vertical'];
            $responsive = '[';
            foreach ($breakpoints as $size => $opt) {
                $responsive .= '{"breakpoint": '.$size.', "settings": {"slidesToShow": '.$data[$opt].'}}';
                $total--;
                if($total) $responsive .= ', ';
            }
            $responsive .= ']';
            $data['slides-To-Show'] = $data['visible'];
            // $data['swipe-To-Slide'] = 'true';
            $data['responsive'] = $responsive;
            $Rm = array('slide', 'visible', 'widescreen', 'desktop', 'laptop', 'notebook', 'tablet', 'landscape', 'portrait', 'mobile'); // require with slick
            foreach ($Rm as $vl) { unset($data[$vl]); }

            return $data;

        } else {
            $responsive = '[';
            foreach ($breakpoints as $size => $opt) {
                $responsive .= '{"breakpoint": '.$size.', "settings": {"grid": '. $data[$opt] . '}}';
                $total--;
                if($total) $responsive .= ', ';
            }
            $responsive .= ']';
            $data['responsive']  = $responsive;
            $data['grid']        = $data['visible'];
            $data['tab-Padding'] = $data['padding'];
            $data['border-Width'] = $data['borderWidth'];
            $data['tab-Border-Color'] = $data['tabBorderColor'];
            $data['content-Border-Color'] = $data['contentBorderColor'];

            $speed = $data['keep'] ? 0 : $data['keep'];
            if ($data['layout'] == 'tab') {
                $dataConfig = '{ "layout": "'.$data['layout'].'", "keepOpen": '.$data['keep'].', "showClose": '.$data['button'] .' , "speed": '.$speed.' , "scrollToTab": '.$data['scroll'].'}';
                $data['config'] = $dataConfig;
            }else{
                $data['selectors'] = '{"tab": ">dd"}';
            }
            
           
            $Rm = array('visible', 'widescreen', 'desktop', 'laptop', 'notebook', 'tablet', 'landscape', 'portrait', 'mobile', 'padding', 'slide', 'enabled', 'keep', 'button', 'speed', 'scroll', 'borderWidth', 'tabBorderColor' ,'contentBorderColor', 'selectors', 'menutext_link', 'topmenulink', 'description', 'url_suffix', 'title', 'router', 'hoverImage'); // require with gridtab
            foreach ($Rm as $vl) { unset($data[$vl]); }

            return $data;
        }
    }

    public function getResponsiveBreakpoints()
    {
        return array(1921=>'visible', 1920=>'widescreen', 1480=>'desktop', 1200=>'laptop', 992=>'notebook', 768=>'tablet', 576=>'landscape', 481=>'portrait', 361=>'mobile', 1=>'mobile');
    }


     public function getUrlRouter()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . $this->getRouter();
    }

    public function getRouter()
    {
        $router = $this->getConfigModule('general/router');
        return $router ? $router : 'lookbook';
    }

    public function getUrlKey($key='', $suffix=true)
    {
        $key = trim($key, '/');
        if($key) $key =  '/' . $key;
        if($suffix) $key  = $key . $this->getUrlSuffix();
        return $this->getRouter() . $key;
    }

    public function getLookbookUrl($key='', $suffix=true)
    {
        return $this->_storeManager->getStore()->getBaseUrl() . $this->getUrlKey($key, $suffix);
    }

    public function getUrlSuffix()
    {
        return $this->getConfigModule('general/url_suffix');

    }

    public function getModuleName()
    {
        return $this->_getModuleName();
    }
}