<?php
	class cpoBackend extends cpo {
		
		public function __construct(){
			add_action( 'admin_enqueue_scripts', array( $this, 'cpo_enqueue_admin_script'));
		}

		public function cpo_enqueue_admin_script(){
			wp_enqueue_style( 'cpo-backendstyle', plugin_dir_url( __FILE__ ) . '../assets/css/cpo-backendstyle.css' );
			wp_enqueue_script( 'cpo-backendscript', plugin_dir_url( __FILE__ ) . '../assets/js/cpo-backendscript.js', array( 'jquery' ), false, true );
		}


		
		
	}