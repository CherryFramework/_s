<?php
/**
 * Example using Cherry Framework V.
 */

$setup_file = get_template_directory() . '/cherry-framework/setup.php';

// Load Cherry V core - below hooks and their priorities are very important.
add_action( 'after_setup_theme', require( $setup_file ),          0 );
add_action( 'after_setup_theme', '_s_get_core',                   1 );
add_action( 'after_setup_theme', 'Cherry_Core::load_all_modules', 2 );

// Load the widget.
add_action( 'after_setup_theme', '_s_include_widget',  9 );

// Initialization of modules.
add_action( 'after_setup_theme', '_s_init_modules',   10 );

/**
 * Loads the core functions.
 *
 * These files are needed before loading anything else in the
 * theme because they have required functions for use.
 */
function _s_get_core() {
	global $chery_core_version;
	static $core = null;

	if ( null !== $core ) {
		return $core;
	}

	if ( 0 < sizeof( $chery_core_version ) ) {
		$core_paths = array_values( $chery_core_version );
		require_once( $core_paths[0] );

	} else {
		die( 'Class Cherry_Core not found' );
	}

	$core = new Cherry_Core( array(
		'base_dir' => get_template_directory() . '/cherry-framework',
		'base_url' => get_template_directory_uri() . '/cherry-framework',
		'modules'  => array(
			'cherry-breadcrumbs' => array(
				'autoload' => false,
			),
			'cherry-term-meta' => array(
				'autoload' => false,
			),
			'cherry-post-meta' => array(
				'autoload' => false,
			),
			'cherry-interface-builder' => array(
				'autoload' => false,
			),
			'cherry-customizer' => array(
				'autoload' => false,
			),
			'cherry-dynamic-css' => array(
				'autoload' => false,
			),
			'cherry-google-fonts-loader' => array(
				'autoload' => false,
			),
			'cherry-widget-factory' => array(
				'autoload' => false,
			),
			'cherry-js-core' => array(
				'autoload' => true,
			),
			'cherry-ui-elements' => array(
				'autoload' => false,
			),
		),
	) );

	return $core;
}

/**
 * Load the widget.
 *
 * If feature used a Cherry Framework functionality, that it is required after core initialization.
 */
function _s_include_widget() {
	require get_template_directory() . '/cherry-framework-example/inc/class-simple-widget.php';
}

/**
 * Initialization of modules.
 */
