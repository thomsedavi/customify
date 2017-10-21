<?php
/**
 * _beacon Theme Customizer
 *
 * @package _beacon
 */

require get_template_directory() . '/inc/customizer/customizer-config.php';
require get_template_directory() . '/inc/customizer/customizer-sanitize.php';
require get_template_directory() . '/inc/customizer/customizer-auto-css.php';

if ( ! class_exists( '_Beacon_Customizer' ) ) {
    class  _Beacon_Customizer {
        static $config;
        static $_instance;
        static $has_icon = false;
        public $devices = array( 'desktop', 'tablet', 'mobile');
        function __construct()
        {
            add_action( 'customize_register', array( $this, 'register' ) );
        }

        static function get_instance(){
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance ;
        }

        static function get_config(){
            if ( is_null( self::$config  ) ) {

                $_config = apply_filters( '_beacon/customizer/config', array() );
                $config = array();
                foreach ( $_config as $f ) {

                    $f = wp_parse_args( $f, array(

                        'priority'    => null,
                        'title'       => null,
                        'label'       => null,
                        'name'        => null,
                        'type'        => null,
                        'description' => null,

                        'capability' => null,
                        'mod' => null, // theme_mod || option default theme_mod

                        'device' => null,
                        'device_settings' => null,

                        // For settings
                        'sanitize_callback'     => '_beacon_sanitize_customizer_input',
                        'sanitize_js_callback'  => null,
                        'theme_supports'        => null,
                        //'transport'             => 'postMessage', // refresh
                        'default' => null,

                        // for selective refresh
                        'selector'        => null,
                        'render_callback' => null,
                        'css_format'      => null,

                        // For control
                        'active_callback' => null,

                    ) );

                    if ( ! isset( $f['type'] ) )  {
                        $f['type'] = null;
                    }

                    switch ( $f['type'] ) {
                        case 'panel':
                            $config['panel|'.$f['name']] = $f;
                            break;
                        case 'section':
                            $config['section|'.$f['name']] = $f;
                            break;
                        default:
                            if ( $f['type'] == 'icon' ) {
                                self::$has_icon = true;
                            }

                            if ( isset( $f['fields'] ) ) {
                                $types = wp_list_pluck( $f['fields'], 'type' );
                                if ( in_array( 'icon', $types ) ) {
                                    self::$has_icon = true;
                                }
                            }
                            $config['setting|'.$f['name']] = $f;

                    }
                }
                self::$config = $config;
            }
            return self::$config;
        }

        /**
         * Check if has icon field;
         *
         * @return bool
         */
        function has_icon(){
            return self::$has_icon;
        }

        /**
         *  Get Customizer setting.
         *
         * @param $name
         * @param string $device
         * @param bool $key
         * @return array|bool|mixed|null|string|void
         */
        function get_setting( $name, $device = 'desktop', $key = false ){
            $config = self::get_config();
            $get_value = null;
            if ( isset( $config['setting|'.$name ] ) ) {
                $default = isset( $config['setting|'.$name ]['default'] ) ? $config['setting|'.$name ]['default'] : false;
                if ( 'option' == $config['setting|'.$name]['mod'] ) {
                    $value =  get_option( $name, $default );
                } else {
                    $value =  get_theme_mod( $name, $default );
                }

            } else {
                $value =  get_theme_mod( $name, null );
            }

            if ( ! $key ) {
                if ( $device != 'all' ) {
                    if ( is_array( $value ) && isset( $value[ $device ] ) ) {
                        $get_value =  $value[ $device ];
                    } else {
                        $get_value =  $value;
                    }
                } else {
                    $get_value = $value;
                }
            } else {
                $value_by_key = isset( $value[ $key ] ) ?  $value[ $key ]: false;
                if ( $device != 'all' && is_array( $value_by_key ) ) {
                    if ( is_array( $value_by_key ) && isset( $value_by_key[ $device ] ) ) {
                        $get_value =  $value_by_key[ $device ];
                    } else {
                        $get_value =  $value_by_key;
                    }
                } else {
                    $get_value = $value_by_key;
                }
            }

            return $get_value;
        }


        function get_field_setting( $key ){
            $config = self::get_config();
            if ( isset($config['setting|'.$key] ) ) {
                return $config['setting|'.$key];
            }
            return false;
        }

        function get_media( $value, $size = null ) {
            if ( is_numeric( $value ) ) {
                if ( ! $size ) {
                    return wp_get_attachment_url( $value );
                } else {
                    $image_attributes = wp_get_attachment_image_src( $value = 8, $size );
                    if ( $image_attributes ) {
                        return $image_attributes[0];
                    } else {
                        return false;
                    }
                }
            }elseif ( is_string( $value ) ) {
                return $value;
            } elseif ( is_array( $value ) ) {
                $value = wp_parse_args( $value, array(
                    'id'    => '',
                    'url'   => '',
                    'mime'  => ''
                ) );

                $url = '';

                if ( strpos( $value['mime'], 'image/' ) !== false ) {
                    $image_attributes = wp_get_attachment_image_src( $value = 8, $size );
                    if ( $image_attributes ) {
                        $url =  $image_attributes[0];
                    }
                } else {
                    $url = wp_get_attachment_url( $value );
                }

                if ( ! $url ) {
                    $url = $value['value'];
                }

                return $url;

            }

            return false;
        }

        /**
         * Register Customize Settings
         *
         * @param $wp_customize
         */
        function register( $wp_customize ){
            require_once get_template_directory().'/inc/customizer/customizer-control.php';

            $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
            $wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
            $wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

            if ( isset( $wp_customize->selective_refresh ) ) {
                $wp_customize->selective_refresh->add_partial( 'blogname', array(
                    'selector'        => '.site-title a',
                    'render_callback' => '_beacon_customize_partial_blogname',
                ) );
                $wp_customize->selective_refresh->add_partial( 'blogdescription', array(
                    'selector'        => '.site-description',
                    'render_callback' => '_beacon_customize_partial_blogdescription',
                ) );
            }

            foreach ( self::get_config() as $args ) {
                switch (  $args['type'] ) {
                    case  'panel':
                        $name = $args['name'];
                        unset( $args['name'] );
                        if ( ! $args['title'] ) {
                            $args['title'] = $args['label'];
                        }
                        if ( ! $name ) {
                            $name = $args['title'];
                        }
                        $wp_customize->add_panel( $name, $args );
                        break;
                    case 'section':
                        $name = $args['name'];
                        unset( $args['name'] );
                        if ( ! $args['title'] ) {
                            $args['title'] = $args['label'];
                        }
                        if ( ! $name ) {
                            $name = $args['title'];
                        }
                        $wp_customize->add_section( $name, $args );
                        break;
                    default:

                        $args['setting_type'] = $args['type'];
                        $settings_args = array(
                           'sanitize_callback' => $args['sanitize_callback'],
                           'sanitize_js_callback' => $args['sanitize_js_callback'],
                           'theme_supports' => $args['theme_supports'],
                           //'transport' => $args['transport'],
                           'default' => $args['default'],
                           'type' => $args['mod'],
                        );
                        $settings_args['transport'] = 'refresh';
                        if ( ! $settings_args['sanitize_callback'] ) {
                            $settings_args['sanitize_callback'] = '_beacon_sanitize_customizer_input';
                        }

                        foreach ( $settings_args as $k => $v ) {
                           unset( $args[ $k ] );
                        }
                        unset( $args['mod'] );

                        $name = $args['name'];
                        unset( $args['name'] );

                        unset( $args['type'] );
                        if ( ! $args['label'] ) {
                           $args['label'] =  $args['title'];
                        }

                        $selective_refresh = null;
                        if ( $args['selector'] && ( $args['render_callback'] || $args['css_format'] ) ) {
                            $selective_refresh= array(
                                'selector'  => $args['selector'],
                                'render_callback' => $args['render_callback'],
                            );

                            if ( $args['css_format'] ) {
                                $selective_refresh['selector'] = '#_beacon-style-inline-css';
                                $selective_refresh['render_callback'] = '_Beacon_Customizer_Auto_CSS';
                            }

                            $settings_args['transport'] = 'postMessage';
                        }
                        unset( $args['default'] );


                        $wp_customize->add_setting( $name, $settings_args );
                        $wp_customize->add_control( new _Beacon_Customizer_Control( $wp_customize, $name, $args ));
                        if ( $selective_refresh ) {
                            $wp_customize->selective_refresh->add_partial( $name, $selective_refresh );
                        }

                        break;
                }

            }
        }

    }
}

