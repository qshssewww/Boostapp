<?php

error_reporting(E_ALL);
ini_set("display_errors", true);

require_once '../../app/init.php';

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;

$GetUrl = DB::table('settings')->where('CompanyNum', $CompanyNum)->first();
$StudioUrl = $GetUrl->StudioUrl;
$classId = $_GET['classId'] ?? null;

require __DIR__ . '/ImgPicker.php';

$options = array(
    // Upload directory path
    'upload_dir' => __DIR__ . '/../files/classes/',
    // Upload directory url:
    'upload_url' => 'files/classes/',
    // Image versions:
    'versions' => array(
        'pageImg' => array(
            'max_width' => 400,
            'max_height' => 260
        ),
    ),
    /**
     * Load callback.
     *
     * @return string|array
     */
    'load' => function ($instance) {
        // return 'avatar.jpg';

        global $StudioUrl;
        global $CompanyNum;
//        global $pageCount;
        global $classId;

        if($classId != null) {
            $class = DB::table('boostapp.classstudio_date')->where('id', $classId)->where('CompanyNum', '=', $CompanyNum)->select('Image')->first();
            if($class->Image) {
                return $class->Image;
            }
        }
        return false;





        //   $results = DB::table('appsettings')
        //                   ->where('CompanyNum', $CompanyNum)
        //                   ->limit(1)
        //                   ->get();
        //     if ($results)
        //         return $results[0]->logoImg;
        //     else
        //         return false;
    },
    /**
     * Delete callback
     *
     * @param string $filename
     * @return boolean
     */
    'delete' => function ($filename) {

        global $StudioUrl;
        global $CompanyNum;
       // global $pageCount;


        $data = array(
            'pageImg' => ''
        );

        //   DB::table('appsettings')
        //            ->where('CompanyNum', $CompanyNum)
        //            ->limit(1)
        //            ->update($data);


        return true;
    },
    /**
     * Upload start callback.
     *
     * @param stdClass $image
     * @return void
     */
    'upload_start' => function ($image) {
        global $CompanyNum;
        global $StudioUrl;

        $image->name = $StudioUrl . '-' .uniqid(). '~pageImg.' . $image->type;
    },
    /**
     * Upload complete callback.
     *
     * @param stdClass $image
     * @return void
     */
    'upload_complete' => function ($image) {
        
    },
    /**
     * Crop start callback.
     *
     * @param stdClass $image
     * @return void
     */
    'crop_start' => function ($image) {
        global $StudioUrl;
    //    global $pageCount;
        $image->name = $StudioUrl . '-' .uniqid(). 'pageImg.' . $image->type;
    },
    /**
     * Crop complete callback.
     *
     * @param stdClass $image
     * @return void
     */
    'crop_complete' => function ($image) {

        global $StudioUrl;
        global $CompanyNum;
        global $pageCount;

        $data = array(
            'pageImg' => $StudioUrl . '-' .uniqid(). 'pageImg-pageImg.' . $image->type
        );

        //   DB::table('appsettings')
        //            ->where('CompanyNum', $CompanyNum)
        //            ->limit(1)
        //            ->update($data);
    }
);

// Create new ImgPicker instance.
new ImgPicker($options);
