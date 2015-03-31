<?php

// This is a PLUGIN TEMPLATE.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Uncomment and edit this line to override:
$plugin['name'] = 'wcz_utf8_url';

$plugin['version'] = '0.1.7';
$plugin['author'] = 'whocarez';
$plugin['author_uri'] = '';
$plugin['description'] = 'Automatic UTF-8 permlinks';
$plugin['type'] = 5; // 0 for regular plugin; 1 if it includes admin-side code; 5 for public- and admin-side code with ajax
$plugin['order'] = 5; # use 5 as a default; ranges from 1 to 9


@include_once('zem_tpl.php');

if (0) {
?>
# --- BEGIN PLUGIN HELP ---

Just install and activate.
Adjust Textpattern->Advanced Options->“Maximum URL length (in characters)” to your needs.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

/*
    wcz_utf8_url - Makes UTF-8 permlinks for SEO

    Written by whocarez with help of the Textpattern Community

    Version history:
    0.1.7		  Minor fix for right-to-left languages
    0.1.6		  Minor fix autodetect charset in mb_strlower
    0.1.5		  Minor fix for mb_strlower and charset
    0.1.4		  Minor fix of preserving already existing dashes/minuses
    0.1.3		- Added remove small words
    0.1.2		- Minor fixes with double dashes and trimming the url string
    0.1.1		- Minor fixes
    0.1.0		- initial release

*/

register_callback('wcz_utf8_url','sanitize_for_url');
function wcz_utf8_url($event,$step,$text) {

// replace slash and backslash before deleting unneeded signs, you may consider to add some more replacings e.g. € with Euro or евро
    $text = str_replace(array("1+1","$","€","%","/","\\"),array("1plus1"," Dollar"," Euro"," Prozent"," "," "),$text);
// Remove all unneeded symbols ...
    $text = preg_replace("/[\p{P}\p{No}\p{Nl}\p{M}\p{C}\p{S}]/u","-",$text);
// Collapse spaces, minuses, (back-)slashes and non-words
    $text = preg_replace('/[\s\-\/\\\\]+/', '-', $text);
// Trim url string
    $text = trim($text,"-");
// Remove small words
    $text = preg_replace("/(^|-)[\p{Ll}\p{Lu}\p{Lt}\p{Lo}]{1,2}(?=-|$)/u","", $text);

#$text = trim(preg_replace("/(^|-)(([\p{Ll}\p{Lu}\p{Lt}\p{Lo}]{1,3})(?<!new|wer|wen|wie|was|wo|wem|how|who|zug|uni|job|gps|bus|tod|tot|eko|öko|eu|dai|gai|hiv|df|ing|ua|upa|oun|omv|otp|ss|umc|twi|tvi|usa|uno|bio|see|kuh|fuß|not|kot|tür|sex|uhu|rat|dvd|cd|tau|rot|tor|tat|bit|sau|ehe|gut|mfg|ard|zdf|rtl|mdr|tee|uhr|zoo|zeh|rss|xml|pdf|axt|fan|nuß|neu|fkk|aal|bug|ost|alt|rom|ddr|fdj|sed|kgb|fbi|cia|sbu|ohr|age|ece|bip|mts|gus|ntn|cme|ntn|iwf|wto|scm|man|uah|eon|nbu|obi|tv|isd|ilo|akw|who|ooo|stb|gas|em))(?=-|$)/ui","", $text),"-");
// Remove all non-whitelisted characters
    $text = preg_replace("/[^\p{Ll}\p{Lu}\p{Lt}\p{Lo}\p{Nd}\-_]/u","",$text);
    $text = trim(mb_strtolower($text,mb_detect_encoding($text)),'-');
    return $text;
}

function update_urls() {
$rs = safe_rows('Title','textpattern','1=1');
foreach($rs as $a)
safe_update('textpattern',"url_title='".sanitizeForUrl($a['Title'])."'","Title='".doSlash($a['Title'])."'");

}


# --- END PLUGIN CODE ---

?>
