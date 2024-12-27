<div class="mb-2">
	<div class="col-6">
		<h2><?php echo $page_title;?></h2>
	</div>
</div>

<div class="card card-block">
	<form>
		<div class="form-group">
			<label>Module name</label>
			<input type="text" name="module" class="form-control" placeholder="mymodule">
			<small>Module where the scaffold files placed</small>
		</div>
		<div class="form-group">
			<label>CRUD Name</label>
			<input type="text" name="crudname" class="form-control" placeholder="Sample">
			<small>Will use as controller and model name</small>
		</div>
		<div class="form-group">
			<label>Table Name</label>
			<input type="text" name="table" class="form-control" placeholder="sample">
			<small>By default will use module name</small>
		</div>
		<div class="form-group border-top pt-3">
			<button type="submit" class="btn btn-primary">Generate Code</button>
		</div>
	</form>
</div>

<p><em>Note: After generate, you still need to change migration number (if 001 has already exist in that module), table creation, table definition in model and also form and table</em></p>