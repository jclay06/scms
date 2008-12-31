window.addEvent('domready',function() { new SmoothScroll({ duration: 1000 }); });
window.addEvent('domready', function() {

	var myScroller = new Fx.Scroll(window);

	var hash = document.location.hash;
	if(hash.length > 1) {
		hash = hash.substr(1);
		myScroller.toElement(hash);
	}
	else {
		myScroller.toElement('comic');
	}

	var comms = $('commentlist');
	var cform = $('commentform');
	var loader = $('ajax-loader');

	cform.addEvent('submit', function(e) {
		e.stop();
		var notice = 'HEY YOU!\n\n';
		var noticeLength = notice.length;
		if(cform.name.value.length < 2) {
			notice += 'You forgot to leave your name!\n';
		}
		if(cform.comment.value.length < 2) {
			notice += 'You forgot to leave a comment!\n';
		}
		if($('security_code') != null && cform.security_code.value.length < 4) {
			notice += 'You forgot to fill out the captcha code!\n';
		}
		if(notice.length != noticeLength) {
			alert(notice);
			return false;
		}
		loader.set('html', '<p><center><img src="/content/img/ajax-loader.gif" /></center></p>');
		cform.setProperty('action', '/php/ajax.php?form=comment');
		this.set('send', {
			onSuccess: function(html) {
				var data = JSON.decode(html);
				if(data.error != null) {
					loader.set('html', '<p class="message">'+data.error+'</p>');
				}
				else {
					data.comment = data.comment.replace(/\\n/g, '<br />');
					data.ID = comms.getChildren('li').length + 1;
					data.alt = (data.ID % 2) ? 'even' : 'odd';
					var el = 'comment-'+data.ID;
					var li = new Element('li', {id: 'comment-'+data.ID, class: 'bubble'});
					if(data.website != '') {
						data.name = '<a href="'+data.website+'" rel="nofollow" class="author" target="_blank">'+data.name+'</a>';
					}
					li.set('html', '<blockquote class="'+data.alt+'"><p>'+data.comment+'</p></blockquote><div class="author_line_'+data.alt+'"><span>'+data.name+'</span> on <a href="#'+el+'" title="Permalink to Comment">'+data.time+'</a></div>');
					loader.empty();
					comms.grab(li, 'bottom');
					cform.comment.value = '';
					myScroller.toElement(el);
				}
				if($('captcha') != null) {
					$('captcha').set('src', '/php/captcha.php?' + Math.random());
				}
				if($('security_code') != null) {
					$('security_code').set('value', '');
				}
			},
			onFailure: function() {
				loader.set('html', '<p class="message">Failed to submit comment!</p>');
			}
		});
		this.send();
	});
	
	$$('#smiliebox img').addEvent('click', function(e) {
		e.stop();
		$('comment').set('value', $('comment').get('value') + ' ' + this.get('alt') + ' ').focus();
	});

});