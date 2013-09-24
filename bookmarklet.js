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
		alert('Added '+title);
	},
}

bookmarks.submitURL(document.location.href, window.document.title);
