<?php
class BrandFilterController{

    public function get(){
       
            $data = [];

            $attr = [];

            $terms = [
                'Colors'=>'pa_color',
                'Pattern'=>'pa_pattern',
                'Size'=>'pa_size',
            ];

            foreach($terms as $key=>$term){
              //  if(term_exists($term)){
                    $attr[] =  $this->getSection($key,$term);
//}
            }


            //size
            $attr[] =  $this->getPriceSection();
             //size
             $attr[] =  $this->getOrderSection();


            $data['sections'] = $attr;
            
            return  $data;
    }

    private function getSection($title,$term){
        $attr = [];
        $attr['title'] = $title;
        $attr['type'] =  'category';
        $attr['categories'] =  get_terms($term);
        
        return $attr;
    }
    

    private function getPriceSection(){
        $attr = [];
        $attr['title'] = 'Price';
        $attr['type']=  'price';
        $attr['minPrice'] =  '0';
        $attr['maxPrice'] = '1000';
        return $attr;
    }

    
    private function getOrderSection(){
        $attr = [];
        $attr['title'] = 'Order By';
        $attr['type']=  'order';
        $attr['orders'] =  [
            [
                'name'=> 'Ascending',
                'value'=>  'asc',
            ],
            [
                'name'=> 'Descending',
                'value'=>  'desc',
            ]

        ];
        return $attr;
    }
    
        
}


