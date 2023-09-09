<?php
/*
Plugin Name: WhatsApp Popup
Description: Display a WhatsApp popup button. Visit our website at https://zakichan.com .
Version: 1.0
Author: Zakariae Belkhnati
*/
 

// Enqueue CSS and JavaScript
function enqueue_whatsapp_popup_scripts() {
    // Enqueue Font Awesome stylesheet
    wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
    
    // Enqueue your plugin's CSS and JavaScript
    wp_enqueue_style('whatsapp-popup-style', plugin_dir_url(__FILE__) . 'whatsapp-popup.css');
   // wp_enqueue_script('whatsapp-popup-script', plugin_dir_url(__FILE__) . 'whatsapp-popup.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_whatsapp_popup_scripts');

// Create a menu item for the settings page in the admin sidebar menu
function whatsapp_popup_admin_menu() {
    add_menu_page(
        'zakichan WhatsApp Popup Settings',
        'zakichan WhatsApp Popup',
        'manage_options',
        'whatsapp-popup-settings',
        'whatsapp_popup_settings_page',
        'dashicons-phone', // Icon for the menu item (you can change this)
        30 // Position in the menu (adjust as needed)
    );
}
add_action('admin_menu', 'whatsapp_popup_admin_menu');

// Callback function to display the settings page
function whatsapp_popup_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h2>WhatsApp Popup Settings (by zakichan)</h2>
        <form method="post" action="options.php">
            <?php settings_fields('whatsapp_popup_settings_group'); ?>
            <?php do_settings_sections('whatsapp-popup-settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">WhatsApp Phone Number:</th>
                    <td>
                        <input type="text" name="whatsapp_phone_number" value="<?php echo esc_attr(get_option('whatsapp_phone_number')); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">WhatsApp Message:</th>
                    <td>
                        <input type="text" name="whatsapp_message" value="<?php echo esc_attr(get_option('whatsapp_message')); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Full URL for Href:</th>
                    <td>
                        <input type="text" name="whatsapp_href_url" value="<?php echo esc_attr(get_option('whatsapp_href_url')); ?>" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register the settings and fields
function whatsapp_popup_register_settings() {
    register_setting('whatsapp_popup_settings_group', 'whatsapp_phone_number');
    register_setting('whatsapp_popup_settings_group', 'whatsapp_message');
    register_setting('whatsapp_popup_settings_group', 'whatsapp_href_url');
}
add_action('admin_init', 'whatsapp_popup_register_settings');

// WhatsApp Popup HTML
function whatsapp_popup_html() {
    ob_start(); // Start output buffering

    $phone_number = get_option('whatsapp_phone_number');
    $message = get_option('whatsapp_message');
    $href_url = get_option('whatsapp_href_url');

    // Check if the user-provided phone number and message are empty
    if (empty($phone_number) || empty($message)) {
        // If either phone number or message is empty, use the user-provided URL
        $href_url = esc_url($href_url);
    } else {
        // If both phone number and message are provided, generate the URL
        $phone_number = urlencode($phone_number);
        $message = urlencode($message);
        $href_url = 'https://api.whatsapp.com/send?phone=' . $phone_number . '&text=' . $message;
    }
    ?>
    <div class="whatsapp-popup">
        <a href="<?php echo $href_url; ?>" target="_blank" class="float">
            <i class="fa fa-whatsapp my-float"></i>
        </a>
    </div>
    <?php
    echo ob_get_clean(); // Output the buffered content
}

// Display the WhatsApp Popup on all pages
function display_whatsapp_popup() {
    whatsapp_popup_html();
}
add_action('wp_footer', 'display_whatsapp_popup');
