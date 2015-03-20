<?php
class shopDeliveryshopPluginFrontendDostavkaAction extends shopFrontendAction
{
  public function execute()
  {
    $url = waRequest::param('url');
    $domain = waRequest::server('HTTP_HOST');
    $model = new waModel();
    $main_domain = $model->query("SELECT value FROM wa_app_settings WHERE app_id = 'webasyst' AND name = 'url'")->fetchField();

    $id = $model->query("SELECT cityCode FROM shop_deliveryshop_city_description WHERE url = '" . $url . "'")->fetchField();
    if (!$id) {
      $id = $model->query("SELECT cityCode FROM shop_deliveryshop_city WHERE city = '" . $url . "'")->fetchField();
    }
    $data_city = $model->query("SELECT city, region FROM shop_deliveryshop_city WHERE cityCode = '" . $id . "'")->fetchAssoc();
    if (is_numeric($data_city['region'])) {
      $data_city['region'] = $model->query("SELECT name FROM wa_region WHERE code='" . $data_city['region'] . "' AND country_iso3='rus'")->fetchField();
    }
    $data = $model->query("SELECT * FROM shop_deliveryshop_city_description WHERE cityCode = '" . $id . "' AND domain = '" . $domain . "'")->fetchAssoc();
    if (!$data) {
      $data = $model->query("SELECT * FROM shop_deliveryshop_city_description WHERE cityCode = '" . $id . "' AND domain = '" . $main_domain . "'")->fetchAssoc();
    }
    $data = $data ? $data : array();
    $info = array_merge($data, $data_city);
    wa()->getResponse()->setTitle($info['meta_title']);
    wa()->getResponse()->setMeta('description', $info['meta_description']);
    wa()->getResponse()->setMeta('keywords', $info['meta_keywords']);

    $pvz = $model->query("SELECT * FROM shop_deliveryshop_pvz WHERE cityCode = '" . $id . "' AND domain = '" . $domain . "' AND status = 'completed'")->fetchAll();
    if (!$pvz) {
      $pvz = $model->query("SELECT * FROM shop_deliveryshop_pvz WHERE cityCode = '" . $id . "' AND domain = '" . $main_domain . "' AND status = 'completed'")->fetchAll();
      if (!$pvz) {
        $pvz = $model->query("SELECT * FROM shop_deliveryshop_pvz WHERE cityCode = '" . $id . "' AND status = 'completed'")->fetchAll();
      }
    }

    $idDomain = $model->query("SELECT id FROM site_domain WHERE name = '" . $domain . "'")->fetchField();

    $template_path = wa()->getDataPath('plugins/deliveryshop/templates/actions/frontend/FrontendDostavka' . $idDomain . '.html', false, 'shop', true);
    if (!file_exists($template_path)) {
      $template_path = wa()->getDataPath('plugins/deliveryshop/templates/actions/frontend/FrontendDostavka.html', false, 'shop', true);
    }

    if (!file_exists($template_path)) {
      $template_path = wa()->getAppPath('plugins/deliveryshop/templates/actions/frontend/FrontendDostavka.html', 'shop');
    }

    $this->view->assign('data', $info);
    $this->view->assign('pvz', $pvz);

    $this->view->assign('page', array(
      'content' => $this->view->fetch($template_path)
    ));

    $this->setThemeTemplate('page.html');
    waSystem::popActivePlugin();
  }
}