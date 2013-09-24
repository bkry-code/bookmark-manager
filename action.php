<?php
	require('managerClass.php');

	$manager = new BookmarkManager(false, 'http://bookmarks/');

	switch($_GET['do']) {
		case 'add': $manager->addBookmark($_GET['url'], $_GET['title']); break;
		case 'del': $manager->delBookmark($_GET['url']); break;
	}

