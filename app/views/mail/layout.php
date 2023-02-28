<?php
/**
 * @var string $logo
 * @var string $text
 * @var string $unsubscribe
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta charset="utf-8">
</head>
<body>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" dir="rtl">
    <tr>
        <td>
            <table width="650" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="border:10px solid #e1e1e1;">
                <tr>
                    <td valign="top">
                        <table width="650" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #cccccc;">
                            <tr>
                                <td width="275" align="start" valign="middle" style="padding:30px;">
                                    <img src="<?= $logo ?>" alt="Boostapp" title="Boostapp" width="180"/>
                                </td>
                                <td width="255" align="end" valign="middle" style="font-family:Arial; font-size:14px; color:#555555; padding:30px;">
                                    <strong><?= lang('system_notice') ?></strong>
                                    <br/>
                                    <?= date('d/m/Y') ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <table width="650" border="0" style="padding: 30px 30px 30px 30px;" cellpadding="0">
                            <tr>
                                <td style="font-family:Arial; font-size:12px;padding-bottom:15px;">
                                    <?= $text ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <?= $unsubscribe ?>
        </td>
    </tr>
</table>
</body>
</html>
