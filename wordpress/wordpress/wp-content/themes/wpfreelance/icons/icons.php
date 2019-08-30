<?php
	function box_icons(){
		return array(
			'earning' => '<img src="'. get_template_directory_uri().'/icons/earning_second.png" />',			
		);
	}
	function box_icon($icon){
		$icons =   box_icons();
		return $icons[$icon];
	}
	function the_box_icon($icon){		
		echo box_icon($icon);
	}

?>