
<?php
/**
 * Plugin Name: Mayurs HTML Notification Customizer
 * Description: Customize HTML before and after Gravity Forms email notifications.
 * Version: 1.0
 * Author: Mayur
 */

// Check if Gravity Forms is active
if (!is_plugin_active('gravityforms/gravityforms.php')) {
    return;
}

add_action( 'admin_menu', 'mayur_html_notification_menu' );

function mayur_html_notification_menu() {
    if (class_exists('GFForms')) {
        GFForms::add_settings_page( 'HTML Notifications', 'mayur_html_notification_settings_page' );
    }
}

function mayur_html_notification_settings_page() {
    // Check if the user has submitted the settings
    // WordPress will add the "settings-updated" $_GET parameter to the url
    if ( isset($_GET['settings-updated']) ) {
        // Add settings saved message with the class of "updated"
        add_settings_error( 'mayur_messages', 'mayur_message', __( 'Settings Saved', 'mayur' ), 'updated' );
    }

    // Show error/update messages
    settings_errors( 'mayur_messages' );

    // Retrieve the stored values
    $html_before = get_option('mayur_html_before', '');
    $html_after = get_option('mayur_html_after', '');

    ?>
    <div class="wrap">
        <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
        <form action="options.php" method="post">
            <?php
            // Output security fields for the registered setting
            settings_fields( 'mayur_options' );
            // Output setting sections and their fields
            do_settings_sections( 'mayur_options' );
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
    // Register a new setting for "mayur" page
    register_setting( 'mayur_options', 'mayur_html_before' );
    register_setting( 'mayur_options', 'mayur_html_after' );
}

// Implement the customization in email notifications here, using the saved settings.
// ...

