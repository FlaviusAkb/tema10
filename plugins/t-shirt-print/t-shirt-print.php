<?php
/**
Plugin Name: T-shirt print
Author: Flavius
Version: 1.0
Text domain: t-shirt-print
**/
// Plugin URL.
define( 'WPR_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
// Plugin path.
define( 'WPR_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );



/**
 * Adds a settings page for the plugin
 *
 * @param args $args this is a comment.
 */
function tema_10_section_a_callback( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Edit Section A settings.', 'wpr' ); ?></p>
	<?php
}


/**
 * Adds
 *
 * @param args $args is ffsdfs.
 */
function single_face_field_callback( $args ) {
	// Get the value of the setting we've registered with register_setting().
	$options = get_option( 'wpr_options' );
	?>
<input
		value="<?php echo esc_attr( $options[ $args['label_for'] ] ); ?>"
		id="<?php echo esc_attr( $args['label_for'] ); ?>"
		data-custom="<?php echo esc_attr( $args['wpr_custom_data'] ); ?>"
		name="wpr_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
		type="text">
	<?php
}
/**
 * Adds
 *
 * @param args $args is ffsdfs.
 */
function both_faces_callback( $args ) {
	// Get the value of the setting we've registered with register_setting().
	$options = get_option( 'wpr_options' );
	?>
<input
		value="<?php echo esc_attr( $options[ $args['label_for'] ] ); ?>"
		id="<?php echo esc_attr( $args['label_for'] ); ?>"
		data-custom="<?php echo esc_attr( $args['wpr_custom_data'] ); ?>"
		name="wpr_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
		type="text">
	<?php
}


	add_action(
		'admin_init',
		function() {
			register_setting(
				'akb_academy',
				'wpr_options'
			);
			add_settings_section(
				'tema_10_section_a_fields',
				'T-shirt print price addons',
				'tema_10_section_a_callback',
				'akb_academy'
			);
			add_settings_field(
				'wpr_single_face_tema',
				'Single Face Addon',
				'single_face_field_callback',
				'akb_academy',
				'tema_10_section_a_fields',
				array(
					'label_for'       => 'wpr_single_face_tema',
					'class'           => 'wpr_row',
					'wpr_custom_data' => 'custom',
				)
			);
			add_settings_field(
				'wpr_both_faces_tema',
				'Both Faces Addon',
				'both_faces_callback',
				'akb_academy',
				'tema_10_section_a_fields',
				array(
					'label_for'       => 'wpr_both_faces_tema',
					'class'           => 'wpr_row',
					'wpr_custom_data' => 'custom',
				)
			);
		}
	);

	add_action(
		'admin_menu',
		function() {
			add_menu_page(
				'Tema 10 settings',
				'Tema 10 options',
				'manage_options',
				'tema_10_settings',
				'tema_10_page_html',
				'dashicons-admin-settings',
				1
			);
		}
	);

	/**
	 * The function to be called to output the content for this page (used in add_menu_page above)
	 */
	function tema_10_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// check if the user have submitted the settings
		// WordPress will add the "settings-updated" $_GET parameter to the url.
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated".
			add_settings_error( 'wpr_messages', 'wpr_message', __( 'Settings Saved', 'wpr' ), 'updated' );
		}

		// show error/update messages.
		settings_errors( 'wpr_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "wpr".
				settings_fields( 'akb_academy' );
				// output setting sections and their fields
				// (sections are registered for "wpr", each field is registered to a specific section).
				do_settings_sections( 'akb_academy' );
				// output save settings button.
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>


		<?php

	}
	flush_rewrite_rules( false );


	// Settings page ends here //////////////////////////////////////////////////////////////////////////////////////////////////




	add_action( 'woocommerce_before_add_to_cart_button', 't_shirt_scripts' );
	function t_shirt_scripts() {
		wp_enqueue_script(
			't-shirt-print',
			WPR_URL . '/assets/t-shirt-print.js',
			array( 'jquery' ),
			'1.0',
			true
		);
		wp_localize_script(
			't-shirt-print',
			'MyAjax',
			array(
				// URL to wp-admin/admin-ajax.php to process the request
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				// generate a nonce with a unique ID "myajax-post-comment-nonce"
				// so that you can check it later when an AJAX request is sent
				'security' => wp_create_nonce( 'my-special-string' ),
			)
		);
	}


	// The function that handles the AJAX request
	add_action( 'wp_ajax_my_actionz', 'my_actionz_callback' );
	add_action( 'wp_ajax_nopriv_my_action', 'my_actionz_callback' );
	function my_actionz_callback() {
		global $product;
		check_ajax_referer( 'my-special-string', 'security' );
		$id      = $_GET['id'];
		$product = wc_get_product( $id );
		$addon   = get_option( 'wpr_options' );
		$price   = $product->get_price();
		$faces   = $_GET['faces'];

		if ( 'Not printed' === $faces ) {

			$added_price = $price;

		} elseif ( 'Front' === $faces ) {

			$addon_price = $addon['wpr_single_face_tema'];
			$added_price = $price + $addon_price;

		} elseif ( 'Back' === $faces ) {

			$addon_price = $addon['wpr_single_face_tema'];
			$added_price = $price + $addon_price;

		} elseif ( 'Both front and back' === $faces ) {
			$addon_price = $addon['wpr_both_faces_tema'];
			$added_price = $price + $addon_price;

		} else {
			echo 'Error!';
		}
			$selected[] = array(
				'price' => $added_price,
			);

			echo wp_json_encode( $selected );
			wp_die();

	}

			// T-shirt starts here //////////////////////////////////////////////////////////////////////////////////////////////////////////

	// Add the custom select to product pages
	add_action( 'woocommerce_before_add_to_cart_button', 'add_t_shirt_text_cs', 10, 0 );
	function add_t_shirt_text_cs() {
		if ( has_term( 'T-shirt', 'product_cat' ) ) {
			?>
					<table class="variations printed-shirt-table" cellspacing="0">
						<tbody>
							<tr>
								<td class="label">
									<label for="text-printed-on"><?php _e( 'Text printed on', 'woocommerce' ); ?></label>
								</td>
								<td class="value">
									<select name="text-printed-on" id="print-selector">
					<?php
					$terms = get_terms(
						array(
							'taxonomy'   => 'pa_text-printed-on',
							'hide_empty' => false,
						)
					);
					foreach ( $terms as $term ) {
						if ( 'Not printed' === $term->name ) {
							echo '<option selected="selected" value="' . $term->name . '">' . $term->name . '</option>';
						} else {
							echo '<option value="' . $term->name . '">' . $term->name . '</option>';
						}
					}
					?>
									</select>
								</td>
							</tr>
							<tr id="print-text-row" style="display:none;">
						<td class="label">
							<label for="text"><?php _e( 'T-shirt text', 'woocommerce' ); ?></label>
						</td>
						<td class="value">
							<textarea type="text" name="t-shirt-text" value="" id="print-text-input" maxlength ="75"></textarea>
						</td>
						<td>
						<span id="char_count">0/75</span>
								</td>
					</tr>
						</tbody>
					</table>
						<?php
		}
	}

			// Save the custom product select data in Cart item
			add_filter( 'woocommerce_add_cart_item_data', 'save_to_cart_t_shirt_text_cs', 10, 2 );
	function save_to_cart_t_shirt_text_cs( $cart_item_data, $product_id ) {
		$cart_item_data['text-printed-on'] = 'Not printed';
		$cart_item_data['t-shirt-text']    = 'N/A';

		if ( isset( $_POST['text-printed-on'] ) && isset( $_POST['t-shirt-text'] ) ) {
			$cart_item_data['text-printed-on'] = $_POST['text-printed-on'];
			$cart_item_data['t-shirt-text']    = $_POST['t-shirt-text'];
			$product                           = wc_get_product( $product_id );
			$price                             = $product->get_price();
			$addon                             = get_option( 'wpr_options' );

			if ( 'Front' === $_POST['text-printed-on'] ) {

				$addon_price                 = $addon['wpr_single_face_tema'];
				$cart_item_data['new_price'] = $price + $addon_price;

			} elseif ( 'Back' === $_POST['text-printed-on'] ) {

				$addon_price                 = $addon['wpr_single_face_tema'];
				$cart_item_data['new_price'] = $price + $addon_price;

			} elseif ( 'Both front and back' === $_POST['text-printed-on'] ) {

				$addon_price                 = $addon['wpr_both_faces_tema'];
				$cart_item_data['new_price'] = $price + $addon_price;

			} else {

				$cart_item_data['new_price'] = $price;

			}

			// When add to cart action make an unique line item
			$cart_item_data['unique_key'] = md5( microtime() . rand() );
			WC()->session->set( 'custom_data_face', $_POST['text-printed-on'] );
			WC()->session->set( 'custom_data_text', $_POST['t-shirt-text'] );
		}

		return $cart_item_data;
	}

			// Render the custom product select in cart and checkout
			add_filter( 'woocommerce_get_item_data', 'render_custom_select_meta_on_cart_and_checkout', 10, 2 );
	function render_custom_select_meta_on_cart_and_checkout( $cart_data, $cart_item ) {

		$custom_items = array();

		if ( ! empty( $cart_data ) ) {
			$custom_items = $cart_data;
		}

		$custom_select_value = $cart_item['text-printed-on'];
		$custom_items[]      = array(
			'name'    => __( 'Text printed on', 'woocommerce' ),
			'value'   => $custom_select_value,
			'display' => $custom_select_value,
		);

		$custom_field_value = $cart_item['t-shirt-text'];

		$custom_items[] = array(
			'name'    => __( 'T-shirt text', 'woocommerce' ),
			'value'   => $custom_field_value,
			'display' => $custom_field_value,
		);

		return $custom_items;
	}

			// Add the the custom product field as item meta data in the order
			add_action( 'woocommerce_add_order_item_meta', 'tshirt_select_order_meta_handler', 10, 3 );
	function tshirt_select_order_meta_handler( $item_id, $cart_item, $cart_item_key ) {
		$custom_select_value = $cart_item['text-printed-on'];
		if ( ! empty( $custom_select_value ) ) {
			wc_update_order_item_meta( $item_id, 'pa_text-printed-on', $custom_select_value );
		}
		$custom_field_value = $cart_item['t-shirt-text'];
		if ( ! empty( $custom_field_value ) ) {
			wc_update_order_item_meta( $item_id, 'pa_t-shirt-text', $custom_field_value );
		}
	}


			add_action( 'woocommerce_before_calculate_totals', 'before_calculate_totals', 10, 1 );
	function before_calculate_totals( $cart_obj ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		// Iterate through each cart item
		foreach ( $cart_obj->get_cart() as $key => $value ) {
			if ( isset( $value['new_price'] ) ) {
				$price = $value['new_price'];
				$value['data']->set_price( ( $price ) );
			}
		}
	}
