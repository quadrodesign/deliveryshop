<?php

/**
 * @author Гречаный Николай <boonkerteam@gmail.com>
 */
 
class shopDeliveryshopPluginBackendDisplayAction extends waViewAction 
{

    public function execute() {
        $this->setLayout(new shopBackendLayout());
        $status = waRequest::get('status');
        $tab = waRequest::get('tab');
        
        $model = new waModel();
        $cities = $model->query("SELECT * FROM shop_deliveryshop_city ORDER BY city ASC")->fetchAll();
        $city['data'] = $cities;
        $city['new'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city WHERE status='new'")->fetchField();
        $city['completed'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city WHERE status='completed'")->fetchField();
        $city['flag-white'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city WHERE status='flag-white'")->fetchField();
        $city['refunded'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city WHERE status='refunded'")->fetchField();
        $city['all'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city")->fetchField();
        
        foreach($city['data'] as $key => &$c)
        {
            if(is_numeric($c['region']))
            {
                $c['region'] = $model->query("SELECT name FROM wa_region WHERE code='".$c['region']."' AND country_iso3='rus'")->fetchField();
            }
            
            if($status && $status != $c['status'] && $tab == 'shop') 
            {
                unset($city['data'][$key]);
            }
        }
        $pvz['data'] = $model->query("SELECT * FROM shop_deliveryshop_pvz ORDER BY city ASC")->fetchAll();
        $pvz['new'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_pvz WHERE status='new'")->fetchField();
        $pvz['completed'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_pvz WHERE status='completed'")->fetchField();
        $pvz['flag-white'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_pvz WHERE status='flag-white'")->fetchField();
        $pvz['refunded'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_pvz WHERE status='refunded'")->fetchField();
        $pvz['all'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_pvz")->fetchField();
        

        $this->view->assign('cities', $city);
        $this->view->assign('pvz', $pvz);
     }
}
