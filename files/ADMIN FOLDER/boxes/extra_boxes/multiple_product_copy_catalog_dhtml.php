<?php
/**
 * @package admin
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
   $Id: multi_product_copy.php ver 1.11 by Kevin L. Shelton 2010-10-15
   $Id: multi_product_copy.php ver 1.391 by Linda McGrath 2011-11-10
*/
/**
 * Category Box Menu
*/
/////////////////////////////////////////
// NOT USED IN Zen Cart v1.5 - see multi_product_copy.sql
/////////////////////////////////////////

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
  $za_contents[] = array('text' => BOX_CATALOG_MUTLI_COPY, 'link' => zen_href_link(FILENAME_MULTI_COPY, '', 'NONSSL'));
