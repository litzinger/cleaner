<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'cleaner/config.php';

/**
 * ExpressionEngine Extension Class
 *
 * @package     ExpressionEngine
 * @subpackage  Extensions
 * @category    Cleaner
 * @author      Brian Litzinger
 * @copyright   Copyright 2010 - Brian Litzinger
 * @link        http://boldminded.com/add-ons/cleaner
 */
 
class Cleaner_ext {

    var $settings       = array();
    var $name           = CLEANER_NAME;
    var $version        = CLEANER_VERSION;
    var $description    = CLEANER_DESC;
    var $settings_exist = 'y';
    var $docs_url       = CLEANER_DOCS_URL;
    
    /**
     * Constructor
     */
    function Cleaner_ext($settings = '') 
    {
        $this->EE =& get_instance();
        $this->settings = $settings;
    }
    
    function sessions_start($sess)
    {
        // Only run this for front end form posts, not CP forms, otherwise things such as the Template editor
        // will turn all HTML into entities. Very annoying.
        if(REQ != "CP")
        {
            if(isset($this->settings['cleaner_xss_clean']) AND $this->settings['cleaner_xss_clean'] == 'yes')
            {
                foreach($_POST as $key => $val)
                {
                    $_POST[$key] = $this->EE->security->xss_clean($val, FALSE);
                }
            }
        
            if(isset($this->settings['cleaner_html_clean']) AND $this->settings['cleaner_html_clean'] == 'yes')
            {
                foreach($_POST as $key => $val)
                {
                    $_POST[$key] = $this->_html_clean($val);
                }
            }
        }
    }
    
    function settings()
    {
        $settings['cleaner_xss_clean'] = array('s', array('no' => 'No', 'yes' => 'Yes'), 'no');
        $settings['cleaner_html_clean'] = array('s', array('no' => 'No', 'yes' => 'Yes'), 'no');
        $settings['cleaner_allow_tags_in_post'] = '';
        
        $settings['cleaner_allow_attr_in_template'] = 'href, class, src, rel, width, height';
        $settings['cleaner_allow_tags_in_template'] = 'img, h1, h2, h3, h4, h5, blockquote, strong, em, p, b, a, i, ul, li, ol, br';
        
        $settings['cleaner_enable_wyvern'] = array('s', array('no' => 'No', 'yes' => 'Yes'), 'no');
        $settings['cleaner_allow_attr_in_wyvern'] = 'href, class, src, rel, width, height';
        $settings['cleaner_allow_tags_in_wyvern'] = 'img, h1, h2, h3, h4, h5, blockquote, strong, em, p, b, a, i, ul, li, ol, br';
        
        return $settings;
    }
    
    /**
     * Install the extension
     */
    function activate_extension()
    {
        // Delete old hooks
        $this->EE->db->query("DELETE FROM exp_extensions WHERE class = '". __CLASS__ ."'");
        
        // Add new hooks
        $ext_template = array(
            'class'    => __CLASS__,
            'settings' => '',
            'priority' => 1, // Needs to be first
            'version'  => $this->version,
            'enabled'  => 'y'
        );
        
        $extensions = array(
            array('hook'=>'sessions_start', 'method'=>'sessions_start'),
        );
        
        foreach($extensions as $extension)
        {
            $ext = array_merge($ext_template, $extension);
            $this->EE->db->insert('exp_extensions', $ext);
        }       
    }

    /**
     * @param string $current currently installed version
     */
    function update_extension($current = '') {}

    /**
     * Uninstalls extension
     */
    function disable_extension() 
    {
        // Delete records
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->delete('exp_extensions');
    }
    
    private function _html_clean($str)
    {
        if (is_array($str))
        {
            while (list($key) = each($str))
            {
                $str[$key] = $this->_html_clean($str[$key]);
            }

            return $str;
        }
        
        $allowed = (isset($this->settings['cleaner_allow_tags_in_post']) AND $this->settings['cleaner_allow_tags_in_post'] != '') ? explode(',', $this->settings['cleaner_allow_tags_in_post']) : false;
        
        // If no allowed tags are defined, remove them all.
        if(!$allowed)
        {
            return trim(strip_tags($str));
        }
        // Run it through HTML Purifier, and allow specified tags, and clean up everything else
        else
        {
            require_once 'htmlpurifier/HTMLPurifier.standalone.php';

            $config = HTMLPurifier_Config::createDefault();

            $config->set('Core.Encoding', 'UTF-8'); // replace with your encoding
            $config->set('HTML.Doctype', 'XHTML 1.1'); // replace with your doctype
            $config->set('HTML.TidyLevel', 'heavy'); // burn baby burn!
            $config->set('HTML.AllowedElements', $allowed);
            $config->set('AutoFormat.RemoveEmpty', true); // remove empty tag pairs
            $config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true); // remove empty, even if it contains an &nbsp;

            $purifier = new HTMLPurifier($config);
            
            return $purifier->purify($str);
        }
    }
    
    private function debug($str)
    {
        echo '<pre>';
        var_dump($str);
        echo '</pre>';
    }
    
    
}