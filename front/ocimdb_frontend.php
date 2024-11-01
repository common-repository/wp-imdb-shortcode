<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('IMDBMAIN_front')) {

    class IMDBMAIN_front {

        protected static $instance;


        function wpoc_imdb($atts, $content = null) {


            extract(shortcode_atts(array(
                'title' => '',
                'name' => '',
            ), $atts));


            

            if(!empty($title)){
                $plain_url = 'http://imdb.com';
                $title_url = 'http://imdb.com/title/'.$title;
                if(get_page_by_title( $title , OBJECT, 'ocimdb' )){
                    $page = get_page_by_title($title , OBJECT, 'ocimdb' );
                    $post_id = $page->ID;

                    $get_final_array = get_post_meta($post_id,"oc_imdb_data",true);
                }else{
                
                    $my_post = array(
                      'post_title'    => $title,
                      'post_type'     => 'ocimdb',
                      'post_status'   => 'publish',
                    );
                    $post_id = wp_insert_post( $my_post ); 



                    include_once('simple_html_dom.php');
                    $html = file_get_html('http://imdb.com/title/'.$title);

                    $obj_array = array(
                        "header" => array(
                                        "heading" => $html->find('.title_wrapper', 0)->find('h1', 0)->plaintext,
                                        "time" => $html->find('.subtext', 0)->find('time', 0)->plaintext,
                                    ), 
                        "rating" => array(
                                        "rating_value" => $html->find('.ratingValue', 0)->innertext,
                                        "total_rating" => array(
                                            "href"=>$plain_url.$html->find('.imdbRating a', 0)->href,
                                            "lable"=>$html->find('.imdbRating a', 0)->innertext,
                                        ),
                                    ), 
                        "images" => array(
                                        "poster_img" => array(
                                            "href"=>$plain_url.$html->find('.poster',0)->find('a',0)->href,
                                            "lable"=>$html->find('.poster',0)->find('a',0)->innertext,
                                        ),     
                                    ), 
                        "description" => Array(
                                        "summary" => $html->find('.summary_text', 0)->plaintext,
                                    ), 

                    );

                    if(!empty($html->find('.slate',0))){

                        $obj_array['images']['video']=array(
                            "href"=>$plain_url.$html->find('.slate',0)->find('a',0)->href,
                            "lable"=>$html->find('div[class="slate"]', 0)->find('a',0)->innertext,
                        );
                    }

                    foreach($html->find('.subtext', 0)->find('a') as $divs) {
                        $obj_array['header']['subtext'][]=array("href"=>$plain_url.$divs->href,"lable"=>$divs->innertext);
                    }

                    foreach($html->find('div[class="caption"] div') as $div) {
                        if($div->innertext){
                            if($div->find('a')){
                                foreach($div->find('a') as $divs) {
                    
                                    if ($divs->tag == 'a'){
                                        $obj_array['images']['caption']['total_img_vid'][] = array(
                                            "href"=>$plain_url.$divs->href,
                                            "lable"=>$divs->innertext
                                        );

                                    }else{
                                        $obj_array['images']['caption']['total_img_vid'][] = $div->innertext;
                                    }
                                }
                            }else{
                                $obj_array['images']['caption']['trailer'] = $div->innertext;
                                
                            }
                        }
                    }

                    foreach($html->find('div[class="credit_summary_item"]') as $div) {
                        //$obj_array['description']['all_data'][$div->find('h4', 0)->plaintext][]; 
                        foreach($div->find('a') as $diva) {
                            //$val = $div->href;
                            if ($diva->tag == 'a' && (strpos($diva->plaintext, 'more') == false) && (strpos($diva->plaintext, 'full cast') == false)){
                                $obj_array['description']['all_data'][$div->find('h4', 0)->plaintext][] = array(
                                    "href"=>$plain_url.$diva->href,
                                    "lable"=>$diva->plaintext
                                );
                                
                            }else{
                                $obj_array['description']['all_data'][$div->find('h4', 0)->plaintext][] = array(
                                    "href"=>$title_url.'/'.$diva->href,
                                    "lable"=>$diva->plaintext
                                );
                            }
                        }
                    }


                    if($html->find('.titleReviewbarItemBorder', 0)){
                        foreach ($html->find('.titleReviewbarItemBorder', 0)->find('div') as $valuesa) {
                            if($valuesa->find('a')){
                                foreach($valuesa->find('a') as $div) {
                                    $obj_array['review']['link'][]=array(
                                        "href"=>$title_url.'/'.$div->href,
                                        "lable"=>$div->plaintext
                                    );
                                    
                                }
                            }else{
                                $obj_array['review']['head'] = $valuesa->innertext;
                            }
                        }
                    }
                    // echo "<pre>";
                    // print_r($obj_array);
                    // echo "</pre>";
                    $get_final_array = serialize($obj_array);
                    update_post_meta($post_id,"oc_imdb_data",$get_final_array);
                    update_post_meta($post_id,"oc_imdb_data_id",$title);
                    update_post_meta($post_id,"oc_imdb_data_key","title");
                }
                ob_start();
                $array_get = unserialize($get_final_array);  
                
                // echo "<pre>";
                // print_r($array_get);
                // echo "</pre>";

                echo '<div class="imdb_body">';

                    echo '<div class="imdb_header">';
                        echo '<div class="imdb_title_div">';
                            echo '<h1>'.$array_get['header']['heading'].'</h1>';
                            $subtext .= $array_get['header']['time'].' | ';
                            echo '<div class="subtext">';
                                    foreach ($array_get['header']['subtext'] as $key => $value) {
                                        $subtext .= '<a href="'.$value['href'].'">'.$value['lable'].'</a> | ';
                                    }
                                    echo trim($subtext,'| ');
                            echo '</div>';
                        echo '</div>';

                        echo '<div class="imdb_rating_div">';
                            echo '<div class="imdbRating">';
                                echo '<div class="ratingValue">';
                                    echo $array_get['rating']['rating_value'];
                                    echo '<a href="'.$array_get['rating']['total_rating']['href'].'" target="_blank">'.$array_get['rating']['total_rating']['lable'].'</a>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';


                    echo '<div class="imdb_image">';
                        echo '<div class="poster">';
                            echo '<a href="'.$array_get['images']['poster_img']['href'].'" target="_blank">';
                                echo $array_get['images']['poster_img']['lable'];
                            echo '</a>';
                        echo '</div>';

                        echo '<div class="slate">';
                            echo '<a href="'.$array_get['images']['video']['href'].'" target="_blank" class="slate_button">';
                                echo $array_get['images']['video']['lable'];
                            echo '</a>';

                            echo '<div class="caption">';
                                echo '<div class="trailer">';
                                    echo $array_get['images']['caption']['trailer'];
                                echo '</div>';
                                foreach($array_get['images']['caption']['total_img_vid'] as $div) {
                                    $vals .= '<a href="'.$div['href'].'" target="_blank" >'.$div['lable'].'</a> | ';
                                }
                                echo '<div class="img_videos">';
                                    echo trim($vals,'| ');
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';


                    echo '<div class="imdb_description">';
                        echo $array_get['description']['summary'];
                        
                        echo '<div class="imdb_details">';
                            foreach($array_get['description']['all_data'] as $keya => $valuea) {
                                echo '<div class="imdb_details_head">';

                                    echo '<h4>'.$keya.'</h4>'; 
                                    echo '<div class="imdb_details_link">';
                                        $linklc='';
                                        foreach($valuea as $div) {
                                            
                                        $linklc .='<a href="'.$div['href'].'" target="_blank">'.$div['lable'].'</a>, ';
                                            
                                        }
                                        echo trim($linklc,', ');
                                    echo '</div>';
                                echo '</div>';
                            }
                        echo '</div>';
                    echo '</div>';

                    if($array_get['review']){
                        echo '<div class="imdb_review">';
                            echo '<h4>'.$array_get['review']['head'].'</h4>';
                            foreach($array_get['review']['link'] as $divse) {
                                $linkt .='<a href="'.$divse['href'].'" target="_blank">'.$divse['lable'].'</a>, ';
                            }
                            echo trim($linkt,', ');
                        echo '</div>';
                    }

                echo '</div>';
                return $var = ob_get_clean();
            }
            if(!empty($name)){
                $plain_url = 'http://imdb.com';
                $title_url = 'https://www.imdb.com/name/'.$name;
                if(get_page_by_title( $name , OBJECT, 'ocimdb' )){
                    $page = get_page_by_title($name , OBJECT, 'ocimdb' );
                    $post_id = $page->ID;

                    $get_final_array = get_post_meta($post_id,"oc_imdb_data",true);
                }else{
                
                    $my_post = array(
                      'post_title'    => $name,
                      'post_type'     => 'ocimdb',
                      'post_status'   => 'publish',
                    );
                    $post_id = wp_insert_post( $my_post ); 



                    include_once('simple_html_dom.php');
                    $html = file_get_html('http://imdb.com/name/'.$name);

                    $obj_array = array(
                        "header" => array(
                                        "heading" => $html->find('.name-overview-widget__section', 0)->find('.header', 0)->innertext,
                                        "role" => $html->find('.infobar', 0)->plaintext,
                                    ), 
                      
                        "images" => array(
                                        "poster_img" => array(
                                            "href"=>$plain_url.$html->find('.image',0)->find('a',0)->href,
                                            "lable"=>$html->find('.image',0)->find('a',0)->innertext,
                                        ),
                                        "video" => array(
                                            "href"=>$plain_url.$html->find('.slate',0)->find('a',0)->href,
                                            "lable"=>$html->find('.slate', 0)->find('a',0)->innertext,
                                        ),    
                                    ), 
                        "description" => Array(
                                        "summary" => $html->find('.inline', 0)->plaintext,
                                        "view_more" => $html->find('.inline span', 0)->plaintext,
                                        "view_more_href" => $plain_url.$html->find('.inline span a', 0)->href,
                                        "born" => $html->find('#name-born-info', 0)->plaintext,
                                    ),
                        "awards" => array(
                                        "awardss" => $html->find('.awards-blurb', 0)->innertext,
                                        "seemore_link" => $plain_url.$html->find('.article.highlighted', 0)->find('.see-more', 0)->find('a', 0)->href,
                                        "seemore" => $html->find('.article.highlighted', 0)->find('.see-more', 0)->find('a', 0)->plaintext,

                                    ),

                    );
                    
                    if($html->find('#name-death-info', 0)){
                        $obj_array['description']['death'] = $html->find('#name-death-info', 0)->plaintext;
                    }

                    foreach($html->find('.caption div') as $div) {

                        if($div->innertext){

                            if($div->find('a')){

                                foreach($div->find('a') as $divs) {
                                
                                    if ($divs->tag == 'a'){
                                        
                                        $obj_array['images']['caption']['total_img_vid'][] = array(
                                            "href"=>$plain_url.$divs->href,
                                            "lable"=>$divs->innertext
                                        );

                                    }else{
                                        $obj_array['images']['caption']['total_img_vid'][] = $div->innertext;
                                    }
                                }
                            }else{
                                $obj_array['images']['caption']['trailer'] = $div->innertext;
                                
                            }
                        }
                    }


                    // echo "<pre>";
                    // print_r($obj_array);
                    // echo "</pre>";
                    
                    $get_final_array = serialize($obj_array);
                    update_post_meta($post_id,"oc_imdb_data",$get_final_array);
                    update_post_meta($post_id,"oc_imdb_data_id",$name);
                    update_post_meta($post_id,"oc_imdb_data_key","name");
                    
                }
                ob_start();
                $array_get = unserialize($get_final_array);
                // echo "<pre>";
                // print_r($array_get);
                // echo "</pre>";
                echo '<div class="imdb_body">';
                    echo '<div class="imdb_header">';
                        echo '<div class="imdb_title_div">';
                            echo '<h1>'.$array_get['header']['heading'].'</h1>';
                            echo $array_get['header']['role'];
                        echo '</div>';
                    echo '</div>';


                    echo '<div class="imdb_image">';
                        echo '<div class="poster">';
                            echo '<a href="'.$array_get['images']['poster_img']['href'].'" target="_blank">';
                                echo $array_get['images']['poster_img']['lable'];
                            echo '</a>';
                        echo '</div>';

                        echo '<div class="slate">';
                            echo '<a href="'.$array_get['images']['video']['href'].'" target="_blank" class="slate_button">';
                                echo $array_get['images']['video']['lable'];
                            echo '</a>';

                            echo '<div class="caption">';
                                echo '<div class="trailer">';
                                    echo $array_get['images']['caption']['trailer'];
                                echo '</div>';
                                foreach($array_get['images']['caption']['total_img_vid'] as $div) {
                                    $vals .= '<a href="'.$div['href'].'" target="_blank" >'.$div['lable'].'</a> | ';
                                }
                                echo '<div class="img_videos">';
                                    echo trim($vals,'| ');
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';


                    echo '<div class="imdb_description">';
                    $str = (explode("See full bio",$array_get['description']['summary']));
                        echo $str[0];
                        echo '<a href="'.$array_get['description']['view_more_href'].'">'.$array_get['description']['view_more'].'</a>';
                        echo '<div class="imdb_details born_dead">';
                            echo '<p>'.$array_get['description']['born'].'</p>';
                            echo '<p>'.$array_get['description']['death'].'</p>';
                        echo '</div>';
                    echo '</div>';

                    echo '<div class="imdb_award">';
                        echo '<p>'.$array_get['awards']['awardss'].' <a href="'.$array_get['awards']['seemore_link'].'" target="_blank">'.$array_get['awards']['seemore'].'</a></p>';
                        
                    echo '</div>';

                echo '</div>';
                return $var = ob_get_clean();
            }
            
        }

      
        function init() {
            add_shortcode( 'wpoc_imdb', array($this,'wpoc_imdb'));
        }


        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        }

    }

    IMDBMAIN_front::instance();
}




