<?php
/*
Plugin Name: Wordpress Plugin
Plugin URI:  
Description: To add portfolio items 
Version:     1.0.0
Author:      Romy Huzen
Author URI:  
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
?>

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<?php
class wp_simple_portfolio{
	
	//magic function (triggered on initialization)
	public function __construct(){
		
		add_action('init', array($this,'set_location_trading_hour_days')); //sets the default trading hour days (used by the content type)
		add_action('init', array($this,'register_location_content_type')); //register location content type
		add_action('add_meta_boxes', array($this,'add_location_meta_boxes')); //add meta boxes
		add_action('save_post_wp_locations', array($this,'save_location')); //save location
		add_action('admin_enqueue_scripts', array($this,'enqueue_admin_scripts_and_styles')); //admin scripts and styles
		add_action('wp_enqueue_scripts', array($this,'enqueue_public_scripts_and_styles')); //public scripts and styles
		add_filter('the_content', array($this,'prepend_location_meta_to_content')); //gets our meta data and dispayed it before the content
		
		register_activation_hook(__FILE__, array($this,'plugin_activate')); //activate hook
		register_deactivation_hook(__FILE__, array($this,'plugin_deactivate')); //deactivate hook
		
	}
	
	//set the default trading hour days (used in our admin backend)
	public function set_location_trading_hour_days(){}
	
	//register the location content type
	public function register_location_content_type(){
		 //Labels for post type
		 $labels = array(
            'name'               => 'Add Portfolio',
            'singular_name'      => 'Portfolio',
            'menu_name'          => 'Add Portfolio',
            'name_admin_bar'     => 'Add Portfolio',
            'add_new'            => 'Add New', 
            'add_new_item'       => 'Add New Portfolio Item',
            'new_item'           => 'New Portfolio Item', 
            'edit_item'          => 'Edit Portfolio Item',
            'view_item'          => 'View Portfolio Item',
            'all_items'          => 'All Portfolio Items',
            'search_items'       => 'Search Portfolio Items',
            'parent_item_colon'  => 'Parent Portfolio Item: ', 
            'not_found'          => 'No Portfolio Items found.', 
            'not_found_in_trash' => 'No Portfolio Items found in Trash.',
        );
        //arguments for post type
        $args = array(
            'labels'            => $labels,
            'public'            => true,
            'publicly_queryable'=> true,
            'show_ui'           => true,
            'show_in_nav'       => true,
            'query_var'         => true,
            'hierarchical'      => false,
            'supports'          => array('title','thumbnail','editor'),
            'has_archive'       => true,
            'menu_position'     => 20,
            'show_in_admin_bar' => true,
            'menu_icon'         => 'dashicons-location-alt',
            'rewrite'			=> array('slug' => 'locations', 'with_front' => 'true')
        );
        //register post type
        register_post_type('wp_locations', $args);
	}

	//adding meta boxes for the location content type*/
	public function add_location_meta_boxes(){
		
		add_meta_box(
			'wp_location_meta_box', //id
			'Portfolio Item', //name
			array($this,'location_meta_box_display'), //display function
			'wp_locations', //post type
			'normal', //location
			'default' //priority
		);
	}
	
	//display function used for our custom location meta box*/
	public function location_meta_box_display($post){
		
		//set nonce field
		wp_nonce_field('wp_location_nonce', 'wp_location_nonce_field');
		
		//collect variables
        $wp_team = get_post_meta($post->ID , 'wp_team', true); 
        $wp_team_size = get_post_meta($post->ID , 'wp_team_size', true); 
        $wp_team_role = get_post_meta($post->ID , 'wp_team_role', true);
		$wp_used_tech = get_post_meta($post->ID , 'wp_used_tech', true); 
        $wp_date = get_post_meta($post->ID , 'wp_date', true); 
        $wp_project = get_post_meta($post->ID , 'wp_project', true);
        $wp_code = get_post_meta($post->ID , 'wp_code', true); 
        $wp_github = get_post_meta($post->ID , 'wp_github', true);
        $wp_facebook = get_post_meta($post->ID , 'wp_facebook', true);
        $wp_linkedin = get_post_meta($post->ID , 'wp_linkedin', true);
        $wp_contact = get_post_meta($post->ID , 'wp_contact', true);
        
		?>
		<p>Meer informatie over uw project</p>
		<div class="field-container">
			<?php 
			//before main form elementst hook
			do_action('wp_location_admin_form_start'); 
			?>
            
			<div class="field">
				<label for="wp_team">Gewerkt in een team</label>
				<textarea name="wp_team" id="wp_team"><?php echo $wp_team;?></textarea>
			</div>
            
            <div class="field">
				<label for="wp_team_size">Teamgroote</label>
				<textarea type="number" name="wp_team_size" id="wp_team_size"><?php echo $wp_team_size;?></textarea>
			</div>
            
            <div class="field">
				<label for="wp_team_role">Mijn rol binnen het team</label>
				<textarea name="wp_team_role" id="wp_team_role"><?php echo $wp_team_role;?></textarea>
			</div>
            
            <div class="field">
				<label for="wp_used_tech">Gebruikte technieken</label>
				<textarea name="wp_used_tech" id="wp_used_tech"><?php echo $wp_used_tech;?></textarea>
			</div>
            
            <div class="field">
				<label for="wp_date">Datum van realisatie</label>
				<textarea type="date" name="wp_date" id="wp_date"><?php echo $wp_date;?></textarea>
			</div>
            
            <div class="field">
				<label for="wp_project">Link naar project/opdracht</label>
				<textarea name="wp_project" id="wp_project"><?php echo $wp_project;?></textarea>
			</div>
            
            <div class="field">
				<label for="wp_code">Link naar de code</label>
				<textarea name="wp_code" id="wp_code"><?php echo $wp_code;?></textarea>
			</div>
            
            <div class="field">
				<label for="wp_github"><i class="fab fa-github"></i></label>
				<textarea type="text" name="wp_github" id="wp_github"><?php echo $wp_github;?></textarea>
			</div>
            
            <div class="field">
				<label for="wp_facebook"><i class="fab fa-facebook-square"></i></label>
				<textarea name="wp_facebook" id="wp_facebook"><?php echo $wp_facebook;?></textarea>
			</div>
            
            <div class="field">
				<label for="wp_linkedin"><i class="fab fa-linkedin"></i></label>
				<textarea name="wp_linkedin" id="wp_linkedin"><?php echo $wp_linkedin;?></textarea>
			</div>	
            
            <div class="field">
				<label for="wp_contact">Neem contact op met mij</label>
                <small>Link om contact op te nemen met u</small>
				<textarea name="wp_contact" id="wp_contact"><?php echo $wp_contact;?></textarea>
			</div>
		<?php 
		//after main form elementst hook
		do_action('wp_location_admin_form_end'); 
		?>
		</div>
		<?php
		
	}
	
	//triggered on activation of the plugin (called only once)
	public function plugin_activate(){
		
		//call our custom content type function
	 	$this->register_location_content_type();
		//flush permalinks
		flush_rewrite_rules();
	}
	
	//trigered on deactivation of the plugin (called only once)
	public function plugin_deactivate(){
		//flush permalinks
		flush_rewrite_rules();
	}
	
	//append our additional meta data for the location before the main content (when viewing a single location)
	public function prepend_location_meta_to_content($content){
			
		global $post, $post_type;
		
		//display meta only on our locations (and if its a single location)
		if($post_type == 'wp_locations' && is_singular('wp_locations')){
			
			//collect variables
			$wp_location_id = $post->ID;
            $wp_team = get_post_meta($post->ID , 'wp_team', true); 
            $wp_team_size = get_post_meta($post->ID , 'wp_team_size', true); 
            $wp_team_role = get_post_meta($post->ID , 'wp_team_role', true);
		    $wp_used_tech = get_post_meta($post->ID , 'wp_used_tech', true); 
            $wp_date = get_post_meta($post->ID , 'wp_date', true); 
            $wp_project = get_post_meta($post->ID , 'wp_project', true);
            $wp_code = get_post_meta($post->ID , 'wp_code', true);
            $wp_github = get_post_meta($post->ID , 'wp_github', true);
            $wp_facebook = get_post_meta($post->ID , 'wp_facebook', true);
            $wp_linkedin = get_post_meta($post->ID , 'wp_linkedin', true);
            $wp_contact = get_post_meta($post->ID , 'wp_contact', true);
			
			//display
			$html = '';
            $html .= $content;
	
			$html .= '<section class="meta-data">';
			
			//hook for outputting additional meta data (at the start of the form)
			do_action('wp_location_meta_data_output_start',$wp_location_id);
			
			$html .= '<p>';
		
			if(!empty($wp_team)){
				$html .= '<b>Gewerkt in team: </b>' . $wp_team . '</br>';
			}
			
			if(!empty($wp_team_size)){
				$html .= '<b>Teamgrootte: </b>' . $wp_team_size . '</br>';
			}
		
			if(!empty($wp_team_role)){
				$html .= '<b>Mijn rol binnen het team: </b>' . $wp_team_role . '</br>';
			}
            
            if(!empty($wp_used_tech)){
				$html .= '<b>Gebruikte technieken: </b>' . $wp_used_tech . '</br>';
			}
            
            if(!empty($wp_date)){
				$html .= '<b>Datum realisatie: </b>' . $wp_date . '</br>';
			}
            
            if(!empty($wp_project)){
				$html .= '<b>Link naar het project/opdracht: </b><a href="' . $wp_project . '">' . $wp_project . '</a></br>';
			}
            
            if(!empty($wp_code)){
				$html .= '<b>Link naar de code: </b><a href="' . $wp_code . '">' . $wp_code . '</a></br>';
			}
            
            if(!empty($wp_github)){
				$html .= '<button type="button" class="icon_link"><a href="' . $wp_github . '"><i class="fab fa-github"></i></a></button>';
			}
            
            if(!empty($wp_facebook)){
				$html .= '<button type="button" class="icon_link"><a href="' . $wp_facebook . '"><i class="fab fa-facebook-square"></i></a></button>';
			}
            
            if(!empty($wp_linkedin)){
				$html .= '<button type="button" class="icon_link"><a href="' . $wp_linkedin . '"><i class="fab fa-linkedin"></i></a></button><br>';
			}
            
            if(!empty($wp_contact)){
				$html .= '<button type="button" class="contact"><a href="' . $wp_contact . '">Neem contact op met mij</a></button><br>';
			}
            
			$html .= '</p>';

			//hook for outputting additional meta data (at the end of the form)
			do_action('wp_location_meta_data_output_end',$wp_location_id);
			
			$html .= '</section>';
			
			
			return $html;	
				
			
		}else{
			return $content;
		}

	}

	//main function for displaying locations (used for our shortcodes and widgets)
	public function get_locations_output($arguments = ""){
			
		//default args
		$default_args = array(
			'location_id'	=> '',
			'number_of_locations'	=> -1
		);
		
		//update default args if we passed in new args
		if(!empty($arguments) && is_array($arguments)){
			//go through each supplied argument
			foreach($arguments as $arg_key => $arg_val){
				//if this argument exists in our default argument, update its value
				if(array_key_exists($arg_key, $default_args)){
					$default_args[$arg_key] = $arg_val;
				}
			}
		}
		
		//output
		$html = '';

		$location_args = array(
			'post_type'		=> 'wp_locations',
			'posts_per_page'=> $default_args['number_of_locations'],
			'post_status'	=> 'publish'
		);
		//if we passed in a single location to display
		if(!empty($default_args['location_id'])){
			$location_args['include'] = $default_args['location_id'];
		}
		
		$locations = get_posts($location_args);
		//if we have locations 
		if($locations){
			$html .= '<article class="location_list cf">';
			//foreach location
			foreach($locations as $location){
				$html .= '<section class="location">';
					//collect location data
					$wp_location_id = $location->ID;
					$wp_location_title = get_the_title($wp_location_id);
					$wp_location_thumbnail = get_the_post_thumbnail($wp_location_id,'thumbnail');
					$wp_location_content = apply_filters('the_content', $location->post_content);
					if(!empty($wp_location_content)){
						$wp_location_content = strip_shortcodes(wp_trim_words($wp_location_content, 40, '...'));
					}
					$wp_location_permalink = get_permalink($wp_location_id);
					$wp_team = get_post_meta($post->ID , 'wp_team', true); 
                    $wp_team_size = get_post_meta($post->ID , 'wp_team_size', true); 
                    $wp_team_role = get_post_meta($post->ID , 'wp_team_role', true);
		            $wp_used_tech = get_post_meta($post->ID , 'wp_used_tech', true); 
                    $wp_date = get_post_meta($post->ID , 'wp_date', true); 
                    $wp_project = get_post_meta($post->ID , 'wp_project', true);
                    $wp_code = get_post_meta($post->ID , 'wp_code', true);
                    $wp_github = get_post_meta($post->ID , 'wp_github', true);
                    $wp_facebook = get_post_meta($post->ID , 'wp_facebook', true);
                    $wp_linkedin = get_post_meta($post->ID , 'wp_linkedin', true);
                    $wp_contact = get_post_meta($post->ID , 'wp_contact', true);
					
					//title
					$html .= '<h2 class="title">';
						$html .= '<a href="' . $wp_location_permalink . '" title="view portfolio item">';
							$html .= $wp_location_title;
						$html .= '</a>';
					$html .= '</h2>';
					
				
					//image & content
					if(!empty($wp_location_thumbnail) || !empty($wp_location_content)){
								
						$html .= '<p class="image_content">';
						if(!empty($wp_location_thumbnail)){
							$html .= $wp_location_thumbnail;
						}
						if(!empty($wp_location_content)){
							$html .=  $wp_location_content;
						}
						
						$html .= '</p>';
					}
					
                
					//readmore
					$html .= '<a class="link" href="' . $wp_location_permalink . '" title="view location">View Portfolio Item</a>';
				$html .= '</section>';
			}
			$html .= '</article>';
			$html .= '<div class="cf"></div>';
		}
		
		return $html;
	}
	
	
	
	//triggered when adding or editing a location
	public function save_location($post_id){
		
		//check for nonce
		if(!isset($_POST['wp_location_nonce_field'])){
			return $post_id;
		}	
		//verify nonce
		if(!wp_verify_nonce($_POST['wp_location_nonce_field'], 'wp_location_nonce')){
			return $post_id;
		}
		//check for autosave
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
			return $post_id;
		}
        
        /*
            $wp_team = get_post_meta($post->ID , 'wp_team', true); 
            $wp_team_size = get_post_meta($post->ID , 'wp_team_size', true); 
            $wp_team_role = get_post_meta($post->ID , 'wp_team_role', true);
		    $wp_used_tech = get_post_meta($post->ID , 'wp_used_tech', true); 
            $wp_date = get_post_meta($post->ID , 'wp_date', true); 
            $wp_project = get_post_meta($post->ID , 'wp_project', true);
            $wp_code = get_post_meta($post->ID , 'wp_code', true);
        */
	
		//get our phone, email and address fields
		$wp_team = isset($_POST['wp_team']) ? sanitize_text_field($_POST['wp_team']) : '';
		$wp_team_size = isset($_POST['wp_team_size']) ? sanitize_text_field($_POST['wp_team_size']) : '';
		$wp_team_role = isset($_POST['wp_team_role']) ? sanitize_text_field($_POST['wp_team_role']) : '';
        $wp_used_tech = isset($_POST['wp_used_tech']) ? sanitize_text_field($_POST['wp_used_tech']) : '';
        $wp_date = isset($_POST['wp_date']) ? sanitize_text_field($_POST['wp_date']) : '';
        $wp_project = isset($_POST['wp_project']) ? sanitize_text_field($_POST['wp_project']) : '';
        $wp_code = isset($_POST['wp_code']) ? sanitize_text_field($_POST['wp_code']) : '';
        $wp_github = isset($_POST['wp_github']) ? sanitize_text_field($_POST['wp_github']) : '';
        $wp_facebook = isset($_POST['wp_facebook']) ? sanitize_text_field($_POST['wp_facebook']) : '';
        $wp_linkedin = isset($_POST['wp_linkedin']) ? sanitize_text_field($_POST['wp_linkedin']) : '';
        $wp_contact = isset($_POST['wp_contact']) ? sanitize_text_field($_POST['wp_contact']) : '';
		
		//update phone, memil and address fields
		update_post_meta($post_id, 'wp_team', $wp_team);
		update_post_meta($post_id, 'wp_team_size', $wp_team_size);
		update_post_meta($post_id, 'wp_team_role', $wp_team_role);
        update_post_meta($post_id, 'wp_used_tech', $wp_used_tech);
        update_post_meta($post_id, 'wp_date', $wp_date);
        update_post_meta($post_id, 'wp_project', $wp_project);
        update_post_meta($post_id, 'wp_code', $wp_code);
        update_post_meta($post_id, 'wp_github', $wp_github);
        update_post_meta($post_id, 'wp_facebook', $wp_facebook);
        update_post_meta($post_id, 'wp_linkedin', $wp_linkedin);
        update_post_meta($post_id, 'wp_contact', $wp_contact);
		
		//search for our trading hour data and update
		foreach($_POST as $key => $value){
			//if we found our trading hour data, update it
			if(preg_match('/^wp_location_trading_hours_/', $key)){
				update_post_meta($post_id, $key, $value);
			}
		}
		
		//location save hook 
		//used so you can hook here and save additional post fields added via 'wp_location_meta_data_output_end' or 'wp_location_meta_data_output_end'
		do_action('wp_location_admin_save',$post_id);
		
	}
	
	//enqueus scripts and stles on the back end
	public function enqueue_admin_scripts_and_styles(){
		wp_enqueue_style('wp_location_admin_styles', plugin_dir_url(__FILE__) . '/css/wp_location_admin_styles.css');
	}
	
	//enqueues scripts and styled on the front end
	public function enqueue_public_scripts_and_styles(){
		wp_enqueue_style('wp_location_public_styles', plugin_dir_url(__FILE__). '/css/wp_location_public_styles.css');
		
	}
	
}
$wp_simple_locations = new wp_simple_portfolio;

//include shortcodes
include(plugin_dir_path(__FILE__) . 'inc/wp_location_shortcode.php');
//include widgets
include(plugin_dir_path(__FILE__) . 'inc/wp_location_widget.php');



?>