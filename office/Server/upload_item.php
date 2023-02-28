<?php


require_once '../../app/init.php';

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;

$GetUrl = DB::table('settings')->where('CompanyNum', $CompanyNum)->first();
$StudioUrl = $GetUrl->StudioUrl;
$itemId = isset($_GET['data']['itemId']) ? $_GET['data']['itemId'] : null;


// $pages = DB::table('payment_pages')->where('CompanyNum', $CompanyNum)->count();
// $pageCount = $pages++;

require __DIR__.'/ImgPicker.php';

$options = array(
    // Upload directory path
    'upload_dir' => __DIR__.'/../files/items/',

    // Upload directory url:
    'upload_url' => 'files/items/',

    // Image versions:
    'versions' => array(
        'pageImg' => array(
            'max_width'  => 400,
            'max_height' => 220
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
        global $itemId;

        if($itemId != null) {
            $item = DB::table('boostapp.items')->where('id', $itemId)->where('CompanyNum', '=', $CompanyNum)->select('Image')->first();
            if($item->Image) {
                return $item->Image;
            }
            return false;
        }
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
    //  global $pageCount; 
         
    //  $data = array(
    //        'pageImg' => ''
    //     );   
    $image->name = '';    
        
        
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
    //  global $pageCount;    
        $image->name = $StudioUrl.'-'.uniqid().'.' . $image->type;
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
        // global $pageCount; 
        $image->name = $StudioUrl.'-'.uniqid().'.'. $image->type;
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
    //  global $pageCount; 
    
     $data = array(
           'pageImg' => $StudioUrl.'-'.uniqid().'.'.$image->type
        );  
        
    }
);

// Create new ImgPicker instance.
new ImgPicker($options);
