<?php
namespace Opencart\Admin\Controller\Extension\Favicon\Module;

class Favicon extends \Opencart\System\Engine\Controller {

	private string $extension_name = 'favicon';
	private string $module_name = 'favicon';

    public function index(): void {
		$this->load->language('extension/favicon/module/favicon');

		$this->document->setTitle($this->language->get('heading_title'));

		$separator = (substr(VERSION, 0, 7) < '4.0.2.0') ? '|' : '.';

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/favicon/module/favicon', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/favicon/module/favicon'. $separator . 'save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		$data['save_icon'] = VERSION == '4.0.0.0' ? 'fas fa-save' : 'fa-solid fa-save';
		$data['back_icon'] = VERSION == '4.0.0.0' ? 'fas fa-reply' : 'fa-solid fa-reply';

		$setting = $this->model_setting_setting->getSetting('module_' . $this->module_name);
		$config_data = ['module_favicon_status', 'module_favicon_icon'];

		foreach ($config_data as $conf) {
			if (isset($this->request->post[$conf])) {
				$data[$conf] = $this->request->post[$conf];
			} elseif (isset($setting[$conf])) {
				$data[$conf] = $setting[$conf];
			} else {
				$data[$conf] = '';
			} 
		}

		$this->load->model('tool/image');

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (!empty($data['module_favicon_icon']) && is_file(DIR_IMAGE . html_entity_decode($setting['module_favicon_icon'], ENT_QUOTES, 'UTF-8'))) {
			$data['alt_icon'] = $this->model_tool_image->resize(html_entity_decode($setting['module_favicon_icon'], ENT_QUOTES, 'UTF-8'), 100, 100);
		} else {
			$data['alt_icon'] = $data['placeholder'];
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/favicon/module/favicon', $data));
	}

	public function save(): void {
		$this->load->language('extension/favicon/module/favicon');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/favicon/module/favicon')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('module_favicon', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

    public function install() {
		$this->load->model('setting/event');

		$separator = (substr(VERSION, 0, 7) < '4.0.2.0') ? '|' : '.';
		$events = [];
				 
		$events[] = [$this->extension_name, 'catalog/view/common/header/after', 'extension/' . $this->extension_name . '/module/' . $this->module_name . $separator . 'addFaviconToHeader', 'Add favicon uploader', 1, true];

		if (VERSION == '4.0.0.0') {
			foreach($events as $key => $value) {
				$this->model_setting_event->addEvent($value[0], $value[3], $value[1], $value[2], $value[5], $value[4]);
			}
		} else {
			foreach($events as $key => $value) {
				$this->model_setting_event->addEvent([
					'code' => $value[0], 
					'trigger' => $value[1],
					'action' => $value[2],
					'description' => $value[3],
					'sort_order' => $value[4],
					'status' => $value[5]
				]);
			}
		}
	}

	public function uninstall() {
		$this->load->model('setting/event');

		$this->model_setting_event->deleteEventByCode($this->extension_name);
	}
}