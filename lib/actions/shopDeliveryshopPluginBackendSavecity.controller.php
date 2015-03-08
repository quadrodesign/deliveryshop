<?php

class shopDeliveryshopPluginBackendSavecityController extends waJsonController {
    
    public function execute()
    {
     try{
            $data = waRequest::post();
            $model = new waModel();
            foreach($data['data'] as $k => $v)
            {                
                $delivery_time = '{$delivery_time}';
                $delivery_price = '{$delivery_price}';
                $courier_time = '{$courier_time}';
                $courier_price = '{$courier_price}';
                
                if($v['description'] == '<p><br></p>' || $v['description'] == '')
                {
                    $v['description'] = '';
                }
                
                if($v['anons_shop'] == '<p><br></p>' || $v['anons_shop'] == '')
                {
                    $v['anons_shop'] = '';
                }
                
                if($v['anons_delivery'] == '<p><br></p>' || $v['anons_delivery'] == '')
                {
                    $v['anons_delivery'] = '';
                }
                
                if($v['id'] != 'new')
                {
                    $id = $model->query("SELECT id FROM shop_deliveryshop_city_description 
                                         WHERE cityCode='".$v['id']."' AND domain='".$k."'")->fetchField();
                
                    $model->query("UPDATE shop_deliveryshop_city SET region = '".$v['region']."' WHERE cityCode = '".$v['id']."'");
                    
                    if($id)
                    {
                        $model->query("UPDATE shop_deliveryshop_city_description SET
                                       ID_TK = '".$v['ID_TK']."',
                                       delivery_time = '".$v['delivery_time']."',
                                       delivery_price = '".$v['delivery_price']."',
                                       courier_time = '".$v['courier_time']."',
                                       courier_price = '".$v['courier_price']."',
                                       description = '".$v['description']."',
                                       url = '".$v['url']."',
                                       anons_shop = '".$v['anons_shop']."',
                                       anons_delivery = '".$v['anons_delivery']."',
                                       meta_title = '".$v['meta_title']."',
                                       meta_keywords = '".$v['meta_keywords']."',
                                       meta_description = '".$v['meta_description']."'
                                       WHERE cityCode = '".$v['id']."' AND domain = '".$k."'");
                    }
                    else
                    {
                        $model->query("INSERT INTO shop_deliveryshop_city_description
                                       (cityCode, ID_TK, delivery_time, delivery_price, 
                                       courier_time, courier_price, description, meta_title,
                                       meta_keywords, meta_description, domain, url, anons_shop, anons_delivery) VALUES
                                       ('".$v['id']."', '".$v['ID_TK']."', '".$v['delivery_time']."',
                                       '".$v['delivery_price']."', '".$v['courier_time']."',
                                       '".$v['courier_price']."', '".$v['description']."', '".$v['meta_title']."',
                                       '".$v['meta_keywords']."', '".$v['meta_description']."', '".$k."',
                                       '".$v['url']."', '".$v['anons_shop']."', '".$v['anons_delivery']."')");                    
                    }
                }
                else
                {
                    if($v['region'])
                    {
                        $model->query("INSERT INTO shop_deliveryshop_city_description
                                       (cityCode, ID_TK, delivery_time, delivery_price, 
                                       courier_time, courier_price, description, meta_title,
                                       meta_keywords, meta_description, domain, url, anons_shop, anons_delivery) VALUES
                                       ('".$v['IDcity']."', '".$v['ID_TK']."', '".$v['delivery_time']."',
                                       '".$v['delivery_price']."', '".$v['courier_time']."',
                                       '".$v['courier_price']."', '".$v['description']."', '".$v['meta_title']."',
                                       '".$v['meta_keywords']."', '".$v['meta_description']."', '".$k."',
                                       '".$v['url']."', '".$v['anons_shop']."', '".$v['anons_delivery']."')"); 
                        
                        $model->query("INSERT INTO shop_deliveryshop_city 
                                       (cityCode, city, region, status) VALUES
                                       ('".$v['IDcity']."', '".$v['city']."', '".$v['region']."', 'new')");
                    }
                    else
                    {
                        $this->response['stat'] = 'error';
                    }
                } 
            }
            $this->response['message'] = 'ok';
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }
    }
}