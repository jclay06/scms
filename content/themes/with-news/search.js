window.addEvent('domready', function() {

	$('search-submit').setStyle('display', 'none');

	var search = $('search');
	var search_text = 'Search Our Comics!';
	search.addEvent('focus', function() {
		if(search.value == search_text) {
			search.set('value', '');
		}
		else if(search.value.length > 2 && prev != null) {
			lives.setStyle('display', 'block');
		}
	});
	search.addEvent('blur', function() {
		if(search.value.length == 0) {
			search.set('value', search_text);
		}
		timer = $clear(timer);
		timer = function(){ lives.setStyle('display', 'none') }.delay(300);
	});
	
	var sform = search.parentNode;
	var lives = $('livesearch');
	var timer = null;
	var prev = null;
	
	sform.addEvent('keyup', function() {
		if(search.value < 2) {
			lives.empty();
			lives.setStyle('display', 'none');
			return;
		}
		timer = $clear(timer);
		if(search.value != prev) {
			prev = null;
			lives.set('html', '<div class="center"><img src="/content/img/ajax-loader.gif" /></div>').setStyle('display', 'block');
			var ajax = new Request.JSON({
				url: '/php/ajax.php?form=search',
				method: 'get',
				onSuccess: function(json) {
					lives.empty();
					if(json == null) {
						lives.set('html', 'Error?');
					}
					else if(json.error != null) {
						lives.set('html', json.error);
					}
					else {
						prev = search.value;
						$each(json, function(result, index) {
							var div = new Element('div');
							result.text = result.text.replace(new RegExp("("+search.value+")","gi"),"<span>$1</span>");
							div.set('html', '<a href="?p='+result.PID+'">'+result.title+'</a><div>'+result.text+'</div>');
							lives.grab(div);
						});
						lives.setStyle('display', 'block');
					}
				},
				onFailure: function() {
					lives.set('html', 'Failed to perform search!');
				}
			});
			timer = function(){ ajax.send('s='+search.value) }.delay(500);
		}
		else if(search.value == prev) {
			lives.setStyle('display', 'block');
		}
	});
	lives.addEvent('click', function() {
		timer = $clear(timer);
	}).addEvent('blur', function() {
		timer = $clear(timer);
		timer = function(){ lives.setStyle('display', 'none') }.delay(300);
	});
	
});