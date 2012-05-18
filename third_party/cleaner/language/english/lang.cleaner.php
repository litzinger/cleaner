<?php

$lang = array(

"cleaner_xss_clean" => "Apply XSS Filter to all POST data?",
"cleaner_html_clean" => "Strip HTML tags?<p style=\"font-weight: normal;\">Applying the XSS filter does not remove basic HTML tags such as &lt;b&gt; or &lt;p&gt;. Turn this on if you want to remove all HTML from the POST data.</p>",
"cleaner_allow_tags_in_post" => "Allow the following HTML tags<p style=\"font-weight: normal;\">If <i>Stript HTML tags</i> above is set to 'Yes' it will remove everything. You can optionally allow specific tags in a comma delimited list. If you allow specific tags, the POST data will be run through HTMLPurifier.</p><p style=\"font-weight: normal;\">For example: b, i, caption</p>",

"cleaner_allow_attr_in_template" => "Allow the following HTML attributes in your template<p style=\"font-weight: normal;\">Define which specific attributes you want to allow when using {exp:cleaner:clean}{/exp:cleaner:clean} around your content.</p><p style=\"font-weight: normal;\">For example: href, src, class</p>",
"cleaner_allow_tags_in_template" => "Allow the following HTML tags in your template<p style=\"font-weight: normal;\">Define which specific tags you want to allow when using {exp:cleaner:clean}{/exp:cleaner:clean} around your content, one tag per line.</p><p style=\"font-weight: normal;\">For example: b, i, caption</p>",

"cleaner_enable_wyvern" => "Apply the following filters to all Wyvern fields?<p style=\"font-weight: normal;\">This will only work if you are using <a href=\"http://boldminded.com/add-ons/wyvern/\">Wyvern</a></p>",
"cleaner_allow_attr_in_wyvern" => "Allow the following HTML attributes when you save data in a Wyvern field<p style=\"font-weight: normal;\">Define which specific attributes you want to allow Wyvern fields to save, one attribute per line.</p><p style=\"font-weight: normal;\">For example: href, src, class</p>",
"cleaner_allow_tags_in_wyvern" => "Allow the following HTML tags when you save data in a Wyvern field<p style=\"font-weight: normal;\">Define which specific tags you want to allow Wyvern fields to save, one tag per line.</p><p style=\"font-weight: normal;\">For example: b, i, caption</p>",

// IGNORE
''=>'');