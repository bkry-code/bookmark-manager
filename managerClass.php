<?php

	class BookmarkManager {

		private $baseURL;
		private $themeId;
		private $template_page;
		private $template_listBookmark;
		private $template_listTags;

		function __construct($theme, $baseURL) {
			header('Content-Type: text/html; charset=utf-8');
			mb_internal_encoding('UTF-8');

			$this->themeId = (!$theme) ? 'default' : $theme;
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
			$this->template_listTags 	= file_get_contents($theme.'tag_item.html');
		}

		private function parseTemplate($data, $template) {
			foreach($data as $key => $value) {
				if($key == 'date') {
					$template = str_replace('{{stamp}}', date('Y-m-d', $value), $template);
					$template = str_replace('{{date}}', date('d.m.Y', $value), $template);
				} else {
					$template = str_replace('{{'.$key.'}}', $value, $template);
				}
			}
			return $template;
		}

		private function openTags() {
			$json = null;
			if(file_exists('tags.json')) {
				$json = file_get_contents('tags.json');
			}
			return $json;
		}

		private function parseTags() {
			$tags = json_decode($this->openTags());
			return $tags->tags;
		}

		public function getListOfTags() {
			return $this->parseTags();
		}

		private function openBookmarks() {
			$json = null;
			if(file_exists('bookmarks.json')) {
				$json = file_get_contents('bookmarks.json');
			}

			return $json;
		}

		private function parseBookmarks() {
			$bookmarks = json_decode($this->openBookmarks());
			return $bookmarks->bookmarks;
		}

		public function getListOfBookmarks() {
			return $this->parseBookmarks();
		}

		public function listBookmarks() {

			$data = array('bookmarks' => '', 'tags' => '', 'bookmarklet' => $this->getBookmarklet());
			$bookmarks 	= $this->getListOfBookmarks();
			$tags 		= $this->getListOfTags();

			if($bookmarks == null) {
				echo $this->parseTemplate(array('bookmarks' => ''), $this->template_page);
				return false;
			}

			if(count($bookmarks) > 0) {
				// reverse array to have the newest item on top
				$bookmarks = array_reverse($bookmarks);
				foreach($bookmarks as $bookmark) {
					$data['bookmarks'] .= $this->parseTemplate($bookmark, $this->template_listBookmark);
				}
			}

			if(count($tags) > 0) {
				foreach($tags as $tag) {
					$data['tags'] .= $this->parseTemplate($tag, $this->template_listTags);
				}
			}

			echo $this->parseTemplate($data, $this->template_page);
		}

		public function addBookmark($url, $title) {
			// get json of currently saved bookmarks
			$json = $this->openBookmarks();

			// check if bookmark is already saved, if not, do so
			if(strpos(stripslashes($json), '"'.$url.'"') === false) {
				$bookmarks = json_decode($json);
				$bookmarks->bookmarks[] = array(
					'url' => $url,
					'title' => $title,
					'date' => time()
				);

				file_put_contents('bookmarks.json', json_encode($bookmarks));
				$this->showBookmarkletNotification('Saved bookmark', true);
			} else {
				$this->showBookmarkletNotification('Already in your list');
			}
			
		}

		public function delBookmark($url) {
			// get json of currently saved bookmarks
			$json = $this->openBookmarks();

			// check if bookmark is already saved, if not, do so
			if(strpos(stripslashes($json), '"'.$url.'"') !== false) {
				$bookmarks = json_decode($json);
				$newList = array('bookmarks' => array());

				foreach($bookmarks->bookmarks as $bookmark) {
					if($bookmark->url != $url) {
						$newList['bookmarks'][] = array(
							'url' => $bookmark->url,
							'title' => $bookmark->title,
							'date' => $bookmark->date
						);
					}
				}

				file_put_contents('bookmarks.json', json_encode($newList));
			}

			header('Location: '.$this->baseURL);
		}

		public function searchBookmark($term) {
			$bookmarks = $this->getListOfBookmarks();
			$results = array();

			foreach($bookmarks as $bookmark) {
				if(strpos(strtolower($bookmark->url), strtolower($term)) > 0 || strpos(strtolower($bookmark->title), strtolower($term)) > 0) {
					$results[] = array(
						'url' => $bookmark->url,
						'label' => $bookmark->title
					);
				}
			}
			echo json_encode($results);
		}

		public function showBookmarkletNotification($message, $spinner = false) {
			$output = '<!DOCTYPE html>
						<html>
							<head>
									<meta charset="utf-8">
									<meta http-equiv="X-UA-Compatible" content="IE=edge">
									<style type="text/css" media="screen">
										body { background: #333; color: #fff; text-align: center; font-family: arial, sans-serif; padding-top: 15px;}
									</style>
							</head>
							<body>';

								if($spinner !== false) : $output .= '<img src="themes/'.$this->themeId.'/img/loader-big.gif" alt="Loading">'; endif;
								$output .= '<p>'.$message.'</p>
							</body>
						</html>';

			echo $output;
		}
	}








