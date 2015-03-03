<?php
if (!defined('APP_VER')) {
    exit('No direct script access allowed');
}
// define the old-style EE object
if (!function_exists('ee')) {
    function ee()
    {
        static $EE;
        if (! $EE) {
          $EE = get_instance();
        }
        return $EE;
    }
}


class Wufoo_shortcode_ext {

  public $settings = array();
  public $name = 'Wufoo_shortcode';
  public $version = '0.1';
  public $description = 'Shortcodes for embedding Wufoo forms.';
  public $settings_exist = 'n';
  public $docs_url = '';

  private $param_defaults = array(
    'username' => null,
    'formhash' => null,
    'autoresize' => 'true',
    'height' => 1086,
    'header' => 'show',
    'ssl' => 'true');

  private $params_required = array(
    'username', 'formhash');

  /**
   * Constructor
   *
   * @param mixed Settings array or empty string if none exist.
   */
  public function __construct($settings = array()) {
    $this->settings = $settings;
  }

  /**
   * Activate Extension
   *
   * This function enters the extension into the exp_extensions table
   *
   * @see http://codeigniter.com/user_guide/database/index.html for more information on the db class.
   *
   * @return void
   */
  public function activate_extension() {
    $hooks = array(
      'template_post_parse' => 'template_post_parse'
    );
    foreach($hooks as $hook => $method) {
      $data = array(
        'class' => __CLASS__,
        'method' => $method,
        'hook' => $hook,
        'priority' => 10,
        'version' => $this->version,
        'enabled' => 'y',
        'settings' => ''
      );
      ee()->db->insert('exp_extensions', $data);
    }
    return true;
  }

  /**
   * Update Extension
   *
   * This function performs any necessary db updates when the extension page is visited.
   *
   * @return mixed void on update / false if none
   */
  public function update_extension($current = '') {
    if($current == '' || $current == $this->version)
      return FALSE;

    ee()->db->where('class', _CLASS__);
    ee()->db->update(
      'extensions',
      array('version' => $this->version)
    );
  }

  /**
   * Disable Extension
   *
   * This method removes information from the exp_extensions table
   *
   * @return void
   */
  public function disable_extension() {
    ee()->db->where('class', __CLASS__);
    ee()->db->delete('extensions');
  }

  /* Hook the template post parsing */
  public function template_post_parse($final_template, $is_partial, $site_id) {
    // play nice with others
    if (isset(ee()->extensions->last_call) && ee()->extensions->last_call) {
     $final_template = ee()->extensions->last_call;
    }

    // don't run if we're in a partial
    if ($is_partial !== false) {
      return $final_template;
    }

    // find all the shortcodes
    preg_match_all("/\[wufoo +(.*) *\]/", $final_template, $shortcode_matches);
    for ($i = 0; $i < count($shortcode_matches[0]); $i++) {
      $shortcode = $shortcode_matches[0][$i];
      $paramstr = $shortcode_matches[1][$i];
      
      // parse the parameters
      preg_match_all("/([a-z]+) *= *[\'\"]([a-z0-9]+)[\'\"]/", $paramstr, $param_matches);
      $params = array();
      for ($j = 0; $j < count($param_matches[0]); $j++) {
        $params[$param_matches[1][$j]] = $param_matches[2][$j];
      }

      // load in the rest as needed
      foreach ($this->param_defaults as $k => $v) {
        if (!isset($params[$k])) {
          $params[$k] = $v;
        }
      }

      // check for required settings
      $shortcode_err = false;
      foreach ($this->params_required as $r) {
        if (!isset($params[$r])) {
          $shortcode_err = true;
          break;
        }
      }

      if ($shortcode_err) {
        $snippet = 'Missing required param(s).';
      }
      else {
        // load the wufoo snippet and sub in params
        ob_start();
        include('wufoo_snippet.php');
        $snippet = ob_get_clean();
      }
        
      $final_template = str_replace($shortcode, $snippet, $final_template);
    }

    return $final_template;
  }


}
?>