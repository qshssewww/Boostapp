<?php


ini_set('memory_limit','8000M');

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] != "localhost:8000") {
     if ($_SERVER["SERVER_PORT"] != 443) {
         $redir = "Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
         header($redir);
         exit();
     }
}

/*
|--------------------------------------------------------------------------
| Set PHP Error Reporting
|--------------------------------------------------------------------------
*/

error_reporting(-1);

/*
|--------------------------------------------------------------------------
| Check Extensions
|--------------------------------------------------------------------------
*/

//  if (!extension_loaded('openssl')) {
//  	echo 'OpenSSL PHP extension required.';
//  	exit(1);
//  }

/*
|--------------------------------------------------------------------------
| Register Class Imports
|--------------------------------------------------------------------------
*/

use Hazzard\Foundation\Application;
use Hazzard\Foundation\AliasLoader;
use Hazzard\Foundation\ClassLoader;
use Hazzard\Support\Facades\Facade;
use Hazzard\Config\Repository as Config;
use Hazzard\Config\LoaderManager as ConfigLoader;


/*
|--------------------------------------------------------------------------
| Install Paths
|--------------------------------------------------------------------------
*/

$paths = array(
	'base' => __DIR__.'/..',
	'app'  => __DIR__.'/../app',
	'storage' => __DIR__.'/../app/storage'
);

/*
|--------------------------------------------------------------------------
| Composer Autoload
|--------------------------------------------------------------------------
*/

require_once $paths['base'] .'/vendor/autoload.php';
require_once __DIR__ . '/../office/Classes/Client.php';
require_once __DIR__ . '/../office/services/ClientService.php';

/*
|--------------------------------------------------------------------------
| Set internal character encoding
|--------------------------------------------------------------------------
*/

if (function_exists('mb_internal_encoding')) {
	mb_internal_encoding('utf-8');
}

/*
|--------------------------------------------------------------------------
| Create New Application
|--------------------------------------------------------------------------
*/



$app = new Application;
$app->instance('app', $app);
$app->bindInstallPaths($paths);

/*
|--------------------------------------------------------------------------
| Load Facades
|--------------------------------------------------------------------------
*/
Facade::setFacadeApplication($app);

/*
|--------------------------------------------------------------------------
| Register The Config Manager
|--------------------------------------------------------------------------
*/

$loader = new ConfigLoader($app['path'].'/config');
$app->instance('config', new Config($loader));

/*
|--------------------------------------------------------------------------
| Database Config Loader
|--------------------------------------------------------------------------
|
| Enabling this might affect the performance of your website.
|
*/

 $app->register('Hazzard\Database\DatabaseServiceProvider');
 $loader->setConnection($app['db']);
 $app->instance('config', new Config($loader));

/*
|--------------------------------------------------------------------------
| Register Custom Exception Handling
|--------------------------------------------------------------------------
*/

if (version_compare(PHP_VERSION, '5.5.9', '>=')) {
    $app->startExceptionHandling();

    if (!$app['config']['app.debug']) {
        ini_set('display_errors', 'Off');
    }
}

/*
|--------------------------------------------------------------------------
| Set The Default Timezone
|--------------------------------------------------------------------------
*/

$config = $app['config']['app'];
if (!empty($config['timezone'])) {
	date_default_timezone_set($config['timezone']);
}

/*
|--------------------------------------------------------------------------
| Register The Alias Loader
|--------------------------------------------------------------------------
*/

$aliases = $config['aliases'];


AliasLoader::getInstance($aliases)->register();

/*
|--------------------------------------------------------------------------
| Register The Core Service Providers
|--------------------------------------------------------------------------
*/

$providers = $config['providers'];

$app->getProviderRepository()->load($app, $providers);

/*
|--------------------------------------------------------------------------
| Register The Class Loader
|--------------------------------------------------------------------------
*/

$dirs = array(
	$app['path'].'/models'
);

ClassLoader::getInstance($dirs)->register();

/*
|--------------------------------------------------------------------------
| Load The Events File
|--------------------------------------------------------------------------
*/

if (file_exists($app['path'].'/events.php')) {
	require_once $app['path'].'/events.php';
}

