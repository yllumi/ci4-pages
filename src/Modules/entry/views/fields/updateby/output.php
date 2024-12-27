<a href="#" data-toggle="popover" class="owner-<?= $result['user']['id']; ?>" title="User Detail" data-trigger="hover">
	<?= $result['user']['name']; ?> 
	<span class="fa fa-hand-o-up"></span>
</a>
<div class="popover-detail owner-detail-<?= $result['user']['id']; ?>" style="display:none">
	<div class="panel panel-default">
		<div class="panel-body">
			<p class="m-0">UserID: <?= $result['user']['id']; ?> <br>
			   Username: <?= $result['user']['username']; ?> <br>
			   Email: <?= $result['user']['email']; ?></p>
		</div>
    </div>
</div>
<script>
	$(function(){
		$('.owner-<?= $result['user']['id']; ?>').popover({
			content: function(){
				return $(this).siblings('.owner-detail-<?= $result['user']['id']; ?>').html();
			},
			html: true
		});
	})
</script>