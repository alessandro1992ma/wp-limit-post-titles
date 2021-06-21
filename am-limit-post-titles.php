<?php
/*
Plugin Name:          AM - Limit Post Titles
Plugin URI:           https://alessandromagri.eu/plugins/am-limit-post-titles
Description:          Limit the number of characters for post type titles
Version:              1.0.0
Author:               Alessandro Magri
Author URI:           https://alessandromagri.eu/
*/

class AmLimitPostTitles {

	/**
	 * Stores the plugins options
	 * @var array
	 */
	private $options;

	/**
	 * List of post types to ignore
	 * @var array
	 */
	private $ignore = array(
		'attachment',
		'revision',
		'nav_menu_item',
        'acf-field',
        'acf-field-group',
        'oembed_cache',
        'user_request',
        'wp_block',
        'customize_changeset',
        'custom_css'
	);

	public function __construct()
	{
		$this->options = get_option('am_title_options');
		add_action('admin_enqueue_scripts', array($this, 'enqueuer'));
		add_action('admin_menu', array($this, 'create_settings_page'));
		add_action('admin_init', array($this, 'register_settings'));
	}

	/**
	 * Enqueue styles and JS
	 */
	public function enqueuer()
	{
		// We only need to enqueue if it's a particular post type page and the limit is > 0
		$p = get_current_screen();
		if(is_admin() && isset($this->options['post_types'][$p->post_type]) && isset($this->options['limit']) && $this->options['limit'] > 0)
		{
            wp_register_style('limit-post-titles-style', plugin_dir_url(__FILE__).'limit-post-titles.css', false, '1.0.0');
			wp_enqueue_style('limit-post-titles-style');
			wp_enqueue_script('limit-post-titles', plugin_dir_url(__FILE__).'limit-post-titles.js');
			wp_localize_script('limit-post-titles', 'am_post_titles', array(
				'limit' => $this->options['limit'],
			));
		}
	}

	/**
	 * Create the options page in WordPress
	 */
	public function create_settings_page()
	{
		add_options_page('Limit Post Titles', 'AM - Limit Post Titles', 'delete_others_posts', 'am_limiter', array($this, 'load_settings_page'));
	}

	/**
	 * Load the template for the settings page
	 */
	public function load_settings_page()
	{
		require_once __DIR__.'/settings.php';
	}

	/**
	 * Register settings and fields
	 */
	public function register_settings()
	{
		register_setting('am_title_group', 'am_title_options', array($this, 'sanitize'));
        add_settings_section('am_title_section', 'Settings', array( $this, 'section_callback'), 'am_limiter');

        add_settings_field('limit', 'Character Limit', array($this, 'limit_callback'), 'am_limiter', 'am_title_section');
		add_settings_field('post_types', 'Post Types', array($this, 'post_types_callback'), 'am_limiter', 'am_title_section');
	}

	/**
	 * Section Callback
	 */
	public function section_callback()
	{
		echo 'Inserisci il limite di caratteri e seleziona i tipi di post che desideri limitare. L\'inserimento di un limite di caratteri pari a 0 disabiliterà il plugin.';
	}

	/**
	 * Callback for the limit filed
	 */
	public function limit_callback()
	{
		printf('<input type="text" id="limit" name="am_title_options[limit]" value="%s">', isset($this->options['limit']) ? esc_attr($this->options['limit']) : 0);
	}

	/**
	 * Callback for the list of post types
	 */
	public function post_types_callback()
	{

		echo '<ul>';
		foreach(get_post_types() as $pt){
			if(in_array($pt, $this->ignore)) continue;
            $singularName = get_post_type_object( $pt )->labels->singular_name;
			printf('<li><label><input type="checkbox" name="am_title_options[post_types][%s]" %s> %s - '. $singularName .'</label></li>', $pt, (isset($this->options['post_types'][$pt])) ? 'checked' : '', $pt);
		}
		echo '</ul>';
	}

	/**
	 * Sanitize the content input in the plugin
	 * @param  array $input Unsanitized content
	 * @return array Sanitized content
	 */
	public function sanitize($input)
	{
		$input['limit'] = (int) $input['limit'];
		return $input;
	}

}

new AmLimitPostTitles();
