
<!-- Variable Modal -->
<div id="editable-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Variable</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <textarea id="summernote" class="summernote" cols="30" rows="6"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-update">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>

<input type="hidden" id="base_url" value="<?php echo site_url();?>" />

<script>
    $(document).ready(function(){
        
        var base_url = $('#base_url').val();
        var content;
        var name;
        
        $('#summernote').summernote({
            toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['view', ['fullscreen', 'codeview']]
            ],
            height: 300,
            dialogsInBody: true
        });

        $( ".note-btn" ).removeClass("btn-default");
        
        $( ".editable" ).on( "dblclick", function( event ) {
            content = $(this).html();
            name = $(this).attr('name');
            
            $('.modal .modal-body').css('overflow-y', 'auto'); 
            $('.modal .modal-body').css('max-height', $(window).height() * 0.7);
            $('#summernote').summernote('code', content);
            $("#summernote").summernote("fullscreen.toggle");
            $("#summernote").summernote("fullscreen.toggle");
            $('#editable-modal').modal();
        });

        $( ".btn-update" ).on( "click", function( event ) {
            
            content = $('.summernote').val();
            $('.editable-' + name).html(content);
            
            $.post( base_url + "variable/update", { name: name, value: content })
            .done(function( data ) {
                console.log(data);
            });
            
            $('#editable-modal').modal('hide');
        });
    });
</script>