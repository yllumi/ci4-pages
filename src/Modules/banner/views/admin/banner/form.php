<div class="title-block">
 	<h3 class="title"><?php echo $page_title; ?>
 		<span class="sparkline bar" data-type="bar"></span>
 	</h3>
</div>

<div class="card card-block">

    <?php if ($form_type == 'edit'):?>
        Created at <?php echo time_ago($result['created_at']); ?>
    <?php endif;?>

    <div style="margin-bottom:30px;"></div>
    
    <form id="post-form" method="post" action="<?php echo $action_url;?>" enctype="multipart/form-data">
        
        <input type="hidden" name="id" value="<?php echo (isset($result['id']) ? $result['id'] : ''); ?>"/>

        <div class="form-group">
            <label>Placing</label>
            <input type="text" name="placing" value="<?php echo (isset($result['placing']) ? $result['placing'] : $this->session->flashdata('placing')); ?>" class="form-control"/>
        </div>

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?php echo (isset($result['name']) ? $result['name'] : $this->session->flashdata('name')); ?>" class="form-control"/>
        </div>

        <div class="form-group">
            <label>Source (HTML)</label>
            <textarea class="form-control" name="source" cols="30" rows="10"><?php echo (isset($result['source']) ? $result['source'] : $this->session->flashdata('source'));?></textarea>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="">Select ..</option>
                <option value="publish" <?php echo (isset($result['status']) && $result['status'] == 'publish') ? 'selected' : '';?>>Publish</option>
                <option value="draft" <?php echo (isset($result['status']) && $result['status'] == 'draft') ? 'selected' : '';?>>Draft</option>
            </select>
        </div>

        <div class="form-group">
            <label>Start</label>
            <input type="text" name="start" data-toggle="datepicker" value="<?php echo (isset($result['start']) ? $result['start'] : $this->session->flashdata('start'));?>" class="form-control" />
        </div>

        <div class="form-group">
            <label>End</label>
            <input type="text" name="end"  data-toggle="datepicker" value="<?php echo (isset($result['end']) ? $result['end'] : $this->session->flashdata('end'));?>" class="form-control" />
        </div>

        <div class="form-group">
            <label>Client</label>
            <input type="text" name="client" value="<?php echo (isset($result['client']) ? $result['client'] : $this->session->flashdata('client'));?>" class="form-control" />
        </div>
        
        <div style="margin-top:30px;"></div>

        <a href="<?php echo $attribute['base_url'];?>" class="btn btn-info">Back to List</a>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>