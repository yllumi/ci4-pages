<div class="title-block">
 	<h3 class="title"><?php echo $page_title; ?>
 		<span class="sparkline bar" data-type="bar"></span>
 	</h3>
</div>

<div class="card card-block">

    <?php if ($form_type == 'edit'):?>
        Created at <?php echo time_ago($result['created_at']); ?>
    <?php endif;?>

    <?php echo form_open_multipart($action_url); ?>
        
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="fullname" value="<?php echo set_value('fullname', $result['fullname'] ?? ''); ?>" class="form-control"/>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" value="<?php echo set_value('email', $result['email'] ?? ''); ?>" class="form-control"/>
        </div>

        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" value="<?php echo set_value('address', $result['address'] ?? ''); ?>" class="form-control" />
        </div>
    
        <div class="form-group">
            <label>Status</label>
            <?= form_dropdown('status', ['draft'=>'Draft', 'publish'=>'Publish'], set_value('status', $result['status'] ?? ''), 'class="form-control"'); ?>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>