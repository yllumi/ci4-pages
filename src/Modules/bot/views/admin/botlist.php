<div class="mb-3">
    <div class="row">
        <div class="col-lg-6">
            <h2>Bot List</h2>
        </div>
    </div>
</div>

<?php echo $this->session->flashdata('message');?>

<table class="table table-striped">
	<thead>
		<tr>
			<th>Bot Name</th>
			<th>Bot Username</th>
			<th></th>
		</tr>
	</thead>
	<tbody>

		<?php if (empty($bots)): ?>
			<tr><td colspan="3">No record found ..</td></tr>
		<?php else: ?>

		<?php foreach ($bots as $botusername => $bot): ?>
			<tr>
				<td><?php echo $bot['botname'];?></td>
				<td><?php echo '@'.$botusername;?></td>
				<td class="text-end">
                    <div class="btn-group">
						<a href="<?= site_url('admin/bot/bot/send_to_premium/'.$botusername); ?>" class="btn btn-sm btn-secondary">
							Send To Premium
						</a>
						<a href="<?= site_url('admin/bot/bot/broadcast_to_member/'.$botusername); ?>" class="btn btn-sm btn-secondary">
							Broadcast to Member
						</a>
                    </div>
				</td>
			</tr>
		<?php endforeach; ?>
		<?php endif ?>

	</tbody>
</table>