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
//sample text
$text = "My dear Mr. Bennet, said his lady to him one day,have you heard that Netherfield Park in London is let at last? 
Mr. Bennet replied that he had not.
But it is, returned she for Mrs. Long has just been here, and she told me and Jane all about it.
Mr. Bennet made no answer. His wife cried impatiently. Even the kind Dr. Smith knew better.  Mr. Bennet was so odd a mixture of quick
parts, sarcastic humour, reserve, and caprice, that the experience of three-and-twenty years living in England had been insufficient to
make his wife understand his character. Her mind, like her sister Lizzy's, was less difficult to develop.";

include("./proper_nouns.php");

//create instance
$pn = new proper_nouns();

//get array with proper nouns
$arr = $pn->get($text);

echo "<pre>";
//output text
echo $text."\n";
//print result
print_r($arr);
echo "</pre>";
?>