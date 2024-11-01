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

            echo "hello";
            $plain_url = 'http://imdb.com';
            $title_url = 'http://imdb.com/title/'.$title;

            include_once('simple_html_dom.php');
            ob_start();  
            
                $html = file_get_html('http://imdb.com/title/'.$title);
                echo '<div class="imdb_body">';

                    echo '<div class="imdb_header">';
                        echo '<div class="imdb_title_div">';
                            echo '<h1>'.$html->find('.title_wrapper', 0)->find('h1', 0)->plaintext.'</h1>';
                            echo '<div class="subtext">';
                                $vals .= $html->find('.subtext', 0)->find('time', 0)->plaintext.' | ';
                                foreach($html->find('.subtext', 0)->find('a') as $divs) {
                                    $vals .= '<a href="'.$plain_url.$divs->href.'" target="_blank" target="_blank">'.$divs->innertext.'</a> | ';
                                }
                                echo trim($vals,'| ');
                            echo '</div>';
                            
                        echo '</div>';
                        echo '<div class="imdb_rating_div">';
                            echo '<div class="imdbRating">';
                                echo '<div class="ratingValue">';
                                    echo $html->find('.ratingValue', 0)->innertext;
                                    echo '<a href="'.$plain_url.$html->find('.imdbRating a', 0)->href.'" target="_blank">'.$html->find('.imdbRating a', 0)->innertext.'</a>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';


                    echo '<div class="imdb_image">';
                        echo '<div class="poster">';
                            echo '<a href="'.$plain_url.$html->find('.poster',0)->find('a',0)->href.'" target="_blank">';
                                echo $html->find('.poster',0)->find('a',0)->innertext;
                            echo '</a>';
                        echo '</div>';
                        echo '<div class="slate">';
                            echo '<a href="'.$plain_url.$html->find('.slate',0)->find('a',0)->href.'" target="_blank" class="slate_button">';
                                echo $html->find('div[class="slate"]', 0)->find('a',0)->innertext;
                            echo '</a>';
                            echo '<div class="caption">';
                                foreach($html->find('div[class="caption"] div') as $div) {
                                    if($div->find('a')){
                                        foreach($div->find('a') as $divs) {
                            
                                            if ($divs->tag == 'a'){
                                                $val .= '<a href="'.$plain_url.$divs->href.'" target="_blank">'.$divs->innertext.'</a> | ';
                                            }else{
                                                $val .= $div->innertext;
                                            }
                                        }
                                    }else{
                                        echo '<div class="trailer">';
                                            echo $div->innertext;
                                        echo '</div>';
                                    }
                                    
                                }
                                echo '<div class="img_videos">';
                                    echo trim($val,'| ');
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';


                    echo '<div class="imdb_description">';
                        echo $html->find('.summary_text', 0)->innertext;
                        echo '<div class="imdb_details">';
                            foreach($html->find('div[class="credit_summary_item"]') as $div) {
                                echo '<div class="imdb_details_head">';

                                    echo '<h4>'.$div->find('h4', 0)->plaintext.'</h4>'; 
                                    echo '<div class="imdb_details_link">';
                                        $linklc='';
                                        foreach($div->find('a') as $div) {
                                            
                                            //$val = $div->href;
                                            
                                            if ($div->tag == 'a' && (strpos($div->plaintext, 'more') == false) && (strpos($div->plaintext, 'full cast') == false)){
                                                $linklc.='<a href="'.$plain_url.$div->href.'" target="_blank">'.$div->plaintext.'</a>, ';
                                            }else{
                                                $linklc.='<a href="'.$title_url.'/'.$div->href.'" target="_blank">'.$div->plaintext.'</a>, ';
                                            }

                                            
                                        }
                                        echo trim($linklc,', ');
                                    echo '</div>';
                                echo '</div>';
                            }
                        echo '</div>';
                    echo '</div>';


                    echo '<div class="review_div">';
                       //echo $html->find('.titleReviewbarItemBorder', 0)->find('div', 0);
                       //echo $html->find('.titleReviewbarItemBorder', 0)->find('div', 1);
                       foreach ($html->find('.titleReviewbarItemBorder', 0)->find('div') as $valuesa) {
                           
                            if($valuesa->find('a')){
                                foreach($valuesa->find('a') as $div) {
                                    //echo $div->href;
                                    //echo $div->plaintext;
                                    echo '<a href="'.$div->href.'" target="_blank">'.$div->plaintext.'</a>';
                                }
                            }else{
                                echo $valuesa->innertext;
                            }
                           
                       }
                    echo '</div>';

                echo '</div>';
                //echo $html->find('div[class="summary_text"]', 0)->innertext;
                //echo $html->find('div[class="credit_summary_item"]', 0)->innertext;
                //echo $html->find('div[class="credit_summary_item"]', 1)->innertext;
                // $head = $html->find('div[class="title_wrapper"] h1', 0)->innertext;
                // echo $head;

            return $var = ob_get_clean();
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




