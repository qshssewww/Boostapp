<?php


require_once '../../app/init.php';

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;

$GetUrl = DB::table('settings')->where('CompanyNum', $CompanyNum)->first();
$StudioUrl = $GetUrl->StudioUrl;
$page = DB::table('payment_pages')->where('id', $_REQUEST['data']['pageId'])->first();
$pagesCnt = DB::table('payment_pages')->where('CompanyNum', $CompanyNum)->count();
$pageCount = $pagesCnt + 1;

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
      global $page;  
      $results = DB::table('payment_pages')
                      ->where('id', $page->id)
                      ->limit(1)  
                      ->get();
        if ($results) 
            return $results[0]->pageImg;
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
     global $page; 
         
     $data = array(
           'pageImg' => ''
        );    
        
      DB::table('payment_pages')
               ->where('id', $page->id)
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
     global $pageCount;
     global $page;   
        $image->name = $StudioUrl.'-'.$page->id.'~pageImg.' . $image->type;
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
        global $pageCount;
        global $page; 
        $image->name = $StudioUrl.'-'.$page->id.'pageImg.' . $image->type;
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
        global $pageCount;
        global $page; 
            
        $data = array(
            'pageImg' => $StudioUrl.'-'.$page->id.'pageImg-pageImg.'.$image->type
        );    
        
        $update = DB::table('payment_pages')
        ->where('id', $page->id)
        ->update($data);   
        
    }
);

// Create new ImgPicker instance.
new ImgPicker($options);
