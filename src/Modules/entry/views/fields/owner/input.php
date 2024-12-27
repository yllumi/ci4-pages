<label for="owner">Submitter</label>
<?php
$uid = $value ?? $this->session->user_id;
$user = $this->User_model->get($uid);
?>
<input type="text" disabled value="<?= $user['name'] .' - '. $user['email']; ?>" class="form-control"/>
<input type="hidden" id="<?= str_replace(['[',']'], ['__',''], $config['field']);?>" name="<?= $config['field'];?>" value="<?= $user['id']; ?>" data-caption="<?= $config['label'];?>"/>
