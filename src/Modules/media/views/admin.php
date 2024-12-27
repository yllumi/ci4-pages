<style>
article.content {
	position: relative;
}
.iframe-container {
    position: absolute;
    top: 50px;
    bottom: 41px;
    left: 0;
    right: 0;
}
</style>

<div class="iframe-container">
    <?php $akey = md5($_ENV['SITENAME'].$_ENV['ENC_KEY']); ?>
	<iframe width="100%" height="100%" src="<?= base_url('filemanager/dialog.php?akey='.$akey); ?>" frameborder="0"></iframe>
</div>