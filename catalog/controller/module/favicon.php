<?php
namespace Opencart\Catalog\Controller\Extension\Favicon\Module;

class Favicon extends \Opencart\System\Engine\Controller {

    public function addFaviconToHeader(string &$route, array &$data, mixed &$output): void {
        $setting = $this->model_setting_setting->getSetting('module_favicon');

        if ($setting['module_favicon_status'] && isset($setting['module_favicon_icon'])) {
            $search = '<script src="catalog/view/javascript/common.js" type="text/javascript"></script>';
            $replace = '<link rel="icon" type="image/x-icon" href="image/' . $setting['module_favicon_icon'] . '">
        <script src="catalog/view/javascript/common.js" type="text/javascript"></script>';
      
            $output = str_replace($search, $replace, $output);
        }
    }
}