function _s_init_modules() {
	/**
	 * Init `cherry-post-meta` - module for manage post metadata.
	 *
	 * How to use?
	 *
	 * In functions.php paste this simple code:
	 *
	 *     add_filter( 'body_class', '_s_add_layout_class' );
	 *     function _s_add_layout_class( $classes ) {
	 *         $sidebar_position = get_post_meta( get_the_ID(), '_s_sidebar_position', true );
	 *
	 *         if ( ! empty( $sidebar_position ) ) {
	 *             $classes[] = '_s-layout--' . esc_attr( $sidebar_position );
	 *         }
	 *
	 *         return $classes;
	 *     }
	 *
	 * After this you have a CSS-class in `<body>` tag for controlling site layout:
	 *     _s-layout--content-sidebar
	 *     _s-layout--sidebar-content
	 *     _s-layout--inherit
	 */
	_s_get_core()->init_module( 'cherry-post-meta', array(
		'id'       => '_s-layout',
		'title'    => esc_html__( 'Layout Options', '_s' ),
		'page'     => array( 'post', 'page' ),
		'context'  => 'normal',
		'priority' => 'high',
		'fields'   => array(
			'_s_sidebar_position' => array(
				'type'    => 'select',
				'title'   => esc_html__( 'Layout', '_s' ),
				'value'   => 'inherit',
				'options' => array(
					'inherit'         => esc_html__( 'Inherit', '_s' ),
					'content-sidebar' => esc_html__( 'Sidebar on right side', '_s' ),
					'sidebar-content' => esc_html__( 'Sidebar on left side', '_s' ),
				),
			),
		),
	) );

	/**
	 * Init `cherry-term-meta` - module for manage terms metadata.
	 *
	 * How to use?
	 *
	 * In template (e.g. archive.php) paste this simple code:
	 *
	 *     $thumbnail_id = get_term_meta( get_queried_object_id(), '_s_term_thumbnail', true );
	 *
	 *     if ( ! empty( $thumbnail_id ) ) {
	 *         echo wp_get_attachment_image( $thumbnail_id );
	 *     }
	 */
	_s_get_core()->init_module( 'cherry-term-meta', array(
		'tax'      => 'category',
		'priority' => 10,
		'fields'   => array(
			'_s_term_thumbnail' => array(
				'type'               => 'media',
				'multi_upload'       => false,
				'library_type'       => 'image',
				'upload_button_text' => esc_html__( 'Set thumbnail', '_s' ),
			),
		),
	) );

	/**
	 * Init `cherry-customizer` - simple wrapper for Customizer API.
	 *
	 * How to use? - Example below
	 */
	_s_get_core()->init_module( 'cherry-customizer', array(
		'prefix'     => '_s',
		'capability' => 'edit_theme_options',
		'type'       => 'theme_mod',
		'options'    => array(

			/* Breadcrumbs panel */
			'breadcrumbs' => array(
				'title'    => esc_html__( 'Breadcrumbs', '_s' ),
				'priority' => 30,
				'type'     => 'panel',
			),

			/* General section */
			'breadcrumbs_general' => array(
				'title'    => esc_html__( 'General', '_s' ),
				'priority' => 1,
				'panel'    => 'breadcrumbs',
				'type'     => 'section',
			),
			'breadcrumbs_visibillity' => array(
				'title'   => esc_html__( 'Enable Breadcrumbs', '_s' ),
				'section' => 'breadcrumbs_general',
				'default' => true,
				'field'   => 'checkbox',
				'type'    => 'control',
			),
			'breadcrumbs_front_visibillity' => array(
				'title'   => esc_html__( 'Enable Breadcrumbs on front page', '_s' ),
				'section' => 'breadcrumbs_general',
				'default' => false,
				'field'   => 'checkbox',
				'type'    => 'control',
			),
			'breadcrumbs_browse_label' => array(
				'title'   => esc_html__( 'Browse label', '__tm' ),
				'section' => 'breadcrumbs_general',
				'default' => esc_html__( 'Browse:', '_s' ),
				'field'   => 'text',
				'type'    => 'control',
			),
			'breadcrumbs_page_title' => array(
				'title'   => esc_html__( 'Enable page title in breadcrumbs area', '_s' ),
				'section' => 'breadcrumbs_general',
				'default' => true,
				'field'   => 'checkbox',
				'type'    => 'control',
			),
			'breadcrumbs_path_type' => array(
				'title'   => esc_html__( 'Show full/minified path', '_s' ),
				'section' => 'breadcrumbs_general',
				'default' => 'full',
				'field'   => 'select',
				'choices' => array(
					'full'     => esc_html__( 'Full', '_s' ),
					'minified' => esc_html__( 'Minified', '_s' ),
				),
				'type' => 'control',
			),

			/* Typography section */
			'breadcrumbs_typography' => array(
				'title'    => esc_html__( 'Typography', '_s' ),
				'priority' => 2,
				'panel'    => 'breadcrumbs',
				'type'     => 'section',
			),
			'breadcrumbs_font_family' => array(
				'title'   => esc_html__( 'Font Family', '_s' ),
				'section' => 'breadcrumbs_typography',
				'default' => 'Montserrat, sans-serif',
				'field'   => 'fonts',
				'type'    => 'control',
			),
			'breadcrumbs_font_style' => array(
				'title'   => esc_html__( 'Font Style', '_s' ),
				'section' => 'breadcrumbs_typography',
				'default' => 'normal',
				'field'   => 'select',
				'choices' => array(
					'normal'  => esc_html__( 'Normal', '_s' ),
					'italic'  => esc_html__( 'Italic', '_s' ),
					'oblique' => esc_html__( 'Oblique', '_s' ),
					'inherit' => esc_html__( 'Inherit', '_s' ),
				),
				'type' => 'control',
			),
			'breadcrumbs_font_weight' => array(
				'title'   => esc_html__( 'Font Weight', '_s' ),
				'section' => 'breadcrumbs_typography',
				'default' => '400',
				'field'   => 'select',
				'choices' => array(
					'100' => '100',
					'200' => '200',
					'300' => '300',
					'400' => '400',
					'500' => '500',
					'600' => '600',
					'700' => '700',
					'800' => '800',
					'900' => '900',
				),
				'type' => 'control',
			),
			'breadcrumbs_font_size' => array(
				'title'       => esc_html__( 'Font Size, px', '_s' ),
				'section'     => 'breadcrumbs_typography',
				'default'     => '14',
				'field'       => 'number',
				'input_attrs' => array(
					'min'  => 6,
					'max'  => 50,
					'step' => 1,
				),
				'type' => 'control',
			),
			'breadcrumbs_line_height' => array(
				'title'       => esc_html__( 'Line Height', '_s' ),
				'description' => esc_html__( 'Relative to the font-size of the element', '_s' ),
				'section'     => 'breadcrumbs_typography',
				'default'     => '1.5',
				'field'       => 'number',
				'input_attrs' => array(
					'min'  => 1.0,
					'max'  => 3.0,
					'step' => 0.1,
				),
				'type' => 'control',
			),

			/* Colors section */
			'breadcrumbs_colors' => array(
				'title'    => esc_html__( 'Colors', '_s' ),
				'priority' => 3,
				'panel'    => 'breadcrumbs',
				'type'     => 'section',
			),
			'breadcrumbs_bg_color' => array(
				'title'   => esc_html__( 'Background color', '_s' ),
				'section' => 'breadcrumbs_colors',
				'default' => 'transparent',
				'field'   => 'hex_color',
				'type'    => 'control',
			),
			'breadcrumbs_text_color' => array(
				'title'   => esc_html__( 'Text Color', '_s' ),
				'section' => 'breadcrumbs_colors',
				'default' => 'inherit',
				'field'   => 'hex_color',
				'type'    => 'control',
			),
			'breadcrumbs_link_color' => array(
				'title'   => esc_html__( 'Link Color', '_s' ),
				'section' => 'breadcrumbs_colors',
				'default' => 'inherit',
				'field'   => 'hex_color',
				'type'    => 'control',
			),
		),
	) );

	/**
	 * Init `cherry-dynamic-css` - CSS-parser which uses variables & functions for CSS code optimization.
	 *
	 * How to use? - Example below
	 */
	_s_get_core()->init_module( 'cherry-dynamic-css', array(
		'type'      => 'theme_mod',
		'single'    => true,
		'css_files' => array(
			get_template_directory() . '/cherry-framework-example/css/dynamic.css', // You may put this file in your theme's CSS directory.
		),
		// This is control's keys from `cherry-customizer` module.
		'options' => array(
			'breadcrumbs_font_style',
			'breadcrumbs_font_weight',
			'breadcrumbs_font_size',
			'breadcrumbs_line_height',
			'breadcrumbs_font_family',
			'breadcrumbs_bg_color',
			'breadcrumbs_text_color',
			'breadcrumbs_link_color',
		),
	) );

	/**
	 * Init `cherry-google-fonts-loader` - enqueue Google Web fonts.
	 *
	 * How to use? - Example below
	 */
	_s_get_core()->init_module( 'cherry-google-fonts-loader', array(
		'type'    => 'theme_mod',
		'single'  => true,
		'options' => array(
			'breadcrumbs' => array(
				'family' => 'breadcrumbs_font_family',
				'style'  => 'breadcrumbs_font_style',
				'weight' => 'breadcrumbs_font_weight',
			),
		),
	) );
}

