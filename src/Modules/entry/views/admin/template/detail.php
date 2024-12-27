<?php embed_entry_style(); ?>

<div class="card">
    <div class="card-body">
        <table class="table">
            <?php foreach ($fields as $key => $conf): ?>
            <tr>
                <th class="bg-grey" style="width:30%"><?= $conf['label']; ?></th>
                <td><?= generate_output($conf, $result); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php embed_entry_script(); ?>