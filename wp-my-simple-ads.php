<?php
/*
Plugin Name: WP My Simple Ads
Plugin URI: http://www.tealictafrica.com//
Description: Creates a an ad manager using custom post types. WordPressed by Alex Muturi
Version: 1.0
Author: Alex Muturi
Author URI: http://www.tealictafrica.com/
License: GPL2
*/

/*  Copyright 2012  WP My Simple Ads - Alex Muturi (alex@tealictafrica.com)
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// Initiate Custom Post Type
	add_action('init', 'create_ad');
	function create_ad() {
    	$ad_args = array(
        	'label' => __('WP My Simple Ads'),
			'singular_label' => __('ad'),
        	'public' => true,
        	'capability_type' => 'post',
        	'hierarchical' => false,
        	'rewrite' => true,
			'menu_icon' => WP_PLUGIN_URL .'/wp-my-simple-ads/images/slider.gif',
        	'supports' => array('thumbnail', 'title', 'editor', 'page-attributes')
        );
    	register_post_type('ads',$ad_args);
	}	

	add_action("admin_init", "add_ad_options");

	// Save Metaboxes
	add_action('save_post', 'update_ad_link'); 
	add_action('save_post', 'update_ad_new_window');                                                 		
	
	// Adds Metaboxes
	function add_ad_options(){
		add_meta_box("ad_link_details", "Add a link to this advert ", "ad_link_options", "ads", "normal", "low");  
	}
	
	// Print Metaboxes
	// Rotator Link
	function ad_link_options(){
		global $post;
 		
		$ad_link =  get_post_meta($post->ID, 'ad_link', true);
		$ad_new_window = get_post_meta($post->ID, 'ad_new_window', true);  
	?>
	<div id="ad-options">
		<input name="ad_link" size="100" value="<?php echo $ad_link; ?>" /> <input type = "checkbox" <?php if($ad_new_window == 'on') echo 'checked'; ?> name = "ad_new_window"> Open link in new window  <br />  
      <p><em>Ex: http://www.domain.com/pagename or /about. If you are linking to an external site, you must include http://</em></p>
	</div><!--end ad-options-->   
	<?php
	}
	
	
	// Saves to Post Meta
	function update_ad_link(){
		global $post;
		update_post_meta($post->ID, "ad_link", $_POST["ad_link"]);
	}  
	
	function update_ad_new_window(){
		global $post;
		update_post_meta($post->ID, "ad_new_window", $_POST["ad_new_window"]);
	}     


add_shortcode('show_ads', 'show_ads_posts');

function show_ads_posts($atts) {
	//Here's our defaults
	extract(shortcode_atts(array(
        'id' => 1,
        'height' => "100",  // default value if none supplied
        'width' => "150",  // default value if none supplied
        'no_ads' => "2"  // default value if none supplied
  ), $atts));

?>

<style type="text/css">
	#simple_ad div{
		height:<?php echo $height; ?>px;
		width:<?php echo $width; ?>px;
		margin-bottom:15px;
	}
</style>

<script type="text/javascript">
 
</script>
		
<div id="simple_ad">

<?php 
	
	query_posts( 'post_type=ads&posts_per_page='.$no_ads); 
	if( have_posts() ) : while( have_posts() ) : the_post(); 

		$ad_title = get_the_title();
		$ad_link = get_post_permalink();
		$adLink = get_post_meta( get_the_ID(), 'ad_link', true );     
		$ad_link = ($adLink !='')?  $adLink : $link;

		$adNewWindow = get_post_meta( get_the_ID(), 'ad_new_window', true );   
		$ad_target = ($adNewWindow !='')? '_blank' : '_self';

		if(has_post_thumbnail()) : ?>
		<div> 
			<?php the_post_thumbnail('post_thumb',array('title'=>$title,'desc'=>$desc,'ad_link'=>$ad_link,'target'=>$ad_target)); ?>
		</div>
		<?php endif; ?>
	<?php endwhile; endif;?>
	<?php wp_reset_query();?>
</div>

<?php } 

?>
