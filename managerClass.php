<?php

	class BookmarkManager {

		private $baseURL;
		private $themeId;
		private $template_page;
		private $template_listBookmark;

		function __construct($theme, $baseURL) {
			$this->themeId = $theme;
			$this->baseURL = $baseURL;
			$this->openTemplate();
		}

		private function getBookmarklet() {
			$script = "javascript:(function(){
				var jsCode=document.createElement('script');
				jsCode.setAttribute('src','".$this->baseURL."bookmarklet.js');
				document.body.appendChild(jsCode);
				}());";
			return $script;
		}

		private function openTemplate() {
			$theme = (is_dir('themes/'.$this->themeId) && $this->themeId !== false) ? 'themes/'.$this->themeId.'/' : 'themes/default/';
			$this->template_page 			= file_get_contents($theme.'template.html');
			$this->template_listBookmark 	= file_get_contents($theme.'bookmark_item.html');

		}

		private function parseTemplate($data, $template) {
			foreach($data as $key => $value) {
				$template = str_replace('{{'.$key.'}}', $value, $template);
			}

			return $template;
		}

		private function openBookmarks() {

			if(file_exists('bookmarks.json')) {
				$json = file_get_contents('bookmarks.json');
			} else {
				touch('bookmarks.json');
			}

			return $json;
		}

		private function parseBookmarks() {
			$bookmarks = json_decode(stripslashes($this->openBookmarks()));
			return $bookmarks->bookmarks;
		}

		public function getListOfBookmarks() {
			return $this->parseBookmarks();
		}

		public function listBookmarks() {

			$data = array('bookmarks' => '', 'bookmarklet' => $this->getBookmarklet());
			$bookmarks = $this->getListOfBookmarks();

			if($bookmarks == null) {
				echo 'Es sind noch keine Lesezeichen vorhanden';
				return false;
			}

			// reverse array to have the newest item on top
			$bookmarks = array_reverse($bookmarks);
			foreach($bookmarks as $bookmark) {
				$data['bookmarks'] .= $this->parseTemplate($bookmark, $this->template_listBookmark);
			}

			echo $this->parseTemplate($data, $this->template_page);
		}

		public function addBookmark($url, $title) {
			// get json of currently saved bookmarks
			$json = $this->openBookmarks();

			// check if bookmark is already saved, if not, do so
			if(strpos(stripslashes($json), '"'.$url.'"') === false) {
				$bookmarks = json_decode($json);
				$bookmarks->bookmarks[] = array('url' => $url, 'title' => $title);
				file_put_contents('bookmarks.json', json_encode($bookmarks));
			}
		}
	}