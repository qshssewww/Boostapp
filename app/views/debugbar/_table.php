<?php
/** @var $dbQueries array */

$i = 0;
$totalTime = 0;

foreach ($dbQueries as $data) {
    $trClass = '';
    if (round($data['time'], 3) > 0.05) {
        $trClass = 'table-warning';
    }

    ?>


    <tr class="<?= $trClass ?>">
        <td><?= ++$i ?></td>
        <td><?= round($data['time'], 3) ?>s</td>
        <td><?= $data['type'] ?></td>
        <td>
            <pre><code class="language-sql"><?= $data['query'] ?></code></pre>
            <?php if (!empty($data['trace'])) { ?>
                <ul style="font-size: 12px;">
                    <?php foreach ($data['trace'] as $trace) { ?>
                        <li><?= $trace ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </td>
    </tr>

    <?php
    $totalTime += round($data['time'], 2);
}
?>

<tr>
    <th>#</th>
    <th><?= round($totalTime, 2) ?>s</th>
    <th></th>
    <th></th>
</tr>
