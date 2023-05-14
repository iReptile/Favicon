<?php
namespace Opencart\Catalog\Controller\Extension\Favicon\Module;

class Favicon extends \Opencart\System\Engine\Controller {

    public function addFaviconToHeader(string &$route, array &$data, mixed &$output): void {
        $setting = $this->model_setting_setting->getSetting('module_favicon');

        if ($setting['module_favicon_status'] && isset($setting['module_favicon_icon'])) {
            $search = '<script type="text/javascript" src="catalog/view/javascript/jquery/datetimepicker/moment.min.js"></script>';
            $replace = '<link rel="icon" type="image/x-icon" href="image/' . $setting['module_favicon_icon'] . '">
      <script type="text/javascript" src="catalog/view/javascript/jquery/datetimepicker/moment.min.js"></script>';
      
            $output = str_replace($search, $replace, $output);
        }
    }
}

