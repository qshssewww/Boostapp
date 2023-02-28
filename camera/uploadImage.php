<?php

require 'imageClasses.php';
require 'Browser.php';
require_once __DIR__ . '/../app/initcron.php';
require_once __DIR__ . '/../office/services/LoggerService.php';

try {
    function correctImageOrientation($filename)
    {
        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($filename);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if ($orientation != 1) {
                    $img = imagecreatefromjpeg($filename);
                    $deg = 0;
                    switch ($orientation) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }
                    if ($deg) {
                        $img = imagerotate($img, $deg, 0);
                    }
                    // then rewrite the rotated image back to the disk as $filename
                    imagejpeg($img, $filename, 95);
                } // if there is some rotation necessary
            } // if have the exif orientation info
        } // if function exists
    }

    $filePath = $_FILES['myfile']['tmp_name'];

    $fileSize = filesize($filePath);
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $filetype = finfo_file($fileInfo, $filePath);

    if ($fileSize === 0) {
        throw new InvalidArgumentException('The file is empty.');
    }

    if ($fileSize > 1024 * 1024 * 10) { // 10 MB (1 byte * 1024 * 1024 * 10 (for 10 MB))
        throw new InvalidArgumentException('The file is too large');
    }

    $allowedTypes = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
    ];

    if (!array_key_exists($filetype, $allowedTypes)) {
        throw new InvalidArgumentException('Wrong file type');
    }

    $UserId = (int)$_REQUEST['recordsid'];

    $extension = $allowedTypes[$filetype];
    $imageName = $UserId . '_' . time() . '.' . $extension;

    correctImageOrientation($filePath);

    $image = new SimpleImage($filePath);
    $image->square(200);
    $image->maxareafill(200, 200, 255, 255, 255);
    $image->save(getcwd() . '/uploads/small/' . $imageName);

    $image = new SimpleImage($filePath);
    $image->square(500);
    $image->maxareafill(500, 500, 255, 255, 255);
    $image->save(getcwd() . '/uploads/large/' . $imageName);

    require_once '../app/initcron.php';

    DB::table('users')
        ->where('id', $UserId)
        ->update(array('UploadImage' => $imageName));

    $redirect_uri = get_loginboostapp_domain() . '/office/AgentProfile.php?u=' . $UserId;
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
} catch (\Throwable $e) {
    LoggerService::error($e->getMessage());
}