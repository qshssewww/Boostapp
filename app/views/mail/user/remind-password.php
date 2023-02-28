<?php
/**
 * @var string $displayName
 * @var string $LunchName
 * @var string $LunchLink
 * @var string $ClientEmail
 * @var string $RandomPassword
 */
?>
    <span style="color:#000;font-family:Arial; font-size:14px; font-weight:bold;">שלום<? echo ($displayName ? ' ' . $displayName : '') ?>,</span>
    <br/>
    <br/>
    להלן פרטי הגישה שלך למערכת BOOSTAPP של <?= $LunchName ?>:
    <br/>
    <br/>
    לינק למערכת BOOSTAPP:
    <br>
    <a href="<?= $LunchLink ?>"><?= $LunchLink ?></a>
    <br>
    <br>
    שם משתמש : <span><?= $ClientEmail ?></span>
    <br>
    סיסמה ראשונית : <span> <?= $RandomPassword ?></span>
    <br>
    <br>
    מומלץ לשמור את הנתונים האלו על מנת לגשת למערכת בעתיד.
    <br>
    לאחר ההתחברות יש לשנות סיסמה באמצעות לחיצה על השם שלך בתפריט בצד ימין.
    <br/>
    <br/>
    בהצלחה,
    <br/>
    צוות <strong><?= $LunchName ?></strong>
