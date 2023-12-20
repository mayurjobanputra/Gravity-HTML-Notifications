<?php
/**
 * Plugin Name: Mayurs Gravity Forms HTML Notification Customizer
 * Description: Customize HTML before and after Gravity Forms email notifications.
 * Version: 1.0
 * Author: Mayur
 */

register_activation_hook( __FILE__, 'mayur_plugin_activation' );
function mayur_plugin_activation() {
    if ( !class_exists( 'GFForms' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); 
        wp_die('Sorry, but this plugin requires the Gravity Forms to be installed and active.', 'Plugin dependency check', array( 'back_link' => true ));
    }
}

add_action( 'admin_menu', 'mayur_add_grav_html_menu' );
function mayur_add_grav_html_menu() {
    if (class_exists('GFForms')) {
        add_menu_page(
            'Grav HTML', // Page title
            'Grav HTML', // Menu title
            'manage_options', // Capability
            'mayur_html_notifications', // Menu slug
            'mayur_html_notification_settings_page', // Function
            'dashicons-email-alt', // Icon URL (optional)
            6 // Position (optional)
        );
    }
}

function mayur_html_notification_settings_page() {
    if ( !current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( isset($_GET['settings-updated']) ) {
        add_settings_error( 'mayur_messages', 'mayur_message', 'Settings Saved', 'updated' );
    }

    settings_errors( 'mayur_messages' );

    $html_before = get_option( 'mayur_html_before', '' );
    $html_after = get_option( 'mayur_html_after', '' );

    ?>
    <div class="wrap">
        <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'mayur_options' );
            do_settings_sections( 'mayur_options' );
            wp_nonce_field( 'mayur_update_html', 'mayur_nonce' );
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="mayur_html_before">HTML Before Notification</label></th>
                    <td><textarea name="mayur_html_before" id="mayur_html_before" class="large-text" rows="10"><?php echo htmlspecialchars_decode( $html_before ); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="mayur_html_after">HTML After Notification</label></th>
                    <td><textarea name="mayur_html_after" id="mayur_html_after" class="large-text" rows="10"><?php echo htmlspecialchars_decode( $html_after ); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button( 'Save Settings' ); ?>
        </form>
    </div>
    <?php
}

add_action( 'admin_init', 'mayur_register_settings' );
function mayur_register_settings() {
    register_setting( 'mayur_options', 'mayur_html_before', 'mayur_sanitize_html_content' );
    register_setting( 'mayur_options', 'mayur_html_after', 'mayur_sanitize_html_content' );
}

function mayur_sanitize_html_content( $input ) {
    return htmlentities( $input, ENT_QUOTES, 'UTF-8' );
}

add_filter( 'gform_notification', 'mayur_customize_gform_email_notification', 10, 3 );
function mayur_customize_gform_email_notification( $notification, $form, $entry ) {
    if ( !current_user_can( 'manage_options' ) ) {
        return $notification;
    }

    $html_before = html_entity_decode( get_option( 'mayur_html_before', '' ), ENT_QUOTES, 'UTF-8' );
    $html_after = html_entity_decode( get_option( 'mayur_html_after', '' ), ENT_QUOTES, 'UTF-8' );

    $notification['message'] = $html_before . $notification['message'] . $html_after;

    return $notification;
}