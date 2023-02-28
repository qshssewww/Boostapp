<?php 
if (Auth::check()) {

    echo View::make('modals.pms')->render();
    
} else {
	echo View::make('modals.login')->render();

		?>

		<?php 

}