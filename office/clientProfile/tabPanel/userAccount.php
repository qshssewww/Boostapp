<?php
    if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");
    $next_year = date($_REQUEST["year"], strtotime('+1 year'));
    $StartDate = $_REQUEST["year"].'-01-01';
    $EndDate =  $_REQUEST["year"].'-12-31';
    $DocGetsC = DB::table('docs')->where('CompanyNum' ,'=', $CompanyNum)->where('ClientId','=', $Supplier->id)->where('Accounts','=','1')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'DESC')->get();
    $DocCountC = count($DocGetsC);
    ?>
    <script>
            function clickUserAccountA(){
                document.querySelector('#user-account').classList.remove('show')
                document.querySelector('#user-account').classList.remove('active')
                document.querySelector('#user-accountmoney').classList.add('show')
                document.querySelector('#user-accountmoney').classList.add('active')
            }
        </script>
        <div id="userAccount-nav" class="user-nav">
                                    <a style="color: black" href="#user-account"  class="nav-a user-account-a">
                                		<span>
                                		<?php echo lang('docs') ?>
                                		</span>
                                		<div class="user-line"></div>
                                	</a>
                                     <a style="color: #B9B9B9" onclick="clickUserAccountA()" style="color: black" href="#user-accountmoney" class="nav-a user-accountmoney-a">
                                		<span>
                                		<?php echo lang('detailed_receipt') ?>
                                		</span>
                                     </a>
                            </div>
    <div class="card spacebottom">
        <div class="card-header text-start">
            <i class="fas fa-shekel-sign">
            </i>
            <strong><?php echo lang('customer_card_bookkeeping') ?> ::
            </strong>
            <?php echo @$DocCountC; ?>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-sm-12">
          <span>
            <select class="form-control" id="CDate" onChange="SetYears(this.value);">
              <?php
              $yearQuarter = Utils::getCurrentAnnualQuarter(date('Y-m-d'));
              $ThisYear = $yearQuarter == 4.0 ? date('Y',strtotime("+1 years")) : date('Y');
              //$ThisYear = date('Y');
              for ($x = $SettingsInfo->StartYear; $x <= $ThisYear; $x++) {
                  if ($x == $_REQUEST["year"]) {echo "<option selected>$x</option>";}	else {echo "<option>$x</option>";}
              }
              ?>
            </select>
            <script type="text/javascript" charset="utf-8">
              function SetYears(value)
              {
                  window.location.href = 'ClientProfile.php?u=<?php echo @$Supplier->id; ?>&year='+value+'#user-account';
              }
            </script>
                </div>
            </div>
            <hr>
            <?php if ($DocCountC != '0') { ?>
                <table style='overflow-x: auto' class="table table-bordered table-hover text-start wrap Carteset"   cellspacing="0" width="100%" id="AccountsTable">
                    <thead class="thead-dark ">
                    <tr style="background-color:#bce8f1;">
                        <th  style="text-align:start;">#
                        </th>
                        <th  style="text-align:start;"><?php echo lang('actions') ?>
                        </th>
                        <th  style="text-align:start;"><?php echo lang('date') ?>
                        </th>
                        <th  style="text-align:start;"><?php echo lang('table_value_date') ?>
                        </th>
                        <th  style="text-align:start;"><?php echo lang('type') ?>
                        </th>
                        <th  style="text-align:start;"><?php echo lang('book_table_duty') ?>
                        </th>
                        <th  style="text-align:start;"><?php echo lang('book_table_credit') ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $d = '1';
                    foreach ($DocGetsC as $DocGet) {
                        $DocsTables = DB::table('docstable')->where('CompanyNum' ,'=', $CompanyNum)->where('id','=',$DocGet->TypeDoc)->first();
                        if (in_array($DocGet->Refound, [1,2]) && $DocGet->RefAction=='0' && $DocGet->TypeHeader=='400'){
                            $Refound = lang('refund_single');
                        }
                        else {
                            $Refound = '';
                        }
                        ?>
                        <tr class="active">
                            <td>
                                <?php echo $d; ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php DocumentGroupButton($DocGet->TypeNumber,$DocsTables->id,$DocGet->TypeHeader,$DocGet->PayStatus, $DocGet); ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php echo with(new DateTime($DocGet->UserDate))->format('d/m/Y'); ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php echo with(new DateTime($DocGet->Dates))->format('d/m/Y'); ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php echo transDbVal(trim($DocsTables->TypeTitleSingle)); ?>
                                <?php echo $Refound; ?>
                            </td>
                            <?php
                            if (in_array($DocsTables->TypeHeader, [320,305,310,330,300,0])) {
                                echo '<td style="vertical-align: middle;" ><span class="unicode-plaintext">'.number_format($DocGet->Amount, 2).'</span> ₪</td>';
                                $ColDebit = $DocGet->Amount;
                            }
                            else {
                                echo '<td style="vertical-align: middle;" ><span class="unicode-plaintext">0.00</span> ₪</td>';
                                $ColDebit = '0.00';
                            }
                            ?>
                            <?php
                            if ($DocsTables->TypeHeader == '400') {
                                echo '<td style="vertical-align: middle;" ><span class="unicode-plaintext">'.number_format($DocGet->Amount, 2).'</span> ₪</td>';
                                $ColDebitGet = $DocGet->Amount;
                            } else if($DocsTables->TypeHeader == '320') {
                                echo '<td style="vertical-align: middle;" ><span class="unicode-plaintext">-'.number_format($DocGet->Amount, 2).'</span> ₪</td>';
                                $ColDebitGet = '-'.$DocGet->Amount;
                            }
                            else {
                                echo '<td style="vertical-align: middle;" ><span class="unicode-plaintext">0.00</span> ₪</td>';
                                $ColDebitGet = '0.00';
                            }
                            ?>
                            <?php
                            $RowBalance = $ColDebit+$ColDebitGet;
                            $BalanceTotal = @$BalanceTotal + $RowBalance;
                            $ColDebitTotal = @$ColDebitTotal + $ColDebit;
                            $ColDebitGetTotal = @$ColDebitGetTotal + $ColDebitGet;
                            ?>
                        </tr>
                        <?php
                        ++$d; }
                    ?>
                    </tbody>
                    <tfoot>
                    <td colspan="5">
                        <strong><?php echo lang('total') ?>:
                        </strong>
                    </td>
                    <td>
                        <strong>
                            <?php echo number_format($ColDebitTotal, 2); ?> ₪
                        </strong>
                    </td>
                    <td>
                        <strong>
                            <?php echo number_format(str_replace("-","",$ColDebitGetTotal), 2); ?> ₪
                        </strong>
                    </td>
                    </tfoot>
                </table>
            <?php } else {echo '<div class="row text-start p-3" ><strong>אין נתונים</strong></div>';} ?>
        </div>
    </div>
</div>