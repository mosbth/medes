<?php
/**
 * Template helpers
 *
 * Extend the current theme's functions by creating your own functions, without creating a new 
 * theme.
 * Enable this, and more files, using the configuration object.
 *
 * @package MedesCore
 */

/**
 * Preprocess hook for page requests. Its called by $pp before handing over to the template 
 * file. This is the place to do the last modifications of $pp, such as adding js or css.
 * Name the function as: 'hook_preprocess_' . str_replace('/', '_', $this->req->GetQueryPartOfUrl()
 * See the example below.
 */
/*
function hook_preprocess_cpage_view_home() {
  global $pp;
  echo $pp->req->GetQueryPartOfUrl();
}
*/