<?php


require_once '../../app/init.php';

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;

$GetUrl = DB::table('settings')->where('CompanyNum', $CompanyNum)->first();
$StudioUrl = $GetUrl->StudioUrl;

require __DIR__.'/ImgPicker.php';

$options = array(
    // Upload directory path
    'upload_dir' => __DIR__.'/../files/logo/',

    // Upload directory url:
    'upload_url' => 'files/logo/',

    // Image versions:
    'versions' => array(
        'logo' => array(
            'max_width'  => 200,
            'max_height' => 200
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
            return $results[0]->logoImg;
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
           'logoImg' => ''
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
        $image->name = $StudioUrl.'~logo.' . $image->type;
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
        $image->name = $StudioUrl.'logo.' . $image->type;
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
           'logoImg' => $StudioUrl.'logo-logo.'.$image->type
        );    
        
      DB::table('appsettings')
               ->where('CompanyNum', $CompanyNum)
               ->limit(1)
               ->update($data);   
        
    }
);

// Create new ImgPicker instance.
new ImgPicker($options);
