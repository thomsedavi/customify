<?php

/**
 * Calls the class on the post edit screen.
 */
function comtomify_metabox_init() {
    new Customify_MetaBox();
}

if ( is_admin() ) {
    add_action( 'load-post.php',     'comtomify_metabox_init' );
    add_action( 'load-post-new.php', 'comtomify_metabox_init' );
}

/**
 * The Class.
 */
class Customify_MetaBox {

    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post',      array( $this, 'save'         ) );
    }

    /**
     * Adds the meta box container.
     */
    public function add_meta_box( $post_type ) {
        // Limit meta box to certain post types.
        $post_types = array( 'page' );

        if ( in_array( $post_type, $post_types ) ) {
            add_meta_box(
                'customify_page_settings',
                __( 'Page Settings', 'customify' ),
                array( $this, 'render_meta_box_content' ),
                $post_type,
                'side',
                'low'
            );
        }
    }

    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save( $post_id ) {

        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['customify_page_settings_nonce'] ) ) {
            return $post_id;
        }


        $nonce = sanitize_text_field( $_POST['customify_page_settings_nonce'] );

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'customify_page_settings' ) ) {
            return $post_id;
        }

        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' == get_post_type( $post_id ) ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }

        $settings = isset( $_POST['customify_page_settings'] ) ? wp_unslash( $_POST['customify_page_settings'] ) : array();
        $settings = wp_parse_args( $settings, array(
            'sidebar' => '',
            'disable_header' => '',
            'disable_page_title' => '',
            'disable_footer_main' => '',
            'disable_footer_bottom' => '',
        ) );

        foreach( $settings as $key => $value ) {
            // Update the meta field.
            update_post_meta( $post_id, '_customify_'.$key, sanitize_text_field( $value ) );
        }

    }


    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content( $post ) {

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'customify_page_settings', 'customify_page_settings_nonce' );
        $values = array(
            'sidebar' => '',
            'content_layout' => '',
            'disable_header' => '',
            'disable_page_title' => '',
            'disable_footer_main' => '',
            'disable_footer_bottom' => '',
        );
        foreach( $values as $key => $value ) {
            $values[ $key ] = get_post_meta( $post->ID, '_customify_'.$key, true );
        }
        ?>
        <p>
            <label for="customify_page_layout"><strong><?php _e( 'Sidebar', 'customify' ); ?></strong></label><br/>
            <select id="customify_page_layout" name="customify_page_settings[sidebar]">
                <option value=""><?php _e( 'Inherit from Customize Setting', 'customify' ); ?></option>
                <?php foreach( customify_get_config_sidebar_layouts() as $k => $label ) { ?>
                <option <?php selected( $values['sidebar'],  $k ); ?> value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="customify_content_layout"><strong><?php _e( 'Content Layout', 'customify' ); ?></strong></label><br/>
            <select id="customify_content_layout" name="customify_page_settings[content_layout]">
                <option value=""><?php _e( 'Default', 'customify' ); ?></option>
                <?php foreach( array(
                        'boxed' => __( 'Boxed', 'customify' ),
                        'boxed-container' => __( 'Boxed Container', 'customify' ),
                        'full-width' => __( 'Full Width', 'customify' ),
                        'full-stretched' => __( 'Full Width - Stretched', 'customify' ),
                       ) as $k => $label ) { ?>
                    <option <?php selected( $values['content_layout'],  $k ); ?> value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></option>
                <?php } ?>
            </select>
        </p>
        <strong><?php _e( 'Disable Elements', 'customify' ); ?></strong>
        <p>
            <label>
                <input type="checkbox" name="customify_page_settings[disable_header]" <?php checked( $values['disable_header'], 1 ); ?> value="1"> <?php _e( 'Disable Header', 'customify' ); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="customify_page_settings[disable_page_title]" <?php checked( $values['disable_page_title'], 1 ); ?> value="1"> <?php _e( 'Disable Title', 'customify' ); ?>
            </label>
        </p>

        <p>
            <label>
                <input type="checkbox" name="customify_page_settings[disable_footer_main]" <?php checked( $values['disable_footer_main'], 1 ); ?> value="1"> <?php _e( 'Disable Footer Main', 'customify' ); ?>
            </label>
        </p>

        <p>
            <label>
                <input type="checkbox" name="customify_page_settings[disable_footer_bottom]" <?php checked( $values['disable_footer_bottom'], 1 ); ?> value="1"> <?php _e( 'Disable Footer Bottom', 'customify' ); ?>
            </label>
        </p>
        <?php
    }
}