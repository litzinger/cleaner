<?php

$lang = array(

"cleaner_xss_clean" => "Apply XSS Filter to all POST data?<p style=\"font-weight: normal;\">This does not apply to POST data in the control panel.</p>",
"cleaner_html_clean" => "Strip HTML tags?<p style=\"font-weight: normal;\">Applying the XSS filter does not remove basic HTML tags such as &lt;b&gt; or &lt;p&gt;. Turn this on if you want to remove all HTML from the POST data.</p>",
"cleaner_allow_tags" => "Allow the following HTML tags<p style=\"font-weight: normal;\">If <i>Strip HTML tags</i> above is set to 'Yes' it will remove everything. You can optionally allow specific tags, one tag per line. If you allow specific tags, the POST data will be run through HTMLPurifier.</p><p style=\"font-weight: normal;\">For example:<br />b<br />i<br />caption</p>",

// IGNORE
''=>'');