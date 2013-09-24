<?php
	require('managerClass.php');

	$manager = new BookmarkManager(false, 'http://bookmarks/');
	$manager->listBookmarks();

