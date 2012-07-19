<?php
/************************************************************* 
 * This script is developed by Arturs Sosins aka ar2rsawseen, http://webcodingeasy.com 
 * Feel free to distribute and modify code, but keep reference to its creator 
 * 
 * Proper nouns class can find and extract proper nouns from given text 
 * using heuristics based on syntactic clues like 
 * first letter uppercased, word position in sentence, etc.
 * 
 * For more information, examples and online documentation visit:  
 * http://webcodingeasy.com/PHP-classes/Find-and-extract-proper-nouns-from-text 
**************************************************************/
class proper_nouns
{
	//symbols to filter out of text to leave only words
	private $symbols = array('/','\\','\'','"',"'",',','. ','<','>','?',';',':','[',']','{','}','|','=','+','-','_',')','(','*','&','^','%','$','#','@','!','~','`','.','0','1','2','3','4','5','6','7','8','9');
	//conjunctions used in multiple word proper nouns
	private $conjunctions = array(
		//can be used in the beggining of proper noun
		"start" => array("the", "mr", "mrs", "ms", "dr", "mstr", "miss", "sir"),
		//needs to be in between two proper nouns
		"middle" => array("of", "the", "and"),
		//can have dots after them and not be end of the sentence
		"dot" => array("mr", "mrs", "ms", "dr")
	);
	private $stop_words = array();
	//symbols to ignore, when determining position in sentence
	//all the symbols that might be between end of one sentence and beggining of another
	private $ignore = array(" ", "\n", "\t", "\r", "\r\n");
	//punctuations used in the end or start of sentence, which justifies first uppercase letter
	// and means that word might not be a proper noun
	private $punctuation = array(".", "?", "!", "'", '"');
	//include acronyms
	private $acronyms = false;
	//include possible proper nouns, that could not be definetly determined, 
	//for example, if word appears only in the beggining of sentence
	private $possible = false;
	//try to combine to multiple word proper nouns
	private $multi_words = true;
	//more strict mode of finding proper nouns
	//for example matches only proper nouns that has first letter uppercased in whole text, 
	//it means that lastname Smith won't be found if word smith also appears in text
	private $strict = false;
	
	/*****************************
	* Some setters
	******************************/
	public function set_conjunctions($arr, $type = "start"){
		if(!is_array($arr))
		{
			$this->conjunctions[$type] = array(mb_strtolower($arr));
		}
		else
		{
			$new = array();
			foreach($arr as $val)
			{
				$new[] = mb_strtolower($val);
			}
			$this->conjunctions[$type] = $new;
		}
	}
	
	public function set_symbols($arr){
		if(!is_array($arr))
		{
			$this->conjunctions = array(mb_strtolower($arr));
		}
		else
		{
			$new = array();
			foreach($arr as $val)
			{
				$new[] = mb_strtolower($val);
			}
			$this->conjunctions = $new;
		}
	}
	
	public function set_ignore($arr){
		if(!is_array($arr))
		{
			$this->ignore = array(mb_strtolower($arr));
		}
		else
		{
			$new = array();
			foreach($arr as $val)
			{
				$new[] = mb_strtolower($val);
			}
			$this->ignore = $new;
		}
	}
	
	public function set_punctuation($arr){
		if(!is_array($arr))
		{
			$this->punctuation = array(mb_strtolower($arr));
		}
		else
		{
			$new = array();
			foreach($arr as $val)
			{
				$new[] = mb_strtolower($val);
			}
			$this->punctuation = $new;
		}
	}
	
	public function acronyms($bool){
		if($bool)
		{
			$this->acronyms = true;
		}
		else
		{
			$this->acronyms = false;
		}
	}
	
	public function possible($bool){
		if($bool)
		{
			$this->possible = true;
		}
		else
		{
			$this->possible = false;
		}
	}
	
	public function multi_words($bool){
		if($bool)
		{
			$this->multi_words = true;
		}
		else
		{
			$this->multi_words = false;
		}
	}
	
	public function strict($bool){
		if($bool)
		{
			$this->strict = true;
		}
		else
		{
			$this->strict = false;
		}
	}
	
	public function stop_words($arr){
		if(!is_array($arr))
		{
			$this->stop_words = array(mb_strtolower($arr));
		}
		else
		{
			$new = array();
			foreach($arr as $val)
			{
				$new[] = mb_strtolower($val);
			}
			$this->stop_words = $new;
		}
	}
	
	/*****************************
	* Some private functions
	******************************/
	private function clean_text($text){
		//replace all not needed symbols
		$text = str_replace($this->symbols, " ", $text);
		//replace multiple spaces
		$text = preg_replace('/\s+/', ' ', $text);
		//split text by words
		$keys = explode(" ", $text);
		return $keys;
	}
	
