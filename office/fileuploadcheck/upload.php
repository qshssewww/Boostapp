<?php

try {
    $uploads_dir = dirname(__FILE__) . '/uploads';

    if ($_FILES['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES["file"]["tmp_name"];
        // basename() may prevent filesystem traversal attacks;
        // further validation/sanitation of the filename may be appropriate
        $name = basename($_FILES["file"]["name"]);
        $check = move_uploaded_file($tmp_name, "$uploads_dir/$name");
        if (!$check) {
            echo 'nup';
        }
    } else {
        echo 'danggggg';
    }

    echo json_encode('all good', JSON_UNESCAPED_UNICODE);
} catch (Error $e) {
    echo json_encode($e, JSON_UNESCAPED_UNICODE);
}
