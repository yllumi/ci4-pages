<div class="mb-3">
    <div class="row">
        <div class="col-lg-6">
            <h2><?php echo $page_title; ?></h2>
        </div>
        <div class="col-lg-6">
			<a href="<?php echo site_url('admin/{module}/{crudurl}/add'); ?>" class="btn btn-primary-outline pull-right">
				<span class="fa fa-plus"></span> New {crudname}
			</a>
        </div>
    </div>
</div>


	<?php echo $this->session->flashdata('message');?>

	<table class="table table-striped">
		<thead>
			<tr>
				<!-- <th><input class="select-all" type="checkbox"/></th> -->
				<th>Full Name</th>
				<th>Email</th>
				<th>Address</th>
				<th>Status</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<!-- Form filter -->
			<form>
			<tr>
				<td><input type="text" class="form-control form-control-sm" name="filter[fullname]" value="<?= $this->input->get('filter[fullname]', true); ?>"></td>
				<td><input type="text" class="form-control form-control-sm" name="filter[email]" value="<?= $this->input->get('filter[email]', true); ?>"></td>
				<td><input type="text" class="form-control form-control-sm" name="filter[address]" value="<?= $this->input->get('filter[address]', true); ?>"></td>
				<td>
					<?= form_dropdown('filter[status]', ['' => 'All', 'draft'=>'Draft', 'publish'=>'Publish'], $this->input->get('filter[status]', true), 'class="form-control form-control-sm"'); ?>
				</td>
				<td>
					<div class="btn-group">
					<button type="submit" class="btn btn-primary">Filter</button>
					<a href="<?= site_url('admin/{module}/{crudurl}'); ?>" class="btn btn-secondary">Reset</a>
					</div>
				</td>
			</tr>
			</form>
			<!-- End form filter -->
			
			<?php if (empty($results)): ?>
				<tr><td colspan="5">No record found ..</td></tr>
			<?php else: ?>

			<?php foreach ($results as $result): ?>
				<tr>
					<td><?php echo $result['fullname'];?></td>
					<td><?php echo $result['email'];?></td>
					<td><?php echo $result['address'];?></td>
					<td><?php echo $result['status'];?></td>
					<td class="text-end">
                        <div class="btn-group">
                            <a class="btn btn-sm btn-success" href="<?php echo site_url('admin/{module}/{crudurl}/edit/'. $result['id']); ?>" title="Edit"><span class="fa fa-pencil"></span></a> 
                            <a class="btn btn-sm btn-danger" onclick="return confirm('are you sure?')" href="<?php echo site_url('admin/{module}/{crudurl}/delete/' . $result['id']); ?>" title="Delete"><span class="fa fa-remove"></span></a>
                        </div>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php endif ?>

		</tbody>
	</table>
    
	<?php if(isset($pagination)) : ?>
		<div class="pagination">
			<?php echo $pagination; ?>
		</div>
	<?php endif; ?>
