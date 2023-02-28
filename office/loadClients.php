<?php 
require_once '../app/init.php';
if(Auth::check()) {
    echo View::make('headernew')->render();
?>

<!-- main content goes here -->

<?php
    echo View::make('footernew')->render();
} else {
    redirect_to('../index.php');
}
?>