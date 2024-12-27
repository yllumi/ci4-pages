<div class="mb-3">
    <div class="row">
        <div class="col-lg-6">
            <h3>@<?= $botname; ?></h3>
        </div>
        <div class="col-lg-6 text-end">
			<a href="<?= site_url('admin/bot/bot'); ?>" class="btn btn-secondary">&laquo; back to list</a>
        </div>
    </div>
</div>

<div class="alert alert-success" id="notif-success" style="display:none"></div>

<h4>Send to Premium Group</h4>
<div class="row">

	<div class="col-9">
		<input type="text" id="imgurl" class="form-control" placeholder="Image url">
		<textarea id="chat_message" class="form-control" placeholder="Your message. Max up to 200 characters if image url provided"></textarea>
	</div>
	<div class="col-3">
		<button class="btn btn-secondary btn-block btn-send" style="margin:0;height:100%;border-radius:5px;">Send</button>
	</div>

</div>

<script>
	$(function(){
		$('.btn-send').on('click', function(e){
			e.preventDefault();
			$(this).text('Sending').addClass('disable').prop('disabled', true);
			$.ajax({
				type:'POST',
				url: '<?= site_url('admin/bot/bot/sendMessage/'.$botname.'/'.$chat_id); ?>', 
				data: {message:$('#chat_message').val(), imgurl: $('#imgurl').val()}, 
				success: function(data){
					console.log(data);
					$('#chat_message').val("");
					$('#imgurl').val("");
					$("#notif-success").text(data).show();
					$('.btn-send').text('Send').removeClass('disable').prop('disabled',false);
				}
			})
		})
	})
</script>