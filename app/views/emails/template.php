<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" dir="rtl">
  <tr>
    <td><table width= "650" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="border:10px solid #e1e1e1;">
        <tr>
          <td align="left" valign="top"><table width="650" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #cccccc;">
            <tr>
              <td width="275" align="right" valign="middle" style="padding:30px;"><img src="<?php echo App::url('assets/img/LogoMail.png') ?>" alt="Boostapp" title="Boostapp" width="180" height="63" /></td>
              <td width="255" align="left" valign="middle" style="font-family:Arial; font-size:14px; color:#555555; padding:30px;"><strong><?php echo lang('system_notice') ?></strong></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td align="left" valign="top"><table width="650" border="0" style="padding: 30px 30px 30px 30px;" cellpadding="0">
           		  
			<tr><td style="font-family:Arial; font-size:12px;padding-bottom:15px;">
             <?php echo $message ?>     
			 <br /><br /> 
       <?php echo lang('sent_by_template') ?> <strong>BOOSTAPP</strong>
			  </td>
			  </tr>
          
          </table></td>
        </tr>
      </table>
    <p align="center" style="font-family:Arial; font-size:11px;">&nbsp;</p></td>
  </tr>
</table>