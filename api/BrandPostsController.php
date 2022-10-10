<?php



class BrandPostsController{

    public function data($posts){
        $data = [];
        $i = 0;
        if(!empty($posts)){
            foreach($posts as $post) {

              
                $data[$i]['id'] = $post->ID;
                $data[$i]['title'] = $post->post_title;
                $data[$i]['content'] = $post->post_content;
                $data[$i]['slug'] = $post->post_name;
                $data[$i]['date'] = $post->post_date;
                $data[$i]['media'] = brand_get_post_media($post->ID);
                $data[$i]['author'] = $this->getAuthor($post);

                $data[$i]['commentCount'] = $post->comment_count;
              //  $data[$i]['favorited'] = isFavorited($post->ID);
                
                $data[$i]['comments'] = $this->getComments($post);
                
        
                $i++;
            }
        }
        return $data;
    }


    public function getAuthor($post){
        $data = [];
        $data['displayName'] = get_the_author_meta('display_name', $post->post_author);
        $data['firstName'] = get_the_author_meta('first_name', $post->post_author);
        $data['lastName'] = get_the_author_meta('last_name', $post->post_author);
        $data['avatar'] = get_Avatar_url($post->post_author);
        return $data;
    }
    public function getComments($post){
        $data = [];
        $i = 0;
        $comments =  get_comments(array('post_id' => $post->ID ));
        foreach($comments as $comment){
            
            if($comment->comment_approved == '1'){
                $data[$i]['id'] = json_decode($comment->comment_ID);    
                $data[$i]['date'] = $comment->comment_date;  
                $data[$i]['approved'] = $comment->comment_approved == '1'? true: false;  
                $data[$i]['content'] = $comment->comment_content; 
                $data[$i]['author']['displayName'] = $comment->comment_author;  
                $data[$i]['author']['avatar'] = get_Avatar_url($comment->user_id);
                $i++;
            }
        }
       
        return $data;
    }


}



