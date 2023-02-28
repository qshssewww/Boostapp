<?php
require_once '../../../app/init.php';
require_once '../../../app/enums/ClassType/EventType.php';
require_once "../../Classes/ItemRoles.php";



if (!isset($_POST['classTypeId']))
    return;

/** @var ClassesType $ClassTypeObj */
$ClassTypeObj = ClassesType::find($_POST['classTypeId']);
if($ClassTypeObj === null) {
    return;
}
$items = $ClassTypeObj->getMembershipsIdByClassType(EventType::EVENT_TYPE_CLASSES, true);

if (empty($items)):
?>
<option value="error">לא קיימים מנויים מתאימים</option>
<?php
endif;

foreach ($items as $key => $item):
?>

<option data-price="<?php echo $item->ItemPrice ?>" value="<?php echo $item->ItemId ?>" data-department="<?php echo $item->Department ?>"><?php echo $item->ItemName; ?></option>

<?php endforeach; ?>