/*
|--------------------------------------------------------------------------
| Fire Init Event
|--------------------------------------------------------------------------
*/

$app['events']->fire('app.init');

//// בדיקת התחברות וחסימת התחברות כפולה מאותו היוזר

$ComanyBarnd = '';

if (Auth::check()) {
    \App\Utils\DebugBar::init();

    $CompanyNum = Auth::user()->CompanyNum;
    $ClientId = Auth::user()->id;
    $UserLang = Auth::user()->language;

    if (isset($UserLang)) {

        $_SESSION['lang'] = $UserLang;
        $dir = ($UserLang !== 'he' && $UserLang !== 'ar') ? 'ltr' : 'rtl';
        $_COOKIE['boostapp_lang'] = $UserLang;
        $_COOKIE['boostapp_dir'] = $dir;
        // make http public cookies
        Cookie::set('boostapp_lang', $UserLang, 0, '/', null, false, false);
        Cookie::set('boostapp_dir', $dir, 0, '/', null, false, false);

    }


    $BrandInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->where('BrandsMain', '!=', '0')->first();
    if ($BrandInfo) {
        $BrandsNames = DB::table('brands')->where('FinalCompanynum', '=', $CompanyNum)->where('Status', '=', '0')->first();
        $ComanyBarnd = ' | ' . @$BrandsNames->BrandName;
    }

} /// סיום בדיקת התחברות כפולה




$day = date("l");

$daynum = date("j");

$month = date("F");
$monthnumber = date("m");

$year = date("Y");
	
if($day == "Monday"){

$day = lang('monday');

}elseif($day == "Tuesday"){

$day = lang('tuesday');

}elseif($day == "Wednesday"){

$day = lang('wednesday');

}elseif($day == "Thursday"){

$day = lang('thursday');

}elseif($day == "Friday"){

$day = lang('friday');

}elseif($day == "Saturday"){

$day = lang('saturday');

}elseif($day == "Sunday"){

$day = lang('sunday');

}



if($month == "January"){

$month = lang('january');
  
}elseif($month == "February"){

$month = lang('february');

}elseif($month == "March"){

$month = lang('march');

}elseif($month == "April"){

$month = lang('april');

}elseif($month == "May"){

$month = lang('may');

}elseif($month == "June"){

$month = lang('june');

}elseif($month == "July"){

$month = lang('july');

}elseif($month == "August"){

$month = lang('august');

}elseif($month == "September"){

$month = lang('september');

}elseif($month == "October"){

$month = lang('october');

}elseif($month == "November"){

$month = lang('november');

}elseif($month == "December"){

$month = lang('december');

}

function NewMonthName($month) {
    
if($month == "January"){

$month = lang('january');
  
}elseif($month == "February"){

$month = lang('february');

}elseif($month == "March"){

$month = lang('march');

}elseif($month == "April"){

$month = lang('april');

}elseif($month == "May"){

$month = lang('may');

}elseif($month == "June"){

$month = lang('june');

}elseif($month == "July"){

$month = lang('july');

}elseif($month == "August"){

$month = lang('august');

}elseif($month == "September"){

$month = lang('september');

}elseif($month == "October"){

$month = lang('october');

}elseif($month == "November"){

$month = lang('november');

}elseif($month == "December"){

$month = lang('december');

}    
 
return $month;    
    
}

$monthNames = array(
	lang('january'), lang('february'), lang('march'), lang('april'), lang('may'), lang('june'), lang('july'), lang('august'), lang('september'), lang('october'), lang('november'), lang('december')
);

$JewishDate = iconv ('WINDOWS-1255', 'UTF-8',jdtojewish(gregoriantojd( date('m'), date('d'), date('Y')), true, CAL_JEWISH_ADD_GERESHAYIM)); // for today

$HolidayTitle = '';

$DateTitleHeader = '<div id="date" style="color:#666; font-size:16px; font-weight:bold; padding-top:7px; float:left;">יום '.$day.' <span style="color:#48AD42;"> '.$daynum.' </span>ב'.$month.', '.$year.' | <span id="date_time"></span><script type="text/javascript">window.onload = date_time("date_time");</script><br />'.@$JewishDate.'  | '.@$ComanyBarnd.'</div>';


