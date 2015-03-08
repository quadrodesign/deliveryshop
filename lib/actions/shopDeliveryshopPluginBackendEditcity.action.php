<?php

/**
 * @author Гречаный Николай <boonkerteam@gmail.com>
 */
 
class shopDeliveryshopPluginBackendEditcityAction extends waViewAction 
{

    public function execute() {
        $this->setLayout(new shopBackendLayout());
        $id = waRequest::get('id');
        $model = new waModel();
        
        $city['new'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city WHERE status='new'")->fetchField();
        $city['completed'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city WHERE status='completed'")->fetchField();
        $city['flag-white'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city WHERE status='flag-white'")->fetchField();
        $city['refunded'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city WHERE status='refunded'")->fetchField();
        $city['all'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_city")->fetchField();
        
        $regions = $model->query("SELECT * FROM wa_region WHERE country_iso3 = 'rus'")->fetchAll();
        
        $domains = $model->query("SELECT * FROM site_domain")->fetchAll();
        $main_data = $model->query("SELECT * FROM shop_deliveryshop_city WHERE cityCode='".$id."'")->fetchAssoc();
        
        if($id != 'new')
        {
            foreach($domains as $d)
            {
                $description = $model->query("SELECT * FROM shop_deliveryshop_city_description WHERE cityCode='".$id."' AND domain='".$d['name']."'")->fetchAssoc();
                $description = $description ? $description : array();
                $info[$d['name']] = array_merge($main_data,$description);
                if(is_numeric($info[$d['name']]['region']))
                    {
                        $info[$d['name']]['region'] = $model->query("SELECT name FROM wa_region WHERE code='".$info[$d['name']]['region']."' AND country_iso3='rus'")->fetchField();
                    }
                $tab = explode('.', $d['name']);
                $info[$d['name']]['tab_name'] = $tab[0];
            }
        }
        else
        {
            foreach($domains as $d)
            {
                $info[$d['name']] = array();

                $tab = explode('.', $d['name']);
                $info[$d['name']]['tab_name'] = $tab[0];
            }
        }
        
        $pvz['data'] = $model->query("SELECT * FROM shop_deliveryshop_pvz WHERE cityCode = '".$id."'")->fetchAll();
        $this->view->assign('pvz', $pvz);
        $this->view->assign('regions', $regions);
        $this->view->assign('info', $info);
        $this->view->assign('id', $id);
        $this->view->assign('city', $city);
     }
}
