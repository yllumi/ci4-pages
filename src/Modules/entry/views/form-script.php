<script type="text/javascript" src="<?= base_url('views/' . config_item('template.admin_theme') . '/assets/chosen/chosen.jquery.js'); ?>"></script>
<script src="<?= base_url('views/' . config_item('template.admin_theme') . '/assets/select2/select2.min.js'); ?>"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinyColorPicker/1.1.1/jqColorPicker.min.js" integrity="sha512-jQ+T1MmwqyWSgkn1MtW6OxXc6wySH9YnmC8rPlEAn0CLgWH4gY1Di/6r42BOqO9zSbLQxZ/47Xs/6qc2rIZmXw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"></script>

<!-- CKEditor -->
<script src="<?= base_url('views/admin/assets/ckeditor5/ckeditor.js'); ?>"></script>

<!-- Ace Code Editor -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.13/ace.min.js" integrity="sha512-jB1NOQkR0yLnWmEZQTUW4REqirbskxoYNltZE+8KzXqs9gHG5mrxLR5w3TwUn6AylXkhZZWTPP894xcX/X8Kbg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.13/mode-html.min.js" integrity="sha512-vSQkVhmiIt31RHmh8b65o0ap3yoL08VJ6MeuiCGo+92JDdSSWAEWoWELEf3WBk4e2tz/0CvnTe87Y2rFrNjcbg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.10.1/mode-yaml.min.js" integrity="sha512-WcvQVyf7ECu3mkQRpaJJ2l05xJAIlFM1bscCbwduQBztxzoGUWqkAawsMdLr6tkD9ke4V6soIh6aufeAuW1ruw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="<?= base_url('views/admin/assets/js/custom.js'); ?>"></script>
<script>
    var myckeditor = [];
    $(function() {
        $('[data-toggle="chosen"]').chosen();

        $('.color').colorPicker();

        // DATEPICKER
        $('[data-toggle="datepicker"]').datepicker({
            format: 'dd-mm-yyyy'
        });

        var form = $('form');
        form.get(0).reset();
        form.find('[data-toggle="chosen"]').trigger('chosen:updated');

        $(".slugify input.title").keyup(function() {
            var title = $(this).val();
            $("input.slug").val(convertToSlug(title));
        });

        var to_reffer = $('.slugify').data('referer');

        $("#" + to_reffer).keyup(function() {
            var content = $(this).val();
            $('.slugify').val(convertToSlug(content));
        });

        $('.btn-connect-relation').click(function() {
            var id = $('#id').val();
            var entry = $('#entry').val();
            var relation = $('#relation').val();
            var choosen = $('#choice input:checked').map(function() {
                return $(this).val();
            });

            $.post(base_url + 'admin/entry/entry/update_relation', {
                    id: id,
                    entry: entry,
                    relation: relation,
                    choosen: choosen.get()
                })
                .done(function(data) {
                    if (data = 'done') {
                        location.reload();
                    }
                });
        });

        $('.ajaxupload').on('change', function(e) {
            console.log(e.target.files);
            $.ajax({
                url: "<?= site_url('entry/upload'); ?>",
                type: "POST",
                data: e.target.files,
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    console.log(data)
                }
            });
        })
    });

    function convertToSlug(Text) {
        return Text
            .toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
    }

    // CKEditor insert image to editor
    function insertImages(editor, url) {
        const imageCommand = editor.commands.get('insertImage');
        if (!imageCommand.isEnabled) {
            const notification = editor.plugins.get('Notification');
            const t = editor.locale.t;
            notification.showWarning(t('Could not insert image at the current position.'), {
                title: t('Inserting image failed'),
                namespace: 'rfm'
            });
            return;
        }
        editor.execute('insertImage', {
            source: url
        });
    }

    // Add rfm callback to place image to ckeditor
    function responsive_filemanager_callback(field_id) {
        let splitID = field_id.split('__');
        if (splitID[1] == 'entry') {
            insertImages(myckeditor[splitID[0]], $('#' + field_id).val());
        }
    }
</script>