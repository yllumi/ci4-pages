<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
	<style>
		.content {display:none;}
		.content.active {display:block;}
	</style>
</head>
<body>
	<div x-data="{ tab: 'foo' }">
		<button :class="{ 'active': tab === 'foo' }" @click="tab = 'foo'">Foo</button>
		<button :class="{ 'active': tab === 'bar' }" @click="tab = 'bar'">Bar</button>

		<div class="content" :class="{ 'active': tab === 'foo' }">Tab Foo</div>
		<div class="content" :class="{ 'active': tab === 'bar' }">Tab Bar</div>
	</div>
</div>
</body>
</html>