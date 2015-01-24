<?php
/*
Plugin Name: Lorem ipsum article shortcode
Version: 1.0.5
Plugin URI: #
Description: #
Author: Ron Valstar
Author URI: http://www.ronvalstar.nl
*/
/*
todo: parenthesis, blockquote, h3/h4/h5, better anchors
min word: 300
https://medium.com/starts-with-a-bang/how-the-sun-really-shines-2dc9fafd3ea4
http://en.wikipedia.org/wiki/Flesch%E2%80%93Kincaid_readability_tests#Flesch_Reading_Ease
*/
if (!class_exists('LoremIpsumArticleShortcode')) {
	class LoremIpsumArticleShortcode {

		private $aLorem = array('a','et','at','in','mi','ac','id','eu','ut','non','dis','cum','sem','dui','nam','sed','est','nec','sit','mus','vel','leo','urna','duis','quam','cras','nibh','enim','quis','arcu','orci','diam','nisi','nisl','nunc','elit','odio','amet','eget','ante','erat','eros','ipsum','morbi','nulla','neque','vitae','purus','felis','justo','massa','donec','metus','risus','curae','dolor','etiam','fusce','lorem','augue','magna','proin','mauris','nullam','rutrum','mattis','libero','tellus','cursus','lectus','varius','auctor','sociis','ornare','magnis','turpis','tortor','semper','dictum','primis','ligula','mollis','luctus','congue','montes','vivamus','aliquam','integer','quisque','feugiat','viverra','sodales','gravida','laoreet','pretium','natoque','iaculis','euismod','posuere','blandit','egestas','dapibus','cubilia','pulvinar','bibendum','faucibus','lobortis','ultrices','interdum','maecenas','accumsan','vehicula','nascetur','molestie','sagittis','eleifend','facilisi','suscipit','volutpat','venenatis','fringilla','elementum','tristique','penatibus','porttitor','imperdiet','curabitur','malesuada','vulputate','ultricies','convallis','ridiculus','tincidunt','fermentum','dignissim','facilisis','phasellus','consequat','adipiscing','parturient','vestibulum','condimentum','ullamcorper','scelerisque','suspendisse','consectetur','pellentesque');
		private $aImageAlign = array('alignright','alignleft','aligncenter','alignnone');
		private $aLineEnd = array('.','?','!','...');
		private $aLineSeparator = array(',',':',';');
		private $iLorem;
		private $fDeviation;

		function __construct() {
			$this->iLorem = count($this->aLorem);
			add_shortcode('lll', array(&$this,'handleShortcode'));
			add_filter( 'the_title', 'do_shortcode', 11 );
		}

		function handleShortcode($atts,$content=null){//,$shortcode
			global $post;
			//post_content
			//post_title
			//post_type
			$aAttributeAlias = array(
				'p'=>'paragraph'
				,'s'=>'sentence'
				,'w'=>'word'
				,'h'=>'header'
			);
			// map alias attributes
			foreach ($aAttributeAlias as $alias=>$key) {
				if (isset($atts[$alias])) {
					$atts[$key] = $atts[$alias];
					unset($atts[$alias]);
				}
			}
			// predefining shortcode attribute variables to prevent IDE from whining about undefined vars
			// the number of paragraphs
			$paragraph = 12;
			// the number of sentences in a paragraph
			$sentence = 5;
			// the number of words in a sentence
			$word = 12;
			// the length of the header
			$header = null;
			$headerSize = 6;
			// the random seed
			$seed = isset($post)?$post->ID:1234;
			// how much the sentence-, paragraph and article length can deviate
			$deviation = .5;
			//
			extract(shortcode_atts(array(
				'paragraph' =>	$paragraph
				,'sentence' =>	$sentence
				,'word' =>		$word
				,'header' =>	$header
				,'seed' =>		$seed
				,'deviation' =>	$deviation
			), $atts));
			//
			//
			$this->fDeviation = floatval($deviation);
			srand($seed);
			//
			// is header
			if (is_array($atts)&&(in_array('h',$atts)||array_key_exists('h',$atts)||in_array('header',$atts)||array_key_exists('header',$atts))) {
				if (!isset($header)) $header = $headerSize;
				$sResult = $this->getSentence($header,false);
				$sResult = ucfirst($sResult);
			} else {
				$sResult = $this->getArticle($paragraph,$sentence,$word);
			}
			//
			/*dump(array(
				'is_array'=>is_array($atts)
				,'atts'=>$atts
				,'in_array h'=>in_array('h',$atts)
				,'in_array header'=>in_array('header',$atts)
				,'array_key_exists h'=>array_key_exists ('h',$atts)
				,'array_key_exists header'=>array_key_exists ('header',$atts)
			));*/
			//
			return $sResult;
		}

		/**
		 * Returns and article
		 * @param $numParagraphs
		 * @param $numSentences
		 * @param $numWords
		 * @return string
		 */
		private function getArticle($numParagraphs,$numSentences,$numWords){
			$aArticle = array();
			for ($i=0;$i<$this->deviateInt($numParagraphs,$this->fDeviation);$i++) {
				$aArticle[] = $this->getParagraph($numSentences,$numWords);
			}
			$sArticle = implode('',$this->enhanceArticle($aArticle));
			return $sArticle;
		}

		/**
		 * Returns a paragraph.
		 * @param $numSentences
		 * @param $numWords
		 * @return string
		 */
		private function getParagraph($numSentences,$numWords){
			$aParagraph = array();
			for ($i=0;$i<$this->deviateInt($numSentences,$this->fDeviation);$i++) {
				$aParagraph[] = $this->getSentence($numWords);
			}
			$sParagraph = implode(' ',$this->enhanceParagraph($aParagraph));
			return '<p>'.$sParagraph.'</p>';
		}

		/**
		 * Returns a sentence.
		 * @param $numWords
		 * @return string
		 */
		private function getSentence($numWords,$enhanced=true){
			$aSentence = array();
			for ($i=0;$i<$this->deviateInt($numWords,$this->fDeviation);$i++) {
				$sWord = $this->aLorem[rand(0,$this->iLorem-1)];
				$aSentence[] = $enhanced&&$i>0?$this->enhanceWord($sWord):$sWord;
			}
			$sSentence = implode(' ',$enhanced?$this->enhanceSentence($aSentence):$aSentence);
			return $sSentence;
		}

		/**
		 * Randomly enhance words with anchor, strong or em
		 * @param string $word
		 * @return string
		 */
		private function enhanceWord($word){
			if (rand(0,100)===1) {
				$word = '<a href="#">'.$word.'</a>';
			} else if (rand(0,100)===1) {
				$word = '<strong>'.$word.'</strong>';
			} else if (rand(0,100)===1) {
				$word = '<em>'.$word.'</em>';
			}
			return $word;
		}

		/**
		 * Enhance sentence with capitalisation, line endings, comma's and colons
		 * @param array $sentence
		 * @return array
		 */
		private function enhanceSentence($sentence){
			$iLength = count($sentence);
			// capitalise
			$sentence[0] = ucfirst($sentence[0]);
			// sentence ends in
			$sEnd = $this->arrayPick($this->aLineEnd,4);
			$sentence[$iLength-1] = $sentence[$iLength-1].$sEnd;
			// comma, colon and semi-colon
			if (rand(0,6)===0) {
				$sSeparator = $this->arrayPick($this->aLineSeparator,3);
				$iPosition = rand(intval(.25*$iLength),intval(.75*$iLength));
				$sentence[$iPosition] = $sentence[$iPosition].$sSeparator;
			}
			//
			return $sentence;
		}

		/**
		 * Randomly enhance paragraphs with image or list
		 * @param array $paragraph
		 * @return array
		 */
		private function enhanceParagraph($paragraph){
			if (rand(0,7)===1) { // image
				$sClass = $this->arrayPick($this->aImageAlign,3);
				$paragraph[0] = '<img class="'.$sClass.'" src="'.$this->createImageData().'" />'.$paragraph[0];
			} else if (rand(0,4)===1) { // list
				$sListType = rand(0,1)===1?'ol':'ul';
				$iListLength = rand(5,10);
				$aList = array();
				for ($i=0;$i<$iListLength;$i++) {
					$aList[] = $this->getSentence($this->deviateInt(8,$this->fDeviation),false);
				}
				$paragraph = array('<'.$sListType.'><li>'.implode('</li><li>',$aList).'</li></'.$sListType.'>');
			}
			return $paragraph;
		}

		/**
		 * Enhances articles with sub-headings
		 * @param array $article
		 * @return array
		 */
		private function enhanceArticle($article) {
			foreach ($article as $nr=>$paragraph) {
				if ($nr>1&&rand(0,3)===1) {
					$article[$nr] = '<h3>'.ucfirst($this->getSentence(6,false)).'</h3>'.$article[$nr];
				} else if (rand(0,10)===1&&strpos($article[$nr],'<img')===false&&strpos($article[$nr],'<li')===false) { // blockquote
					$article[$nr] = '<blockquote>'.$article[$nr].'<footer>— <a href="#">'.ucfirst($this->getSentence(2,false)).'</a></footer></blockquote>';
				}
			}
			return $article;
		}

		/**
		 * Create a base64 dummy image for use in src
		 * @return string
		 */
		private function createImageData(){
			$aSizes = $this->getImageSizes();
			$fAspectRatio = 4/3;
			$iW = $aSizes['medium'][0];
			$iH = $iW/$fAspectRatio;
			//
			$oImg = imagecreatetruecolor($iW,$iH);
			$oClrFill = imagecolorallocate($oImg, 64, 64, 64);
			$oClrLine = imagecolorallocate($oImg, 192, 192, 192);
			//
			imagefill($oImg,0,0,$oClrFill);
			imagerectangle($oImg,0,0,$iW-1,$iH-1,$oClrLine);
			imageline($oImg,0,0,$iW-1,$iH-1,$oClrLine);
			imageline($oImg,0,$iH-1,$iW-1,0,$oClrLine);
			//
			ob_start();
			imagepng($oImg);
			$data = ob_get_contents();
			ob_end_clean();
			imagedestroy($oImg);
			//
			return 'data:image/png;base64,' . base64_encode($data);
		}

		/**
		 * Deviates randomly for a given integer.
		 * @param $int
		 * @param $deviation
		 * @return $int
		 */
		private function deviateInt($int,$deviation) {
			$iDeviation = intval($int*$deviation);
			return $int + rand(-$iDeviation,$iDeviation);
		}

		/**
		 * Pick a random value from an array. A higher exponent will cause it more likely for the first array entries to be returned.
		 * @param $array
		 * @param int $exponent
		 * @return mixed
		 */
		private function arrayPick($array,$exponent=1){
			return $array[intval(pow(rand(0,1000)/1000,$exponent)*count($array))];
		}

		/**
		 * Read all Wordpress image sizes
		 * @return array
		 */
		private function getImageSizes() {
			global $_wp_additional_image_sizes;
			$sizes = array();
			foreach (get_intermediate_image_sizes() as $s) {
				$sizes[$s] = array(0,0);
				if (in_array($s,array('thumbnail','medium','large'))) {
					$sizes[$s][0] = get_option($s.'_size_w');
					$sizes[$s][1] = get_option($s.'_size_h');
				} else {
					if (isset($_wp_additional_image_sizes)&&isset($_wp_additional_image_sizes[$s])) {
						$sizes[$s] = array($_wp_additional_image_sizes[$s]['width'],$_wp_additional_image_sizes[$s]['height'],);
					}
				}
			}
			return $sizes;
		}
	}
	new LoremIpsumArticleShortcode();
}

if (!function_exists("dump")) {
	/**
	 * Dumps an Object or Array
	 * @param $s
	 */
	function dump($s) {
		echo "<pre>";
		print_r($s);
		echo "</pre>";
	}
}
?>