<?php

class shopDeliveryshopPlugin extends shopPlugin
{
    public function backend_menu() 
    {
        $model = new waModel();
        $count_new = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city WHERE status='new'")->fetchField();
            $html = '<li ' . (waRequest::get('plugin') == $this->id ? 'class="selected s-deliveryshop-tab"' : 'class="no-tab s-deliveryshop-tab"') . '>
                        <a href="?plugin=deliveryshop&action=display">Магазины
                        <sup class="red" style="display:inline;">'.$count_new.'</sup> </a>
                    </li>';
            return array('core_li' => $html);
    } 
    
    public function displayAll()
    {
        $domain = waRequest::server('HTTP_HOST');
        $template_path = wa()->getAppPath('plugins/deliveryshop/templates/AllCities.html', 'shop');
        
        $model = new waModel();
        $data = $model->query("SELECT * FROM shop_deliveryshop_city WHERE status = 'completed'")->fetchAll('cityCode');
        
        foreach($data as $k => $v)
        {
            $desc = $model->query("SELECT * FROM shop_deliveryshop_city_description WHERE cityCode = '".$k."' AND domain = '".$domain."'")->fetchAssoc();
            if(!$desc)
            {
                $desc = array();
            }
            else
            {
                $delivery_time = '{$delivery_time}';
                $delivery_price = '{$delivery_price}';
                $courier_time = '{$courier_time}';
                $courier_price = '{$courier_price}';
                
                $staticDeliveryPrice = $model->query("SELECT price FROM shop_deliveryshop_delivery WHERE domain = '".$domain."'")->fetchField();
                $staticDeliveryPrice = $staticDeliveryPrice ? $staticDeliveryPrice : 0 ;
                $desc['delivery_price'] = (int)$desc['delivery_price'] - (int)$staticDeliveryPrice;
                if($desc['delivery_price'] <= 0)
                {
                    $desc['delivery_price'] = 'Бесплатная доставка';
                }
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
            if(is_numeric($data[$k]['region']))
            {
                $data[$k]['region'] = $model->query("SELECT name FROM wa_region WHERE code='".$data[$k]['region']."' AND country_iso3='rus'")->fetchField();
            }
            
            $data[$k]['url'] = $data[$k]['url'] ? $data[$k]['url'] : $data[$k]['city'];
            $data[$k]['url'] = wa()->getRouteUrl('shop/frontend/dostavka', array('url'=> $data[$k]['url']));
        }

        return $data;
    }

}
