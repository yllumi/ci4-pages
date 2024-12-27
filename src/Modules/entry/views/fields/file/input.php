<?php $akey = md5($_ENV['SITENAME'].$_ENV['ENC_KEY']); 
$idname = str_replace(['[',']'], ['__',''], $config['field']); ?>
<div class="form-group mb-3">
    <div class="input-group mb-3">
        <input type="text" id="file_<?= $idname;?>" name="<?php echo $config['field'];?>" class="form-control" placeholder="Choose file .." value="<?php echo $value ?? ''; ?>" data-caption="<?= $config['label'];?>">
        <div class="input-group-append">
            <a data-fancybox data-type="iframe" data-options='{"iframe" : {"css" : {"width" : "80%", "height" : "80%"}}}' href="<?= base_url('filemanager/dialog.php?type=2&field_id=file_'.$idname.'&akey='.$akey); ?>" class="input-group-text btn-file-manager">Choose</a>
        </div>
    </div>
</div>
