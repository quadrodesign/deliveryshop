<?php

class shopDeliveryshopPluginSettingsSaveController extends waJsonController {
    
    public function execute()
    {
        $plugin_id = array('shop', 'deliveryshop');
        
        try {
            $app_settings_model = new waAppSettingsModel();
            $settings = waRequest::post('settings');
            
            $app_settings_model->set($plugin_id, 'status', (int) $settings['status']);
            
            $model = new waModel();
            $domains = $model->query("SELECT * FROM site_domain")->fetchAll();
            
            $reset_tpls = waRequest::post('reset_tpls');
            $template= waRequest::post('template');
            $prices = waRequest::post('prices');
            
            $this->response['pri'] = $prices;
            
            foreach($domains as $d)
            {
                $tab = explode('.', $d['name']);
                
                $id_price = $model->query("SELECT id FROM shop_deliveryshop_delivery WHERE domain = '".$d['name']."'")->fetchField();
                if($id_price)
                {
                    $model->query("UPDATE shop_deliveryshop_delivery SET price = '".$prices[$d['name']]['price']."' WHERE domain = '".$d['name']."'");
                }
                else
                {
                    $model->query("INSERT INTO shop_deliveryshop_delivery (domain, price)
                                   VALUES ('".$d['name']."', '".$prices[$d['name']]['price']."')");
                }
                
                if (isset($reset_tpls[$d['name']])) {
                    $template_path = wa()->getDataPath('plugins/deliveryshop/templates/actions/frontend/FrontendDostavka'.ucfirst($tab[0]).'.html', false, 'shop', true);
                    @unlink($template_path);
                } else {
                    if (!isset($template[$d['name']])) {
                        throw new waException('Не определён шаблон');
                    }
                    $template_path = wa()->getDataPath('plugins/deliveryshop/templates/actions/frontend/FrontendDostavka'.ucfirst($tab[0]).'.html', false, 'shop', true);
                    if (!file_exists($template_path)) {
                        $template_path = wa()->getAppPath('plugins/deliveryshop/templates/actions/frontend/FrontendDostavka.html', 'shop');
                    }
    
                    $template_content = file_get_contents($template_path);
                    if ($template_content != $template[$d['name']]) {
                        $template_path = wa()->getDataPath('plugins/deliveryshop/templates/actions/frontend/FrontendDostavka'.ucfirst($tab[0]).'.html', false, 'shop', true);
    
                        $f = fopen($template_path, 'w');
                        if (!$f) {
                            throw new waException('Не удаётся сохранить шаблон. Проверьте права на запись ' . $template_path);
                        }
                        fwrite($f, $template[$d['name']]);
                        fclose($f);
                    }
                }
                $this->response['template'] = $template;
            }
            
            $this->response['message'] = "Сохранено";
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }
    }
}