<?php

class shopDeliveryshopPluginFrontendDostavkaAction extends shopFrontendAction
{
  public function execute()
  {
    $url = waRequest::param('url');
    $domain = waRequest::server('HTTP_HOST');
    $model = new waModel();
//    $main_domain = $model->query("SELECT value FROM wa_app_settings WHERE app_id = 'webasyst' AND name = 'url'")->fetchField();

    $app_settings_model = new waAppSettingsModel();
    $main_domain = trim(str_replace(array('https','http','://'),'',$app_settings_model->get('webasyst', 'url')),"/");

    $data = $model->query("
SELECT
  shop_deliveryshop_city_description.*
, shop_deliveryshop_city.city
, shop_deliveryshop_city.region
FROM
  shop_deliveryshop_city_description
LEFT JOIN
  shop_deliveryshop_city ON shop_deliveryshop_city_description.cityCode = shop_deliveryshop_city.cityCode
LEFT JOIN
  wa_region ON wa_region.code = shop_deliveryshop_city.region AND wa_region.country_iso3='rus'
WHERE
  (url = '$url' OR city = '$url')
AND
  domain IN ('$domain', '$main_domain')
LIMIT 1
  ")->fetchAssoc();

    // Уменьшаем стоимость доставки на сумму указанную в настройках плагина
    $delivery_compensation = $model->query("SELECT price FROM shop_deliveryshop_delivery WHERE domain = '" . $domain . "'")->fetchField();
    $delivery_compensation = intval($delivery_compensation);
    $delivery_price = intval($data['delivery_price']);
    $courier_price = intval($data['courier_price']);
    if ($delivery_price > $delivery_compensation) {
      $data['delivery_price'] = (int)(($delivery_price - $delivery_compensation) / 50) * 50; //Уменьшаем до ближайшего полтинника
    } else {
      $data['delivery_price'] = 0;
    }

    if ($courier_price > $delivery_compensation) {
      $data['courier_price'] = (int)(($courier_price - $delivery_compensation) / 50) * 50; //Уменьшаем до ближайшего полтинника
    } else {
      $data['courier_price'] = 0;
    }

    foreach (array(
               'meta_title' => $main_domain,
               'meta_description' => '',
               'meta_keywords' => '',
               'delivery_time' => '',
               'courier_time' => '',
             ) as $key => $value) {
      $data[$key] = isset($data[$key]) ? $data[$key] : $value;
    }

    wa()->getResponse()->setTitle($data['meta_title']);
    wa()->getResponse()->setMeta('description', $data['meta_description']);
    wa()->getResponse()->setMeta('keywords', $data['meta_keywords']);

    $city_code = isset($data['cityCode']) ? $data['cityCode'] : 0 ;
    $pvz = $model->query("
SELECT
  shop_deliveryshop_pvz.*
FROM
  shop_deliveryshop_pvz
WHERE
  cityCode = $city_code
AND
  status = 'completed'
AND
  (domain IN ('$domain', '$main_domain') OR domain IS NULL)
")->fetchAll();

    //$site_model = new siteDomainModel();
    //$domain_id = $site_model->getByName($domain);
    $domain_id = $model->query("SELECT id FROM site_domain WHERE name = '" . $domain . "'")->fetchField();

    $template_path = wa()->getDataPath('plugins/deliveryshop/templates/actions/frontend/FrontendDostavka' . $domain_id . '.html', false, 'shop', true);
    if (!file_exists($template_path)) {
      $template_path = wa()->getDataPath('plugins/deliveryshop/templates/actions/frontend/FrontendDostavka.html', false, 'shop', true);
    }

    if (!file_exists($template_path)) {
      $template_path = wa()->getAppPath('plugins/deliveryshop/templates/actions/frontend/FrontendDostavka.html', 'shop');
    }

    $this->view->assign('data', $data);
    $this->view->assign('pvz', $pvz);

    $this->view->assign('page', array(
      'id' => null,
      'name' => '', //TODO: Сделать отдельное поле для заголовка страницы в настройках города, сейчас выводится в шаблоне описания.
      'content' => $this->view->fetch($template_path)
    ));

    $this->setThemeTemplate('page.html');
    waSystem::popActivePlugin();
  }
}