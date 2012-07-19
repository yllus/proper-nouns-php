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
echo "<table border='0' cellpadding='50' cellspacing='0'><tr><td>";
echo "<form method='post' action=''>";
echo "<p><textarea name='text' rows='30' cols='80'>The Red Planet is about to be spectacular! 

    This month and next, Earth is catching up with Mars in an encounter that 
    will culminate in the closest approach between the two planets in 
    recorded history. The next time Mars may come this close is
    in 2287. Due to the way Jupiter's gravity tugs on 
    Mars and perturbs its orbit, astronomers can only be 
    certain that Mars has not come this close to Earth 
    in the last 5,000 years, but it may be as long as 
    60,000 years before it happens again. 

    The encounter will culminate on August 27th when
    Mars comes to within 34,649,589 miles of Earth and
    will be (next to the moon) the brightest object in 
    the night sky. It will attain a magnitude of -2.9
    and will appear 25.11 arc seconds wide. At a modest
    75-power magnification 

    Mars will look as large as the full moon to the naked eye. 

    Mars will be easy to spot. At the
    beginning of August it will rise in the east at 10p.m. 
    and reach its azimuth at about 3 a.m. 

    By the end of August when the two planets are
    closest, Mars will rise at nightfall and reach its 
    highest point in the sky at 12:30a.m. That's pretty
    convenient to see something that no human being has 
    seen in recorded history. So, mark your calendar at
    the beginning of August to see Mars grow 
    progressively brighter and brighter throughout the 
    month.</textarea></p>";
echo "<p><input type='submit' value='Get proper nouns' /></p>";
echo "</td><td><h3>Proper nouns: </h3>";
if(isset($_POST["text"]))
{
	include("./proper_nouns.php");
	$pn = new proper_nouns();
	echo "<pre>";
	$arr = $pn->get($_POST["text"]);
	print_r($arr);
	echo "</pre>";
}
echo "</td></tr></table>";
?>