<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('IMDBMAIN_menu')) {

    class IMDBMAIN_menu {

        protected static $instance;



        function ocimdb_create_menu() {
             $post_type = 'ocimdb';
             $singular_name = 'IMDB';
             $plural_name = 'IMDB';
             $slug = 'ocimdb';
             $labels = array(
                'name'               => _x( $plural_name, 'post type general name', 'ocimdb' ),
                'singular_name'      => _x( $singular_name, 'post type singular name', 'ocimdb' ),
                'menu_name'          => _x( $singular_name, 'admin menu name', 'ocimdb' ),
                'name_admin_bar'     => _x( $singular_name, 'add new name on admin bar', 'ocimdb' ),
                'add_new'            => __( 'Add New', 'ocimdb' ),
                'add_new_item'       => __( 'Add New '.$singular_name, 'ocimdb' ),
                'new_item'           => __( 'New '.$singular_name, 'ocimdb' ),
                'edit_item'          => __( 'Edit '.$singular_name, 'ocimdb' ),
                'view_item'          => __( 'View '.$singular_name, 'ocimdb' ),
                'all_items'          => __( 'All '.$plural_name, 'ocimdb' ),
                'search_items'       => __( 'Search '.$plural_name, 'ocimdb' ),
                'parent_item_colon'  => __( 'Parent '.$plural_name.':', 'ocimdb' ),
                'not_found'          => __( 'No Table found.', 'ocimdb' ),
                'not_found_in_trash' => __( 'No Table found in Trash.', 'ocimdb' )
             );

             $args = array(
                'labels'             => $labels,
                'description'        => __( 'Description.', 'ocimdb' ),
                'public'             => false,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => $slug ),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => array( 'title' ),
                'menu_icon'          => 'dashicons-editor-table'
             );
             register_post_type( $post_type, $args );
        }


        function ocimdb_add_meta_box() {
            add_meta_box(
                'ocimdb_metabox',
                 __( 'IMDB Data', 'ocimdb' ),
                array($this, 'ocimdb_metabox_cb'),
                'ocimdb',
                'normal'
            );
        }


        function ocimdb_metabox_cb( $post ) {
            if(get_post_meta($post->ID,"oc_imdb_data_key",true) == "title"){
                $get_final_arrays = get_post_meta($post->ID,"oc_imdb_data",true);
                $array_get = unserialize($get_final_arrays);

                foreach ($array_get['header']['subtext'] as $key => $value) {
                    $subtext .= $value['lable'].' | ';
                }
                
                ?> 
                    <div class="ocimdb-container">
                    	<h2><a href="https://www.imdb.com/title/<?php echo get_post_meta($post->ID,"oc_imdb_data_id",true); ?>" target="_blank">IMDB link</a></h2>
                        <h2>Details</h2>
                        <div class="details_div">
                            
                            <p><span>Title :</span> <?php echo $array_get['header']['heading']; ?></p>
                            <p><span>Duration :</span> <?php echo $array_get['header']['time']; ?></p>
                            <p><span>Movie Category :</span> <?php echo trim($subtext,'| '); ?></p>
                        
                        </div>
                        <h2>Review</h2>
                        <div class="details_div">

                            <p><span>Total Review :</span> <?php echo $array_get['rating']['rating_value']; ?></p>
                            <p><span>Total User Review :</span> <?php echo $array_get['rating']['total_rating']['lable']; ?></p>
                            
                        </div>
                        <h2>Images & Videos</h2>
                        <div class="imges">
                            
                            <?php echo $array_get['images']['poster_img']['lable']; ?>
                            <?php echo $array_get['images']['video']['lable']; ?>
                            <?php   foreach($array_get['images']['caption']['total_img_vid'] as $div) {
                                        echo '<a href="'.$div['href'].'" target="_blank" >'.$div['lable'].' >></a><br>';
                                    } 
                                    ?>
                        </div>   
                        <h2>Description</h2>    
                        <div>
                            <?php echo $array_get['description']['summary']; ?>
                        </div>        
                        <div> 
                        <?php 
                            echo '<div class="imdb_details">';
                                foreach($array_get['description']['all_data'] as $keya => $valuea) {
                                    echo '<div class="imdb_details_head">';

                                        echo '<h4>'.$keya.'</h4>'; 
                                        echo '<div class="imdb_details_link">';
                                            $linklc='';
                                            foreach($valuea as $div) {
                                                
                                            $linklc .= $div['lable'].', ';
                                                
                                            }
                                            echo trim($linklc,', ');
                                        echo '</div>';
                                    echo '</div>';
                                }
                            echo '</div>';
                        ?>       
                        </div> 
                        <h2>Review</h2>
                        <div>
                            <h4><?php echo $array_get['review']['head']; ?></h4>
                            <?php 
                            foreach($array_get['review']['link'] as $divse) {
                                $linkt .= $divse['lable'].' , ';
                            }
                            echo trim($linkt,', ');
                            ?>
                        </div>  
                        
                    </div>

                <?php
            }else if(get_post_meta($post->ID,"oc_imdb_data_key",true) == "name"){
                $get_final_arrays = get_post_meta($post->ID,"oc_imdb_data",true);
                $array_get = unserialize($get_final_arrays);

                
                
                ?> 
                    <div class="ocimdb-container">
                        <h2><a href="https://www.imdb.com/name/<?php echo get_post_meta($post->ID,"oc_imdb_data_id",true); ?>" target="_blank">IMDB link</a></h2>
                        <h2>Details</h2>
                        <div class="details_div">
                            
                            <p><span>Title :</span> <?php echo $array_get['header']['heading']; ?></p>
                            <p><span>Role :</span> <?php echo $array_get['header']['role']; ?></p>
                        
                        </div>
                        <h2>Images & Videos</h2>
                        <div class="imges">
                            
                            <?php echo  $array_get['images']['poster_img']['lable']; ?>
                            <?php echo $array_get['images']['video']['lable']; ?>
                            <p>
                            <?php   foreach($array_get['images']['caption']['total_img_vid'] as $div) {
                                        echo '<a href="'.$div['href'].'" target="_blank" >'.$div['lable'].' >></a><br>';
                                    } 
                                    ?>
                            </p>
                        </div>   
                        <h2>Description</h2>    
                        <div>
                            <?php echo $array_get['description']['summary']; ?>
                        </div>        
                        <div> 
                            <p><?php echo $array_get['description']['born']; ?></p>
                           <?php if($array_get['description']['death']){ ?>
                                <p><?php echo $array_get['description']['death']; ?></p>
                           <?php } ?>
                            
                        </div> 
                        <h2>Awards</h2>
                        <div>
                            <p><span>Awards</span><?php echo $array_get['awards']['awardss']; ?></p>
                        </div>  
                        
                    </div>

                <?php
            }
        }



        function init() {
            add_action('init', array($this, 'ocimdb_create_menu')); 
            add_action('add_meta_boxes', array($this, 'ocimdb_add_meta_box'));   
        }


        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        }
    }
    IMDBMAIN_menu::instance();
}
