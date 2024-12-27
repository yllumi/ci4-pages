<!-- Dependency -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.css">
<style>
  div.tagsinput {
    background: #FFF;
    padding: 5px;
    overflow-y: auto;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    width: 100% !important;
  }

  div.tagsinput span.tag {
    border: 1px solid #444;
    background: #444;
    color: #FFF;
  }

  .editor-toolbar.fullscreen,
  .CodeMirror-fullscreen,
  .editor-preview-side {
    z-index: 20000 !important;
  }

  #editor {
    border-top: 2px solid #ddd;
    min-height: 400px;
    padding: 10px 20px;
    margin: 0 -15px -32px;
    background: #fafafa;
  }

  input.title {
    font-size: 1.45em !important;
  }

  .desc {
    margin: -8px 0 10px;
    display: block;
  }

  .nav-tabs .nav-link {
    color: white;
  }

  .form-slug {
    font-size: 14px;
    margin-top: -12px;
    color: #777;
  }

  #slug:focus {
    box-shadow: none !important;
  }

  #slug {
    width: 85%;
    margin-left: 10px;
    border: 0;
    border-bottom: 1px solid #ccc;
    border-radius: 0;
    color: #777;
  }
</style>

<div class="mb-4">
  <div class="row">
    <div class="col-12">
      <h2><a href="<?= site_url('admin/post/index/all/' . $post_type); ?>"><?= $page_title; ?></a></h2>
      <?php if ($form_type == 'edit') : ?>
        <small class="me-2 text-light">Created by <?= $result['username']; ?> at <?= $result['created_at']; ?></small>
        <!--<a href="#" class="btn btn-info btn-sm" target="#"><span class="fa fa-external-link"></span></a>-->
      <?php endif; ?>
    </div>
  </div>
</div>

<ul class="nav nav-tabs" id="myTab" role="tablist" style="width:90%">
  <li class="nav-item" role="presentation">
    <a class="nav-link active" id="content-tab" data-bs-toggle="tab" href="#content" role="tab" aria-controls="content" aria-selected="true">Content</a>
  </li>
  <li class="nav-item" role="presentation">
    <a class="nav-link" id="meta-tab" data-bs-toggle="tab" href="#meta" role="tab" aria-controls="meta" aria-selected="false">Meta</a>
  </li>
</ul>

