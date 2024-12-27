<?php $idname = str_replace(['[', ']'], ['__', ''], $config['field']); ?>
<button type="button" id="<?= $idname; ?>Btn" class="btn p-2 btn-info"><?= $value ? 'Upload File Lain' : 'Upload File'; ?></button>
<input type="text" name="<?= $config['field']; ?>" id="<?= $idname; ?>" value="<?= $value; ?>" data-caption="<?= $config['label']; ?>" <?= strpos($config['rules'] ?? '', 'required') !== false ? 'required' : ''; ?> style="opacity:0">
<small class="d-block"><?= $value; ?></small>
<img style="width:200px" src="<?= base_url('uploads/' . $_ENV['SITENAME'] . '/entry_files/' . $value); ?>" id="<?= $idname; ?>thethumbnail" class="img-fluid my-3" onerror="this.style.display='none'">
<div>&nbsp;</div>

<div id="<?= $idname; ?>progressOuter" class="progress progress-striped active" style="display:none;">
  <div id="<?= $idname; ?>progressBar" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
</div>
<small class="d-block" id="<?= $idname; ?>msgBox"></small>

<script>
  $(function() {

    let btn = document.getElementById('<?= $idname; ?>Btn'),
      progressBar = document.getElementById('<?= $idname; ?>progressBar'),
      progressOuter = document.getElementById('<?= $idname; ?>progressOuter'),
      inputText = document.getElementById('<?= $idname; ?>'),
      thumbnail = document.getElementById('<?= $idname; ?>thethumbnail'),
      msgBox = document.getElementById('<?= $idname; ?>msgBox');

    var <?= $idname; ?>_uploader = new ss.SimpleUpload({
      button: btn,
      url: '<?= site_url('entry/upload'); ?>',
      name: 'uploadfile',
      multipart: true,
      hoverClass: 'hover',
      focusClass: 'focus',
      responseType: 'json',
      startXHR: function() {
        progressOuter.style.display = 'block'; // make progress bar visible
        this.setProgressBar(progressBar);
      },
      onSubmit: function() {
        msgBox.innerHTML = ''; // empty the message box
        btn.innerHTML = 'Mengunggah...'; // change button text to "Uploading..."
      },
      onComplete: function(filename, response) {
        console.log(response)
        btn.innerHTML = 'Upload File Lain';
        progressOuter.style.display = 'none'; // hide progress bar when upload is completed

        if (!response) {
          msgBox.innerHTML = response.msg;
          return;
        }

        if (response.success === true) {
          msgBox.innerHTML = '<strong>' + escapeTags(response.file) + '</strong>' + ' berhasil diunggah.';
          inputText.value = response.file;
          thumbnail.src = '<?= base_url('uploads/' . $_ENV['SITENAME'] . '/entry_files/'); ?>/' + escapeTags(response.file);
          thumbnail.style.display = 'block';

        } else {
          if (response.msg) {
            msgBox.innerHTML = escapeTags(response.msg);

          } else {
            msgBox.innerHTML = 'Terjadi kesalahan saat proses upload.';
          }
        }
      },
      onError: function(filename, errorType, status, statusText, response, uploadBtn, fileSize) {
        console.log(filename, errorType, status, statusText, response, uploadBtn, fileSize)
        progressOuter.style.display = 'none';
        msgBox.innerHTML = errorType + ': Error saat mengunggah ke server: ' + status + ' ' + statusText;
      }
    });
  });
</script>