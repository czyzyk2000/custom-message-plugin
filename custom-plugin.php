<?php
/*
Plugin Name: Custom Message Plugin
Description: zadanie
Version: 1.0
Author: Patryk
*/

function cmp_get_message() {
    $message = get_option('cmp_custom_message');
    if (!$message) {
        $message = 'Hello, WordPress!';
    }
    return $message;
}


function cmp_shortcode() {
    return esc_html(cmp_get_message());
}
add_shortcode('custom_message', 'cmp_shortcode');


function cmp_admin_menu() {
    add_menu_page(
        'Custom Plugin',            
        'Custom Plugin',          
        'manage_options',         
        'custom-plugin',            
        'cmp_admin_page_callback',  
        'dashicons-admin-generic'   
    );
}
add_action('admin_menu', 'cmp_admin_menu');

// Zawartość strony administracyjnej
function cmp_admin_page_callback() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Nie masz uprawnień, aby uzyskać dostęp do tej strony.'));
    }
    ?>
    <div class="wrap">
        <h1>Ustawienia Custom Plugin</h1>
        <form id="cmp-settings-form">
            <?php wp_nonce_field('cmp_save_message', 'cmp_nonce'); ?>
            <label for="cmp_message">Wiadomość:</label>
            <input type="text" id="cmp_message" name="cmp_message" value="<?php echo esc_attr(cmp_get_message()); ?>">
            <input type="submit" value="Zapisz">
        </form>
        <div id="cmp-message"></div>
    </div>
    <?php
}


function cmp_save_message() {
    check_ajax_referer('cmp_save_message', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Brak uprawnień.'));
    }
    
    $new_message = isset($_POST['cmp_message']) ? sanitize_text_field($_POST['cmp_message']) : '';
    update_option('cmp_custom_message', $new_message);
    
    wp_send_json_success(array('message' => $new_message));
}
add_action('wp_ajax_cmp_save_message', 'cmp_save_message');


function cmp_enqueue_admin_scripts($hook) {

    if ($hook != 'toplevel_page_custom-plugin') {
        return;
    }
    
    wp_enqueue_script(
        'cmp-admin-js',
        plugin_dir_url(__FILE__).'cmp-admin.js',
        array('jquery'),
        '1.0',
        true
    );

    wp_localize_script('cmp-admin-js', 'cmp_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cmp_save_message')
    ));
}
add_action('admin_enqueue_scripts', 'cmp_enqueue_admin_scripts');
