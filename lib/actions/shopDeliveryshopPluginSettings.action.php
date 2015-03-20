<?php

class shopDeliveryshopPluginSettingsAction extends waViewAction
{
    
    public function execute()
    {
        $model_settings = new waAppSettingsModel();
        $settings = $model_settings->get($key = array('shop', 'deliveryshop')); 
        $model = new waModel();
        $domains = $model->query("SELECT * FROM site_domain")->fetchAll();
        
        $prices = $model->query("SELECT * FROM shop_deliveryshop_delivery")->fetchAll('domain');
        
        foreach($domains as $d)
            {
                $tab = explode('.', $d['name']);
                $info[$d['name']]['tab_name'] = $tab[0];

              $template_path = wa()->getDataPath('plugins/deliveryshop/templates/actions/frontend/FrontendDostavka' . $d['id'] . '.html', false, 'shop', true);
                $change_tpl[$d['name']] = true;
                if (!file_exists($template_path)) 
                {
                    $template_path = wa()->getAppPath('plugins/deliveryshop/templates/actions/frontend/FrontendDostavka.html', 'shop');
                    $change_tpl[$d['name']] = false;
                }
                
                $template_content[$d['name']] = file_get_contents($template_path);
                unset($template_path);
            }
        
        $this->view->assign('info', $info);
        $this->view->assign('prices', $prices);
        $this->view->assign('change_tpl', $change_tpl);
        $this->view->assign('template', $template_content);
        $this->view->assign('settings', $settings);
    }       
}
