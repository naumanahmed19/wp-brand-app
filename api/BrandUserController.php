<?php

class BrandUserController{


    public function get($user){
     
        $avatar = get_user_meta($user->ID,'avatar',true);
        $data['id'] =$user->ID;
        $data['email'] = $user->user_email;
        $data['firstName'] = $user->first_name;
        $data['lastName'] = $user->last_name;
        //$data['avatar'] = get_avatar_url( $current_user->ID, 64 ) ; 
        $data['avatar'] = $avatar ?  $avatar : get_avatar_url( $user->ID, 64 );

        return $data;
    }
    public function update($request){

         global $current_user;
    //    return  $current_user;


        $request['ID'] = $current_user->ID;
        wp_update_user($request); 

       return [
        'data' => $this->get($current_user->ID),
        'status' => 200
    ];
    }
}