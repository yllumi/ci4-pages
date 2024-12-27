<form id="post-form" method="post" action="<?= ($form_type == 'new' ? site_url('admin/user/insert') : site_url('admin/user/update')); ?>" enctype="multipart/form-data">
    <div class="mb-3">
        <div class="row">
            <div class="col-lg-6">
                <h2><a href="<?= site_url('admin/user'); ?>">User</a> &bull; <?= ucfirst($form_type); ?></h2>
                <?php if ($form_type == 'edit') : ?>
                    <em class="text-white"><?= t('Registered at'); ?> <?= time_ago($result['created_at']); ?></em>
                <?php endif; ?>
            </div>
            <div class="col-lg-6 text-end">
                <button type="submit" class="btn btn-success mb-0"><span class="fa fa-save"></span> Simpan</button>
                <?php if ($form_type == 'edit') : ?>
                    <?php if ($result['status'] == 'inactive') : ?>
                        <a href="<?= site_url('admin/user/user/activate/' . $result['id']); ?>" class="btn btn-success mb-0">Aktifkan</a>
                    <?php else : ?>
                        <a href="<?= site_url('admin/user/user/block/' . $result['id']); ?>" onclick="return confirm('Anda yakin?')" class="btn btn-danger mb-0"><span class="fa fa-ban"></span> Blok</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-3">
                <div class="card-header px-3 py-2 h5">Account</div>

                <div class="card-body">
                    <input type="hidden" name="user_id" value="<?= (isset($result['id']) ? $result['id'] : ''); ?>" />

                    <div class="mb-3">
                        <label class="form-label"><?= t('Name'); ?> <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="<?= (isset($result['name']) ? $result['name'] : $this->session->flashdata('name')); ?>" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= t('Username'); ?> <span class="text-danger">*</span></label>
                        <input type="text" name="username" value="<?= (isset($result['username']) ? $result['username'] : $this->session->flashdata('username')); ?>" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= t('Email'); ?> <span class="text-danger">*</span></label>
                        <input type="text" name="email" value="<?= (isset($result['email']) ? $result['email'] : $this->session->flashdata('email')); ?>" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= t('Phone'); ?> <span class="text-danger">*</span></label>
                        <small>&bull; <?= t('Begin with country code, i.e. 62'); ?><span style="color:#aaa">8XXXXXXXXX</span></small>
                        <input type="text" name="phone" value="<?= (isset($result['phone']) ? $result['phone'] : $this->session->flashdata('phone')); ?>" class="form-control" required />
                    </div>

                    <?php if (isPermitted('activate', 'user')) : ?>
                        <div class="mb-3">
                            <label class="form-label"><?= t('Status'); ?> <span class="text-danger">*</span></label>

                            <select name="status" class="form-control" required>
                                <option value="">Select ..</option>
                                <option value="active" <?= 'active' == ($result['status'] ?? null) || 'active' == $this->session->flashdata('status') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?= 'inactive' == ($result['status'] ?? null) || 'inactive' == $this->session->flashdata('status') ? 'selected' : ''; ?>>Inactive/Block</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if (isPermitted('set_role', 'user')) : ?>
                        <div class="mb-3">
                            <label class="form-label"><?= t('Role'); ?> <span class="text-danger">*</span></label>

                            <select name="role_id" class="form-control" required>
                                <option value="">Select..</option>
                                <?php foreach ($roles as $r) : ?>
                                    <option <?= $r->id == ($result['role_id'] ?? null) || $r->id == $this->session->flashdata('role_id') ? 'selected' : ''; ?> value="<?= $r->id ?>"><?= $r->role_name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3 mb-3">
                        <label class="form-label"><?= t('Avatar'); ?></label>
                        <div class="input-group mb-3">
                            <input type="text" id="avatar" name="avatar" class="form-control" placeholder="Choose file .." value="<?= (isset($result['avatar']) ? $result['avatar'] : $this->session->flashdata('avatar')); ?>">
                            <div class="input-group-append">
                                <a href="#" class="input-group-text btn-file-manager" data-bs-target="avatar">Choose</a>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= t('URL'); ?></label>
                        <input type="text" name="url" value="<?= (isset($result['url']) ? $result['url'] : $this->session->flashdata('url')); ?>" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= t('Short Description'); ?></label>
                        <input type="text" name="short_description" value="<?= (isset($result['short_description']) ? $result['short_description'] : $this->session->flashdata('short_description')); ?>" class="form-control" />
                    </div>

                    <h4 class="mt-5 pt-4 pb-2 border-top"></h4>

                    <div class="mb-3">
                        <label class="form-label"><?= t('Password'); ?></label>
                        <?php if($form_type == 'edit'): ?>
                        <small>&bull; Kosongkan bila tidak ingin mengedit</small>
                        <?php endif; ?>
                        <input type="password" name="password" class="form-control" autocomplete="new-password" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= t('Confirm Password'); ?></label>
                        <input type="password" name="confirm_password" class="form-control" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header px-3 py-2 h5">Profile</div>

                <div class="card-body">
                    <?php foreach ($entry_profile['fields'] as $field => $fieldConfig) : ?>
                        <div class="mb-3">
                            <label class="form-label"><?= t($fieldConfig['label']); ?></label><br>
                            <?= generate_input($fieldConfig, $result[$field] ?? ''); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-3">
            <div class="card mb-5">
                <div class="card-body text-end">
                    <button type="submit" class="btn btn-success mb-0"><span class="fa fa-save"></span> Simpan</button>
                    <?php if ($form_type == 'edit') : ?>
                        <?php if ($result['status'] == 'inactive') : ?>
                            <a href="<?= site_url('admin/user/user/activate/' . $result['id']); ?>" class="btn btn-success mb-0">Aktifkan</a>
                        <?php else : ?>
                            <a href="<?= site_url('admin/user/user/block/' . $result['id']); ?>" onclick="return confirm('Anda yakin?')" class="btn btn-danger mb-0"><span class="fa fa-ban"></span> Blok</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>