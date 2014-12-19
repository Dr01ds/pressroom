<?php

class PR_options_page {

  /**
   * constructor method
   * Add class functions to wordpress hooks
   *
   * @void
   */
  public function __construct() {

    if( !is_admin() ) {
      return;
    }

    add_action( 'admin_enqueue_scripts', array( $this, 'add_chosen_script' ) );
    add_action( 'admin_footer', array( $this, 'add_custom_script' ) );

    add_action( 'admin_menu', array( $this, 'pr_add_admin_menu' ) );
    add_action( 'admin_init', array( $this, 'pr_settings_init' ) );
    add_filter( 'pre_update_option_pr_settings', array( $this, 'pr_save_options' ), 10, 2 );
  }

  /**
   * Add options page to wordpress menu
   */
  public function pr_add_admin_menu() {

    add_menu_page( 'pressroom', 'Pressroom', 'manage_options', 'pressroom', array( $this, 'pr_options_page' ) );
    add_submenu_page('pressroom', __('Settings'), __('Settings'), 'manage_options', 'pressroom', array( $this, 'pr_options_page' ));
  }

  /**
   * add option to database
   *
   * @void
   */
  protected function pr_settings_exist() {

  	if( false == get_option( 'pressroom_settings' ) ) {

  		add_option( 'pressroom_settings' );

  	}

  }

  /**
   * register section field
   *
   * @void
   */
  public function pr_settings_init() {

  	register_setting( 'pressroom', 'pr_settings' );

  	add_settings_section(
  		'pr_pressroom_section',
  		'',
  		array( $this, 'pr_settings_section_callback' ),
  		'pressroom'
  	);

    add_settings_field(
      'pr_sharing_domain',
      __( 'Sharing Domain', 'pressroom' ),
      array( $this, 'pr_sharing_domain' ),
      'pressroom',
      'pr_pressroom_section'
    );

    add_settings_field(
      'custom_post_type',
      __( 'Connected custom post types', 'pressroom' ),
      array( $this, 'pr_custom_post_type' ),
      'pressroom',
      'pr_pressroom_section'
    );
  }

  public function pr_sharing_domain() {

    $options = get_option( 'pr_settings' );
    $value = isset( $options['pr_sharing_domain'] ) ? $options['pr_sharing_domain'] : '';
    $html = '<input type="text" placeholder="' . get_site_url() . '" name="pr_settings[pr_sharing_domain]" value="' . $value . '">';
    echo $html;
  }

  /**
   * Render custom_post_type field
   *
   * @void
   */
  public function pr_custom_post_type() {

  	$options = get_option( 'pr_settings' );
    $value = isset( $options['pr_custom_post_type'] ) ? ( is_array( $options['pr_custom_post_type'] ) ? implode( ',', $options['pr_custom_post_type'] ) : $options['pr_custom_post_type'] ) : '';
  	$html = '<input id="pr_custom_post_type" type="text" name="pr_settings[pr_custom_post_type]" value="' . $value . '">';
  	echo $html;
  }

  /**
   * render setting section
   *
   * @echo
   */
  public function pr_settings_section_callback() {

  	echo __( '<hr/>', 'pressroom' );

  }

  /**
   * Render option page form
   *
   * @echo
   */
  public function pr_options_page() {
  ?>
    <form action='options.php' method='post'>
    <h2>Pressroom Options</h2>
  <?php
  	settings_fields( 'pressroom' );
  	do_settings_sections( 'pressroom' );
  	submit_button();
  ?>
  	</form>
  <?php
  }

  /**
   * filter value before saving.
   *
   * @param array $new_value
   * @param array $old_value
   * @return array $new_value
   */
  public function pr_save_options( $new_value, $old_value ) {

    if( isset( $new_value['pr_custom_post_type'] ) ) {
      $post_type = is_array( $new_value['pr_custom_post_type'] ) ? $new_value['pr_custom_post_type'] : explode( ',', $new_value['pr_custom_post_type'] );
      $new_value['pr_custom_post_type'] = $post_type;
    }

    return $new_value;
  }

  /**
   * add custom script to metabox
   *
   * @void
   */
  public function add_custom_script() {

    global $pagenow;
    if( $pagenow == 'admin.php' && $_GET['page'] == 'pressroom' ) {
      wp_register_script( 'options_page', PR_ASSETS_URI . '/js/pr.option_page.js', array( 'jquery' ), '1.0', true );
      wp_enqueue_script( 'options_page' );
    }
  }

  /**
   * add chosen.js to metabox
   *
   * @void
   */
  public function add_chosen_script() {

    global $pagenow;
    if( $pagenow == 'admin.php' && $_GET['page'] == 'pressroom' ) {
      wp_enqueue_style( 'chosen', PR_ASSETS_URI . 'css/chosen.min.css' );
      wp_register_script( 'chosen', PR_ASSETS_URI . '/js/chosen.jquery.min.js', array( 'jquery'), '1.0', true );
      wp_enqueue_script( 'chosen' );
      wp_enqueue_style( 'tagsinput', PR_ASSETS_URI . 'css/jquery.tagsinput.css' );
      wp_register_script( 'tagsinput', PR_ASSETS_URI . '/js/jquery.tagsinput.min.js', array( 'jquery'), '1.0', true );
      wp_enqueue_script( 'tagsinput' );
    }
  }
}

$pr_options_page = new PR_options_page();
