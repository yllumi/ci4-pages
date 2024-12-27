<div class="mb-2">
    <div class="row">
        <div class="col-lg-6">
            <h2><?php echo $page_title; ?></h2>
        </div>
        <div class="col-lg-6">
            <form id="form-search" method="post" action="<?php echo $attribute['base_url'] . '/search'; ?>" enctype="application/x-www-form-urlencoded" class="form-search">
                <div class="input-group">
                    <input type="text" class="form-control" name="keyword" placeholder="Keyword ..">
                    <div class="input-group-append">
                        <span class="input-group-text">Search</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="btn-group mb-3">
	<a class="btn btn-primary-outline" href="<?php echo $attribute['base_url'] . '/all';?>">all</a>
	<a class="btn btn-primary-outline" href="<?php echo $attribute['base_url'] . '/active';?>">active</a>
	<a class="btn btn-primary-outline" href="<?php echo $attribute['base_url'] . '/inactive'; ?>">inactive</a>
</div>

<a href="<?php echo $attribute['base_url'] . '/add'?>" class="btn btn-primary-outline pull-right">New <?php echo $attribute['caption'];?></a>

<?php if (empty($results)): ?>
	<div class="alert alert-danger">No record found ..</div>
<?php else: ?>

	<?php echo $this->session->flashdata('message');?>

	<table class="table table-striped">
		<thead>
			<tr>
				<th><input class="select-all" type="checkbox"/></th>
				<th>Placing</th>
				<th>Name</th>
				<th>Status</th>
				<th>Start</th>
				<th>End</th>
				<th>Client</th>
				<th>Options</th>
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
					<td><?php echo $result->placing;?></td>
					<td><?php echo $result->name;?></td>
					<td><?php echo ($result->status == 'publish') ? '<span class="badge badge-success">Publish</span>' : '<span class="badge badge-danger">Draft</span>' ;?></td>
					<td><?php echo time_ago($result->start);?></td>
					<td><?php echo time_ago($result->end);?></td>
					<td><?php echo $result->client;?></td>
					<td class="">
                        <div class="btn-group">
                            <?php if($result->status == 'inactive'): ?>
                                <a class="btn btn-sm btn-success" onclick="return confirm('are you sure?')" href="<?php echo $attribute['base_url'] . '/activate/' . $result->id; ?>">Turn active</a> 
                                <a class="btn btn-sm btn-danger" onclick="return confirm('are you sure?')" href="<?php echo $attribute['base_url'] . '/delete/' . $result->id; ?>">Delete</a>
                            <?php else: ?>
                                <a class="btn btn-sm btn-success" href="<?php echo $attribute['base_url'] . '/edit/' . $result->id; ?>">Edit</a>
                                <a class="btn btn-sm btn-danger" onclick="return confirm('are you sure?')" href="<?php echo $attribute['base_url'] . '/block/' . $result->id; ?>">Block</a>
                            <?php endif ?>
                        </div>
					</td>
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