function ErrorPage($Title,$Content) {
    echo '<nav aria-label="breadcrumb" dir="rtl">
      <ol class="breadcrumb">	
      <li class="breadcrumb-item"><a href="index.php" class="text-info">'.lang('path_main').'</a></li>
      <li class="breadcrumb-item active">'.$Title.'</li>
      </ol>  
    </nav>  
    <div class="card text-start" dir="rtl">
      <div class="card-body">
        <h5 class="card-title">'.$Title.'</h5>
        <p class="card-text">'.$Content.'</p>
      <button type="button" class="btn btn-info" onClick="javascript:window.history.back();"><i class="fas fa-undo-alt"></i> חזור אחורה</button>
      </div>
    </div>';
}

function DocumentGroupButton($DocId, $TypeId, $TypeHeader, $Status, $DocsInfo)
{
    $CompanyNum = Auth::user()->CompanyNum;
    $CompanyInfo = Company::getInstance();

    echo '<div class="dropdown text-dark">
  <button class="btn btn-primary dropdown-toggle btn-sm btn-cutsom-grey" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    ' . $DocId . '
  </button>
  <div class="dropdown-menu text-start dropdown-menu-right py-0" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item py-7" href="javascript:void(0);" onclick="TINY.box.show({iframe:\'PDF/Docs.php?DocType=' . $TypeId . '&DocId=' . $DocId . '\',boxid:\'frameless\',width:750,height:470,fixed:false,maskid:\'bluemask\',maskopacity:40,closejs:function(){}})"><span class="text-dark">' . lang('view_doc_init') . '</span></a>
    <a class="dropdown-item py-7" href="javascript:void(0);" onClick="SendDocumentModal(' . $TypeId . ',' . $DocId . ',1)"><span class="text-dark">' . lang('send_doc_init') . '</span></a>';

    if((int)$DocsInfo->Status === Docs::STATUS_SOURCE) {
        echo '<a class="dropdown-item btn py-7" onclick="clickAndDisable(this)" target="_blank" href="' . LinkHelper::getPrefixUrlByHttpHost() .  '/office/action/DownloadSourceDoc.php?docId=' . $DocsInfo->id . '">
                <span class="text-dark">' . lang('download_source_doc') . '</span>
               </a>';
    } else {
        echo '<a class="dropdown-item py-7 btn disabled">
                <span class="text-dark">' . lang('download_source_doc') . '</span>
               </a>';
    }

//    <a class="dropdown-item py-7" href="javascript:void(0);" onClick="OpenDocumentModal(' . $TypeId . ',' . $DocId . ')"><span class="text-dark">' . lang('report_doc_init') . '</span></a>';



    /** @var Docs $DocsInfo */

    if (isset($DocsInfo)) {
        //todo-bp-909 (cart) remove-beta
        $CompanySettingsDash = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
        if(in_array($CompanySettingsDash->beta, [1]) && $DocsInfo->TextId !== null) {
            echo ' <a class="dropdown-item py-7" href="javascript:void(0);" onClick="handleOpenRelDocsPopup(' . $DocsInfo->id . ', ' . $DocId . ', '. $TypeId .' )"><span class="text-dark">' . lang("connected_documents_title"). '</span></a>';


            require_once __DIR__ . '/../office/Classes/Docs.php';

            if((int)$DocsInfo->PayStatus !== Docs::PAY_STATUS_CANCELED && (int)$DocsInfo->Refound === Docs::REFUND_STATUS_OFF ) {
                //payment on Invoice
                if(Docs::checkIsInvoice($DocsInfo) && Docs::checkIsOpen($DocsInfo)) {
                    echo '<a class="text-success dropdown-item py-7"  target="_blank" href="' . LinkHelper::getPrefixUrlByHttpHost() .  '/office/checkout.php?docId=' . $DocsInfo->id . '"><span class="text-success">' . lang('checkout_invoice_payment') . '</span></a>';
                }
                //החזר כספי
                if(Docs::checkIsReceiptDocs($DocsInfo) && Docs::checkConnectedInvoiceWithReceipts($DocsInfo))
                {
                    echo '<a class="text-danger dropdown-item py-7"  target="_blank"href="' . LinkHelper::getPrefixUrlByHttpHost() .  '/office/refund.php?docId=' . $DocsInfo->id . '"><span class="text-danger">' . lang('documents_refund') . '</span></a>';
                }
                //קיזוז חוב
                if(Docs::checkIsInvoice($DocsInfo) && Docs::checkIsOpen($DocsInfo)) {
                    echo '<a class="text-danger dropdown-item py-7" href="javascript:void(0);" onClick="OffsetDebtReception(' . $DocsInfo->id . ',' . $DocsInfo->Amount . ',' . $DocsInfo->BalanceAmount . ')"><span class="text-danger">' . lang('debt_offset') . '</span></a>';
                }
                //ביטול מסמך
                if(Docs::checkIsInvoice($DocsInfo) && (int)$DocsInfo->RefAction === Docs::BEFORE_REF_ACTION) {
                    echo '<a class="text-danger dropdown-item py-7" href="javascript:void(0);" onClick="cancelDocumentsByInvoice('.$DocsInfo->id.')"><span class="text-danger">' . lang('checkout_cancel_all_transaction') . '</span></a>';
                }
            }
        } else {
            if ($TypeHeader == '0' && $Status != '8' && $CompanyInfo->__get('BusinessType') != '5' && $DocsInfo->ManualInvoice == '1' && $DocsInfo->Refound == '0') {
                echo '<a class="dropdown-item py-7" href="javascript:void(0);" onClick="ConvertDocumentModal(' . $TypeId . ',' . $DocId . ',' . $TypeHeader . ',305)"><span class="text-dark">' . lang('convert_to_invoice_init') . '</span></a>';
                echo '<a class="dropdown-item py-7" href="javascript:void(0);" onClick="ConvertDocumentModal(' . $TypeId . ',' . $DocId . ',' . $TypeHeader . ',320)"><span class="text-dark">' . lang('convert_to_receipt_init') . '</span></a>';
            }

            if ($TypeHeader == '0' && $Status != '8' && $CompanyInfo->__get('BusinessType') == '5' && $DocsInfo->ManualInvoice == '1' && $DocsInfo->Refound == '0') {
                echo '<a class="dropdown-item py-7" href="javascript:void(0);" onClick="ConvertDocumentModal(' . $TypeId . ',' . $DocId . ',' . $TypeHeader . ',300)"><span class="text-dark">' . lang('convert_transactio_invoice_init') . '</span></a>';
            }

//            if ($TypeHeader != '330' && $TypeHeader != '0' && $TypeHeader != '400' && $TypeHeader != '320' && $DocsInfo->Refound == '0' && $DocsInfo->ManualInvoice == '1') {
//                echo '<a class="text-danger dropdown-item py-7" href="javascript:void(0);" onClick="CancelDocumentModal(' . $TypeId . ',' . $DocId . ',' . $TypeHeader . ')"><span class="text-danger">' . lang('cancel_doc') . '</span></a>';
//            }

            require_once __DIR__ . '/../office/services/payment/PaymentTypeEnum.php';

            if (($TypeHeader == '320' && $DocsInfo->ManualInvoice == '1' && $DocsInfo->Refound == '0')
                || ($TypeHeader == '400' && $DocsInfo->ManualInvoice == '1' && $DocsInfo->Refound == '0')
                || ($TypeHeader == '320' && in_array($DocsInfo->TypeShva, [PaymentTypeEnum::TYPE_MESHULAM, PaymentTypeEnum::TYPE_TRANZILA]) && $DocsInfo->Refound == '0')
                || ($TypeHeader == '400' && in_array($DocsInfo->TypeShva, [PaymentTypeEnum::TYPE_MESHULAM, PaymentTypeEnum::TYPE_TRANZILA]) && $DocsInfo->Refound == '0')) {
                echo '<a class="text-danger dropdown-item py-7" href="javascript:void(0);" onClick="CancelDocumentModalRefound(' . $TypeId . ',' . $DocId . ',' . $TypeHeader . ')"><span class="text-danger">' . lang('cancel_doc') . '</span></a>';
            }
        }
    }
    echo '</div>
</div>';
}

function isMobileDevice()
{
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}


function check_email($email) {  
    if( (preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $email)) || 
        (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,4}$/',$email)) ) { 
         return true;
    } else {
         return false;
    }       
}


function check_phone($phone) {  
    if( (preg_match(Client::mobileRegex, $phone))) {
         return true;
    } else {
         return false;
    }
}

function checkIsAValidDate($DateString){
    return (bool)strtotime($DateString);
}

function AddNewLead($ContactMobile, $Email, $CompanyName)
{
    $UserId = Auth::user()->id;
    $CompanyNum = Auth::user()->CompanyNum;
    $ItemId = Auth::user()->ItemId;

    $validator = Validator::make(
        array('ContactMobile' => $ContactMobile, 'Email' => $Email),
        array('ContactMobile' => 'numeric|digits:10|required_if:Email,', 'Email' => 'email|required_if:ContactMobile,')
    );

    if ($validator->passes()) {
			$CompanyNameExplode = strpos($CompanyName, ' ');
			$FirstName = substr($CompanyName, 0, $CompanyNameExplode);
		    $LastName = substr($CompanyName, $CompanyNameExplode+1);   
			
			if ($FirstName==''){
			$FirstName = lang('without');
			}  
			
			if ($LastName==''){
			$LastName = lang('class_table_name');
			}   
			   
			$CompanyName = $FirstName.' '.$LastName;     
			   
			$Status = '1';
			
			//// בדיקת לקוח קיים במערכת
			if ($ContactMobile!='' && $Email==''){   
			$CheckClient = DB::table('client')->where('ContactMobile', '=', $ContactMobile)->where('CompanyNum', '=', $CompanyNum)->first();   
			}
			else if ($ContactMobile!='' && $Email!=''){
			$CheckClient = DB::table('client')->where('ContactMobile', '=', $ContactMobile)->where('CompanyNum', '=', $CompanyNum)->Orwhere('Email', '=', $Email)->where('CompanyNum', '=', $CompanyNum)->first();	
			}   
			else {
			$CheckClient = DB::table('client')->where('Email', '=', $Email)->where('CompanyNum', '=', $CompanyNum)->first();  	
			}   
        if (@$CheckClient->id!='') {
				
			//// בדיקת ליד קיים על נציג אחר	
				
			$CheckLead = DB::table('pipeline')->where('ClientId', '=', $CheckClient->id)->where('CompanyNum', '=', $CompanyNum)->where('ItemId', '=', $ItemId)->first(); 	
			
			if (@$CheckLead->id!=''){
				
			/// נמצא ליד קיים	
				
			$ErrorText = lang('lead_exist_ajax');
			//json_message($ErrorText, false);	
				
			}	
			else {
				
			/// לא נמצא ליד במערכת - הוספה כליד חדש
				
			if (@$CheckClient->ContactMobile!=''){
			$ContactInfo = $CheckClient->ContactMobile;	
			}
			else {
			$ContactInfo = $CheckClient->Email;	
			}	
				
		    DB::table('pipeline')->insertGetId(
            array('PipeId' => $Status , 'ClientId' => $CheckClient->id, 'CompanyName' => $CheckClient->CompanyName, 'ContactInfo' => $ContactInfo, 'UserId' => $UserId, 'ItemId' => $ItemId, 'CompanyNum' => $CompanyNum) );		
			$ItemDetails = DB::table('items')->where('id', $ItemId)->where('CompanyNum', '=', Auth::user()->CompanyNum)->first();
			CreateLogMovement(lang('created_lead_card_init').' '.$ItemDetails->ItemName.' '.lang('to_exist_customer_init'), $CheckClient->id);
				
			}


        } else {
            ////  הוספת לקוח + ליד חדש למערכת
            ClientService::addClient([
                'Email' => $Email,
                'ContactMobile' => $ContactMobile,
                'FirstName' => $FirstName,
                'LastName' => $LastName,
                // Pipeline part
                'PipeId' => $Status,
                'ItemId' => $ItemId,
            ], ClientService::CLIENT_STATUS_LEAD);
        }
    }
}
