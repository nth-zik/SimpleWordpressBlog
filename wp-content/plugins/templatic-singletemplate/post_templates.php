<?php
/*
Plugin Name: Custom Post Template By Templatic
Plugin URI: https://wordpress.org/plugins/templatic-singletemplate/
Description: This plugin allows theme authors to include single post templates, It is much like how admin use page template files while createing a page.
Author: Templatic
Author URI: https://www.templatic.com/
Version: 1.1
*/

/*	Plugin activation hook 	*/
register_activation_hook(__FILE__,'tmpl_add_post_template_message');	
if(!function_exists('tmpl_add_post_template_message'))
{
	function tmpl_add_post_template_message()
	{
		update_option('tmpl_post_detail_plugin_active', 'true');
	}
}

/*	Plugin deactivation hook */
register_deactivation_hook(__FILE__,'tmpl_remove_post_template_message');	
function tmpl_remove_post_template_message()
{
	delete_option('tmpl_post_detail_plugin_active');
}

add_action('admin_init','tmpl_showactivation_message');
/* show message while plugin activation */
if(!function_exists('tmpl_showactivation_message'))
{
	function tmpl_showactivation_message()
	{
		if(get_option('tmpl_post_detail_plugin_active') == 'true'){
			update_option('tmpl_post_detail_plugin_active', 'false');
			wp_safe_redirect(admin_url('plugins.php?act_plug=tmpl_post_template'));
			exit;
		}
		if( isset($_GET['act_plug']) && $_GET['act_plug'] == "tmpl_post_template" ){
		?>
			<div id="message" class="updated"><p><?php  echo sprintf(__("Templatic Post Detail Plugin is active now, <a href='%s'>click here</a> to get started. For detailed information, refer readme.txt and screenshot contain in plugin folder.",'templatic-posttemplate'),admin_url( 'post-new.php' ));?></p></div>
		<?php
		}
	}
}

/* Define templatic post template class */
class Templatic_Post_Template_Plugin {

	/* set the default constuctor of single post template */
	function __construct() {

		$locale = get_locale();
		
		/* Plugin Folder Path*/
		define( 'TEMPLATIC_POST_TEMPLATE_DIR', plugin_dir_path( __FILE__ ) );
		
		/* localization file for translation of plugins string */
		load_textdomain( 'templatic-posttemplate', TEMPLATIC_POST_TEMPLATE_DIR .'languages/'.$locale.'.mo' );
		
		/* Show metabox of page template */
		add_action( 'admin_menu', array( $this, 'tmpl_add_metabox' ) );
		
		/* save post type template for post type */
		add_action( 'save_post', array( $this, 'tmpl_metabox_save' ), 1, 2 );
	
		/* include the template saved in post template else return default */
		add_filter( 'single_template', array( $this, 'tmpl_get_post_template' ),20 );

	}
	
	/* include the template saved in post template else return default */
	function tmpl_get_post_template($template) {

		global $post,$template;

		$tmpl_selected_template = get_post_meta( $post->ID, 'tmpl_wp_post_template', true );

		/* if default posttype template is selected than return the default post template */
		if( ! $tmpl_selected_template ){ 
			return  $template;
		}

		/* Prevent directory traversal */
		$tmpl_selected_template = str_replace( '..', '', $tmpl_selected_template );
		if( file_exists( get_stylesheet_directory() . "/{$tmpl_selected_template}" ) ){
			$template = get_stylesheet_directory() . "/{$tmpl_selected_template}";
		}elseif( file_exists( get_template_directory() . "/{$tmpl_selected_template}" ) ){ 
			$template = get_template_directory() . "/{$tmpl_selected_template}";
		}
		
		return $template;
		
	}
	
	/* read each file from theme directory and search for  'PostType Page Template' */
	function tmpl_get_post_templates() {

		$templates = wp_get_theme()->get_files( 'php', 1 );
		$post_templates = array();

		$base = array( trailingslashit( get_template_directory() ), trailingslashit( get_stylesheet_directory() ) );
		
		foreach ( (array) $templates as $file => $full_path ) {

			if ( ! preg_match( '|PostType Page Template:(.*)$|mi', file_get_contents( $full_path ), $header ) )
				continue;

			$post_templates[ $file ] = _cleanup_header_comment( $header[1] );

		}
		return $post_templates;

	}
	
