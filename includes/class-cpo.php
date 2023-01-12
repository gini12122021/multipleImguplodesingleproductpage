<?php
	if ( ! class_exists( 'cpoBackend' ) ) {
		include_once dirname( __FILE__ ) . '/class-cpobackend.php';
	}
	if ( ! class_exists( 'cpoFrontend' ) ) {
		include_once dirname( __FILE__ ) . '/class-cpofrontend.php';
	}
	class cpo {
		
		public function __construct(){
			
		}
	}
	
	new cpoBackend;
	new cpoFrontend;

