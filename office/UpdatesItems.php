<?php require_once '../app/init.php'; ?>


<?php if (Auth::check()): ?>

    <?php

    $UserId = Auth::user()->id;
    $CompanyNum = Auth::user()->CompanyNum;
    $Dates = date('Y-m-d H:i:s');

    $TypeDoc = $_REQUEST['TypeDoc'];

    $TempId = @$_REQUEST['TempId'];

    $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();

    $GeneralItemId = $SettingsInfo->GeneralItemId;

    if ($SettingsInfo->CompanyVat == '0' && $TypeDoc != '300') {
        $Vat = $SettingsInfo->Vat;
    } else {
        $Vat = '0';
    }

    //// פתיחת הזמנה זמנית
    if (@$TempId != '') {
        $headerimage = DB::table('temp')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $TempId)->where('TypeDoc', '=', $TypeDoc)->where('Status', '=', '0')->first();
    } else {

        if (!empty($_REQUEST['ClientId'])) {
            $ClientId = $_REQUEST['ClientId'];
            $headerimage = DB::table('temp')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->where('TypeDoc', '=', $TypeDoc)->where('Status', '=', '0')->first();
        } else {
            $ClientId = '0';
            $headerimage = DB::table('temp')->where('CompanyNum', '=', $CompanyNum)->where('UserId', '=', Auth::user()->id)->where('TypeDoc', '=', $TypeDoc)->where('Status', '=', '0')->first();
        }
    }

    if (!empty($headerimage)) {

        $TempId = $headerimage->id;

        if (!empty($_REQUEST['ClientId'])) {
            $ClientId = $_REQUEST['ClientId'];
        } else {
            $ClientId = $headerimage->ClientId;
        }

        DB::table('temp')
            ->where('UserId', $UserId)
            ->where('id', $TempId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->update(array('ClientId' => $ClientId, 'TypeDoc' => $TypeDoc, 'Dates' => $Dates));


    } else {

        /// Crate New

        if (!empty($_REQUEST['ClientId'])) {
            $ClientId = $_REQUEST['ClientId'];
        } else {
            $ClientId = '0';
        }

        $TempId = DB::table('temp')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'ClientId' => $ClientId, 'Dates' => $Dates, 'UserId' => $UserId, 'Vat' => $Vat)
        );

    }

    if (!empty($_REQUEST['ItemId'])) {
        $ItemId = $_REQUEST['ItemId'];

        $Items = DB::table('items')->where('id', $ItemId)->first();
        if (!empty($_REQUEST['ItemTextNew'])) {
            $ItemName = stripslashes($_REQUEST['ItemTextNew']);
        } else {
            $ItemName = $Items->ItemName;
        }
        $ItemText = @$Items->Remarks;

        /// בדיקת קבלת סכום חדש    
        $ItemPrice = $Items->ItemPrice;
        $ItemQuantity = '1';
        $Itemtotal = $ItemPrice;

        $headerTemps = DB::table('templist')
            ->where('TempId', $TempId)
            ->where('ItemId', $ItemId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('ItemId', '!=', $GeneralItemId)
            ->get();

        $AddQuantity = '1';

    } else {

        @$TempListId = $_REQUEST['TempListId'];

        if (@$_REQUEST['DellId'] != '') {
            $DellId = $_REQUEST['DellId'];
            DB::table('templist')->where('id', '=', $DellId)->where('CompanyNum', '=', $CompanyNum)->delete();
        }


        if (@$_REQUEST['ActTemp'] != '') {
            $AddQuantity = '0';
            /// עדכון רשימת פריטים
            $headerTemps = DB::table('templist')
                ->where('TempId', $TempId)
                ->get();
        } else {
            /// עדכון פריט בודד
            $headerTemps = DB::table('templist')
                ->where('TempId', $TempId)
                ->where('id', $TempListId)
                ->where('CompanyNum', '=', $CompanyNum)
                ->get();
        }

    }


    if (!empty($headerTemps)) {

        foreach ($headerTemps as $headerTemp) {
            $Vat = DB::table('temp')->where('id', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->pluck('Vat');

            if (@$_REQUEST['ItemPrice'] != '') {
                $ItemPrice = $_REQUEST['ItemPrice'];
                $Act = $_REQUEST['Act'];
                $AddQuantity = '0';
            } else {
                $ItemPrice = $headerTemp->ItemPrice;
                $Act = '1';
            }

//            if (@$_REQUEST['ActTemp'] != '') {
//                if ($Vat == '0' && @$_REQUEST['ActTemp'] != '') {
//                    $ItemPrice = $headerTemp->ItemPriceVat;
//                } else {
//                    $ItemPrice = $headerTemp->ItemPrice;
//                    // $Vat = $Vat;
//                    $TotalVatItemPrice = $ItemPrice * $Vat / 100;
//                    $TotalVatItemPrice = round($TotalVatItemPrice, 2);
//                    $ItemPrice = round($ItemPrice + $TotalVatItemPrice, 2);
//                }
//            }

            if ($Vat == '0') {
                // $ItemPrice = $ItemPrice;
                $TotalVatItemPrice = '0';
            } else {
                if ($SettingsInfo->CompanyVat == '0' && $Act == '1') {

                    $Vats = '1.' . $Vat;
                    // $Vat = $Vat;

                    $TotalVatItemPrice = $ItemPrice / $Vats;
                    // $TotalVatItemPrice = $TotalVatItemPrice;
                    // $TotalVatItemPrice = round($ItemPrice - $TotalVatItemPrice, 2);

                    // $ItemPrice = round($ItemPrice - $TotalVatItemPrice, 2);
                    $TotalVatItemPrice = $ItemPrice - $TotalVatItemPrice;
                    $ItemPrice = $ItemPrice - $TotalVatItemPrice;


                } else {

                    // $ItemPrice = $ItemPrice;

                    // $Vat = $Vat;
                    $TotalVatItemPrice = $ItemPrice * $Vat / 100;

                }
            }

// בדיקת כמות והנחה קיימת לפריט


            if (@$_REQUEST['ItemDiscountType'] != '') {
                $DiscountsTypeItem = $_REQUEST['ItemDiscountType'];
                $AddQuantity = '0';
            } else {
                $DiscountsTypeItem = $headerTemp->ItemDiscountType;
            }

            if (@$_REQUEST['ItemDiscount'] != '') {
                $DiscountsItem = $_REQUEST['ItemDiscount'];
                $DiscountsItemDB = $_REQUEST['ItemDiscount'];
                $AddQuantity = '0';
            } else {
                $DiscountsItem = $headerTemp->ItemDiscount;
                $DiscountsItemDB = $headerTemp->ItemDiscount;
            }

            if (@$_REQUEST['ItemQuantity'] != '') {
                $ItemQuantity = $_REQUEST['ItemQuantity'];
                if ($ItemQuantity == '0') {
                    $ItemQuantity = '1';
                }

            } else {
                $ItemQuantity = $headerTemp->ItemQuantity + @$AddQuantity;
            }


            $Total = $ItemPrice * $ItemQuantity;

            $CheckTotal = $ItemPrice * $ItemQuantity + $TotalVatItemPrice;

            if ($DiscountsTypeItem == '1') {

                if ($DiscountsItem > '100') {
                    $DiscountsItem = '100';
                    $DiscountsItemDB = '100';
                }

            } else {

                if ($DiscountsItem > $CheckTotal) {
                    $DiscountsItem = $CheckTotal;
                    $DiscountsItemDB = $CheckTotal;
                }

            }


            if ($Vat == '0' || $DiscountsTypeItem == '1') {
                $TotalVatDiscount = $DiscountsItem;
            } else {
                if ($SettingsInfo->CompanyVat == '0') {

                    $Vats = '1.' . $Vat;
                    // $Vat = $Vat;

                    $TotalVatDiscount = $DiscountsItem / $Vats;
                    // $TotalVatDiscount = $TotalVatDiscount;
                    $TotalVatDiscount = $DiscountsItem - $TotalVatDiscount;

                    $DiscountsItem = $DiscountsItem - $TotalVatDiscount;


                } else {
                    // $DiscountsItem = $DiscountsItem;
                }
            }


            if ($DiscountsTypeItem == '1') {

                $NewDiscount = $Total * $DiscountsItem / 100;
                $TotalNewPrice = $Total - $NewDiscount;
                $Total = $TotalNewPrice;
            } else {

                $TotalNewPrice = $Total - $DiscountsItem;
                $NewDiscount = $DiscountsItem;
                $Total = $TotalNewPrice;

            }

            if ($Vat == '0') {
                $TotalVat = '0';
            } else {

                $TotalVat = $Total * $Vat / 100;
                $TotalVat = $TotalVat;

            }


            $ItemTotal = $Total + $TotalVat;
            $ItemDiscountAmount = $NewDiscount;

            $ItemPriceVat = $ItemPrice;
            $ItemPrice = $ItemPrice + $TotalVatItemPrice;

            /// בדיקת עיגול אגורות

            $CheckAgura = $ItemTotal - $Total;

            if ($TotalVat != $CheckAgura) {

                $MinusAgura = $TotalVat - $CheckAgura;
                $TotalVat = $TotalVat - $MinusAgura;

            }

            //$TotalVatItemPrice *= $ItemQuantity;



            if (@$_REQUEST['ItemTitle'] != '') {
                $ItemNameFix = stripslashes(@$_REQUEST['ItemTitle']);
            } else {
                $ItemNameFix = $headerTemp->ItemName;
            }

            DB::table('templist')
                ->where('id', $headerTemp->id)
                ->where('TempId', $TempId)
                ->where('CompanyNum', '=', $CompanyNum)
                ->update(array('TypeDoc' => $TypeDoc, 'ItemName' => $ItemNameFix, 'ItemPrice' => $ItemPrice, 'ItemPriceVat' => $ItemPriceVat, 'ItemPriceVatDiscount' => $Total, 'ItemDiscount' => $DiscountsItemDB, 'ItemDiscountAmount' => $ItemDiscountAmount, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $DiscountsTypeItem, 'Itemtotal' => $ItemTotal, 'VatAmount' => $TotalVat));


        }

    } //// צור חדש

    else {

        if (@$_REQUEST['DellId'] != '') {
            $DellId = $_REQUEST['DellId'];
            DB::table('templist')->where('id', '=', $DellId)->where('CompanyNum', '=', $CompanyNum)->delete();

        } else {

            if (@$ItemId == $GeneralItemId) {

                /// יצירת פריט כללי

                $ItemDiscount = '0';
                $ItemDiscountType = '1';
                $SKU = '0';
                $ItemName = lang('general_item');

                $TempList = DB::table('templist')->insertGetId(
                    array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'ItemId' => $ItemId, 'SKU' => $SKU, 'ItemName' => $ItemName, 'ItemPrice' => $ItemPrice, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $ItemDiscountType, 'ItemDiscount' => $ItemDiscount, 'Itemtotal' => $Itemtotal, 'Vat' => $Vat)
                );

            } else {

                if (@$ItemId != '') {

                    /// יצירת פריט קיים

                    $ItemDiscount = '0';
                    $ItemDiscountType = '1';
                    $SKU = '0';


                    $Vat = DB::table('temp')->where('id', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->pluck('Vat');
                    $TotalVatItemPrice = '0';
                    if ($Vat == '0') {
                        $ItemPrice = $ItemPrice;
                    } else {
                        if ($SettingsInfo->CompanyVat == '0') {

                            $Vats = '1.' . $Vat;
                            // $Vat = $Vat;

                            $TotalVatItemPrice = $ItemPrice / $Vats;
                            $TotalVatItemPrice = $TotalVatItemPrice;
                            $TotalVatItemPrice = $ItemPrice - $TotalVatItemPrice;

                            $ItemPrice = $ItemPrice - $TotalVatItemPrice;


                        } else {

                            // $ItemPrice = $ItemPrice;

                            // $Vat = $Vat;
                            $TotalVatItemPrice = $ItemPrice * $Vat / 100;
                            $TotalVatItemPrice = $TotalVatItemPrice;


                        }
                    }

                    $Total = $ItemPrice;

                    $TotalVat = $TotalVatItemPrice;


                    $ItemTotal = $Total + $TotalVat;
                    $ItemPriceVat = $ItemPrice;
                    $ItemPrice = $ItemPrice + $TotalVatItemPrice;


                    $TempList = DB::table('templist')->insertGetId(
                        array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'ItemId' => $ItemId, 'SKU' => $SKU, 'ItemName' => $ItemName, 'ItemPrice' => $ItemPrice, 'ItemPriceVat' => $ItemPriceVat, 'ItemPriceVatDiscount' => $ItemPriceVat, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $ItemDiscountType, 'ItemDiscount' => $ItemDiscount, 'Itemtotal' => $ItemTotal, 'Vat' => $Vat, 'VatAmount' => $TotalVat)
                    );

                }
            }
        }
    }


//// עדכון טבלה ראשית

    $Vat = DB::table('temp')->where('id', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->pluck('Vat');
    $DiscountsItem = DB::table('temp')->where('id', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->pluck('Discount');
    $DiscountsTypeItem = DB::table('temp')->where('id', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->pluck('DiscountType');
    $Total = DB::table('templist')->where('TempId', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->sum('ItemPriceVatDiscount');
    $TotalNew = $Total;

    if ($Total == '0' || $Total == '' || $Total == '0.00') {
        DB::table('temp')
            ->where('id', $TempId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->update(array('Amount' => '0', 'VatAmount' => '0', 'RoundDiscount' => '0', 'AmountVat' => '0', 'DiscountType' => '1', 'Discount' => '0', 'DiscountAmount' => '0'));


    } else {
        if ($Vat == '0' || $DiscountsTypeItem == '1') {
            $TotalVat = '0';
        } else {
            if ($SettingsInfo->CompanyVat == '0') {

                $Vats = '1.' . $Vat;
                // $Vat = $Vat;

                $TotalVatDiscount = $DiscountsItem / $Vats;
                // $TotalVatDiscount = $TotalVatDiscount;
                $TotalVatDiscount = $DiscountsItem - $TotalVatDiscount;

                $DiscountsItem = $DiscountsItem - $TotalVatDiscount;


            } else {
                // $DiscountsItem = $DiscountsItem;
            }
        }


        if ($DiscountsTypeItem == '1') {

            $NewDiscount = $Total * $DiscountsItem / 100;
            $NewDiscount = $NewDiscount;
            $TotalNewPrice = $Total - $NewDiscount;
            $Total = $TotalNewPrice;
            $DiscountType2 = '%';
        } else {

            $TotalNewPrice = $Total - $DiscountsItem;
            $NewDiscount = $DiscountsItem;
            $Total = $TotalNewPrice;
            $DiscountType2 = '₪';

        }


        if ($Vat == '0') {
            $TotalVat = '0';
        } else {

            $totalItemPrices = DB::table('templist')->select('Itemtotal', 'ItemQuantity')->where('TempId', $TempId)->get();
            $TotalVat = 0;

            if (!empty($totalItemPrices)) {
                foreach ($totalItemPrices as $totalItemPrice) {
                    $totalFixItemPrice = $totalItemPrice->Itemtotal;
                    $TotalVat += ($totalFixItemPrice) - ($totalFixItemPrice / 1.17);
                }
            } else {
                $TotalVat = $TotalVatItemPrice;
            }
        }


        $ItemTotal = $Total + $TotalVat;
        $ItemDiscountAmount = $NewDiscount;

        //// עיגול אגורות

        $A = round($Total + $TotalVat, 2);
        $B = round($Total + $TotalVat, 1);

        $RoundDiscount = $A >= $B ? $A - $B : $B - $A;

        if (round($ItemTotal + $RoundDiscount, 2) > $B) {
            $RoundDiscount *= -1;
        }

        $ItemTotal = $ItemTotal + $RoundDiscount;


        DB::table('temp')
            ->where('id', $TempId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->update(array('Amount' => number_format((float)$ItemTotal, 2, '.', ''), 'AmountVat' => number_format((float)$TotalNew, 2, '.', ''), 'RoundDiscount' => number_format((float)$RoundDiscount, 2, '.', ''), 'VatAmount' => number_format((float)$TotalVat, 2, '.', ''), 'DiscountAmount' => number_format((float)$ItemDiscountAmount, 2, '.', '')));

    }


///// החזרת מספר טבלה זמנית

    // $TempId = $TempId;


//// בדיקת תשלום בקופה
    $TempInfo = DB::table('temp')->where('id', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->where('TypeDoc', '=', $TypeDoc)->where('Status', '=', '0')->first();
    $GetAmountCount = DB::table('temp_receipt_payment')->where('TempId', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->count();
    $GetAmountNow = DB::table('temp_receipt_payment')->where('TempId', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->sum('Amount');
    $GetAmountExcess = DB::table('temp_receipt_payment')->where('TempId', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->sum('Excess');

    if (@$GetAmountExcess > '0' && $TempInfo->Amount > $GetAmountNow) {

        DB::table('temp_receipt_payment')
            ->where('TempId', $TempId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('TypePayment', '=', '1')
            ->update(array('Excess' => number_format((float)'0', 2, '.', '')));

        $UpdatePaymentPage = '1';
    }
    if (@$GetAmountCount > '0') {
        $UpdatePaymentPage = '1';
    }

    ?>


    <div id="MeItem">

        <table class="table table-striped text-start" style="margin-bottom: 0px;">

            <thead>

            <tr>
                <th class="text-start" style="width: 5%;">X</th>
                <th class="text-start" style="width: 30%;"><?php echo lang('item_name') ?></th>
                <th class="text-start" style="width: 15%;"><?php echo lang('Item_price_without_tax') ?></th>
                <th class="text-start" style="width: 15%;"><?php echo lang('Item_price_including_tax') ?></th>
                <th class="text-start" style="width: 10%;"><?php echo lang('quantity') ?></th>
                <th class="text-start" style="width: 10%;"><?php echo lang('line_discount') ?></th>
                <th class="text-start" style="width: 15%;"><?php echo lang('total') ?></th>

            </tr>

            </thead>
            <tbody>

<?php 

$headertemplists = DB::table('templist')->where('TempId', '=', $TempId)->where('CompanyNum' ,'=', $CompanyNum)->get();

            foreach ($headertemplists as $headertemplist) { ?>

                <tr>
                    <td style="width: 5%;">
                        <a class="CloseEditItems" href="javascript:void(0);" id="DellItemAll<?php echo $headertemplist->id ?>">
                        <i class="fas fa-minus-circle text-danger fa-lg"></i>
                        </a>
                    </td>
                    <td style="width: 30%;">
                        <a class="CloseEditItems" style="cursor:pointer;"id="NewTitleButton<?php echo $headertemplist->id ?>">
                            <span class="text-dark"><?php echo $headertemplist->ItemName ?></span>
                        </a>
                        <span class="text-dark OpenEditItems"><?php echo $headertemplist->ItemName ?></span>
                    </td>
                    <td style="width: 15%;">
                        <a style="cursor:pointer;" class="CloseEditItems" id="NewPricesButton<?php echo $headertemplist->id ?>">
                            <i class="text-dark fas fa-edit fa-fw"></i>
                        </a> 
                        <?php echo $headertemplist->ItemPriceVat ?> ₪
                    </td>
                    <td style="width: 15%;">
                        <a style="cursor:pointer;" class="CloseEditItems" id="NewPriceButton<?php echo $headertemplist->id ?>">
                            <i class="text-dark fas fa-edit fa-fw"></i>
                        </a> <?php echo $headertemplist->ItemPrice ?> ₪
                    </td>
                    <td style="width: 10%;">
                        <a style="cursor:pointer;" class="CloseEditItems" id="NewQuantityButton<?php echo $headertemplist->id ?>">
                            <i class="text-dark fas fa-edit fa-fw"></i>
                        </a> <?php echo $headertemplist->ItemQuantity ?>
                    </td>
                    <td style="width: 10%;">
                        <a style="cursor:pointer;" class="CloseEditItems" id="DiscountButton<?php echo $headertemplist->id ?>">
                            <i class="text-dark fas fa-edit fa-fw"></i>
                        </a> <?php if ($headertemplist->ItemDiscountType == '1') {
                            echo $headertemplist->ItemDiscount;
                            echo '%';
                        } else if ($headertemplist->ItemDiscountType == '2') {
                            echo '₪';
                            echo $headertemplist->ItemDiscountAmount;
                        } else {
                            echo $headertemplist->ItemDiscount;
                            echo '%';
                        }; ?>
                        <script>


                            $('#DellItemAll<?php echo $headertemplist->id ?>').off().on('click', function (e) {

                                /* Stop form from submitting normally */
                                e.preventDefault(e);
                                /* Get some values from elements on the page: */
                                var url = 'UpdatesItems.php?TypeDoc=<?php echo $headertemplist->TypeDoc; ?>&DellId=<?php echo $headertemplist->id ?>&TempListId=<?php echo $headertemplist->id; ?>&TempId=<?php echo $TempId; ?>';
                                $('#GetItems').load(url + '#MeItem', null, window.updateTotalAmount);
                                return false;
                            });


                            var discount<?php echo $headertemplist->id ?> = $('.discount<?php echo $headertemplist->id ?>');

                            $('#DiscountButton<?php echo $headertemplist->id ?>').off().on('click', function (event) {
                                event.preventDefault(event);
                                discount<?php echo $headertemplist->id ?>.show();

                            });

                            $('#DiscountClose<?php echo $headertemplist->id ?>').off().on('click', function (event) {
                                event.preventDefault(event);
                                discount<?php echo $headertemplist->id ?>.hide();

                            });


                            $('#Newdiscountemsubmit<?php echo $headertemplist->id ?>').off().on('click', function (e) {

                                /* Stop form from submitting normally */
                                e.preventDefault(e);
                                /* Get some values from elements on the page: */
                                var values = $('#AddDiscountItem<?php echo $headertemplist->id ?>').serialize();

                                $("#GetItems").load("UpdatesItems.php?" + values + "#MeItem", null, window.updateTotalAmount);
                                return false;
                            });


                            var NewPrice<?php echo $headertemplist->id ?> = $('.NewPrice<?php echo $headertemplist->id ?>');

                            $('#NewPriceButton<?php echo $headertemplist->id ?>').off().on('click', function (event) {
                                event.preventDefault(event);
                                NewPrice<?php echo $headertemplist->id ?>.show();

                                //   white.hide();
                            });

                            $('#NewPriceClose<?php echo $headertemplist->id ?>').off().on('click', function (event) {
                                event.preventDefault(event);
                                NewPrice<?php echo $headertemplist->id ?>.hide();
                            });


                            $('#NewPriceItemsubmit<?php echo $headertemplist->id ?>').off().on('click', function (e) {

                                /* Stop form from submitting normally */
                                e.preventDefault(e);
                                /* Get some values from elements on the page: */
                                var values = $('#NewPriceItem<?php echo $headertemplist->id ?>').serialize();
                                $("#GetItems").load("UpdatesItems.php?" + values + "#MeItem", null, window.updateTotalAmount);
                                return false;
                            });


                            var NewPrices<?php echo $headertemplist->id ?> = $('.NewPrices<?php echo $headertemplist->id ?>');

                            $('#NewPricesButton<?php echo $headertemplist->id ?>').off().on('click', function (event) {
                                event.preventDefault(event);
                                NewPrices<?php echo $headertemplist->id ?>.show();

                            });

                            $('#NewPricesClose<?php echo $headertemplist->id ?>').off().on('click', function (event) {
                                event.preventDefault(event);
                                NewPrices<?php echo $headertemplist->id ?>.hide();

                            });


                            $('#NewPricesItemsubmit<?php echo $headertemplist->id ?>').off().on('click', function (e) {

                                /* Stop form from submitting normally */
                                e.preventDefault(e);
                                /* Get some values from elements on the page: */
                                var values = $('#NewPricesItem<?php echo $headertemplist->id ?>').serialize();

                                $("#GetItems").load("UpdatesItems.php?" + values + "#MeItem", null, window.updateTotalAmount);
                                return false;
                            });


                            var NewQuantity<?php echo $headertemplist->id ?> = $('.NewQuantity<?php echo $headertemplist->id ?>');

                            $('#NewQuantityButton<?php echo $headertemplist->id ?>').off().on('click', function (event) {
                                event.preventDefault(event);
                                NewQuantity<?php echo $headertemplist->id ?>.show();

                            });

                            $('#NewQuantityClose<?php echo $headertemplist->id ?>').off().on('click', function (event) {
                                event.preventDefault(event);
                                NewQuantity<?php echo $headertemplist->id ?>.hide();

                            });


                            $('#NewQuantitysubmit<?php echo $headertemplist->id ?>').off().on('click', function (e) {

                                /* Stop form from submitting normally */
                                e.preventDefault(e);
                                /* Get some values from elements on the page: */
                                var values = $('#NewQuantityItem<?php echo $headertemplist->id ?>').serialize();

                                $("#GetItems").load("UpdatesItems.php?" + values + "#MeItem", null, window.updateTotalAmount);
                                return false;
                            });

                            var NewTitle<?php echo $headertemplist->id ?> = $('.NewTitle<?php echo $headertemplist->id ?>');

                            $('#NewTitleButton<?php echo $headertemplist->id ?>').off().on('click', function (event) {
                                event.preventDefault(event);
                                NewTitle<?php echo $headertemplist->id ?>.show();

                            });

                            $('#NewTitleClose<?php echo $headertemplist->id ?>').off().on('click', function (event) {
                                event.preventDefault(event);
                                NewTitle<?php echo $headertemplist->id ?>.hide();

                            });


                            $('#NewTitlesubmit<?php echo $headertemplist->id ?>').off().on('click', function (e) {

                                /* Stop form from submitting normally */
                                e.preventDefault(e);
                                /* Get some values from elements on the page: */
                                var values = $('#NewTitleItems<?php echo $headertemplist->id ?>').serialize();

                                $("#GetItems").load("UpdatesItems.php?" + values + "#MeItem", null, window.updateTotalAmount);
                                return false;
                            });


                        </script>
                    </td>
                    <td class="filterable-cell"
                        style="width: 15%;"><?php echo $headertemplist->Itemtotal ?> ₪
                    </td>

                </tr>


                <tr class="NewTitle<?php echo $headertemplist->id ?> table-secondary" style="display:none;">
                    <td colspan="8" height="50" style="height:50px; width:100%; align-content:center;">

                        <form name="NewTitleItem<?php echo $headertemplist->id ?>"
                              id="NewTitleItems<?php echo $headertemplist->id ?>"
                              style="align-content:center;" autocomplete="off" method="post">class="form-inline"
                            <div class="form-row align-items-center">
                                <input name="TempListId" type="hidden" value="<?php echo $headertemplist->id ?>">
                                <input name="TypeDoc" type="hidden" value="<?php echo $headertemplist->TypeDoc ?>">
                                <input name="TempId" type="hidden" value="<?php echo $TempId; ?>">

                                <div class="form-group">
                                    <label style="padding-left: 5px;"><?php echo lang('item_name') ?></label>
                                    <input type="text" name="ItemTitle" class="form-control"
                                           value="<?php echo $headertemplist->ItemName ?>" required>
                                </div>

                                <div class="col-auto">
                                    <button name="submit" id="NewTitlesubmit<?php echo $headertemplist->id ?>"
                                            class="btn btn-primary"><?php echo lang('save_changes_button') ?></button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" id="NewTitleClose<?php echo $headertemplist->id ?>"
                                            class="btn btn-light"><?php echo lang('close') ?></button>
                                </div>

                            </div>
                        </form>

                    </td>

                </tr>


                <tr class="discount<?php echo $headertemplist->id ?> table-secondary" style="display:none;">
                    <td colspan="8" height="50" style="height:50px; width:100%; align-content:center;">

                        <form name="AddDiscountItem<?php echo $headertemplist->id ?>"
                              id="AddDiscountItem<?php echo $headertemplist->id ?>" class="form-inline"
                              style="align-content:center;" autocomplete="off" method="post">
                            <div class="form-row align-items-center">
                                <input name="TempListId" type="hidden" value="<?php echo $headertemplist->id ?>">
                                <input name="TypeDoc" type="hidden" value="<?php echo $headertemplist->TypeDoc ?>">
                                <input name="TempId" type="hidden" value="<?php echo $TempId; ?>">

                                <div class="form-check form-check-inline">
                                    <label class="form-check-label" style="text-decoration:underline;"><?php echo lang('discount_type') ?> </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" name="ItemDiscountType" class="form-check-input"
                                           id="inlineRadio1<?php echo $headertemplist->id ?>"
                                           value="1" <?php if ($headertemplist->ItemDiscountType == '1') {
                                        echo 'checked';
                                    } else {
                                    }; ?>>
                                    <label class="form-check-label" for="inlineRadio1<?php echo $headertemplist->id ?>"
                                           style="padding-right: 5px;"> % </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" name="ItemDiscountType" class="form-check-input"
                                           id="inlineRadio2<?php echo $headertemplist->id ?>"
                                           value="2" <?php if ($headertemplist->ItemDiscountType == '2') {
                                        echo 'checked';
                                    } else {
                                    }; ?>>
                                    <label class="form-check-label" for="inlineRadio2<?php echo $headertemplist->id ?>"
                                           style="padding-right: 5px;"> ₪ </label>
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label" style="padding-left: 5px; text-decoration:underline;"><?php echo lang('add_discount') ?></label>
                                    <input type="text" name="ItemDiscount" min="0" class="form-control"
                                           placeholder="<?php echo lang('only_numbers') ?>" value="<?php echo $headertemplist->ItemDiscount ?>"
                                           onkeypress='validate(event)' required>
                                </div>

                                <div class="col-auto">
                                    <button type="submit" name="submit"
                                            id="Newdiscountemsubmit<?php echo $headertemplist->id ?>"
                                            class="btn btn-primary"><?php echo lang('save_changes_button') ?></button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" id="DiscountClose<?php echo $headertemplist->id ?>"
                                            class="btn btn-light"><?php echo lang('close') ?></button>
                                </div>

                            </div>
                        </form>

                    </td>

                </tr>

                <tr class="NewPrice<?php echo $headertemplist->id ?> table-secondary" style="display:none;">
                    <td colspan="8" height="50" style="height:50px; width:100%; align-content:center;">

                        <form name="NewPriceItem<?php echo $headertemplist->id ?>"
                              id="NewPriceItem<?php echo $headertemplist->id ?>" class="form-inline"
                              style="align-content:center;" autocomplete="off" method="post">
                            <div class="form-row align-items-center">
                                <input name="TempListId" type="hidden" value="<?php echo $headertemplist->id ?>">
                                <input name="Act" type="hidden" value="1">
                                <input name="TypeDoc" type="hidden" value="<?php echo $headertemplist->TypeDoc ?>">
                                <input name="TempId" type="hidden" value="<?php echo $TempId; ?>">

                                <div class="form-group">
                                    <label style="padding-left: 5px;"><?php echo lang('Item_price_including_tax') ?></label>
                                    <input type="text" name="ItemPrice" min="0" class="form-control"
                                           placeholder="<?php echo lang('only_numbers') ?>" value="<?php echo $headertemplist->ItemPrice ?>"
                                           onkeypress='validate(event)' required>
                                </div>

                                <div class="col-auto">
                                    <button name="submit" id="NewPriceItemsubmit<?php echo $headertemplist->id ?>"
                                            class="btn btn-primary"><?php echo lang('save_changes_button') ?></button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" id="NewPriceClose<?php echo $headertemplist->id ?>"
                                            class="btn btn-light"><?php echo lang('close') ?></button>
                                </div>

                            </div>
                        </form>

                    </td>

                </tr>

                <tr class="NewPrices<?php echo $headertemplist->id ?> table-secondary" style="display:none;">
                    <td colspan="8" height="50" style="height:50px; width:100%; align-content:center;">

                        <form name="NewPricesItem<?php echo $headertemplist->id ?>"
                              id="NewPricesItem<?php echo $headertemplist->id ?>" class="form-inline"
                              style="align-content:center;" autocomplete="off" method="post">
                            <div class="form-row align-items-center">
                                <input name="TempListId" type="hidden" value="<?php echo $headertemplist->id ?>">
                                <input name="Act" type="hidden" value="0">
                                <input name="TempId" type="hidden" value="<?php echo $TempId; ?>">
                                <input name="TypeDoc" type="hidden" value="<?php echo $TypeDoc; ?>">

                                <div class="form-group">
                                    <label style="padding-left: 5px;"><?php echo lang('Item_price_without_tax') ?></label>
                                    <input type="text" name="ItemPrice" min="0" class="form-control"
                                           placeholder="<?php echo lang('only_numbers') ?>" value="<?php echo $headertemplist->ItemPriceVat ?>"
                                           onkeypress='validate(event)' required>
                                </div>

                                <div class="col-auto">
                                    <button type="submit" name="submit"
                                            id="NewPricesItemsubmit<?php echo $headertemplist->id ?>"
                                            class="btn btn-primary"><?php echo lang('save_changes_button') ?></button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" id="NewPricesClose<?php echo $headertemplist->id ?>"
                                            class="btn btn-light"><?php echo lang('close') ?></button>
                                </div>

                            </div>
                        </form>

                    </td>

                </tr>


                <tr class="NewQuantity<?php echo $headertemplist->id ?> table-secondary" style="display:none;">
                    <td colspan="8" height="50" style="height:50px; width:100%; align-content:center;">

                        <form name="NewQuantityItem<?php echo $headertemplist->id ?>"
                              id="NewQuantityItem<?php echo $headertemplist->id ?>" class="form-inline"
                              style="align-content:center;" autocomplete="off" method="post">
                            <div class="form-row align-items-center">
                                <input name="TempListId" type="hidden" value="<?php echo $headertemplist->id ?>">

                                <input name="TypeDoc" type="hidden" value="<?php echo $headertemplist->TypeDoc ?>">
                                <input name="TempId" type="hidden" value="<?php echo $TempId; ?>">


                                <div class="form-group">
                                    <label style="padding-left: 5px;"><?php echo lang('item_quantity') ?></label>
                                    <input type="number" name="ItemQuantity" min="0" class="form-control"
                                           placeholder="<?php echo lang('only_numbers') ?>" value="<?php echo $headertemplist->ItemQuantity ?>"
                                           onkeypress='validate(event)' required>
                                </div>

                                <div class="col-auto">
                                    <button type="submit" name="submit"
                                            id="NewQuantitysubmit<?php echo $headertemplist->id ?>"
                                            class="btn btn-primary"><?php echo lang('save_changes_button') ?></button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" id="NewQuantityClose<?php echo $headertemplist->id ?>"
                                            class="btn btn-light"><?php echo lang('close') ?></button>
                                </div>
                            </div>
                        </form>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

        <?php

        /// עדכון נתונים למסמך

        $DocsTemps = DB::table('temp')
            ->where('id', $TempId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->first();

        if ($DocsTemps->DiscountType == '2') {
            $DiscountType = '₪';
        } else {
            $DiscountType = '%';
        }
        $DiscountAmount = $DocsTemps->Discount;
        ?>

        <input id="Temptotal" name="Temptotal" type="hidden"
               value="<?php echo number_format((float)$DocsTemps->AmountVat, 2, '.', ''); ?>">
        <input id="TempVat" name="TempVat" type="hidden"
               value="<?php echo number_format((float)$DocsTemps->VatAmount, 2, '.', ''); ?>">

        <input id="TempDiscount" name="TempDiscount" type="hidden"
               value="<?php echo number_format((float)$DiscountAmount, 2, '.', ''); ?>">
        <input id="TempDiscount2" name="TempDiscount2" type="hidden" value="<?php echo $DiscountType; ?>">
        <input id="TempNEWDiscount" name="TempNEWDiscount" type="hidden"
               value="<?php echo number_format((float)$DocsTemps->DiscountAmount, 2, '.', ''); ?>">

        <input id="VATIn" name="VATIn" type="hidden"
               value="<?php echo number_format((float)$DocsTemps->Vat, 2, '.', ''); ?>">

        <input id="Temptotal2" name="Temptotal2" type="hidden"
               value="<?php echo number_format((float)$DocsTemps->Amount, 2, '.', ''); ?>">

        <input id="TempId" name="TempId" type="hidden" value="<?php echo $TempId; ?>">


        <script>

            $('.OpenEditItems').hide();
            <?php if ($DocsTemps->DocsId != '0') { ?>
            $('.CloseCheckBoxPayment').attr("disabled", true);
            $('.CloseEditItems').hide();
            $('.OpenEditItems').show();
            <?php } ?>



            $("#Items1").select2("val", "");
            $("#TextValue").val("");

            <?php if ($TypeDoc == '320' && $DocsTemps->Amount == '0'){ ?>
            $('#ShowPaymentDiv').hide();
            var price2 = document.getElementById('Temptotal2').value;
            $('#ReceiptBtn').prop('disabled', true);
            $("#Finalinvoicenum").val(parseFloat(price2).toFixed(2));
            $("#TrueFinalinvoicenum").val(parseFloat(price2).toFixed(2));

            <?php } else if ($TypeDoc == '320' && $DocsTemps->Amount > '0') { ?>
            $('#ShowPaymentDiv').show();
            $('#ReceiptBtn').prop('disabled', true);
            var price2 = document.getElementById('Temptotal2').value;
            $("#Finalinvoicenum").val(parseFloat(price2).toFixed(2));
            $("#TrueFinalinvoicenum").val(parseFloat(price2).toFixed(2));
            <?php } ?>

            $(document).ready(function () {


                var price = document.getElementById('Temptotal').value;
                var vat = document.getElementById('TempVat').value;
                var discount = document.getElementById('TempDiscount').value;
                var discount2 = document.getElementById('TempDiscount2').value;
                var TempNEWDiscount = document.getElementById('TempNEWDiscount').value;
                var price2 = document.getElementById('Temptotal2').value;
                var VAT2 = document.getElementById('VATIn').value;
                var TempId = document.getElementById('TempId').value;
                document.getElementById('resultFinal').innerHTML = parseFloat(price).toFixed(2);
                document.getElementById('resultVAT').innerHTML = parseFloat(vat).toFixed(2);

                document.getElementById('resultFinalDiscount').innerHTML = parseFloat(TempNEWDiscount).toFixed(2);

                document.getElementById('resultDiscountIn2').innerHTML = parseFloat(discount);
                $("#resultDiscountIn").text(discount2);

                document.getElementById('resultFinal2').innerHTML = parseFloat(price2).toFixed(2);
                document.getElementById('resultVATIn').innerHTML = parseFloat(VAT2);
                $("#TempsId").val(TempId);
                $("#TempsIds").val(TempId);
                $("#TempsIdRemarks").val(TempId);
                $("#TempsIdDiscount").val(TempId);
                $("#TempsIdVat").val(TempId);
                $("#Prints").data('ajax', TempId);
                $("#DocTempId").val(TempId);
                $("#CancelDocs_TempsId").val(TempId);
                $("#TempsIdVat").val(TempId);
                $("#DocTempGroupNumber").val(TempId);


                <?php if ($DocsTemps->RoundDiscount != '0'){ ?>
                document.getElementById('resultFinalRound').innerHTML = parseFloat('<?php echo number_format((float)$DocsTemps->RoundDiscount, 2, '.', ''); ?>').toFixed(2);
                <?php } else { ?>
                document.getElementById('resultFinalRound').innerHTML = parseFloat('<?php echo number_format((float)'0.00', 2, '.', ''); ?>').toFixed(2);
                <?php } ?>

//// חישוב סה"כ לתשלום

                $("#TotalHide2").val(parseFloat(price2).toFixed(2));
                $('#CancelDocButton').prop('disabled', false);

                <?php if (@$UpdatePaymentPage == '1'){ ?>
                $("#DocsPayments").load("DocPaymentInfo.php?TypeDoc=<?php echo $TypeDoc; ?>&TempId=" + TempId + "&Act=99");
                <?php } ?>
            });


        </script>


    </div>

<?php endif ?>
