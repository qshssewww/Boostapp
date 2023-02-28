<?php


require_once '../../app/init.php';

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;

$GetUrl = DB::table('settings')->where('CompanyNum', $CompanyNum)->first();
$StudioUrl = $GetUrl->StudioUrl;

require __DIR__.'/ImgPicker.php';

$options = array(
    // Upload directory path
    'upload_dir' => __DIR__.'/../files/cover/',

    // Upload directory url:
    'upload_url' => 'files/cover/',

    // Image versions:
    'versions' => array(
        'cover' => array(
            'max_width'  => 400,
            'max_height' => 260
        ),
    ),

    /**
     * Load callback.
     *
     * @return string|array
     */
    'load' => function($instance) {
        // return 'avatar.jpg';
        
      global $StudioUrl; 
      global $CompanyNum;
        
      $results = DB::table('appsettings')
                      ->where('CompanyNum', $CompanyNum)
                      ->limit(1)  
                      ->get();
        if ($results) 
            return $results[0]->studioCoverImg;
        else 
            return false;  
        
        
        
    },

    /**
     * Delete callback
     *
     * @param  string $filename
     * @return boolean
     */
    'delete' => function ($filename) {
        
     global $StudioUrl; 
     global $CompanyNum; 
         
     $data = array(
           'studioCoverImg' => '',
        );    
        
      DB::table('appsettings')
               ->where('CompanyNum', $CompanyNum)
               ->limit(1)
               ->update($data);     
        
        
        return true;
    },

    /**
     * Upload start callback.
     *
     * @param  stdClass $image
     * @return void
     */
    'upload_start' => function ($image) {
     global $CompanyNum; 
     global $StudioUrl;    
        $image->name = $StudioUrl.'~cover.' . $image->type;
    },

    /**
     * Upload complete callback.
     *
     * @param  stdClass $image
     * @return void
     */
    'upload_complete' => function ($image) {
    },

    /**
     * Crop start callback.
     *
     * @param  stdClass $image
     * @return void
     */
    'crop_start' => function ($image) {
        global $StudioUrl; 
        $image->name = $StudioUrl.'cover.' . $image->type;
    },

    /**
     * Crop complete callback.
     *
     * @param  stdClass $image
     * @return void
     */
    'crop_complete' => function ($image) {
        
     global $StudioUrl; 
     global $CompanyNum; 
         
     $data = array(
           'studioCoverImg' => $StudioUrl.'cover-cover.'.$image->type
        );    
        
      DB::table('appsettings')
               ->where('CompanyNum', $CompanyNum)
               ->limit(1)
               ->update($data);   
        
    }
);

// Create new ImgPicker instance.
new ImgPicker($options);
