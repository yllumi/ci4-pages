<div class="mb-3">
    <div class="row">
        <div class="col-lg-6">
            <h3>@<?= $username; ?></h3>
        </div>
        <div class="col-lg-6 text-end">
			<a href="<?= site_url('admin/bot/bot'); ?>" class="btn btn-secondary">&laquo; back to list</a>
        </div>
    </div>
</div>

<?php echo $this->session->flashdata('message');?>

<div class="row">
	
	<div class="col-md-3" style="height:406px;overflow:auto;">
		<ul class="list-group">
		  <li class="list-group-item">Cras justo odio</li>
		  <li class="list-group-item">Dapibus ac facilisis in</li>
		  <li class="list-group-item">Morbi leo risus</li>
		  <li class="list-group-item">Porta ac consectetur ac</li>
		  <li class="list-group-item">Vestibulum at eros</li>
		</ul>
	</div>

	<div class="col-md-9">
		<div class="chat-list bg-secondary px-2" style="height:350px;overflow:auto;">
			<div class="chat-list-content bg-info" style="height:500px;">
				
			</div>
		</div>
		<div class="chat-form bg-secondary p-2">
			<div class="row">
				<div class="col-10">
					<input type="text" name="message" id="message" class="form-control">
				</div>
				<div class="col-2">
					<button class="btn btn-secondary btn-block" style="margin:0;height:100%;border-radius:5px;">Send</button>
				</div>
			</div>
		</div>
	</div>

</div>