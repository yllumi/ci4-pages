<?php if($result[$config['field']] ?? ''): ?>
<a data-fancybox="gallery" class="btn btn-sm btn-secondary" href="<?php echo base_url('uploads/'.$_ENV['SITENAME'].'/entry_files/'.$result[$config['field']]); ?>" title="<?php echo $result[$config['field']]; ?>"><span class="fa fa-image"></span></a>
<?php endif; ?>