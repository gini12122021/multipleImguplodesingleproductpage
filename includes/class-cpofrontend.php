<?php
	class cpoFrontend extends cpo {
		
		public function __construct(){
			add_action( 'wp_enqueue_scripts', array( $this, 'cpo_enqueue_front_script'));
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'cpo_loop_add_to_cart_link'),10,2);
			add_action( 'woocommerce_before_add_to_cart_button', array($this ,'cpo_display_additional_product_fields'), 9 );
			add_filter( 'woocommerce_add_cart_item_data', array($this ,'cpo_add_custom_fields_data_as_custom_cart_item_data'), 10, 2 );
			add_filter( 'woocommerce_get_item_data', array($this,'cpo_display_custom_item_data'), 10, 2 );
			add_action( 'woocommerce_after_cart_item_name', array( $this,'cpo_after_cart_item_name'), 10, 2 );
			add_action( 'woocommerce_checkout_create_order_line_item', array($this ,'cpo_custom_field_update_order_item_meta'), 20, 4 );
			add_action( 'woocommerce_after_order_itemmeta', array($this,'cpo_backend_image_link_after_order_itemmeta'), 10, 3 );
			add_action( 'woocommerce_email_after_order_table', array($this,'cpo_wc_email_new_order_custom_meta_data'), 10, 4);
			/*img uplode ajax */
			add_action( 'wp_ajax_cpo_update_cart_imguplode_custome', array( $this, 'cpo_update_cart_imguplode_custome' ));
			add_action( 'wp_ajax_nopriv_cpo_update_cart_imguplode_custome', array( $this, 'cpo_update_cart_imguplode_custome' ));
			/*remove img uplode custom*/
			add_action( 'wp_ajax_cpo_remove_imgcartid', array( $this, 'cpo_remove_imgcartid') );
			add_action( 'wp_ajax_nopriv_cpo_remove_imgcartid', array( $this, 'cpo_remove_imgcartid') );
			/*hide order meta */
			add_filter('woocommerce_hidden_order_itemmeta', array($this, 'cpo_custom_woocommerce_hidden_order_itemmeta'), 10, 1);
			add_action('woocommerce_thankyou', array($this, 'cpo_teilnehmer_thankyou') ,10,1);
		
		}
		/*Define style and js */
        public function cpo_enqueue_front_script() {   
			wp_enqueue_style( 'cpo-frontstyle', plugin_dir_url( __FILE__ ) . '../assets/css/cpo-frontstyle.css?rand='.rand(1,10000) );
			wp_enqueue_script( 'cpo-frontscript', plugin_dir_url( __FILE__ ) . '../assets/js/cpo-frontscript.js?rand='.rand(1,100), array( 'jquery' ), false, true );
			$variables = array();
			wp_localize_script( 'cpo-frontscript', 'cpo_data',array( 'ajax_url' => admin_url('admin-ajax.php'), 'variables' => $variables ) );
		}

		/*Add to cart button redirect in single page*/
		public function cpo_loop_add_to_cart_link($button, $product ) {
			
				$button_text = __("Add to cart", "woocommerce");
				$button_link = $product->get_permalink();
				$button = '<a class=" button alt " href="' . $button_link . '">' . $button_text . '</a>';
			
			return $button;
		}
		
	   /*Add Multiple Image uplode */
	   // Display additional product fields (+ jQuery code)
	   public function cpo_display_additional_product_fields(){
			?>
			<p class="form-row validate-required" id="image" >
				<label for="file_field"><?php echo __("Upload Image") . ': '; ?>
					<input type='file' name='upload_attachment[]' class="upload_file"  multiple="multiple"  accept='image/*'>
				</label>
			</p>
			<div id="image_preview"></div>
			<?php
		}

		// Add custom fields data as the cart item custom data
		public function cpo_add_custom_fields_data_as_custom_cart_item_data( $cart_item, $product_id ){

		$galleryImages = array();
		
		$irlink=__DIR__;
		$explodev=explode('plugins',$irlink);
	    $dirparth = $explodev[0].'uploads'.'/'.date('Y');
		foreach ($_POST['wc_uplode_img'] as $uplodeimg){

			$img= $uplodeimg;
			$explod_url=explode(';base64,',$img);
			$explod_extension=explode('data:image/',$explod_url[0]);
		
			$img = str_replace('data:image/'.$explod_extension[1].';base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$data = base64_decode($img);
			$file = $dirparth .'/'. uniqid() . '.'.$explod_extension[1];
			
			$success = file_put_contents($file, $data);
			//print $success ? $file : 'Unable to save the file.';
			$urlname=explode('wp-content',$file);
			
			 $newattchment=get_site_url().'/wp-content/'.$urlname[1];
			array_push($galleryImages, $newattchment);
		}

		$cart_item['file_upload'] =$galleryImages;
		$cart_item['file_upload_cust'] =serialize($galleryImages);
		$cart_item['unique_key'] = md5( microtime().rand() );
	



			if (!empty($_FILES['upload_attachment']['name'][0])) {

				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
				
				$files = $_FILES['upload_attachment'];
				$count = 0;
			
				foreach ($files['name'] as $count => $value) {
					if ($files['name'][$count]) {
		
						$file = array(
							'name'     => $files['name'][$count],
							'type'     => $files['type'][$count],
							'tmp_name' => $files['tmp_name'][$count],
							'error'    => $files['error'][$count],
							'size'     => $files['size'][$count]
						);
		
						$upload_overrides = array( 'test_form' => false );
						$upload = wp_handle_upload($file, $upload_overrides);
						$filename = $upload['file'];
						$filetype = wp_check_filetype( basename( $filename ), null );
						$wp_upload_dir = wp_upload_dir();
						$attachment = array(
							'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
							'post_mime_type' => $filetype['type'],
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
							'post_content'   => '',
							'post_status'    => 'inherit'
						);
		
						require_once( ABSPATH . 'wp-admin/includes/image.php' );
						$attachment_url=$wp_upload_dir['url'] . '/' . basename( $filename );
						array_push($galleryImages, $attachment_url);
		
					}
		
					$count++;
		
					//$cart_item['file_upload'] =$galleryImages;
					//$cart_item['file_upload_cust'] =serialize($galleryImages);
					//$cart_item['unique_key'] = md5( microtime().rand() );
				}
			}

			return $cart_item;
		}

		// Display custom cart item data in cart (optional)
		public function cpo_display_custom_item_data( $cart_item_data, $cart_item ) {
       		if ( isset( $cart_item['file_upload']) ){

				foreach( $cart_item['file_upload'] as $mediaid){
					$cart_item_data[] = array(
						'name' => __( 'Image uploaded', 'woocommerce' ),
						'value' =>  '<a id="removeid_'.$cart_item['key'].'" data-cartid="'.$cart_item['key'].'"  data-src="'.$mediaid.'" class="cpo_removeimg">×</a><img src="'.$mediaid.'">',
					
					);
				}
			}
			return $cart_item_data;
		}


		/*cart page uplode multiple image*/
		public function cpo_after_cart_item_name( $cart_item, $cart_item_key ) {

			if ( isset( $cart_item['file_upload']) ){

				foreach( $cart_item['file_upload'] as $mediaid){

					$cart_item_data[] = array(
						'name' => __( 'Image uploaded', 'woocommerce' ),
						'value' =>  '<a id="removeid_'.$cart_item['key'].'" data-cartid="'.$cart_item['key'].'"  data-src="'.$mediaid.'" class="cpo_removeimg">×</a><img src="'.$mediaid.'">',
					
					);

				}
			}

			printf(
				'<div id="cartid"> <label for="file_field">Upload Image<input type="file" name="upload_attachment[]" class="prefix-cart-image"  multiple="multiple"    value="%s" accept="image"
				 id="cart_notes_%s" data-cart-id="%s" ></label></div>',
				'prefix-cart-notes',
				$cart_item_key,
				$cart_item_key,
				 );
			
		  }



		  public function cpo_update_cart_imguplode_custome() {
			// Do a nonce check
			if( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'woocommerce-cart' ) ) {
			wp_send_json( array( 'nonce_fail' => 1 ) );
			exit;
			}
			$cart = WC()->cart->cart_contents;
			$cart_id = $_POST['cart_id'];
			$cart_item = $cart[$cart_id];
			$galleryImages = array();
		
			if( isset($_FILES['image']) && ! empty($_FILES['image']) ) {
				$upload       = wp_upload_bits( $_FILES['image']['name'], null, file_get_contents( $_FILES['image']['tmp_name'] ) );
				$filetype     = wp_check_filetype( basename( $upload['file'] ), null );
				$upload_dir   = wp_upload_dir();
				$upl_base_url = is_ssl() ? str_replace('http://', 'https://', $upload_dir['baseurl']) : $upload_dir['baseurl'];
				$base_name    = basename( $upload['file'] );
		
				// $cart_item['file_upload'] = array(
				// 	'guid'      => $upl_base_url .'/'. _wp_relative_upload_path( $upload['file'] ), // Url
				// 	'file_type' => $filetype['type'], // File type
				// 	'file_name' => $base_name, // File name
				// 	'title'     => ucfirst( preg_replace('/\.[^.]+$/', '', $base_name ) ), // Title
				// );
				$attachment_url=$upl_base_url .'/'. _wp_relative_upload_path( $upload['file'] );
				array_push($galleryImages, $attachment_url);
				
			}
			if(!empty($cart_item['file_upload'])){
				
				$a3= array_merge($galleryImages,$cart_item['file_upload']);
			}else{
				$a3= $galleryImages ;
			}
			
			
			$cart_item['file_upload']=$a3;
			$cart_item['unique_key'] = md5( microtime().rand() ); // Avoid merging items
			WC()->cart->cart_contents[$cart_id] = $cart_item;
			WC()->cart->set_session();
			wp_send_json( array( 'success' => 1,'imgparth'=> $cart_item['file_upload'] ) );

		}
		public function cpo_remove_imgcartid(){
        
			$cart = WC()->cart->cart_contents;
			$cart_id = $_POST['cart_id'];
			$cart_item = $cart[$cart_id];

		
			$imgurl_store=array();
			foreach($cart_item['file_upload'] as $imgurl){

				if($_POST['imgsrc'] == $imgurl ){}
				else{
					array_push($imgurl_store, $imgurl);
				}
				

			}
			
		     $cart_item['file_upload']=$imgurl_store;
			 WC()->cart->cart_contents[$cart_id] = $cart_item;
			 WC()->cart->set_session();
			 wp_send_json( array( 'success' => 1));
			exit;
			
		}

		// Save Image data as order item meta data
		public function cpo_custom_field_update_order_item_meta( $item, $cart_item_key, $values, $order ) {
			if ( isset( $values['file_upload'] ) ){
				$item->update_meta_data( '_img_file', serialize($values['file_upload'])  );
			}
		}
		//hide order meta in order page in admin
		public function cpo_custom_woocommerce_hidden_order_itemmeta($arr) {
			$arr[] = '_img_file';
			return $arr;
		}
		
	

		// Admin orders: Display a linked button + the link of the image file
		public function cpo_backend_image_link_after_order_itemmeta( $item_id, $item, $product ) {
			// Only in backend for order line items (avoiding errors)
			if( is_admin() && $item->is_type('line_item') && $file_data = $item->get_meta( '_img_file' ) ){
				// retrieves the value of the user meta "_bookmark_article"

				$customimguplode = unserialize( $file_data );
				if(!empty($customimguplode)){

					foreach($customimguplode as $imgcst){
						echo '<p><a href="'.$imgcst.'" target="_blank" class="button"><img  style="width:60px;height:60px;" src="'.$imgcst.'"></a></p>'; // Optional
					
					}
				}	

				
			}
		}


		// Admin new order email: Display a linked button + the link of the image file
		public function cpo_wc_email_new_order_custom_meta_data( $order, $sent_to_admin, $plain_text, $email ){
			// On "new order" email notifications
			if ( 'new_order' === $email->id ) {
				foreach ($order->get_items() as $item ) {
					if ( $file_data = $item->get_meta( '_img_file' ) ) {
						$customimguplode = unserialize( $file_data );
						if(!empty($customimguplode)){

							foreach($customimguplode as $imgcst){
								echo '<p><a href="'.$imgcst.'" target="_blank" class="button"><img  style="width:60px;height:60px;" src="'.$imgcst.'"></a></p>'; // Optional
							
							}
						}	
		
						
					}
				}
			}
		}
		
			// Display order custom meta data in Order received (thankyou) page

		public	function cpo_teilnehmer_thankyou( $order_id ) {

					$order = wc_get_order( $order_id );
					$count = 1;

					// Loop through order items
					foreach ( $order->get_items() as $item ){
					

						// Loop through item quantity
						for($i = 1; $i <= $item->get_quantity(); $i++ ) {
							if ( $file_data = $item->get_meta( '_img_file' ) ) {
								$customimguplode = unserialize( $file_data );
								if(!empty($customimguplode)){
	
									foreach($customimguplode as $imgcst){
										echo '<p><a href="'.$imgcst.'" target="_blank" class="button"><img  style="width:60px;height:60px;" src="'.$imgcst.'"></a></p>'; // Optional
									
									}
								}
							}
						}
						$count++;
					}
				}

	}



