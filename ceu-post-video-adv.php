<?php
/*
	Plugin Name: Smashing Fields Plugin: Approach 1
	Description: Setting up custom fields for our plugin.
	Author: Matthew Ray
	Version: 1.0.0
*/
class Ceuton_Video_Advertisement_Plugin {
    public function __construct() {
    	// Hook into the admin menu
    	add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );

        // Add Settings and Fields
    	add_action( 'admin_init', array( $this, 'setup_sections' ) );
    	add_action( 'admin_init', array( $this, 'setup_fields' ) );
    }

    // Plugin Settings
    public function create_plugin_settings_page() {
    	// Add the menu item and page
    	$page_title = 'VideoAds';
    	$menu_title = 'VideoAds';
    	$capability = 'manage_options';
    	$slug = 'ceuton_video_adv_fields';
    	$callback = array( $this, 'plugin_settings_page_content' );
    	$icon = 'dashicons-admin-plugins';
    	$position = 100;

    	add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
    }

    public function plugin_settings_page_content() {?>
    	<div class="wrap">
    		<h2>VideoAds</h2><?php
            if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ){
                  $this->admin_notice();
            } ?>
    		<form method="POST" action="options.php">
                <?php
                    settings_fields( 'ceuton_video_adv_fields' );
                    do_settings_sections( 'ceuton_video_adv_fields' );
                    submit_button();
                ?>
    		</form>
    	</div> <?php
    }
    
    public function admin_notice() { ?>
        <div class="notice notice-success is-dismissible">
            <p>As definições foram guardadas com sucesso !</p>
        </div><?php
    }

    public function setup_sections() {
        add_settings_section( 'global_settings', 'Definições Globais', array( $this, 'section_callback' ), 'ceuton_video_adv_fields' );
    }

    public function section_callback( $arguments ) {
        echo 'O que preencher nesta secção será exibido em todas as páginas sem definições individuais.';
    }

    public function setup_fields() {
        $fields = array(
        	array(
        		'uid' => 'ceu_video_adv_embed_url',
        		'label' => 'Link/URL',
        		'section' => 'global_settings',
        		'type' => 'text',
        		'supplimental' => 'Ex: https://www.youtube.com/embed/abcdefghij',
        	),
        	array(
        		'uid' => 'ceu_video_adv_player',
        		'label' => 'Player/HTML',
        		'section' => 'global_settings',
        		'type' => 'textarea',
        	),
            array(
                'uid' => 'ceu_video_adv_type',
                'label' => 'Mostrar',
                'section' => 'global_settings',
                'type' => 'radio',
                'options' => array(
                    'url' => 'Link/URL',
                    'html' => 'Player/HTML',
                ),
                'default' => array(
                        'url'
                )
            ),
            array(
                'uid' => 'ceu_video_adv_status',
                'label' => 'Estado',
                'section' => 'global_settings',
                'type' => 'radio',
                'options' => array(
                    'on' => 'Activado',
                    'off' => 'Desactivado',
                ),
                'default' => array(
                        'off'
                )
            )
        );
    	foreach( $fields as $field ){
        	add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'ceuton_video_adv_fields', $field['section'], $field );
            register_setting( 'ceuton_video_adv_fields', $field['uid'] );
    	}
    }

    public function field_callback( $arguments ) {

        $value = get_option( $arguments['uid'] );

        if( ! $value ) {
            $value = $arguments['default'];
        }

        switch( $arguments['type'] ){
            case 'text':
            case 'password':
            case 'number':
                printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                break;
            case 'textarea':
                printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
                break;
            case 'select':
            case 'multiselect':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $attributes = '';
                    $options_markup = '';
                    foreach( $arguments['options'] as $key => $label ){
                        $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value[ array_search( $key, $value, true ) ], $key, false ), $label );
                    }
                    if( $arguments['type'] === 'multiselect' ){
                        $attributes = ' multiple="multiple" ';
                    }
                    printf( '<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup );
                }
                break;
            case 'radio':
            case 'checkbox':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = '';
                    $iterator = 0;
                    foreach( $arguments['options'] as $key => $label ){
                        $iterator++;
                        $options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator );
                    }
                    printf( '<fieldset>%s</fieldset>', $options_markup );
                }
                break;
        }

        if( $helper = $arguments['helper'] ){
            printf( '<span class="helper"> %s</span>', $helper );
        }

        if( $supplimental = $arguments['supplimental'] ){
            printf( '<p class="description">%s</p>', $supplimental );
        }

    }
}

