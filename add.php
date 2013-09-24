<?php
	require('managerClass.php');

	$manager = new BookmarkManager(false, 'http://bookmarks/');
	$manager->addBookmark($_GET['url'], $_GET['title']);