/**
 * Callback-function that printed breadcrumbs.
 *
 * You'll have to add it manually in your template files (recommended in header.php).
 */
function _s_site_breadcrumbs() {
	$customizer  = _s_get_core()->modules['cherry-customizer'];
	$visibillity = get_theme_mod( 'breadcrumbs_visibillity', $customizer->get_default( 'breadcrumbs_visibillity' ) );

	if ( ! $visibillity ) {
		return;
	}

	$browse_label  = get_theme_mod( 'breadcrumbs_browse_label', $customizer->get_default( 'breadcrumbs_browse_label' ) );
	$show_title    = get_theme_mod( 'breadcrumbs_page_title', $customizer->get_default( 'breadcrumbs_page_title' ) );
	$path_type     = get_theme_mod( 'breadcrumbs_path_type', $customizer->get_default( 'breadcrumbs_path_type' ) );
	$show_on_front = get_theme_mod( 'breadcrumbs_front_visibillity', $customizer->get_default( 'breadcrumbs_front_visibillity' ) );

	_s_get_core()->init_module( 'cherry-breadcrumbs', array(
		'wrapper_format' => '<div class="breadcrumbs__title">%1$s</div><div class="breadcrumbs__items">%2$s</div>',
		'show_title'     => $show_title,
		'path_type'      => $path_type,
		'show_on_front'  => $show_on_front,
		'action'         => '_s_breadcrumbs_render',
		'labels'         => array(
			'browse' => $browse_label,
		),
		'css_namespace' => array(
			'module'    => 'breadcrumbs',
			'content'   => 'breadcrumbs__content',
			'wrap'      => 'breadcrumbs__wrap',
			'browse'    => 'breadcrumbs__browse',
			'item'      => 'breadcrumbs__item',
			'separator' => 'breadcrumbs__item-sep',
			'link'      => 'breadcrumbs__item-link',
			'target'    => 'breadcrumbs__item-target',
		),
	) );

	// Let's show a breadcrumbs in your site!
	do_action( '_s_breadcrumbs_render' );
}
