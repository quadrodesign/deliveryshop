<?php

class shopDeliveryshopPluginBackendSavepvzController extends waJsonController {
    
    public function execute()
    {
     try{
            $data = waRequest::post();
            $model = new waModel();
            
            foreach($data['data'] as $k => $v)
            {
                if($v['id'] != 'new')
                {
                    $is = $model->query("SELECT * FROM shop_deliveryshop_pvz WHERE code = '".$v['id']."' AND domain = '".$k."'")->fetchAssoc();
                    if(!$is)
                    {
                        $d = $model->query("SELECT * FROM shop_deliveryshop_pvz WHERE code = '".$v['id']."'")->fetchAssoc();
                        if($d && $d['domain'] == '')
                        {
                            $model->query("UPDATE shop_deliveryshop_pvz SET 
                                           address = '".$v['address']."',
                                           workTime = '".$v['workTime']."',
                                           email = '".$v['email']."',
                                           TK = '".$v['TK']."',
                                           note = '".$v['note']."',
                                           phone = '".$v['phone']."',
                                           domain = '".$k."'
                                           WHERE id = '".$d['id']."'");
                        }
                        else
                        {
                            $model->query("INSERT INTO shop_deliveryshop_pvz 
                                           (code, name, city, cityCode, workTime, address, phone, note, coordX, coordY, 
                                           weightMin, weightMax, TK, domain, status, email)
                                           VALUES ('".$v['id']."', '".$d['name']."', '".$d['city']."',
                                           '".$d['cityCode']."', '".$v['workTime']."',
                                           '".$v['address']."', '".$v['phone']."', '".$v['note']."',
                                           '".$d['coordX']."', '".$d['coordY']."', '".$d['weightMin']."',
                                           '".$d['weightMax']."', '".$v['TK']."', '".$k."', '".$d['status']."', '".$v['email']."')");
                        }
                    }
                    else
                    {
                        $model->query("UPDATE shop_deliveryshop_pvz SET 
                                       address = '".$v['address']."',
                                       workTime = '".$v['workTime']."',
                                       email = '".$v['email']."',
                                       TK = '".$v['TK']."',
                                       note = '".$v['note']."',
                                       phone = '".$v['phone']."'
                                       WHERE id = '".$is['id']."' AND domain = '".$k."'");
                    }
                }
                else
                {
                    if($v['city'])
                    {
                        $city = $model->query("SELECT city FROM shop_deliveryshop_city WHERE cityCode = '".$v['city']."'")->fetchField();
                        $model->query("INSERT INTO shop_deliveryshop_pvz 
                                       (code, name, city, cityCode, workTime, address, phone, note, coordX, coordY, 
                                       weightMin, weightMax, TK, domain, status, email)
                                       VALUES ('".$v['code']."', '".$v['name']."', '".$city."',
                                       '".$v['city']."', '".$v['workTime']."',
                                       '".$v['address']."', '".$v['phone']."', '".$v['note']."',
                                       '".$v['coordX']."', '".$v['coordY']."', '".$v['weightMin']."',
                                       '".$v['weightMax']."', '".$v['TK']."', '".$k."', 'new', '".$v['email']."')");
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