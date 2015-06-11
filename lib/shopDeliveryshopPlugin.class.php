<?php

class shopDeliveryshopPlugin extends shopPlugin
{
  public function backend_menu()
  {
    $model = new waModel();
    $count_new = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city WHERE status='new'")->fetchField();
    $html_count = $count_new > 0 ? '<sup class="red" style="display:inline;">' . $count_new . '</sup>' : '';
    $html = '<li ' . (waRequest::get('plugin') == $this->id ? 'class="selected s-deliveryshop-tab"' : 'class="no-tab s-deliveryshop-tab"') . '>
                        <a href="?plugin=deliveryshop&action=display">Магазины
                        ' . $html_count . ' </a>
                    </li>';
    return array('core_li' => $html);
  }

  public static function displayAll()
  {
    $domain = waRequest::server('HTTP_HOST');

    $model = new waModel();
    $delivery_compensation = $model->query("SELECT price FROM shop_deliveryshop_delivery WHERE domain = '" . $domain . "'")->fetchField();
    $delivery_compensation = intval($delivery_compensation);

    $data = $model->query("SELECT * FROM shop_deliveryshop_city LEFT JOIN shop_deliveryshop_city_description ON shop_deliveryshop_city_description.cityCode = shop_deliveryshop_city.cityCode WHERE status = 'completed' ORDER BY city ASC")->fetchAll();

    foreach ($data as &$city) {

// Вычитаем компенсацию магазина и из стоимости всех видов доставок
      $array_price = array('delivery_price', 'courier_price');
      foreach ($array_price as $price_name) {
        $price_value = intval($city[$price_name]);
        if ($price_value > $delivery_compensation) {
          $city[$price_name] = (int)(($price_value - $delivery_compensation) / 50) * 50; //Уменьшаем до ближайшего полтинника
        } else {
          $city[$price_name] = 0;
        }
      }

      $array_variables = array('delivery_time', 'delivery_price', 'courier_time', 'courier_price');
      $array_search = array();
      $array_replace = array();
      foreach ($array_variables as $var) {
        if (array_key_exists($var, $city)) {
          $array_search[] = '{$' . $var . '}';
          $array_replace[] = $city[$var];
        }
      }

      $array_fields = array('description', 'anons_shop', 'anons_delivery');
      foreach ($array_fields as $field) {
        if (array_key_exists($field, $city)) {
          $city[$field] = str_replace($array_search, $array_replace, $city[$field]);
        } else {
          $city[$field] = '';
        }
      }
      if (is_numeric($city['region'])) {
        $city['region'] = $model->query("SELECT name FROM wa_region WHERE code='" . $city['region'] . "' AND country_iso3='rus'")->fetchField();
      }

      $city['url'] = wa()->getRouteUrl('shop/frontend/dostavka', array('url' => ($city['url'] ? $city['url'] : $city['city'])));
    }
    return $data;
  }
}