function ceu_get_html($post_id = null)
{
    // General Settings
    $status = get_option('ceu_video_adv_status');
    if (!isset($post_id) && $status[0] == "on") {

        $type = get_option('ceu_video_adv_type');

        if($type[0] == "url"){ ?>
            <div id="videoAdv" class="vdgk_sticky">
                <button id="videoAdvCls" type="button" class="vdgk_close" aria-label="Close" data-id="<?php echo get_the_ID(); ?>" data-value="<?php echo is_single() ? "article" : "page" ?>">
                    <span aria-hidden="true">×</span>
                </button>
                <iframe class="vdgk_sticky_play" src="<?php echo get_option('ceu_video_adv_embed_url'); ?>?autoplay=1&controls=0&showinfo=0&modestbranding=1&disablekb=1&fs=0&rel=0"></iframe>
            </div>
        <?php
        } elseif ($type == "html")
            echo get_option('ceu_video_adv_player');

    } else {

        $type = get_post_meta($post_id, '_ceu_adv_meta_type', true);
        $value = get_post_meta($post_id, '_ceu_adv_meta_value', true);
        $status = get_post_meta($post_id, '_ceu_adv_meta_status', true);

        if($status == "on")
            if($type == "url"){
                ?>
                <div id="videoAdv" class="vdgk_sticky">
                    <button id="videoAdvCls" type="button" class="vdgk_close" aria-label="Close" data-id="<?php echo get_the_ID(); ?>" data-value="<?php echo is_single() ? "article" : "page" ?>">
                        <span aria-hidden="true">×</span>
                    </button>
                    <iframe class="vdgk_sticky_play" src="<?php echo $value; ?>?autoplay=1&controls=0&autoplay=1&showinfo=0&modestbranding=1&disablekb=1&fs=0&rel=0"></iframe>
                </div>
                <?php
            } elseif ($type == "html")
                echo $value;
    }
}

function ceu_check_if_has_settings($post_id){
    $value = get_post_meta($post_id, '_ceu_adv_meta_value', true);

    if($value != null)
        return true;
    else
        return false;
}

function ceu_adv_add_video()
{
    $screens = ['post'];
    foreach ($screens as $screen) {
        add_meta_box(
            'ceu_box_id',                   // Unique ID
            'VideoAds',          // Box title
            'ceu_adv_custom_box_html',      // Content callback, must be of type callable
            $screen                             // Post type
        );
    }
}

function ceu_adv_custom_box_html($post)
{
    $type = get_post_meta($post->ID, '_ceu_adv_meta_type', true);
    $value = get_post_meta($post->ID, '_ceu_adv_meta_value', true);
    $status = get_post_meta($post->ID, '_ceu_adv_meta_status', true);
    ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row">Link/URL</th>
            <td>
                <input name="ceu_video_adv_embed_url" id="ceu_video_adv_embed_url" type="text" placeholder="" value="<?php echo $type == "url" ? esc_attr($value) : "" ?>">
                <p class="description">Ex: https://www.youtube.com/embed/abcdefghij</p>
            </td>
        </tr>
        <tr>
            <th scope="row">Player/HTML</th>
            <td>
                <textarea name="ceu_video_adv_player" id="ceu_video_adv_player" placeholder="" rows="5" cols="50"><?php echo $type == "html" ? esc_attr($value) : "" ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row">Mostrar</th>
            <td>
                <fieldset>
                    <label for="ceu_video_adv_type_1">
                        <input id="ceu_video_adv_type_1" name="ceu_video_adv_type" type="radio" value="url" <?php echo $type == "url" ? "checked" : ""  ?>> Link/URL
                    </label><br>

                    <label for="ceu_video_adv_type_2">
                        <input id="ceu_video_adv_type_2" name="ceu_video_adv_type" type="radio" value="html" <?php echo $type == "html" ? "checked" : ""  ?>> Player/HTML
                    </label><br>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">Estado</th>
            <td>
                <fieldset>
                    <label for="ceu_video_adv_status_1">
                        <input id="ceu_video_adv_status_1" name="ceu_video_adv_status" type="radio" value="on" <?php echo $status == "on" ? "checked" : ""  ?>> Activado
                    </label><br>

                    <label for="ceu_video_adv_status_2">
                        <input id="ceu_video_adv_status_2" name="ceu_video_adv_status" type="radio" value="off" <?php echo $status == "off" ? "checked" : ""  ?>> Desactivado
                    </label><br>
                </fieldset>
            </td>
        </tr>
        </tbody>
    </table>
    <?php
}

function ceu_adv_save_postdata($post_id)
{
    // Type
    update_post_meta(
        $post_id,
        '_ceu_adv_meta_type',
        $_POST['ceu_video_adv_type']
    );

    // Value
    update_post_meta(
        $post_id,
        '_ceu_adv_meta_value',
        ($_POST['ceu_video_adv_type'] == "url" ? $_POST['ceu_video_adv_embed_url'] : $_POST['ceu_video_adv_player'])
    );

    // Status
    update_post_meta(
        $post_id,
        '_ceu_adv_meta_status',
        $_POST['ceu_video_adv_status']
    );
}

function ceu_adv_scripts() {
    wp_register_style( 'ceu-adv-styles',  plugin_dir_url( __FILE__ ) . 'assets/style.css' );
    wp_enqueue_style( 'ceu-adv-styles' );

    wp_register_script( 'ceu-adv-jquery',  plugin_dir_url( __FILE__ ) . 'assets/jquery.min.js' );
    wp_register_script( 'ceu-adv-js-cookie',  plugin_dir_url( __FILE__ ) . 'assets/js.cookie.min.js' );
        wp_register_script( 'ceu-adv-scripts',  plugin_dir_url( __FILE__ ) . 'assets/scripts.js' );

    wp_enqueue_script('ceu-adv-jquery');
    wp_enqueue_script('ceu-adv-js-cookie');
    wp_enqueue_script('ceu-adv-scripts');
}

add_action('add_meta_boxes', 'ceu_adv_add_video');
add_action('save_post', 'ceu_adv_save_postdata');
add_action('wp_enqueue_scripts', 'ceu_adv_scripts' );

new Ceuton_Video_Advertisement_Plugin();