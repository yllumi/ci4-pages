<?php
if($uid = $this->session->user_id)
	$user = $this->User_model->get($uid);
else
	$user = null;
?>
<input type="hidden" id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>" name="<?= $config['field'];?>" value="<?= $user['id'] ?? ''; ?>" data-caption="<?= $config['label'];?>"/>
