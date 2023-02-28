<?php

// Error reporting
//error_reporting(0);

// HTTP access control
// header('Access-Control-Allow-Origin: yourwebsite.com');
// header('Access-Control-Allow-Origin: www.yourwebsite.com');

require_once '../app/init.php';

$user_id = Auth::user()->id;

require dirname(__FILE__) . '/ImgPicker.php';

require dirname(__FILE__) . '/Database/Database.php';


$options = array(

	// Upload directory path
	'upload_dir' => dirname(__FILE__) . '/../files/',

	// Upload directory url:
	'upload_url' => 'files/',

    // Image versions:
    'versions' => array(
    	'header' => array(
    		'max_width' => 1000,
    		'max_height' => 170
    	),
    ),

    /**
	 * 	Load callback
	 *
	 *  @param 	ImgPicker 		$instance
	 *  @return string|array
	 */
  'load' => function($instance) {
        global $user_id;        
        // Select the image for the current user
        $db = new Database;
        $results = $db->table('header_images')
                      ->where('user_id', $user_id)
                      ->limit(1)
                      ->get();
        if ($results) 
            return $results[0]->image;
        else 
            return false;
    },
    
	
    'delete' => function($filename, $instance) {
    	return true;
    },
	
    // Upload start callback
    'upload_start' => function($image, $instance) {
        global $user_id;
        // Name the temp image as $user_id
        $image->name = '~'.$user_id.'.'.$image->type;	
    },
    // Crop start callback
    'crop_start' => function($image, $instance) {
       global $user_id;
       // Change the name of the image
       $image->name = $user_id.'.'.$image->type;
    },
    // Crop complete callback
    'crop_complete' => function($image, $instance) {
        global $user_id;
        // Save the image to database
        $data = array(
	       'user_id' => $user_id,
	       'image' => $image->name,
		   'image_header' => $user_id.'-header.'.$image->type,
        );

        $db = new Database;
        // First check if the image exists
        $results = $db->table('header_images')
                      ->where('user_id', $user_id)
                      ->limit(1)
                      ->get();

        // If exists update, otherwise insert
        if ($results) 
            $db->table('header_images')
               ->where('user_id', $user_id)
               ->limit(1)
               ->update($data);
        else
            $db->table('header_images')->insert($data);
    }
);

// Create ImgPicker instance
new ImgPicker($options);