if ( ! function_exists( '_Beacon_Customizer' ) ) {
    function _Beacon_Customizer(){
        return _Beacon_Customizer::get_instance();
    }
}
_Beacon_Customizer();

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function _beacon_customize_preview_js() {
    wp_enqueue_script( '_beacon-customizer', get_template_directory_uri() . '/assets/js/customizer/customizer.js', array( 'customize-preview' ), '20151215', true );
    wp_localize_script( '_beacon-customizer', '_Beacon_Preview_Config_Fields', _Beacon_Customizer::get_config() );

}
add_action( 'customize_preview_init', '_beacon_customize_preview_js' );


function _test_1_render_callback( $partial = false ){
    echo '<div class="_test_text1">';

    if( $partial ) {
        $control_settings = $partial->component->manager->get_control($partial->id);
        echo '<pre>';
        var_dump($control_settings);
        echo '</pre>';

    }

    $html = '<h2 class="dsadsadsa">'.esc_html( _Beacon_Customizer()->get_setting( 'text' ) ).'<div class="_test_text_2">'.esc_html( _Beacon_Customizer()->get_setting( 'text2' ) ).'</div></h2>';
    echo $html;

    echo '</div>';
}
function _test_2_render_callback(){
    $html = '<div class="_test_text_2">'.esc_html( get_theme_mod( 'text2' ) ).'</div>';
    echo $html;
}


/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function _beacon_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function _beacon_customize_partial_blogdescription() {
	bloginfo( 'description' );
}


