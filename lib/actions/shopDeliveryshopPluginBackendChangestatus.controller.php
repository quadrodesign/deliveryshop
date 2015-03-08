<?php

class shopDeliveryshopPluginBackendChangestatusController extends waJsonController {
    
    public function execute()
    {
     try{
            $id = waRequest::post('id');
            $status = waRequest::post('status');
            $g = waRequest::post('g');
            $model = new waModel();
            
            if($g == 'shop')
            {
                $model->query("UPDATE shop_deliveryshop_city SET status='".$status."' WHERE cityCode='".$id."'");
            }
            else if($g == 'delivery')
            {
                $model->query("UPDATE shop_deliveryshop_pvz SET status='".$status."' WHERE code='".$id."'");
            }
            $this->response['message'] = 'ok';
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }
    }
}