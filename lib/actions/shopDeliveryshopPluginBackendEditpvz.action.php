<?php

/**
 * @author Гречаный Николай <boonkerteam@gmail.com>
 */
 
class shopDeliveryshopPluginBackendEditpvzAction extends waViewAction 
{

    public function execute() {
        $this->setLayout(new shopBackendLayout());
        $id = waRequest::get('id');
        $model = new waModel();
        
        $city['new'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_pvz WHERE status='new'")->fetchField();
        $city['completed'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_pvz WHERE status='completed'")->fetchField();
        $city['flag-white'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_pvz WHERE status='flag-white'")->fetchField();
        $city['refunded'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_pvz WHERE status='refunded'")->fetchField();
        $city['all'] = $model->query("SELECT COUNT(*) FROM shop_deliveryshop_pvz")->fetchField();
        
        $domains = $model->query("SELECT * FROM site_domain")->fetchAll();
        
        if($id == 'new')
        {
            $description = $model->query("SELECT * FROM shop_deliveryshop_city ORDER BY city ASC")->fetchAll('cityCode');
            foreach($domains as $d)
            {
                $info[$d['name']]['city'] = $description;
                $tab = explode('.', $d['name']);
                $info[$d['name']]['tab_name'] = $tab[0];
            }
        }
        else
        {
            foreach($domains as $d)
            {
                $description = $model->query("SELECT * FROM shop_deliveryshop_pvz WHERE code='".$id."' AND domain='".$d['name']."'")->fetchAssoc();
                if(!$description)
                {
                    $description = $model->query("SELECT * FROM shop_deliveryshop_pvz WHERE code='".$id."'")->fetchAssoc();
                }
                $description = $description ? $description : array();
                $id_city = $description['cityCode'];
                $info[$d['name']] = $description;
                $tab = explode('.', $d['name']);
                $info[$d['name']]['tab_name'] = $tab[0];
            }
            
            $pvz['data'] = $model->query("SELECT * FROM shop_deliveryshop_pvz WHERE cityCode = '".$id_city."' AND code NOT IN ('".$id."')")->fetchAll();
            $this->view->assign('pv', $pvz);
        }
        
        
        
        $this->view->assign('info', $info);
        $this->view->assign('id', $id);
        $this->view->assign('pvz', $city);
     }
}
