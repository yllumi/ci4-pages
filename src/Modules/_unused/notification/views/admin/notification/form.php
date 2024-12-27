<div class="title-block">
 	<h3 class="title"><?php echo $page_title; ?>
 		<span class="sparkline bar" data-type="bar"></span>
 	</h3>
</div>

<div class="card card-block">
    
    <form id="post-form" method="post" action="<?php echo $action_url;?>" enctype="multipart/form-data">
        
        <div class="form-group">
            <label>Message</label>
            <textarea name="notif" class="form-control"/><?php echo $this->session->flashdata('notif')?></textarea>
            <small id="passwordHelpBlock" class="form-text text-muted">
            Kirim ke semua pengguna Codepolitan
            </small>
        </div>

        <div class="form-group">
            <label>URI / Link</label>
            <input name="uri" class="form-control" value="<?php echo $this->session->flashdata('uri')?>"/>
            <small id="passwordHelpBlock" class="form-text text-muted">
            Contoh: learn/detail-article
            </small>
        </div>

        <div class="form-group">
            <input type="checkbox" name="confirm"/> Sudah oke!
        </div>

        <div style="margin-top:30px;"></div>

        <button type="submit" class="btn btn-success">Send</button>
    </form>
</div>