	private function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false){
		if (!function_exists('mb_ucfirst')) {
			$first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
			$str_end = "";
			if ($lower_str_end) {
				$str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
			}
			else {
				$str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
			}
			$str = $first_letter . $str_end;
			return $str;
		}
		else
		{
			return mb_ucfirst($str);
		}
    }
	
	private function get_candidates($words, $text){
		$proper = array();
		foreach($words as $key)
		{
			if(trim($key) != "" && $key == $this->mb_ucfirst($key) && !in_array($key, $proper))
			{
				//acronyms
				if($key == mb_strtoupper($key))
				{
					if($this->acronyms)
					{
						$proper[] = $key;
					}
				}
				//check case sensitivity in whole text
				else
				{
					$stop = false;
					if($this->strict)
					{
						foreach($words as $val)
						{
							if(mb_strtolower($key) == mb_strtolower($val) && $val != $this->mb_ucfirst($val))
							{
								$stop = true;
								break;
							}
						}
					}
					//check position in sentence
					if(!$stop)
					{
						$parts = explode($key, $text);
						$stop = false;
						foreach($parts as $cnt => $part)
						{
							if(sizeof($parts) > $cnt + 1)
							{
								$cut = 1;
								while(mb_strlen($part)-$cut >= 0 && in_array(substr($part, mb_strlen($part)-$cut, 1), $this->ignore))
								{
									$cut++;
								}
								if(mb_strlen($part)-$cut > 0 && !in_array(substr($part, mb_strlen($part)-$cut, 1), $this->punctuation))
								{
									$proper[] = $key;
									$stop = true;
								}
								else if(!$this->strict)
								{
									$cut++;
									$str = "";
									while(mb_strlen($part)-$cut >= 0 && (!in_array(substr($part, mb_strlen($part)-$cut, 1), $this->ignore) && !in_array(substr($part, mb_strlen($part)-$cut, 1), $this->punctuation)))
									{
										$str = substr($part, mb_strlen($part)-$cut, 1).$str;
										$cut++;
									}
									if(in_array(mb_strtolower($str), $this->conjunctions["dot"]))
									{
										$proper[] = $key;
										$stop = true;
									}
								}
							}
							if($stop)
							{
								break;
							}
						}
						if(!$stop && $this->possible)
						{
							$proper[] = $key;
						}
					}
				}
			}
		}
		return $proper;
	}
	
	private function get_multi_words($proper, $words){
		$multi = array();
		for($i = 0; $i < sizeof($proper); $i++)
		{
			$word = "";
			for($j = 0; $j < sizeof($words); $j++)
			{
				if($proper[$i] == $words[$j])
				{
					$word = $proper[$i];
					//look before proper noun
					$start = "";
					for($k = $j-1; $k >= 0; $k--)
					{
						//if conjuction can be used at start we add it
						if(in_array(mb_strtolower($words[$k]), $this->conjunctions["start"]))
						{
							if($start != "")
							{
								$word = $words[$k]." ".$start." ".$word;
								$start = "";
							}
							else
							{
								$word = $words[$k]." ".$word;
							}
						}
						//if conjunction can be used in the middle, we wait and see if it will be surrounded
						else if(in_array(mb_strtolower($words[$k]), $this->conjunctions["middle"]))
						{
							$start = $words[$k]." ".$start;
						}
						//if it's another proper noun - add it
						else if(in_array($words[$k], $proper))
						{
							if($start != "")
							{
								$word = $words[$k]." ".$start." ".$word;
								$start = "";
							}
							else
							{
								$word = $words[$k]." ".$word;
							}
						}
						else
						{
							break;
						}
					}
					$end = "";
					//look after proper noun
					for($k = $j+1; $k < sizeof($words); $k++)
					{
						//if conjunction can be used in the middle, we wait and see if it will be surrounded
						if(in_array(mb_strtolower($words[$k]), $this->conjunctions["middle"]))
						{
							$end = $words[$k]." ".$end;
						}
						//if it's another proper noun - add it
						else if(in_array($words[$k], $proper))
						{
							if($end != "")
							{
								$word = $word." ".$end." ".$words[$k];
								$end = "";
							}
							else
							{
								$word = $word." ".$words[$k];
							}
						}
						else
						{
							break;
						}
					}
					if(!in_array($word, $multi))
					{
						$multi[] = $word;
					}
				}
			}
		}
		return $multi;
	}
	
	/******************************
	* Main function to get what you need
	******************************/
	public function get($text){
		$words = $this->clean_text($text);
		$proper = $this->get_candidates($words, $text);
		if(!empty($this->stop_words))
		{
			$new = array();
			foreach($proper as $val)
			{
				if(!in_array(mb_strtolower($val), $this->stop_words))
				{
					$new[] = $val;
				}
			}
			$proper = $new;
		}
		if($this->multi_words)
		{
			$proper = $this->get_multi_words($proper, $words);
		}
		return $proper;
	}
}
?>