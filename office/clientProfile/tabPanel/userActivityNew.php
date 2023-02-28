<div class="card spacebottom">
    <div class="card-header text-start">
        <i class="fas fa-list-alt">
        </i>
        <b><?php echo lang('customer_card_membership') ?>
        </b>
    </div>
    <div class="card-body text-end">
        <div class="row text-start " style="padding-bottom: 15px;">
            <div class="col-md-12">
                <?php
                if (!$isRandomClient) {
                    $checkTryLimit = ClientActivities::where('CompanyNum', $CompanyNum)
                        ->where('Department', 3)
                        ->where('Status', '!=', 2)
                        ->where(function ($q) use ($ClientId) {
                            return $q->where('ClientId', $ClientId)
                                ->orWhere('TrueClientId', 'LIKE', '%' . $ClientId . '%');
                        })->sum('TrueBalanceValue');

                    $linkCart = LinkHelper::getPrefixUrlByHttpHost() .  '/office/cart.php?u=' .$ClientId;
                    ?>
                    <a href="<?=$linkCart?>" target="_blank"
                       class="btn btn-primary text-white"><?php echo lang('customer_card_add_membership') ?>
                    </a>
                <?php } ?>
            </div>
        </div>
        <?php if (@$Supplier->PayClientId == '0') {
        } else {
            $CheckClientInfo = DB::table('client')->where('CompanyNum', $CompanyNum)->where('id', $Supplier->PayClientId)->first();
            ?>
            <div class="row text-start">
                <div class="alertb alert-warning"><?php echo lang('familty_customer_notice') ?>:
                    <a href="ClientProfile.php?u=<?php echo @$CheckClientInfo->id; ?>">
                        <?php echo htmlentities(@$CheckClientInfo->CompanyName); ?>
                    </a>, <?php echo lang('family_customer_notice_2') ?>
                </div>
            </div>
        <?php } ?>
        <?php
        $CheckClientInfoer = DB::table('client')->where('CompanyNum', $CompanyNum)->where('PayClientId', $Supplier->id)->get();
        foreach ($CheckClientInfoer as $CheckClientInfo) {
            if (@$CheckClientInfo->id != '') {
                $CheckClientInfos = DB::table('client')->where('CompanyNum', $CompanyNum)->where('id', $CheckClientInfo->id)->first();
                ?>
                <div class="row text-start" style="padding-bottom: 15px;padding-right: 30px;">
                    <div class="alertb alert-warning"><?php echo lang('family_customer_notice_3') ?>:
                        <a href="ClientProfile.php?u=<?php echo @$CheckClientInfos->id; ?>">
                            <?php echo htmlentities(@$CheckClientInfos->CompanyName); ?>
                        </a>.
                    </div>
                </div>
            <?php }
        } ?>
        <?php
        $CheckClientInfoer = DB::select('select id,ClientId,ItemText from client_activities where CompanyNum = "' . $CompanyNum . '" AND Status = 0 AND FIND_IN_SET("' . $Supplier->id . '",TrueClientId) > 0 ');
        foreach ($CheckClientInfoer as $CheckClientInfo) {
            if ($CheckClientInfo->id) {
                $CheckClientInfos = DB::table('client')->where('CompanyNum', $CompanyNum)->where('id', $CheckClientInfo->ClientId)->first();
                ?>
                <div class="row text-start" style="padding-bottom: 15px;padding-right: 30px;">
                    <div class="alertb alert-warning"><?php echo lang('family_membership_notice') ?>:
                        <?php echo $CheckClientInfo->ItemText; ?> ::
                        <a href="ClientProfile.php?u=<?php echo @$CheckClientInfos->id; ?>">
                            <?php echo htmlentities(@$CheckClientInfos->CompanyName); ?>
                        </a>.
                    </div>
                </div>
            <?php }
        } ?>
        <div class="row">
            <table class="table dt-responsive text-start wrap ActivityTable" cellspacing="0" width="100%"
                   id="ActivityTable">
                <thead class="thead-dark">
                <tr>
                    <th style="text-align:start;">#
                    </th>
                    <th style="text-align:start;"><?php echo lang('customer_card_table_membership') ?>
                    </th>
                    <th style="text-align:start;"><?php echo lang('price') ?>
                    </th>
                    <th style="text-align:start;"><?php echo lang('remainder_of_payment') ?>
                    </th>
                    <th style="text-align:start;"><?php echo lang('customer_card_buy_date') ?>
                    </th>
                    <th style="text-align:start;"><?php echo lang('customer_card_start_date') ?>
                    </th>
                    <th style="text-align:start;"><?php echo lang('customer_card_end_date') ?>
                    </th>
                    <th style="text-align:start;"><?php echo lang('entries_number_membership') ?>
                    </th>
                    <th style="text-align:start;"> <?php echo lang('status_table') ?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = '1';
                $ColorClass = '';
                $LineThrough = '';
                $ColorTextClass = 'class="text-success"';
                $MemberShipClients = DB::table('client_activities')->where('CompanyNum', $CompanyNum)->where('ClientId', $Supplier->id)
                    ->where('isDisplayed', 1)->orderBy('Status', 'ASC')->orderBy('CardNumber', 'DESC')->get();
                $clientRegObj = new ClientRegistrationFees();
                $clientRegs = $clientRegObj->clientRegForClientProfile($Supplier->id, $CompanyNum, count($MemberShipClients));
                if ($clientRegs != false && !empty($clientRegs)) {
                    $MemberShipClients = array_merge($clientRegs, $MemberShipClients);
                }

                $membershipIdsList = array_unique(array_column($MemberShipClients, 'MemberShip'));
                sort($membershipIdsList);
                $membershipTypesList = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->whereIn('id', $membershipIdsList)->get();
                $tempList = [];
                foreach ($membershipTypesList as $item) {
                    $tempList[$item->id] = $item;
                }
                $membershipTypesList = $tempList;

                foreach ($MemberShipClients as $MemberShipClient) {

                    // get quantity
                    if (isset($MemberShipClient->ReceiptId)) {
                        $MemberShipClientDocId = json_decode($MemberShipClient->ReceiptId)->data[0]->DocId;
                        $MemberShipClientQuantity = OrderItems::getOrderQuantity($MemberShipClientDocId, $MemberShipClient->ItemId);
                    } else {
                        $MemberShipClientQuantity = 1;
                    }
                    $ColorClass = '';
                    $LineThrough = '';
                    $ColorTextClass = 'class="text-success"';

                    if (!isset($MemberShipClient->registration) || $MemberShipClient->registration != 1) {
                        $membership_type = $membershipTypesList[$MemberShipClient->MemberShip] ?? null;
                    }
                    if (!isset($membership_type) || $MemberShipClient->MemberShip == 'BA999') {
                        $Type = lang('no_membership_type');
                    } else {
                        $Type = $membership_type->Type;
                    }
                    if ($MemberShipClient->TrueDate >= date('Y-m-d')) {
                        $TrueDateColor = 'text-success';
                    } else {
                        $TrueDateColor = 'text-danger';
                    }
                    if (isset($MemberShipClient->registration) && $MemberShipClient->registration == 1) {
                        $Type = lang('permanent_payment_clientprofile'); //lang('no_membership_type');
                        if ($MemberShipClient->stiilValid == 0) {
                            $ColorClass = 'class="text-secondary"';
                            $TrueBalanceValueColor = 'text-secondary';
                            $TrueDateColor = 'text-secondary';
                            $ColorTextClass = 'class="text-secondary"';
                            $LineThrough = 'style="text-decoration: line-through;"';
                        }
                    }
                    if ($MemberShipClient->TrueBalanceValue >= '1') {
                        $TrueBalanceValueColor = 'text-success';
                    } else {
                        $TrueBalanceValueColor = 'text-danger';
                    }
                    if ($MemberShipClient->Status == '2') {
                        $ColorClass = 'class="text-secondary"';
                        $TrueBalanceValueColor = 'text-secondary';
                        $TrueDateColor = 'text-secondary';
                        $ColorTextClass = 'class="text-secondary"';
                        $LineThrough = 'style="text-decoration: line-through;"';
                    } else if ($MemberShipClient->Status == '3') {
                        $ColorClass = 'class="text-secondary"';
                        $TrueBalanceValueColor = 'text-secondary';
                        $TrueDateColor = 'text-secondary';
                        $ColorTextClass = 'class="text-secondary"';
                        $LineThrough = '';
                    }
//// חישוב זמן ניצול מנוי
                    if ($MemberShipClient->Department == '1') {
                        $start = strtotime(@$MemberShipClient->StartDate);
                        $end = strtotime(@$MemberShipClient->TrueDate);
                        $current = strtotime(date('Y-m-d'));
                        @$completed = round(((@$current - @$start) / (@$end - @$start)) * 100);
                        if (@$completed <= 0) {
                            $completed = '0';
                        } else if (@$completed > 100) {
                            $completed = '100';
                        } else {
                            $completed = $completed;
                        }
                    } else if ($MemberShipClient->Department == '2' || $MemberShipClient->Department == '3') {
                        $start = @$MemberShipClient->BalanceValue;
                        $end = @$MemberShipClient->TrueBalanceValue;
                        @$completed = round(($start - $end) / ($start) * 100);
                        if (@$completed <= 0) {
                            $completed = '0';
                        } else if (@$completed > 100) {
                            $completed = '100';
                        } else {
                            $completed = $completed;
                        }
                        if ($MemberShipClient->TrueDate != '' && $MemberShipClient->TrueDate <= date('Y-m-d')) {
                            $completed = '100';
                        }
                    }
                    $totalPrice = (float)number_format($MemberShipClient->ItemPrice * $MemberShipClientQuantity, 2);
                    $discount = $MemberShipClient->DiscountAmount ?? 0;
                    $totalPriceAfterDiscount = number_format($totalPrice - $discount, 2);
                    ?>
                    <tr
                        <?php echo $ColorClass; ?>>
                        <!--1 number-->
                        <td>
                            <?php echo $MemberShipClient->CardNumber; ?>
                        </td>
                        <!--2 title-->
                        <td
                            <?php echo $LineThrough; ?>
                            <?php echo $ColorTextClass; ?>>
                            <?php if (Auth::userCan('55')): ?>
                            <div class='d-flex align-items-center'>
                                <a <?php if (!$isRandomClient) { ?>
                                    href='javascript:OptionActivity("<?php echo $MemberShipClient->id; ?>", <?php echo $Type == lang('permanent_payment_clientprofile') ?>)'
                                <?php } ?> >
                      <span
                            <?php echo $ColorTextClass; ?>>
                      <?php echo $MemberShipClient->ItemText; ?>
                      <?php if ($MemberShipClientQuantity > 1) echo " (" . lang("quantity") . " : " . $MemberShipClientQuantity . ")"; ?>
                      </span>
                                </a>
                                <?php else: ?>
                                    <span
                      <?php echo $ColorTextClass; ?>>
                <?php echo $MemberShipClient->ItemText; ?>
                <?php if ($MemberShipClientQuantity > 1) echo " (" . lang("quantity") . " : " . $MemberShipClientQuantity . ")"; ?>
                </span>
                                <?php endif ?>
                                <?php
                                if (isset($MemberShipClient->ItemDetailsId) && $MemberShipClient->ItemDetailsId != 0) {
                                    $itemDetailsObj = new ItemDetails();
                                    $itemDetails = $itemDetailsObj->getItemDetailsById($MemberShipClient->ItemDetailsId);
                                    $marginClass = "";
                                    if (isset($itemDetails->colors))
                                        echo " <div class='smallColorCube' style='background-color: " . $itemDetails->colors->hex . ";'></div> ";
                                    else
                                        $marginClass = "mx-7";
                                    if (isset($itemDetails->sizes))
                                        echo "<span class='$marginClass' style='color: black;'>" . $itemDetails->sizes->name . "</span>";

                                }
                                ?>
                            </div>
                        </td>
                        <!--3 price-->
                        <td
                            <?php echo $LineThrough; ?> >
                            <?php echo $totalPriceAfterDiscount; ?>
                        </td>

                        <!--3.5 balance money payment-->
                        <td>
                            <?php if (empty($MemberShipClient->InvoiceId)) {
                                if($MemberShipClient->BalanceMoney > 0) {
                                    echo '<a class="text-danger" target="_blank" href="' . LinkHelper::getPrefixUrlByHttpHost() .  '/office/cart.php?u=' . $MemberShipClient->ClientId . '&debt=' . $MemberShipClient->id .  '"> 
                                        <span class="fa-layers fa-fw danger">' . $MemberShipClient->BalanceMoney . '</span>';
                                }else {
                                    echo $MemberShipClient->BalanceMoney;
                                }
                            } else {
                                if ($MemberShipClient->BalanceMoney > 0) {
                                    echo '<a class="text-success" target="_blank" href="' . LinkHelper::getPrefixUrlByHttpHost() .  '/office/checkout.php?docId=' . $MemberShipClient->InvoiceId . '"> 
                                        <span class="fa-layers fa-fw text-success">' . lang("invoices") . ' - ' . Docs::getDocNumber($MemberShipClient->InvoiceId);
                                } else {
                                    echo $MemberShipClient->BalanceMoney;
                                }
                            } ?>
                        </td>


                        <!--4 purchase date-->
                        <td
                            <?php echo $LineThrough; ?> >
                            <?php echo with(new DateTime($MemberShipClient->Dates))->format('d/m/Y'); ?>
                        </td>
                        <!--5 start date-->
                        <td
                            <?php echo $LineThrough; ?> >
                            <?php if ($MemberShipClient->FirstDateStatus == '0') {
                                echo with(new DateTime($MemberShipClient->StartDate))->format('d/m/Y');
                            } else {
                                echo lang('short_first_class_date');
                            } ?>
                        </td>
                        <!--6 end date-->
                        <?php if (Auth::userCan('124')): ?>
                            <td
                                <?php echo $LineThrough; ?> >
                                <?php if ($MemberShipClient->TrueDate != '') { ?>
                                    <a href='javascript:LogActivity("<?php echo $MemberShipClient->id; ?>");'>
                                        <span class="<?php echo $TrueDateColor; ?>">
                                        <?php if ($MemberShipClient->FirstDateStatus == '0') {
                                            echo with(new DateTime($MemberShipClient->VaildDate))->format('d/m/Y');
                                        } else {
                                            echo '';
                                        } ?>
                                        </span>
                                    </a>
                                <?php } ?>
                            </td>
                        <?php else: ?>
                            <td
                                <?php echo $LineThrough; ?> >
                                <?php if (!empty($MemberShipClient->TrueDate) && !empty($MemberShipClient->VaildDate)) { ?>
                                    <?php if ($MemberShipClient->FirstDateStatus == '0') {
                                        echo with(new DateTime($MemberShipClient->VaildDate))->format('d/m/Y');
                                    } else {
                                        echo '';
                                    } ?>
                                    </span>
                                <?php } ?>
                            </td>
                        <?php endif; ?>
                        <!--7 remaining lessons-->
                        <?php if (Auth::user()->role_id == 1 || !$MemberShipClient->isForMeeting) { ?>
                            <?php if (Auth::userCan('124')): ?>
                                <td
                                    <?php echo $LineThrough; ?> >
                                    <?php if ($MemberShipClient->Department == '2' || $MemberShipClient->Department == '3') { ?>
                                        <a href='javascript:LogActivity("<?php echo $MemberShipClient->id; ?>");'>
  <span class="<?php echo $TrueBalanceValueColor; ?> text-center unicode-plaintext"
        id="ClientProfileTRDiv_Card<?php echo $MemberShipClient->id ?>">
    <?php echo $MemberShipClient->TrueBalanceValue . ' / ' . $MemberShipClient->BalanceValue ?>
  </span>
                                        </a>
                                    <?php } ?>
                                </td>
                            <?php else: ?>
                                <td
                                    <?php echo $LineThrough; ?> >
                                    <?php if ($MemberShipClient->Department == '2' || $MemberShipClient->Department == '3') { ?>
                                        <span class="<?php echo $TrueBalanceValueColor; ?> text-center unicode-plaintext"
                                              id="ClientProfileTRDiv_Card<?php echo $MemberShipClient->id ?>">
  <?php echo $MemberShipClient->TrueBalanceValue . ' / ' . $MemberShipClient->BalanceValue ?>
</span>
                                    <?php } ?>
                                </td>
                            <?php endif; ?>
                        <?php } else { ?>
                            <td></td>
                        <?php } ?>

                        <!--8 status payment-->

                        <td>
                            <?php if (empty($MemberShipClient->InvoiceId)) {
                                if ($MemberShipClient->BalanceMoney > 0) {
                                    echo '<a class="text-success"  href="' . LinkHelper::getPrefixUrlByHttpHost() .  '/office/cart.php?u=' . $MemberShipClient->ClientId . '"> 
                                        <span class="fa-layers fa-fw text-danger">' . lang("in_debt");
                                } else {
                                    echo lang('paid');
                                }
                            } else {
                                if ($MemberShipClient->BalanceMoney > 0) {
                                    echo '<a class="text-success"  href="' . LinkHelper::getPrefixUrlByHttpHost() .  '/office/checkout.php?docId=' . $MemberShipClient->InvoiceId . '"> 
                                        <span class="fa-layers fa-fw text-danger">' . lang("in_debt");
                                } else {
                                    echo '<span class="fa-layers fa-fw text-success">' . lang('paid');
                                }
                            } ?>
                        </td>
                    </tr>
                    <?php if ($MemberShipClient->Freez == '1' && $MemberShipClient->Status == '0') { ?>
                        <tr class="text-danger" style="font-size: 13px;">
                            <td>
                                <i class="fas fa-reply" data-fa-transform="flip-v" aria-hidden="true">
                                </i>
                            </td>
                            <td><?php echo lang('freezed_membership') ?>
                            </td>
                            <td>
                                <?php echo $MemberShipClient->ItemText; ?>
                            </td>
                            <td>
                                <?php echo $MemberShipClient->FreezDays; ?><?php echo lang('days') ?>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                                <?php echo with(new DateTime(@$MemberShipClient->StartFreez))->format('d/m/Y'); ?>
                            </td>
                            <td>
                                <?php echo with(new DateTime(@$MemberShipClient->EndFreez))->format('d/m/Y'); ?>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a href='javascript:CancelFreez("<?php echo $MemberShipClient->id; ?>","<?php echo $MemberShipClient->ClientId; ?>");'>
                                    <i class="fas fa-snowflake" aria-hidden="true">
                                    </i> <?php echo lang('unfreeze_membership') ?>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($MemberShipClient->StudioVaildDateLog != '' && $MemberShipClient->Status == '0' || $MemberShipClient->FreezEndLog != '' && !empty($MemberShipClient->StudioVaildDate)) { ?>
                        <tr class="text-info" style="font-size: 13px;">
                            <td>
                                <i class="fas fa-reply" data-fa-transform="flip-v" aria-hidden="true">
                                </i>
                            </td>
                            <td><?php echo lang('change_validity') ?>
                            </td>
                            <td>
                                <?php echo $MemberShipClient->ItemText; ?>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                                <?php echo with(new DateTime(@$MemberShipClient->StudioVaildDate))->format('d/m/Y'); ?>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($MemberShipClient->BalanceValueLog != '' && $MemberShipClient->Status == '0') {
                        $Loops = json_decode($MemberShipClient->BalanceValueLog, true);
                        foreach ($Loops['data'] as $key => $val) {
                            $ClassNumber = $val['ClassNumber'];
                            if ($ClassNumber > 0) {
                                $BalanceText = lang('add_single');
                            } else {
                                $BalanceText = lang('deduction_single');
                            }
                        }
                        ?>
                        <tr class="text-secondary" style="font-size: 13px;">
                            <td>
                                <i class="fas fa-reply" data-fa-transform="flip-v" aria-hidden="true">
                                </i>
                            </td>
                            <td><?php echo lang('manual_reception') ?>
                            </td>
                            <td>
                                <?php echo $MemberShipClient->ItemText; ?>
                            </td>
                            <td>
                                <?php echo (!empty($BalanceText)) ? $BalanceText : ''; ?>
                            </td>
                            <td>
                                <?php echo (!empty($ClassNumber)) ? $ClassNumber : ''; ?>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($MemberShipClient->TrueClientId != 0) {
                        $i = '1';
                        $myArray = explode(',', $MemberShipClient->TrueClientId);
                        $ClientNames = '';
                        $GetClientMulits = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->whereIn('id', $myArray)->orderBy('id', 'ASC')->get();
                        $SoftCount = !empty($GetClientMulits) ? count($GetClientMulits) : 0;
                        foreach ($GetClientMulits as $GetClientMulit) {
                            $ClientNames .= '<a href="ClientProfile.php?u=' . $GetClientMulit->id . '">' . htmlentities($GetClientMulit->CompanyName) . '</a>';
                            if ($SoftCount == $i) {
                            } else {
                                $ClientNames .= ', ';
                            }
                            ++$i;
                        }
                        $ClientNames = $ClientNames;
                        ?>
                        <tr class="text-secondary" style="font-size: 13px;">
                            <td>
                                <i class="fas fa-reply" data-fa-transform="flip-v" aria-hidden="true">
                                </i>
                            </td>
                            <td><?php echo lang('family_membersip') ?>
                            </td>
                            <td>
                                <?php echo @$ClientNames; ?>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php ++$i;
                } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

