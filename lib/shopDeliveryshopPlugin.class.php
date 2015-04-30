<?php

class shopDeliveryshopPlugin extends shopPlugin
{
  public function backend_menu()
  {
    $model = new waModel();
    $count_new = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city WHERE status='new'")->fetchField();
    $html = '<li ' . (waRequest::get('plugin') == $this->id ? 'class="selected s-deliveryshop-tab"' : 'class="no-tab s-deliveryshop-tab"') . '>
                        <a href="?plugin=deliveryshop&action=display">Магазины
                        <sup class="red" style="display:inline;">' . $count_new . '</sup> </a>
                    </li>';
    return array('core_li' => $html);
  }

  public function displayAll()
  {
    $domain = waRequest::server('HTTP_HOST');
//        $template_path = wa()->getAppPath('plugins/deliveryshop/templates/AllCities.html', 'shop');

    $model = new waModel();
    $delivery_compensation = $model->query("SELECT price FROM shop_deliveryshop_delivery WHERE domain = '" . $domain . "'")->fetchField();
    $delivery_compensation = intval($delivery_compensation);

    $data = $model->query("SELECT * FROM shop_deliveryshop_city WHERE status = 'completed'")->fetchAll('cityCode');

//TODO: Загружать данные по городам одним запросом, сейчас на каждый город делаем несколько запросов в базу
    foreach ($data as $k => $v) {
      $desc = $model->query("SELECT * FROM shop_deliveryshop_city_description WHERE cityCode = '" . $k . "' AND domain = '" . $domain . "'")->fetchAssoc();
      if (!$desc) {
        $desc = array();
      } else {
        $delivery_price = intval($desc['delivery_price']);
        $courier_price = intval($desc['courier_price']);

// Вычитаем компенсацию магазина и из стоимости доставки курьером
        if ($delivery_price > $delivery_compensation) {
          $desc['delivery_price'] = (int)(($delivery_price - $delivery_compensation) / 50) * 50; //Уменьшаем до ближайшего полтинника
        } else {
          $desc['delivery_price'] = 0;
        }

        if ($courier_price > $delivery_compensation) {
          $desc['courier_price'] = (int)(($courier_price - $delivery_compensation) / 50) * 50; //Уменьшаем до ближайшего полтинника
        } else {
          $desc['courier_price'] = 0;
        }

        $delivery_time = '{$delivery_time}';
        $delivery_price = '{$delivery_price}';
        $courier_time = '{$courier_time}';
        $courier_price = '{$courier_price}';

        $desc['description'] = str_replace($delivery_time, $desc['delivery_time'], $desc['description']);
        $desc['description'] = str_replace($delivery_price, $desc['delivery_price'], $desc['description']);
        $desc['description'] = str_replace($courier_time, $desc['courier_time'], $desc['description']);
        $desc['description'] = str_replace($courier_price, $desc['courier_price'], $desc['description']);

        $desc['anons_shop'] = str_replace($delivery_time, $desc['delivery_time'], $desc['anons_shop']);
        $desc['anons_shop'] = str_replace($delivery_price, $desc['delivery_price'], $desc['anons_shop']);
        $desc['anons_shop'] = str_replace($courier_time, $desc['courier_time'], $desc['anons_shop']);
        $desc['anons_shop'] = str_replace($courier_price, $desc['courier_price'], $desc['anons_shop']);

        $desc['anons_delivery'] = str_replace($delivery_time, $desc['delivery_time'], $desc['anons_delivery']);
        $desc['anons_delivery'] = str_replace($delivery_price, $desc['delivery_price'], $desc['anons_delivery']);
        $desc['anons_delivery'] = str_replace($courier_time, $desc['courier_time'], $desc['anons_delivery']);
        $desc['anons_delivery'] = str_replace($courier_price, $desc['courier_price'], $desc['anons_delivery']);
      }
      $data[$k] = array_merge($data[$k], $desc);
      if (is_numeric($data[$k]['region'])) {
        $data[$k]['region'] = $model->query("SELECT name FROM wa_region WHERE code='" . $data[$k]['region'] . "' AND country_iso3='rus'")->fetchField();
      }

      $data[$k]['url'] = $data[$k]['url'] ? $data[$k]['url'] : $data[$k]['city'];
      $data[$k]['url'] = wa()->getRouteUrl('shop/frontend/dostavka', array('url' => $data[$k]['url']));
    }

    return $data;
  }

}
