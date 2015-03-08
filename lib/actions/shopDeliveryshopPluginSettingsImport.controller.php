<?php

class shopDeliveryshopPluginSettingsImportController extends waJsonController {
    
    public function execute()
    {
        try {
            
            $PvzList = simplexml_load_file('http://gw.edostavka.ru:11443/pvzlist.php');
            $model = new waModel();
            $update = 0;
            $insert = 0;
            
            if($PvzList)
            {
                foreach($PvzList->Pvz as $pvz)
                {
                    if(!$weight = $pvz->WeightLimit)
                    {
                        $weight['WeightMin'] = '';
                        $weight['WeightMax'] = '';
                    }
                    $id = $model->query("SELECT id FROM shop_deliveryshop_pvz WHERE code='".(string)$pvz['Code']."'")->fetchField();
                    $id_city = $model->query("SELECT cityCode FROM shop_deliveryshop_city WHERE cityCode='".(string)$pvz['CityCode']."'")->fetchField();
                    
                    $youcity = new shopDeliveryshopPluginHelper;
                    $data = $youcity->getCitySearch($pvz[0]['City']); 
                    if($data[2] != 0 && is_numeric($data[2]))
                    {
                        $region = $data[2];
                    }
                    else
                    {
                        if($data[4] == 'ukr')
                        {
                            $region = 'Украина';
                        }
                        else if($data[4] == 'kaz')
                        {
                            $region = 'Казахстан';
                        }
                    }  
                    
                    
                    if($id_city)
                    {
                        $model->query("UPDATE shop_deliveryshop_city SET 
                                       cityCode='".mysql_escape_string((string)$pvz[0]['CityCode'])."',
                                       city='".mysql_escape_string((string)$pvz[0]['City'])."',
                                       region='".$region."'
                                       WHERE cityCode='".$id_city."'");
                    }
                    else
                    {
                        $model->query("INSERT INTO shop_deliveryshop_city
                                       (cityCode, city, region, status)
                                        VALUES ('".mysql_escape_string((string)$pvz[0]['CityCode'])."',
                                        '".mysql_escape_string((string)$pvz[0]['City'])."', '".$region."', 'new')");
                    }

                    if($id)
                    {
                        $model->query("UPDATE shop_deliveryshop_pvz SET
                                       code='".mysql_escape_string((string)$pvz['Code'])."',
                                       name='".mysql_escape_string((string)$pvz[0]['Name'])."',
                                       cityCode='".mysql_escape_string((string)$pvz[0]['CityCode'])."',
                                       city='".mysql_escape_string((string)$pvz[0]['City'])."',
                                       workTime='".mysql_escape_string((string)$pvz[0]['WorkTime'])."',
                                       address='".mysql_escape_string((string)$pvz[0]['Address'])."',
                                       phone='".mysql_escape_string((string)$pvz[0]['Phone'])."',
                                       note='".mysql_escape_string((string)$pvz[0]['Note'])."',
                                       coordX='".mysql_escape_string((string)$pvz[0]['coordX'])."',
                                       coordY='".mysql_escape_string((string)$pvz[0]['coordY'])."',
                                       weightMin='".mysql_escape_string((string)$weight['WeightMin'])."',
                                       weightMax='".mysql_escape_string((string)$weight['WeightMax'])."'
                                       WHERE id='".$id."'");
                        $update++;
                    }
                    else
                    {
                        $model->query("INSERT INTO shop_deliveryshop_pvz
                                       (code, name, cityCode, city, workTime, address, phone, note, coordX, coordY, weightMin, weightMax, status)
                                        VALUES ('".mysql_escape_string((string)$pvz['Code'])."',
                                        '".mysql_escape_string((string)$pvz[0]['Name'])."',
                                        '".mysql_escape_string((string)$pvz[0]['CityCode'])."',
                                        '".mysql_escape_string((string)$pvz[0]['City'])."',
                                        '".mysql_escape_string((string)$pvz[0]['WorkTime'])."',
                                        '".mysql_escape_string((string)$pvz[0]['Address'])."',
                                        '".mysql_escape_string((string)$pvz[0]['Phone'])."',
                                        '".mysql_escape_string((string)$pvz[0]['Note'])."',
                                        '".mysql_escape_string((string)$pvz[0]['coordX'])."',
                                        '".mysql_escape_string((string)$pvz[0]['coordY'])."',
                                        '".mysql_escape_string((string)$weight['WeightMin'])."',
                                        '".mysql_escape_string((string)$weight['WeightMax'])."',
                                        'new')");
                    }
                }
            }
            
            $this->response['message'] = 'Импортировано';
            $this->response['update'] = $update;
            $this->response['ins'] = $insert;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }
    }
}