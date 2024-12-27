<div class="row">
    <div class="col-sm-10 col-md-8 col-xxl-6">

        <div class="mb-3">
            <div class="row">
                <div class="col-lg-6">
                    <h2><?php echo $page_title; ?></h2>
                </div>
            </div>
        </div>

        <div class="card card-block">

            <?php if ($form_type == 'edit') : ?>
                Created at <?php echo time_ago($result['created_at']); ?>
            <?php endif; ?>

            <form id="post-form" class="slugify" method="post" action="<?php echo $action_url; ?>" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Role Name</label>
                    <input type="text" name="role_name" value="<?php echo set_value('role_name', $result['role_name'] ?? ''); ?>" class="form-control title" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Role Slug</label>
                    <input type="text" name="role_slug" value="<?php echo set_value('role_name', $result['role_name'] ?? ''); ?>" class="form-control slug" data-referer="title" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <?= form_dropdown('status', ['active' => 'Active', 'inactive' => 'Inactive'], set_value('status', $result['status'] ?? ''), 'class="form-control"'); ?>
                </div>

                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
</div>