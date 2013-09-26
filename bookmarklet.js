var bookmarks = {

	// edit your url here
	baseUrl : 'http://bookmarks/action.php',

	submitURL : function(source, title) {   
		var parameters = '?do=add&url=' + encodeURIComponent(source) + '&title=' + encodeURIComponent(title);
		var iframe = document.createElement('iframe');

		iframe.setAttribute('id', 'bookmarkFrame');
		iframe.setAttribute('src', bookmarks.baseUrl + parameters);
		iframe.style.width = 1 + 'px';
		iframe.style.height = 1 + 'px';

		document.body.appendChild(iframe);
		this.showNotification();
	},

	showNotification: function() {
		var notify = document.getElementById('bookmarkFrame');

		notify.style.width = '150px';
		notify.style.height = '125px';
		notify.style.border = '0px';
		notify.style.position = 'fixed';
		notify.style.top = '0px';
		notify.style.left = '50%';
		notify.style.marginLeft = '-75px';
		notify.style.boxShadow = '0 0 10px #999';
		notify.style.zIndex = '9999';

		window.setTimeout(function() {
			document.getElementsByTagName('body')[0].removeChild(notify);
		}, 3000);

	}
}

bookmarks.submitURL(document.location.href, window.document.title);
