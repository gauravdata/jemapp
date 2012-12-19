<?php
class pap_update_4_5_26 {
    
    private function changeItem($itmes) {
        foreach ($itmes as $item) {
            $code = $item->code;
            unset($item->code);
            $item->data = new stdClass();
            $item->data->code = $code;
            if (isset($item->items) && count($item->items) > 0) {
                $this->changeItem($item->items);
            }
        }
    }
    
    public function execute() {
        $tree = Gpf_Settings::get(Pap_Settings::AFFILIATE_MENU);
        
        if (strpos($tree, '"data":{"code":')) {
            return;
        }
        
        $json = new Gpf_Rpc_Json();
        $tree = $json->decode($tree);
        
        $this->changeItem($tree);
        
        Gpf_Settings::set(Pap_Settings::AFFILIATE_MENU,$json->encode($tree));
    }
}
?>