<div class="card mb-5 <?= $form_type != 'edit' ? 'slugify' : ''; ?>" style="border-radius: 0 15px 15px; border-top: 0;">
  <div class="card-body">
    <div class="row justify-content-end">
      <div class="col-12 text-end">
        <?php if ($form_type == 'edit') : ?>
          <?php if ($result['status'] == 'draft' || $result['status'] == 'review') : ?>
            <a href="<?= site_url('admin/post/publish/' . $result['id'] . '?callback=' . current_url()); ?>" class="btn btn-lg btn-info-outline me-2"><span class="fa fa-upload"></span> Publish</a>
          <?php else : ?>
            <a href="<?= site_url('admin/post/draft/' . $result['id'] . '?callback=' . current_url()); ?>" class="btn btn-lg btn-warning-outline me-2"><span class="fa fa-file-o"></span> Draft</a>
          <?php endif; ?>
        <?php endif; ?>
        <button type="button" class="btn btn-lg btn-success btn-save"><span class="fa fa-save"></span> Simpan</button>
      </div>
    </div>
    <form id="post-form" method="post" class="mt-3" action="<?= ($form_type == 'new' ? site_url('admin/post/insert') : site_url('admin/post/update')); ?>" enctype="multipart/form-data">

      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="content" role="tabpanel" aria-labelledby="content-tab">

          <input type="hidden" name="post_type" id="post_type" value="<?= $result['type'] ?? $post_type; ?>">
          <input type="hidden" name="post_id" id="post_id" value="<?= $result['id'] ?? ''; ?>" />
          <input type="hidden" name="mode" id="mode" value="<?= (isset($result['id'])) ? 'update' : 'insert'; ?>" />

          <div class="mb-3">
            <input type="text" placeholder="Title" name="title" id="title" value="<?= (isset($result['title'])) ? $result['title'] : $this->session->flashdata('title'); ?>" class="form-control form-control-lg title" autofocus />
          </div>

          <div class="mb-3 d-flex align-items-center form-slug">
            <label class="form-label mb-0">URL / Slug</label>
            <input type="text" name="slug" id="slug" value="<?= (isset($result['slug'])) ? $result['slug'] : $this->session->flashdata('slug'); ?>" class="form-control slug" />
          </div>

          <div class="mb-3 editor-container" id="content-container">
            <!-- Create the editor container -->
            <div class="d-flex justify-content-between">
              <label style="color:#888;font-size:14px; padding:4px 0 0 10px;">Content</label>
              <div class="col-6 d-flex justify-content-end">
                <label style="color:#888;font-size:14px; padding-top: 4px;" class="text-nowrap pe-2">Editor type</label>
                <?= form_dropdown('content_type', ['html' => 'WYSIWYG', 'code' => 'Code'], $result['content_type'] ?? $content_type, 'class="form-control form-control-sm" style="max-width:120px" id="content_type"'); ?>
              </div>
            </div>

            <?php $content = !empty($result['content']) ? $result['content'] : $this->session->flashdata('content'); ?>
            <div id="editor"><?= ($result['content_type'] ?? $content_type) == 'code' ? htmlentities($content ?? '') : $content; ?></div>
            <input type="hidden" name="content" id="content_hidden" value="<?= htmlentities($content ?? ''); ?>">
            <input type="text" style="display:none" id="content__entry__rfm_image_input">
          </div>

        </div>
        <div class=" tab-pane fade" id="meta" role="tabpanel" aria-labelledby="meta-tab">

          <div class="row">

            <div class="col-md-8">

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3 mb-3">
                    <label class="form-label">Featured Image</label>
                    <div class="input-group mb-3">
                      <input type="text" id="featured_image" name="featured_image" class="form-control" placeholder="Featured image .." value="<?= (isset($result['featured_image'])) ? $result['featured_image'] : $this->session->flashdata('featured_image'); ?>">
                      <div class="input-group-append">
                        <?php $akey = md5($_ENV['SITENAME'] . $_ENV['ENC_KEY']); ?>
                        <a data-fancybox data-type="iframe" data-options='{"iframe" : {"css" : {"width" : "80%", "height" : "80%"}}}' href="<?= base_url('filemanager/dialog.php?type=1&field_id=featured_image&akey=' . $akey); ?>" class="input-group-text btn-file-manager">Choose</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3 mb-3">
                    <label class="form-label">Embed Video</label>
                    <div class="input-group mb-3">
                      <input type="text" id="embed_video" name="embed_video" class="form-control" placeholder="Youtube Video ID, i.e. T5y7tpkyDuY" value="<?= (isset($result['embed_video'])) ? $result['embed_video'] : $this->session->flashdata('embed_video'); ?>">
                    </div>
                  </div>
                </div>
                <div class="col-md-12 col-lg-6">
                  <div class="mb-3 mb-3">
                    <label class="form-label">Video Duration</label>
                    <div class="d-block">
                      <?= generate_input(['field'=>'video_duration', 'label'=>'Durasi', 'form'=>'duration'], $result['video_duration'] ?? ''); ?>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="mb-3 mb-3">
                    <label class="form-label">Intro</label>
                    <textarea name="intro" id="intro" class="form-control" rows="4"><?= (isset($result['intro'])) ? $result['intro'] : $this->session->flashdata('intro'); ?></textarea>
                  </div>
                </div>
              </div>

              <?php if ($posttypes[$post_type]['table'] ?? '') : ?>
                <div class="row mt-4">
                  <?php foreach ($posttypes[$post_type]['fields'] as $field => $fieldConf) : ?>
                    <?php if ($field == 'post_id') continue; ?>
                    <div class="col-md-6 mb-3">
                      <?php
                      $fieldname = "meta[$field]";
                      $fieldConf['field'] = $fieldname;
                      ?>
                      <label for="<?= $field; ?>"><?= $fieldConf['label']; ?></label>
                      <?php if ($fieldConf['description'] ?? '') : ?>
                        <br><small class="desc"><?= $fieldConf['description']; ?></small>
                      <?php endif; ?>
                      <?= generate_input($fieldConf, $meta[$field] ?? ''); ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

            </div>

            <div class="col-md-4">

              <?php if ($result['status'] ?? '' == 'publish') : ?>
                <div class="mb-3">
                  <label class="form-label">Tanggal Publikasi</label>
                  <div class="input-group mb-3">
                    <?= generate_input(['field'=>'published_at', 'label'=>'Tanggal Publikasi', 'form'=>'datetime'], $result['published_at'] ?? ''); ?>
                  </div>
                </div>
              <?php endif; ?>

              <?php if ($post_type != 'page') : ?>
                <div class="mb-3-inline">
                  <label class="form-label">Kategori</label>
                  <div class="input-group">
                    <select name="category_id" id="category_id" class="form-control">
                      <option value="0" selected="selected">Choose ..</option>
                      <?php
                      if (isset($category->term_id))
                        $current = $category->term_id;
                      else
                        $current = null;

                      foreach ($categories as $c) {
                      ?>
                        <option value="<?= $c->term_id; ?>" <?= ($c->term_id == $current) ? 'selected' : ''; ?>><?= $c->name; ?></option>
                      <?php
                      }
                      ?>
                    </select>
                    <div class="input-group-append">
                      <a href="#" data-bs-toggle="modal" data-bs-target="#categoryModal" class="input-group-text btn btn-primary-outline mb-0">+ New Category</a>
                    </div>
                  </div>
                </div>
              <?php endif ?>

              <div class="mb-3">
                <label class="form-label">Tags</label>
                <input type="text" id="tags" name="tags" value="<?= (isset($tags)) ? $tags : $this->session->flashdata('tags'); ?>" class="form-control" placeholder="Ex: This, Is, Tag" />
              </div>

              <div class="mb-3">
                <label class="form-label">Layout</label>
                <?= form_dropdown('template', $template, $result['template'] ?? 'basic.html', 'class="form-control"'); ?>
              </div>

              <div class="mb-3">
                <label class="form-label">Is this featured?</label>
                <select id="featured" name="featured" class="form-control">
                  <option value="">Select ..</option>
                  <option value="Yes" <?= (isset($result['featured']) && !empty($result['featured'])) ? 'selected' : ''; ?>>Yes</option>
                  <option value="No" <?= (isset($result['featured']) && $result['featured'] == null) ? 'selected' : ''; ?>>No</option>
                </select>
              </div>


            </div>
          </div>

        </div>
      </div>
    </form>
  </div>

