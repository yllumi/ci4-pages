<?= ($result[$config['field']] ?? '') > 0
	? PHP81_BC\strftime("%d %B %Y, %H:%M", strtotime($result[$config['field']]), ci()->config->item('locale'))
	: '-';