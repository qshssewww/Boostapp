<div class="col-md-2 col-sm-12" >
   
   
   <div class="card spacebottom">
  <a data-toggle="collapse" href="#MenuSearch" aria-expanded="true" aria-controls="MenuSearch" style="color: black;">
  <div class="card-header text-start">
    <strong><i class="fas fa-search fa-fw"></i> <?php echo lang('documet_search_docs') ?></strong>
  </div>
  </a>
  
  <div class="collapse <?php if ((@$Act == 'Search') && (@$SearchValue != '')) {echo "show";} ?>" id="MenuSearch">
  <div class="card-body text-start">
      <div style="font-size: 13px;padding-bottom: 10px;"><?php echo lang('general_search_docs') ?></div>
<form action="DocsList.php" method="get">
  <div class="form-group row">
    <div class="col-sm-12">
      <select class="form-control mb-2" name="Types">
      	<?php
        $typeTitleArr = array(
          "0" => lang('settings_bids'),
          "200" => lang('Shipping_documents'),
          "100" => lang('docs_orders'),
          "305" => lang('tax_invoice'),
          "320" => lang('Tax_invoices_receipts'),
          "310" => lang('concentration_invoices'),
          "330" => lang('credit_tax_invoices'),
          "400" => lang('receipts'),
          "300" => lang('transaction_invoices'),
          "210" => lang('return_certificates'),
          "1" => lang('manual_tax_invoices'),
          "2" => lang('refund_receipt')
        );
        $typeTitleSingleArr = array(
          "0" => lang('settings_bid'),
          "200" => lang('shipping_document'),
          "100" => lang('docs_order'),
          "305" => lang('tax_invoice_single'),
          "320" => lang('Tax_invoice_receipt'),
          "310" => lang('concentration_invoice'),
          "330" => lang('credit_tax_invoice'),
          "400" => lang('receipt'),
          "300" => lang('transaction_invoice_singe'),
          "210" => lang('return_certificates'),
          "1" => lang('manual_tax_invoice'),
          "2" => lang('refund_receipt')
        );
        $CompanyNum = Auth::user()->CompanyNum;  
        $SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
          
        if ($SettingsInfo->BusinessType=='5' || $SettingsInfo->BusinessType=='6'){  
		$DocsKindSearchs = DB::table('docstable')->where('CompanyNum','=',$CompanyNum)->whereIn('TypeHeader', array(0,300,400))->where('Status','=','0')->get();
        }
        else {
        if (Auth::user()->id=='1'){    
        $DocsKindSearchs = DB::table('docstable')->where('CompanyNum','=',$CompanyNum)->whereIn('TypeHeader', array(0,300,305,320,330,400,2))->where('Status','=','0')->get();   
        }
        else {
        $DocsKindSearchs = DB::table('docstable')->where('CompanyNum','=',$CompanyNum)->whereIn('TypeHeader', array(0,300,305,320,330,400))->where('Status','=','0')->get();      
        }    
        }  
          
		foreach ($DocsKindSearchs as $DocsKindSearch) {
		?>
      		<option value="<?php echo $DocsKindSearch->TypeHeader; ?>" <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Types='.$DocsKindSearch->TypeHeader) !== false) {echo "selected";} ?>><?php echo $typeTitleArr[$DocsKindSearch->TypeHeader]; ?></option>
      	<?php
		}
		?>
      </select>
    </div>
    <div class="col-sm-12">
      <input type="text" name="SearchValue" class="form-control" placeholder="<?php echo lang('what_look_docs') ?>" value="<?php echo $SearchValue; ?>" required autocomplete="off">
    </div>
  </div>
  <div class="form-group row">
    <div class="col-md-12 col-sm-12">
      <input type="hidden" name="Act" value="Search">
      <button type="submit" class="btn btn-primary"><?php echo lang('search_button') ?></button>
      <?php if ((@$Act == 'Search') && (@$SearchValue != '')) {echo '<a href="DocsList.php?Types='.$Types.'" class="btn btn-dark">'.lang('clear_docs').'</a>';} ?>
	  </div>
  </div>

</form>
      
  </div>
	</div></div>
   
   <div class="card spacebottom" style="margin-top: 20px;">
  <a data-toggle="collapse" href="#MenuNewDoc" aria-expanded="true" aria-controls="MenuNewDoc" style="color: black;">
  <div class="card-header text-start">
    <strong><i class="fas fa-plus-square fa-fw"></i> <?php echo lang('generate_document_docs') ?></strong>
  </div>
  </a>
  
  <div class="collapse <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Docs.php') !== false) {echo "show";} ?>" id="MenuNewDoc">
  <div class="card-body">
      
<div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
      	<?php
		foreach ($DocsKindSearchs as $DocsKindSearch) {
		?>
 			<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Docs.php?Types='.$DocsKindSearch->TypeHeader) !== false) {echo "active";} ?>" href="Docs.php?Types=<?php echo $DocsKindSearch->TypeHeader; ?>" aria-selected="true"><?php echo $typeTitleSingleArr[$DocsKindSearch->TypeHeader]; ?></a>
      	<?php
		}
		?>
</div>      
      
  </div>
	</div></div>
    
    <div class="card spacebottom" style="margin-top: 20px;">
  <a data-toggle="collapse" href="#MenuArchiveDoc" aria-expanded="true" aria-controls="MenuArchiveDoc" style="color: black;">
  <div class="card-header text-start">
    <strong><i class="fas fa-archive fa-fw"></i> <?php echo lang('docs_archive') ?></strong>
  </div>
  </a>
  
  <div class="collapse <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'DocsList.php') !== false) {echo "show";} ?>" id="MenuArchiveDoc">
  <div class="card-body">
      
<div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
      	<?php
		foreach ($DocsKindSearchs as $DocsKindSearch) {
		?>
 			<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'DocsList.php?Types='.$DocsKindSearch->TypeHeader) !== false) {echo "active";} ?>" href="DocsList.php?Types=<?php echo $DocsKindSearch->TypeHeader; ?>" aria-selected="true"><?php echo $typeTitleArr[$DocsKindSearch->TypeHeader]; ?></a>
      	<?php
		}
		?>
</div>      
      
  </div>
	</div></div>
    
    
</div>






<script>
	
$(document).ready(function(){
  var windowWidth = $(window).width();
  if(windowWidth <= 1024) //for iPad & smaller devices
     $('#MenuNewDoc, #MenuArchiveDoc, #MenuSearch').removeClass('show');
	 $('html,body').scrollTop(0);
});
	</script>