</div>

<!-- Modal -->
<div id="categoryModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">New Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body slugify">
        <input type="hidden" id="category_post_type" value="<?= $result['type'] ?? $post_type; ?>" />

        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" id="category_name" class="form-control title" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Slug</label>
          <input type="text" id="category_slug" class="form-control slug" required />
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-save-category">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<input type="hidden" id="base_url" value="<?= base_url(); ?>" />
<input type="text" id="input-image" style="display:none">

<!-- CKEditor -->
<script src="<?= base_url('views/admin/assets/ckeditor5/ckeditor.js'); ?>"></script>

<!-- Ace Code Editor -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.13/ace.min.js" integrity="sha512-jB1NOQkR0yLnWmEZQTUW4REqirbskxoYNltZE+8KzXqs9gHG5mrxLR5w3TwUn6AylXkhZZWTPP894xcX/X8Kbg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.13/mode-html.min.js" integrity="sha512-vSQkVhmiIt31RHmh8b65o0ap3yoL08VJ6MeuiCGo+92JDdSSWAEWoWELEf3WBk4e2tz/0CvnTe87Y2rFrNjcbg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.js"></script>

<?php $akey = md5($_ENV['SITENAME'] . $_ENV['ENC_KEY']); ?>
<script>
  $(function() {
    var base_url = $('#base_url').val();
    var post_id = $('#post_id').val();

    <?php if (($result['content_type'] ?? $content_type) == 'code') : ?>

      var editor = ace.edit("editor");
      editor.session.setMode("ace/mode/html");
      document.getElementById('editor').style.fontSize = '16px';
      editor.session.setUseWrapMode(true);

    <?php else : ?>

      ClassicEditor.create(document.querySelector('#editor'), {
          placeholder: 'Type the content here..'
        })
        .then(editor => {
          myckeditor['content'] = editor;
          myckeditor['content'].model.document.on('change:data', () => {
            $('#content_hidden').val(myckeditor['content'].getData());
          });
        })
        .catch(error => {
          console.error('There was a problem initializing the editor.', error);
        });

    <?php endif; ?>

    $('.editor-container#content-container').on('click', '.rfm_image', function() {
      $.fancybox.open({
        src: '<?= base_url(); ?>filemanager/dialog.php?type=1&field_id=content__entry__rfm_image_input&akey=<?= $akey; ?>',
        type: 'iframe'
      });
    })

    // Before close browser or change page send alert.
    var alertCloseMessage = "You didn't save changes.";
    let formChanged = false;
    window.onbeforeunload = function() {
      if (formChanged) {
        return alertCloseMessage;
      }
    }

    $('#tags').tagsInput();
    // $('#tags').tagsInput({
    //     'onChange': function(){
    //         formChanged = true;
    //     }
    // });

    // Check if form changed
    $("form :input").change(function() {
      formChanged = true;
    });
    $("form").submit(function() {
      window.onbeforeunload = null;
    });

    $('#content_type').on('change', function() {
      toastr.options.timeOut = 10000;
      toastr.warning('WARNING: Your current content may appear different if you change editor.<br>You will have to save and refresh page to change content editor.');
      toastr.options.timeOut = 5000;
    })

    // Save post
    $('.btn-save').on('click', function() {
      $('#post-form').submit();
    })

    $('#post-form').submit(function(e) {
      e.preventDefault();

      <?php if (($result['content_type'] ?? $content_type) == 'code') : ?>
        var editorData = editor.getValue();
      <?php else : ?>
        var editorData = myckeditor['content'].getData();
      <?php endif; ?>

      var post = $(this).serialize();
      var postArray = $(this).serializeArray();
      postArray.push({
        name: "content",
        value: editorData
      });

      $('.btn-save').html('Please wait ..');

      let mode = $('#mode').val();
      $.post(base_url + 'admin/post/' + mode, postArray)
        .done(function(response) {

          $('.btn-save').html('Save');

          if (response.status == 'success') {
            formChanged = false;

            if (mode == 'insert') {
              window.location.replace(base_url + 'admin/post/edit/' + response.id);
            } else {
              toastr.success(response.message);
            }
          } else {
            toastr.info(response.message);
          }

        });
    });

    // Save category
    $('.btn-save-category').click(function() {
      var post_type = $('#category_post_type').val();
      var name = $('#category_name').val();
      var slug = $('#category_slug').val();
      var option;

      $('.btn-save-category').html('Please wait ..');

      $.post(base_url + 'admin/post/category/insert', {
          post_type: post_type,
          name: name,
          slug: slug
        })
        .done(function(response) {

          $('.btn-save-category').html('Save');

          $('#category_name').val('');
          $('#category_slug').val('');

          if (response.status == 'failed') {
            toastr.warning(response.message);
          } else {
            option = '<option value="' + response.term_id + '" selected>' + name + '</option>';
            $('#category_id').append(option);

            $('#categoryModal').modal('hide');

            toastr.success('Successfully added ..');
          }
        });

    });
  })
</script>