	/* show post template dropdown in backend while adding psot*/
	function tmpl_post_templates_dropdown() {

		global $post;

		$post_templates = $this->tmpl_get_post_templates();

		/* Loop through templates, make them options */
		foreach ( (array) $post_templates as $template_file => $template_name ) {
			
			$selected = ( $template_file == get_post_meta( $post->ID, 'tmpl_wp_post_template', true ) ) ? ' selected="selected"' : '';
			
			$opt = '<option value="' . esc_attr( $template_file ) . '"' . $selected . '>' . esc_html( $template_name ) . '</option>';
			
			echo $opt;
		}

	}

	/* add meta box to select post template while adding post */
	function tmpl_add_metabox() {
		
		if ( $this->tmpl_get_post_templates() )
		{
			
			/* names or objects */
			$output = 'objects'; 
			$args = array();

			/* names or objects, note names is the default */
			$output = 'names'; 
			
			/* 'and' or 'or' */
			$operator = 'and'; 

			$post_types = get_post_types( $args, $output, $operator ); 

			$exclude_post_type = apply_filters('tmpl_unset_post_type',array('page','attachment','revision','nav_menu_item'));
			
			/* loop for post type to show post detail template */
			foreach ( $post_types  as $post_type ) {

				if(in_array($post_type,$exclude_post_type))
					continue;
			   
			   /*show single page template for custom post type*/
				add_meta_box( 'tmpl_post_templates', __( 'Post Detail Template', 'templatic-posttemplate' ), array( $this, 'tmpl_metabox' ), $post_type, 'side', 'high','' );
			}
			
		}
		
	}
	
	/* html in meta box */
	function tmpl_metabox( $post ) {

		?>
		<input type="hidden" name="tmpl_post_template_nonce" id="tmpl_post_template_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />

		<label class="hidden" for="post_template"><?php  _e( 'Post Template', 'templatic-posttemplate' ); ?></label><br />
		<select name="tmpl_wp_post_template" id="post_template" class="dropdown">
			<option value=""><?php _e( 'Default', 'templatic-posttemplate' ); ?></option>
			<?php $this->tmpl_post_templates_dropdown(); ?>
		</select><br /><?php
		_e("Some themes have custom templates to use on single posts. This custom templates usually have additional features or custom layouts for the posts. If so, you'll see them listed in the dropdown above.",'templatic-posttemplate');

	}

	/* save post template selected while add/edit post */
	function tmpl_metabox_save( $post_id, $post ) {

		/*
		 * Verify this came from our screen and with proper authorization,
		 * because save_post can be triggered at other times
		 */
		 
		if ( ! wp_verify_nonce( $_POST['tmpl_post_template_nonce'], plugin_basename( __FILE__ ) ) )
			return $post->ID;

		/** Is the user allowed to edit the post or page? */
		if ( 'page' == $_POST['post_type'] )
			if ( ! current_user_can( 'edit_page', $post->ID ) )
				return $post->ID;
		else
			if ( ! current_user_can( 'edit_post', $post->ID ) )
				return $post->ID;

		
		/** Put the data into an array to make it easier to loop though and save */
		$mydata['tmpl_wp_post_template'] = $_POST['tmpl_wp_post_template'];

		/** Add values of $mydata as custom fields */
		foreach ( $mydata as $key => $value ) {
			/** Don't store custom data twice */
			if( 'revision' == $post->post_type )
				return;

			/** If $value is an array, make it a CSV (unlikely) */
			$value = implode( ',', (array) $value );

			/** Update the data if it exists, or add it if it doesn't */
			if( get_post_meta( $post->ID, $key, false ) )
				update_post_meta( $post->ID, $key, $value );
			else
				add_post_meta( $post->ID, $key, $value );

			/** Delete if blank */
			if( ! $value )
				delete_post_meta( $post->ID, $key );
		}

	}

}

/*
 * Instantiate the class.
 */
$tmpl = new Templatic_Post_Template_Plugin(); // go
