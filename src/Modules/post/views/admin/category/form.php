<div class="mb-4">
    <div class="row">
        <div class="col-lg-6">
            <h2><?= t($page_title); ?></h2>
        </div>
    </div>
</div>

<?php echo $this->session->flashdata('message'); ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo ($form_type == 'new' ? site_url('admin/post/category/insert') : site_url('admin/post/category/update')); ?>" method="post">
                    <input type="hidden" name="id" value="<?php echo (isset($result->term_id) ? $result->term_id : ''); ?>" />
                    <input type="hidden" name="post_type" value="<?php echo $post_type; ?>" />

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="name" value="<?php echo (isset($result->name) ? $result->name : ''); ?>" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" value="<?php echo (isset($result->slug) ? $result->slug : ''); ?>" class="form-control slugify" data-referer="name" />
                    </div>

                    <div class="pt-3 border-top">
                        <button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Simpan</button>
                        
                        <?php if ($form_type == 'edit') : ?>
                        <button type="submit" name="btnSaveExit" value="1" class="btn btn-outline-success">
                            <span class="fa fa-save"></span> Save &amp; Exit
                        </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>