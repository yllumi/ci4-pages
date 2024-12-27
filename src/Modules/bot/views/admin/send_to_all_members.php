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

<h4>Broadcast to Premium Members</h4>
<div class="row">

	<div class="col-9">
		<input type="text" id="imgurl" class="form-control" placeholder="Image url">
		<textarea id="chat_message" class="form-control" placeholder="Your message. Max up to 200 characters if image url provided"></textarea>
	</div>
	<div class="col-3">
		<button class="btn btn-secondary btn-block btn-send" style="margin:0;height:100%;border-radius:5px;">Send to <?= count($members); ?> members</button>
	</div>

</div>

<style>.progress-bar, .progress-bar span{display: inline}</style>
<div class="sending-indicator mt-3" style="display:none;">
	<strong>sending..</strong>
	<div class="progress">
	  <div class="progress-bar" style="width:10%;" role="progressbar"><span>0</span>/<?= count($members); ?></div>
	</div>
</div>

<script>
	let progress = 0;
	let total = <?= count($members) ?>;
	var members = [<?php 
		$str = '';
		foreach($members as $member):
			$str .= '{id:'.$member['id'].',name:"'.$member['first_name'].'"},';
		endforeach; 
		echo rtrim($str,','); 
		?>];
	$(function(){
		$('.btn-send').on('click', function(e){
			e.preventDefault();
			if(confirm("Serius udah siap buat dikirim?")){	
				$(this).text('Sending..').addClass('disable').prop('disabled', true);

				members.forEach(function(member){
					$('.sending-indicator').fadeIn('fast');
					$.ajax({
						type:'POST',
						url: '<?= site_url('admin/bot/bot/sendMessage/'.$botname); ?>/' + member.id + '/' + member.name, 
						data: {message:$('#chat_message').val(), imgurl: $('#imgurl').val()}, 
						success: function(data){
							console.log(data);
							if(data == 'success'){
								progress++;
								console.log(progress + ' sent');

								$('.progress-bar').css('width', (progress/total*100)+'%');
								$('.progress-bar span').text(progress);
								if(progress == total) {
									progress = 0;
									finishSending();
								}
							}
						}
					})
				});
			}
		})
	})

	function finishSending()
	{
		$('#chat_message').val("");
		$('#imgurl').val("");
		$("#notif-success").text('Sending broadcast succeed.').show();
		$('.btn-send').text('Send')
					  .removeClass('disable')
					  .prop('disabled',false);
		setTimeout(function(){
			$('.sending-indicator').fadeOut('fast');
			$('.progress-bar').css('width', '10%');
		}, 1000);
	}
</script>