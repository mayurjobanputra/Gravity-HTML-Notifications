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

add_action( 'admin_menu', 'mayur_html_notification_menu' );
function mayur_html_notification_menu() {
    if (class_exists('GFForms')) {
        add_submenu_page(
            'gf_edit_forms',
            'HTML Notifications',
            'HTML Notifications',
            'manage_options',
            'mayur_html_notifications',
            'mayur_html_notification_settings_page'
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
                    <td><textarea name="mayur_html_before" rows="5" cols="50" id="mayur_html_before" class="large-text"><?php echo esc_textarea( $html_before ); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="mayur_html_after">HTML After Notification</label></th>
                    <td><textarea name="mayur_html_after" rows="5" cols="50" id="mayur_html_after" class="large-text"><?php echo esc_textarea( $html_after ); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button( 'Save Settings' ); ?>
        </form>
    </div>
    <?php
}

add_action( 'admin_init', 'mayur_register_settings' );
function mayur_register_settings() {
    register_setting( 'mayur_options', 'mayur_html_before', 'sanitize_textarea_field' );
    register_setting( 'mayur_options', 'mayur_html_after', 'sanitize_textarea_field' );
}

add_filter( 'gform_notification', 'mayur_customize_gform_email_notification', 10, 3 );
function mayur_customize_gform_email_notification( $notification, $form, $entry ) {
    if ( !current_user_can( 'manage_options' ) ) {
        return $notification;
    }

    $html_before = get_option( 'mayur_html_before', '' );
    $html_after = get_option( 'mayur_html_after', '' );

    $notification['message'] = $html_before . $notification['message'] . $html_after;

    return $notification;
}
