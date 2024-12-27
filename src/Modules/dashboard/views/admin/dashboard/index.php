<style>
    .btn-shortcuts a span {
        font-size: 24px;
        color: #aaa;
    }

    .btn-shortcuts a {
        font-size: 16px;
        color: #999;
        line-height: 30px;
    }
</style>

<!-- Header Page -->
<div class="mb-5">
    <div class="row">
        <div class="col-12 text-white">
            <h1>Welcome, <?= $me['name']; ?>!</h1>
        </div>
    </div>
</div>
<!-- End Header Page -->

<?php if ($shortcuts['navigations'] ?? '') : ?>
    <div class="row btn-shortcuts">
        <?php foreach ($shortcuts['navigations'] as $link) : ?>
            <?php if ($link['status'] != 'publish') continue; ?>
            <div class="col-4 col-sm-3 col-md-2 col-xxl-3 mb-3">
                <a href=" <?= $link['url_type'] == 'uri' ? site_url($link['url']) : $link['url']; ?>" class="btn btn-secondary btn-lg d-flex flex-column align-items-center h-100 shadow-sm" style="padding: 32px 0" target="<?= $link['target']; ?>">
                    <span class="<?= $link['icon']; ?> mb-1"></span>
                    <div><?= $link['caption']; ?></div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <hr>
<?php endif; ?>