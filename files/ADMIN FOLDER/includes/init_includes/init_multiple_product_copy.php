<?php
// -----
// Part of the Multiple Product Copy plugin
//
// @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
//

if (defined('IS_ADMIN_FLAG') && !empty($_SESSION['admin_id']) && !zen_page_key_exists('multipleProductCopy')) {
    zen_register_admin_page('multipleProductCopy', 'BOX_CATALOG_MULTIPLE_PRODUCT_COPY', 'FILENAME_MULTIPLE_PRODUCT_COPY', '', 'catalog', 'Y');
    $messageStack->add('Admin page registered: Catalog -> "Multiple Product Copy"', 'info');
}
