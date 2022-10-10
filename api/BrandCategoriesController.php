<?php
class BrandCategoriesController{


    public function get(){
        $data = [];
        $ctrl = new BrandHomeController();
        $data['sections'] =  $ctrl->getWidgets('search_screen');

        return   $data;
    }



     
}


