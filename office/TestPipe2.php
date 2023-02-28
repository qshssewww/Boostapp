
   <?php
 require_once '../app/init.php'; 

    $paged=$_POST['page'];
	$resultsPerPage=3;
    if($paged>0){
           $page_limit=$resultsPerPage*($paged-1);
           $pagination_sql="$page_limit, $resultsPerPage";
           }
    else{
    $pagination_sql="0,$resultsPerPage";
    }
    $PipeLines = DB::table('pipeline')->where('PipeId','=', 1)->limit($pagination_sql)->get();                                 
    foreach ($PipeLines as $PipeLine) { 
	$CompanyName = $PipeLine->CompanyName;
	$ContactInfo = $PipeLine->ContactInfo;	
	$ClientId = $PipeLine->ClientId;	
	if(count($PipeLines)>0){
		echo '<li>'.@$CompanyName.'</li>';
	}
}
if($resultsPerPage == $resultsPerPage){ ?>
    <li class="loadbutton"><button class="loadmore" data-page="<?php echo  $paged+1 ;?>">Load More</button></li>
 <?php
  }else{
    echo "<li>No More Feeds</li>";
 }
?>