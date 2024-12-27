<div class="mb-2">
    <div class="row">
        <div class="col-lg-6">
            <h2><?php echo $page_title; ?></h2>
        </div>
        <div class="col-lg-6">
            <form id="form-search" method="get" action="<?php echo site_url('admin/download/member/search'); ?>" enctype="application/x-www-form-urlencoded" class="form-search">
                <div class="input-group">
                    <input type="text" class="form-control" name="keyword" placeholder="Keyword .." value="<?php echo (isset($_GET['keyword'])) ? $_GET['keyword'] : ''?>">
                    <div class="input-group-append">
                        <span class="input-group-text">Search</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (empty($results)): ?>
	<div class="alert alert-danger">No record found ..</div>
<?php else: ?>

	<?php echo $this->session->flashdata('message');?>

	<table class="table table-striped">
		<thead>
			<tr>
				<th><input class="select-all" type="checkbox"/></th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Status</th>
				<th>Created</th>
			</tr>
		</thead>
		<tbody>

			<?php
			$i = 1;
			foreach ($results as $result)
			{
				?>
                
				<tr>
					<td><input id="checkbox_<?php echo $i;?>" name="record[]" class="record" type="checkbox"  value="<?php echo $result->id?>" /></td>
					<td><?php echo $result->name;?></td>
					<td><?php echo $result->email;?></td>
					<td><?php echo $result->phone;?></td>
					<td><?php echo ($result->status == 'valid') ? '<span class="badge badge-success">' . $result->status . '</span>' : '<span class="badge badge-danger">'. $result->status .'</span';?></td>
					<td><?php echo $result->created_at;?></td>
				</tr>

				<?php
				$i++;
			}
			?>

		</tbody>
	</table>
    
	<?php if(isset($pagination)) : ?>
		<div class="pagination">
			<?php echo $pagination; ?>
		</div>
	<?php endif; ?>

<?php endif ?>