<?php
class ControllerAccountShippingPickup extends Controller {
	private $error = array();

	public function index() {
  $seller_id = $this->customer->getId();
    if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/shipping/pickup', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		} else if ($this->customer->hasSellerPermission($seller_id) == 0) {
			$this->session->data['redirect'] = $this->url->link('account/shipping/pickup', 'token=' . $this->session->data['token'], true);

			$this->response->redirect($this->url->link('account/account', 'token=' . $this->session->data['token'], true));
		}
    
		$this->load->language('account/shipping/pickup');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/sellersetting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_sellersetting->editSellersetting('pickup', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('account/catalog/shipping', 'token=' . $this->session->data['token'], true));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_none'] = $this->language->get('text_none');

		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('account/account', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_shipping'),
			'href' => $this->url->link('account/catalog/shipping', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/shipping/pickup', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('account/shipping/pickup', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('account/catalog/shipping', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['pickup_geo_zone_id'])) {
			$data['pickup_geo_zone_id'] = $this->request->post['pickup_geo_zone_id'];
		} else {
			$data['pickup_geo_zone_id'] = $this->customer->getSellersetting('pickup_geo_zone_id', $seller_id);
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['pickup_status'])) {
			$data['pickup_status'] = $this->request->post['pickup_status'];
		} else {
			$data['pickup_status'] = $this->customer->getSellersetting('pickup_status', $seller_id);
		}

		if (isset($this->request->post['pickup_sort_order'])) {
			$data['pickup_sort_order'] = $this->request->post['pickup_sort_order'];
		} else {
			$data['pickup_sort_order'] = $this->customer->getSellersetting('pickup_sort_order', $seller_id);
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/shipping/pickup', $data));
	}

	protected function validate() {
		$seller_id = $this->customer->getId();
    if ($this->customer->hasSellerPermission($seller_id) == 0) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}