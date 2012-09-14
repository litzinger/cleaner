<?php

require PATH_THIRD.'cleaner/config.php';

/**
 * ExpressionEngine Extension Class
 *
 * @package     ExpressionEngine
 * @subpackage  Plugins
 * @category    Cleaner
 * @author      Brian Litzinger
 * @copyright   Copyright 2010 - Brian Litzinger
 * @link        http://boldminded.com/add-ons/cleaner
 */

$plugin_info = array(
    'pi_name'           => CLEANER_NAME,
    'pi_version'        => CLEANER_VERSION,
    'pi_author'         => CLEANER_AUTHOR,
    'pi_author_url'     => CLEANER_DOCS_URL,
    'pi_description'    => CLEANER_DESC,
    'pi_usage'          => Cleaner::usage()
);

class Cleaner {

    var $return_data;   
    var $purifier; 
    var $EE;

    function Cleaner($allowed_attr = false, $allowed_tags = false)
    {
        $this->EE =& get_instance();
        
        if(!isset($this->EE->session->cache['cleaner']['plugin']))
        {
            $settings = $this->EE->db->select('settings')
                                     ->where('class', 'Cleaner_ext')
                                     ->limit(1)
                                     ->get('extensions')
                                     ->row('settings');
                                     
            $settings = unserialize($settings);
            
            $this->EE->session->cache['cleaner']['plugin']['tags'] = $settings['cleaner_allow_tags_in_template'];
            $this->EE->session->cache['cleaner']['plugin']['attr'] = $settings['cleaner_allow_attr_in_template'];
        }
        
        // replace this with the path to the HTML Purifier library
        require_once 'htmlpurifier/HTMLPurifier.standalone.php';

        $config = HTMLPurifier_Config::createDefault();
        
        $allowed_attr = $allowed_attr ? $allowed_attr : $this->EE->session->cache['cleaner']['plugin']['attr'];
        $allowed_tags = $allowed_tags ? $allowed_tags : $this->EE->session->cache['cleaner']['plugin']['tags'];

        $config->set('Core.Encoding', 'UTF-8'); // replace with your encoding
        $config->set('HTML.Doctype', 'XHTML 1.1'); // replace with your doctype
        $config->set('HTML.TidyLevel', 'heavy'); // burn baby burn!
        $config->set('HTML.AllowedAttributes', $allowed_attr); // strip all html attributes, mostly for style and class
        $config->set('HTML.AllowedElements', $allowed_tags);
        $config->set('AutoFormat.RemoveEmpty', true); // remove empty tag pairs
        $config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true); // remove empty, even if it contains an &nbsp;
        $config->set('AutoFormat.AutoParagraph', true); // remove empty tag pairs

        $cache_path = APPPATH . 'cache/htmlpurifier';

        // Make sure our cache folder exists.
        if ( ! is_dir($cache_path))
        {
            if(mkdir($cache_path, DIR_WRITE_MODE, TRUE))
            {
                $config->set('Cache.SerializerPath', $cache_path);
            }
        }
        else
        {
            $config->set('Cache.SerializerPath', $cache_path);
        }

        $this->purifier = new HTMLPurifier($config);
    }
    
    function clean($str = '')
    {
        if ($str == '')
        {
            $str = $this->EE->TMPL->tagdata;
        }

        try 
        {
            $str = $this->purifier->purify($str);
        } 
        catch (Exception $e)
        {
            if(REQ == 'CP')
            {
                show_error($e);
            }
            else
            {
                return $this->EE->output->show_user_error('general', array($e));
            }
        }

        // Remove any <br /> tags between <p> tags. Don't know how WP allowed this...
        $str = preg_replace('#</p>\s*<br />\s*<p#mi', '</p></p', $str);
        $str = str_replace('<br/><br/>', '', $str);

        // If there are any instances of double br tags, blow them up and wrap the strings in <p></p> tags instead
        // HTMLPurifier's AutoParagraph didn't seem to work how I wanted it to....
        $paragraphs = explode("<br /><br />", $str);
        
        if(count($paragraphs) > 1)
        {
            for ($i = 0; $i < count($paragraphs); $i++)
            {
                if($paragraphs[$i] != "" AND $paragraphs != "&nbsp;")
                {
                    $paragraphs[$i] = '<p>' . $paragraphs[$i] . '</p>';
                }
            }
        
            $str = implode ('', $paragraphs);
        }
        
        $this->return_data = $str;

        // Optionally update the database with the cleaned data. 
        // After this is done you can basically remove this plugin entirely.
        $update_field = $this->EE->TMPL->fetch_param('update_field');
        $entry_id = $this->EE->TMPL->fetch_param('entry_id');

        if ($update_field AND $entry_id)
        {
            $this->EE->db->where('entry_id', $entry_id)
                         ->update('channel_data', array($update_field => $str));
        }
        
        return $this->return_data;
    }
    // END
    
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.
//  Make sure and use output buffering

function usage()
{
ob_start(); 
?>
Clean really bad HTML text.

{exp:cleaner:clean}
    {blog_body}
{/exp:cleaner:clean}
   
<?php
$buffer = ob_get_contents();
    
ob_end_clean(); 

return $buffer;
}
// END
}
// END CLASS
?>