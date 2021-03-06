<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<tr class="item <?php if ( ! empty( $class ) ) echo $class; ?>" data-order_item_id="<?php echo $item_id; ?>">
	<td class="thumb">
		<a href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $_product->id ) . '&action=edit' ) ); ?>" class="tips" data-tip="<?php
			
			echo '<strong>' . __( 'Product ID:', 'woocommerce' ) . '</strong> ' . absint( $item['product_id'] );
			
			if ( $item['variation_id'] ) 
				echo '<br/><strong>' . __( 'Variation ID:', 'woocommerce' ) . '</strong> ' . absint( $item['variation_id'] ); 
			
			if ( $_product->get_sku() ) 
				echo '<br/><strong>' . __( 'Product SKU:', 'woocommerce' ).'</strong> ' . esc_html( $_product->get_sku() );
			
		?>"><?php echo $_product->get_image(); ?></a>
	</td>
	<td class="sku" width="1%">
		<?php if ( $_product->get_sku() ) echo esc_html( $_product->get_sku() ); else echo '-'; ?>
		<input type="hidden" class="order_item_id" name="order_item_id[]" value="<?php echo esc_attr( $item_id ); ?>" />
	</td>
	<td class="name">

		<div class="row-actions">
			<span class="trash"><a class="remove_order_item" href="#"><?php _e( 'Delete item', 'woocommerce' ); ?></a> | </span>
			<span class="view"><a href="<?php echo esc_url( admin_url( 'post.php?post='. absint( $_product->id ) .'&action=edit' ) ); ?>"><?php _e( 'View product', 'woocommerce' ); ?></a>
		</div>

		<?php echo esc_html( $item['name'] ); ?>
		
		<?php
			if ( isset( $_product->variation_data ) ) 
				echo '<br/>' . woocommerce_get_formatted_variation( $_product->variation_data, true );
		?>
		<table class="meta" cellspacing="0">
			<tfoot>
				<tr>
					<td colspan="4"><button class="add_order_item_meta button"><?php _e( 'Add&nbsp;meta', 'woocommerce' ); ?></button></td>
				</tr>
			</tfoot>
			<tbody class="meta_items">
			<?php
				if ( $metadata = $order->has_meta( $item_id )) {
					foreach ( $metadata as $meta ) {
						
						// Skip hidden core fields
						if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array( 
							'_qty',
							'_tax_class',
							'_product_id',
							'_variation_id',
							'_line_subtotal',
							'_line_subtotal_tax',
							'_line_total',
							'_line_tax'
						) ) ) ) continue;
						
						// Handle serialised fields
						if ( is_serialized( $meta['meta_value'] ) ) {
							if ( is_serialized_string( $meta['meta_value'] ) ) {
								// this is a serialized string, so we should display it
								$meta['meta_value'] = maybe_unserialize( $meta['meta_value'] );
							} else {
								continue;
							}
						}

						$meta['meta_key'] = esc_attr( $meta['meta_key'] );
						$meta['meta_value'] = esc_textarea( $meta['meta_value'] ); // using a <textarea />
						$meta['meta_id'] = (int) $meta['meta_id'];			
							
						echo '<tr data-meta_id="' . $meta['meta_id'] . '">
							<td><input type="text" name="meta_key[' . $meta['meta_id'] . ']" value="' . $meta['meta_key'] . '" /></td>
							<td><input type="text" name="meta_value[' . $meta['meta_id'] . ']" value="' . $meta['meta_value'] . '" /></td>
							<td width="1%"><button class="remove_order_item_meta button">&times;</button></td>
						</tr>';
					}
				}
			?>
			</tbody>
		</table>
	</td>

	<?php do_action( 'woocommerce_admin_order_item_values', $_product, $item, absint( $item_id ) ); ?>

	<td class="tax_class" width="1%">
		<select class="tax_class" name="order_item_tax_class[<?php echo absint( $item_id ); ?>]" title="<?php _e( 'Tax class', 'woocommerce' ); ?>">
			<?php
			$item_value = isset( $item['tax_class'] ) ? sanitize_title( $item['tax_class'] ) : '';
			
			$tax_classes = array_filter( array_map( 'trim', explode( "\n", get_option('woocommerce_tax_classes' ) ) ) );
			
			$classes_options = array();
			$classes_options[''] = __( 'Standard', 'woocommerce' );
			
			if ( $tax_classes ) 
				foreach ( $tax_classes as $class )
					$classes_options[ sanitize_title( $class ) ] = $class;
			
			foreach ( $classes_options as $value => $name ) 
				echo '<option value="' . esc_attr( $value ) . '" ' . selected( $value, $item_value, false ) . '>'. esc_html( $name ) . '</option>';
			?>
		</select>
	</td>

	<td class="quantity" width="1%">
		<input type="number" step="any" min="0" autocomplete="off" name="order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" value="<?php echo esc_attr( $item['qty'] ); ?>" size="4" class="quantity" />
	</td>

	<td class="line_subtotal" width="1%">
		<label><?php _e( 'Cost', 'woocommerce' ); ?>: <input type="text" name="line_subtotal[<?php echo absint( $item_id ); ?>]" placeholder="0.00" value="<?php if ( isset( $item['line_subtotal'] ) ) echo esc_attr( $item['line_subtotal'] ); ?>" class="line_subtotal" /></label>

		<label><?php _e( 'Tax', 'woocommerce' ); ?>: <input type="text" name="line_subtotal_tax[<?php echo absint( $item_id ); ?>]" placeholder="0.00" value="<?php if ( isset( $item['line_subtotal_tax'] ) ) echo esc_attr( $item['line_subtotal_tax'] ); ?>" class="line_subtotal_tax" /></label>
	</td>

	<td class="line_total" width="1%">
		<label><?php _e( 'Cost', 'woocommerce' ); ?>: <input type="text" name="line_total[<?php echo absint( $item_id ); ?>]" placeholder="0.00" value="<?php if ( isset( $item['line_total'] ) ) echo esc_attr( $item['line_total'] ); ?>" class="line_total" /></label>

		<label><?php _e( 'Tax', 'woocommerce' ); ?>: <input type="text" name="line_tax[<?php echo absint( $item_id ); ?>]" placeholder="0.00" value="<?php if ( isset( $item['line_tax'] ) ) echo esc_attr( $item['line_tax'] ); ?>" class="line_tax" /></label>
	</td>

</tr>