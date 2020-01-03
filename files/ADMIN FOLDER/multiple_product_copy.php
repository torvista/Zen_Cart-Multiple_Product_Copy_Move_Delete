<?php
/**
 * @package admin
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

require('includes/application_top.php');

///////////////////////////////////////////////////////
//temporary debugging code
/**steve for phpStorm inspections
 * @var messageStack $messageStack
 * @var zcObserverLogEventListener $zco_notifier
 * @var products $zc_products
 */
$debug_mpc = false;
if ($debug_mpc) {//steve debug
    ob_start();
    if (!function_exists('mv_printVar')) {
        function mv_printVar($a)
        {
            $backtrace = debug_backtrace()[0];
            $fh = fopen($backtrace['file'], 'rb');
            $line = 0;
            while (++$line <= $backtrace['line']) {
                $code = fgets($fh);
            }
            fclose($fh);
            preg_match('/' . __FUNCTION__ . '\s*\((.*)\)\s*;/u', $code, $name);
            echo '<pre><strong>' . trim($name[1]) . ":</strong>\n";
            //var_export($a);
            print_r($a);
            echo '</pre><br>';
        }
    }
    mv_printVar($_POST);//steve debug
    $output = ob_get_clean();//steve debug
    $messageStack->add($output, 'info');//steve debug
}
///////////////////////////////////////////////////////////


require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$action = !empty($_GET['action']) ? $_GET['action'] : 'new'; // initial page load: set default action to "new" to show search form

$copy_options = ['link', 'duplicate', 'move']; // allowed Copy/Move options
$delete_options = ['delete_specials', 'delete_linked', 'delete_all']; // allowed Delete options

$copy_as = !empty($_POST['copy_as']) ? $_POST['copy_as'] : $_POST['copy_as'] = 'link'; // initial page load: set default function/radio button to Copy Linked (safest)
if (in_array($copy_as, $delete_options, true)) {
    $delete_option = true;
} elseif (in_array($copy_as, $copy_options, true)) {
    $delete_option = false;
    $target_category_id = '';
} else {
    $messageStack->add_session(ERROR_ILLEGAL_OPTION, 'error');
    zen_redirect(zen_href_link(FILENAME_MULTIPLE_PRODUCT_COPY));
}

//Copy Duplicate products only: set default/initial page load as Yes
$copy_attributes = isset($_POST['copy_attributes']) && $_POST['copy_attributes'] === 'copy_attributes_no' ? 'copy_attributes_no' : 'copy_attributes_yes';// name passed to copy_product_confirm.php
$copy_metatags = isset($_POST['copy_metatags']) && $_POST['copy_metatags'] === 'copy_metatags_no' ? 'copy_metatags_no' : 'copy_metatags_yes'; // name passed to copy_product_confirm.php
$copy_linked_categories = isset($_POST['copy_linked_categories']) && $_POST['copy_linked_categories'] === 'copy_linked_categories_no' ? 'copy_linked_categories_no' : 'copy_linked_categories_yes'; // name passed to copy_product_confirm.php
$copy_discounts = isset($_POST['copy_discounts']) && $_POST['copy_discounts'] === 'copy_discounts_no' ? 'copy_discounts_no' : 'copy_discounts_yes'; // name passed to copy_product_confirm.php
$copy_specials = isset($_POST['copy_specials']) && $_POST['copy_specials'] === 'copy_specials_no' ? 'copy_specials_no' : 'copy_specials_yes';
$copy_featured = isset($_POST['copy_featured']) && $_POST['copy_featured'] === 'copy_featured_no' ? 'copy_featured_no' : 'copy_featured_yes';

$inc_subcats = isset($_POST['inc_subcats']) && $_POST['inc_subcats'] === '1' ? 1 : 0; // for Delete only

if (!$delete_option && isset($_POST['target_category_id']) && $_POST['target_category_id'] !== '' && zen_get_categories_status((int)$_POST['target_category_id']) !== '') { // not used for Delete.
    $target_category_id = (int)$_POST['target_category_id'];
} else {
    $target_category_id = '';
}

if (isset($_POST['search_category_id'])) {
    if ($_POST['search_category_id'] === '0') { // '0' is "Any Category"
        $search_category_id = 0;
    } elseif (zen_get_categories_status((int)$_POST['search_category_id']) !== '') { // check for a valid category
        $search_category_id = (int)$_POST['search_category_id'];
    } else {
        $search_category_id = ''; // "Please Select"
    }
} else {
    $search_category_id = ''; // "Please Select"
}

$keywords = (isset($_POST['keywords']) ? zen_db_prepare_input($_POST['keywords']) : '');
$search_all = isset($_POST['search_all']) && $_POST['search_all'] === '1'; // search filter in name, model or manufacturers only (name) or also descriptions (all)
$manufacturer_id = isset($_POST['manufacturer_id']) ? (int)$_POST['manufacturer_id'] : 0; // '0' is Any Manufacturer, so an invalid string is set to 0
$min_price = isset($_POST['min_price']) && is_numeric($_POST['min_price']) ? zen_db_prepare_input($_POST['min_price']) : '';
$max_price = isset($_POST['max_price']) && is_numeric($_POST['max_price']) ? zen_db_prepare_input($_POST['max_price']) : '';
$product_quantity = isset($_POST['product_quantity']) && is_numeric($_POST['product_quantity']) ? $_POST['product_quantity'] : '';
$autocheck = isset($_POST['autocheck']) && $_POST['autocheck'] === '1' ? 1 : 0; // only displayed when javascript not enabled
$show_images = isset($_POST['show_images']) && $_POST['show_images'] === '0' ? 0 : 1;

$results_order_by_options = ['id', 'manufacturer', 'model', 'name', 'price', 'quantity', 'status'];
$results_order_by = isset($_POST['results_order_by']) && in_array($_POST['results_order_by'], $results_order_by_options, true) ? $_POST['results_order_by'] : 'model';
$results_order_by_array = [];
foreach ($results_order_by_options as $key) {
    $results_order_by_array[] = [
        'id' => $key,
        'text' => constant('TEXT_ORDER_BY_' . strtoupper($key))
    ];
}
//validation of search parameters
$error_message = '';
if ($action === 'find' || $action === 'confirm') { // validate form values from Preview and Confirm
    switch (true) {
        case (!$delete_option && $target_category_id === '')://no target set
            $error_message = ERROR_NO_TARGET_CATEGORY;
            break;
        case (($copy_as === 'link' || $copy_as === 'move') && ($target_category_id === $search_category_id)): // Copy-link & Move only: search and target categories cannot be the same.
            $error_message = sprintf(ERROR_SAME_CATEGORIES, $target_category_id, zen_get_category_name($target_category_id, $_SESSION['languages_id']));
            break;
        case (!$delete_option && zen_childs_in_category_count($target_category_id) !== 0): // Copy/Move only: target has subcategories.
            $error_message = sprintf(ERROR_TARGET_CATEGORY_HAS_SUBCATEGORY, $target_category_id, zen_get_category_name($target_category_id, $_SESSION['languages_id']));
            break;
        case ($copy_as !== 'delete_specials' && $search_category_id === 0 && $manufacturer_id === 0 && $keywords === '' && $min_price === '' && $max_price === '' && $product_quantity === ''):  // "Any Category" selected, so another search term is required
            $error_message = ERROR_SEARCH_CRITERIA_REQUIRED;
            break;
        case (zen_not_null($keywords) && !zen_parse_search_string($search_keywords, $keywords)):
            $error_message = ERROR_INVALID_KEYWORDS;
            break;
        case ($search_category_id !== 0 && zen_products_in_category_count($search_category_id, true, $inc_subcats) < 1): // no products found for Copy/Move/Delete
            $error_message = sprintf(ERROR_NO_PRODUCTS_IN_CATEGORY, $search_category_id, zen_get_category_name($search_category_id, $_SESSION['languages_id'])) . ($inc_subcats ? ERROR_OR_SUBS : '.');
            break;
    }

    if ($error_message !== '') {
        $messageStack->add($error_message);
        $action = 'new';
    } elseif (!$delete_option) { // build a list of the products already in the target category, not for Delete. This is used in both 'find' and 'confirm'.
        $check = $db->Execute("SELECT products_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE categories_id = " . $target_category_id);
        $products_in_target_category = [];
        foreach ($check as $row) {
            $products_in_target_category[] = (int)$row['products_id'];
        }
    }

    if ($action === 'confirm' && $error_message === '') { // perform additional validations prior to actual Copy/Move/Delete
        $cnt = (int)$_POST['product_count']; // total of products as found by search / as listed on Preview (find) page

        $found_string = explode(',', $_POST['items_found']); // make array of product ids as found by the search/displayed on Preview page 2
        $found = array_map(static function ($value) { // make array of integers
            return (int)$value;
        }, $found_string);
        $products_selected = array_map(static function ($value) { // make array (integers) of product IDs as selected on Preview page 2
            return (int)$value;
        }, $_POST['product']);

        // for delete with subcats, need to know in which category was the selected linked product
        $categories_selected = array_map(static function ($value) { // make array (integers) of category IDs of products as selected on Preview page 2. For Delete One
            return (int)$value;
        }, $_POST['category']);
        if ($debug_mpc) {//steve
            mv_printVar($products_selected);
            mv_printVar($categories_selected);
        }

        switch (true) {
            case ($cnt !== count($found)): // should never happen!
                $error_message = ERROR_ARRAY_COUNTS;
                break;
            case (count($products_selected) === 0): // no checkboxes selected
                $error_message = ERROR_NO_SELECTION;
                break;
            case (!is_array($products_selected)):  //array of checkboxes is not an array
                $error_message = ERROR_CHECKBOXES_NOT_ARRAY;
                break;
            case (is_array($products_selected)):
                foreach ($products_selected as $item) {
                    if (!in_array($item, $found, true)) { // a selected checkbox value references a product id that is not in the found array
                        $error_message = sprintf(ERROR_CHECKBOX_ID, $item);
                        break 2;
                    }
                }
                break;
        }
        if ($error_message !== '') {
            $messageStack->add($error_message);
            $action = 'find';
        }
    }
}

switch ($action) {
    case 'find':
        /* $search_sql = "SELECT p.products_id, p.manufacturers_id, p.master_categories_id, p.products_image, p.products_model, p.products_price_sorter, p.products_quantity, p.products_status, pd.products_name, pd.products_description, m.manufacturers_name, ptoc.categories_id FROM " . TABLE_PRODUCTS . " p
             LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " ptoc
             WHERE p.products_id = pd.products_id
             AND p.products_id = ptoc.products_id
             AND pd.language_id =  " . (int)$_SESSION['languages_id'];
 */
        $search_sql = "SELECT p.products_id, p.manufacturers_id, p.master_categories_id, p.products_image, p.products_model, p.products_price_sorter, p.products_quantity, p.products_status, pd.products_name, pd.products_description, m.manufacturers_name, ptoc.categories_id, sp.specials_id
FROM " . TABLE_PRODUCTS . " p 
            LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id 
            LEFT JOIN " . TABLE_SPECIALS . " sp ON p.products_id = sp.products_id, " .
            TABLE_PRODUCTS_DESCRIPTION . " pd, " .
            TABLE_PRODUCTS_TO_CATEGORIES . " ptoc
            WHERE p.products_id = pd.products_id 
            AND p.products_id = ptoc.products_id 
            AND pd.language_id =  " . (int)$_SESSION['languages_id'];

        if ($copy_as === 'delete_specials') {
            $search_sql .= ' AND p.products_id = sp.products_id';
            if (!($search_category_id > 0)) { // restrict results or includes linked products
                $search_sql .= ' AND ptoc.categories_id = p.master_categories_id';
            }
        }
        if (($copy_as === 'link' || $copy_as === 'move') && count($products_in_target_category) > 0) {
            $search_sql .= ' AND (NOT (p.products_id in (' . implode(',', $products_in_target_category) . ')))';
        }
        if ($manufacturer_id > 0) { // 0=all
            $search_sql .= ' AND p.manufacturers_id = ' . $manufacturer_id;
        }
        if ($search_category_id > 0) { // 0=all
            if ($inc_subcats) { // Delete only
                $subcats_array = zen_get_category_tree($search_category_id, '', '0');
                foreach ($subcats_array as $key => $value) {
                    $subcats .= ',' . $value['id'];
                }
                $search_sql .= ' AND (ptoc.categories_id in (' . $search_category_id . $subcats . '))';
            } else {
                $search_sql .= ' AND ptoc.categories_id = ' . $search_category_id;
            }
        }
        if (is_numeric($min_price)) {
            $search_sql .= ' AND p.products_price_sorter >= "' . zen_db_input($min_price) . '"';
        }
        if (is_numeric($max_price)) {
            $search_sql .= ' AND p.products_price_sorter <= "' . zen_db_input($max_price) . '"';
        }
        if (is_numeric($product_quantity)) {
            $search_sql .= ' AND p.products_quantity <= "' . zen_db_input($product_quantity) . '"';
        }

        $where_str = '';
        if (isset($search_keywords) && (count($search_keywords) > 0)) {
            $where_str .= " AND (";
            for ($i = 0, $n = count($search_keywords); $i < $n; $i++) {
                switch ($search_keywords[$i]) {
                    case '(':
                    case ')':
                    case 'and':
                    case 'or':
                        $where_str .= " " . $search_keywords[$i] . " ";
                        break;
                    default:
                        $keyword = zen_db_prepare_input($search_keywords[$i]);
                        $where_str .= "(pd.products_name LIKE '%" . zen_db_input($keyword) . "%' OR p.products_model LIKE '%" . zen_db_input($keyword) . "%' OR m.manufacturers_name LIKE '%" . zen_db_input($keyword) . "%'";
                        if ($search_all === 'all') {
                            $where_str .= " OR pd.products_description LIKE '%" . zen_db_input($keyword) . "%'";
                        }
                        $where_str .= ')';
                        break;
                }
            }
            $where_str .= " )";
        }

        switch ($results_order_by) {
            case ('id'):
                $order_by_str = ' ORDER BY p.products_id';
                break;
            case ('manufacturer'):
                $order_by_str = ' ORDER BY m.manufacturers_name';
                break;
            case ('model'):
                $order_by_str = ' ORDER BY p.products_model';
                break;
            case ('name'):
                $order_by_str = ' ORDER BY pd.products_name';
                break;
            case ('price'):
                $order_by_str = ' ORDER BY p.products_price_sorter';
                break;
            case ('quantity'):
                $order_by_str = ' ORDER BY p.products_quantity';
                break;
            case ('status'):
                $order_by_str = ' ORDER BY p.products_status';
                break;
        }
        $search_sql .= $where_str . $order_by_str; // ORDER BY pd.products_name
        $search_results = $db->Execute($search_sql);
        if ($debug_mpc) {//steve debug
            $messageStack->add($search_sql, 'info');
        }
        if ($search_results->EOF) {
            $action = 'new';
            $messageStack->add(TEXT_NO_MATCHING_PRODUCTS_FOUND, 'info');
        }
        break;

    case 'confirm':
        $products_modified = [];
        foreach ($products_selected as $key => $id) { //$id is an integer

            $found_product = $db->Execute("SELECT p.products_id, p.products_model, p.master_categories_id, p.products_price_sorter, p.products_quantity,  pd.products_name,  m.manufacturers_name FROM " . TABLE_PRODUCTS . " p 
                    LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
                    WHERE p.products_id = pd.products_id 
                    AND pd.language_id =  " . (int)$_SESSION['languages_id'] . ' 
                    AND p.products_id = ' . $id . ' LIMIT 1');

            if ($found_product->RecordCount() === 1) {

// bof: copy-link
                if ($copy_as === 'link') {
                    $products_modified[] = [
                        'id' => (int)$found_product->fields['products_id'],
                        'model' => $found_product->fields['products_model'],
                        'name' => $found_product->fields['products_name'],
                        'category' => $target_category_id,
                        'master_category' => $found_product->fields['master_categories_id'],
                        'quantity' => $found_product->fields['products_quantity'],
                        'price' => zen_get_products_display_price($found_product->fields['products_id']),
                        'manufacturer' => $found_product->fields['manufacturers_name']
                    ];

                    $data_array = [
                        'products_id' => $id,
                        'categories_id' => $target_category_id
                    ];
                    zen_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, $data_array);
                }
// eof copy/link

// bof: copy-duplicate
                if ($copy_as === 'duplicate') { //if product found
                    $action = 'multiple_product_copy_return'; // used in copy_product_confirm.php (core modification required) to bypass default redirect and so allow multiple copy
                    $_POST['products_id'] = $id; // for copy_product_confirm
                    $_POST['categories_id'] = $target_category_id; // for copy_product_confirm
                    $product_type = zen_get_products_type($id); // for copy_product_confirm
                    // new product creation is handled by the following module, creating $dup_products_id (copy_attributes, copy_metatags, copy_linked_categories, copy_discounts also handled here)
                    if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/copy_product_confirm.php')) {
                        require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/copy_product_confirm.php');
                    } else {
                        require(DIR_WS_MODULES . 'copy_product_confirm.php');
                    }
                    //get confirmation messages
                    if (isset($_SESSION['messageToStack']) && is_array($_SESSION['messageToStack'])) {
                        foreach ($_SESSION['messageToStack'] as $row) {
                            $messageStack->add($row['text'], $row['type']);
                        }
                        $_SESSION['messageToStack'] = '';
                    }

                    $dup_products_id = !empty($dup_products_id) ? $dup_products_id : 0; // $dup_products_id is the new product id created by the previous module, is integer. This check added to satisfy IDE
                    if ($dup_products_id > 0) {
                        if ($copy_specials === 'copy_specials_yes') {
                            $chk_specials = $db->Execute("SELECT * FROM " . TABLE_SPECIALS . " WHERE products_id= " . (int)$id);
                            foreach ($chk_specials as $row) {
                                $db->Execute("INSERT INTO " . TABLE_SPECIALS . " 
                                        (products_id, specials_new_products_price, specials_date_added, expires_date, status, specials_date_available) VALUES 
                                        (" . $dup_products_id . ", '" . zen_db_input($row['specials_new_products_price']) . "', now(), '" . zen_db_input($row['expires_date']) . "', '1', '" . zen_db_input($row['specials_date_available']) . "')");
                                $messageStack->add(sprintf(TEXT_COPY_AS_DUPLICATE_SPECIALS, $id, $dup_products_id), 'success');
                            }
                        }

                        if ($copy_featured === 'copy_featured_yes') {
                            $chk_featured = $db->Execute("SELECT * FROM " . TABLE_FEATURED . " WHERE products_id= " . (int)$id);
                            foreach ($chk_featured as $row) {
                                $db->Execute("INSERT INTO " . TABLE_FEATURED . " 
                                        (products_id, featured_date_added, expires_date, status, featured_date_available) VALUES 
                                        (" . $dup_products_id . ", now(), '" . zen_db_input($row['expires_date']) . "', '1', '" . zen_db_input($row['featured_date_available']) . "')");

                                $messageStack->add(sprintf(TEXT_COPY_AS_DUPLICATE_FEATURED, $id, $dup_products_id), 'success');
                            }
                        }

                        // reset products_price_sorter for searches etc.
                        zen_update_products_price_sorter($id);

                        $products_modified[] = [
                            'id' => $dup_products_id,
                            'model' => zen_get_products_model($dup_products_id),
                            'name' => zen_get_products_name($dup_products_id),
                            'category' => $target_category_id,
                            'master_category' => $target_category_id,
                            'quantity' => zen_products_lookup($dup_products_id, 'products_quantity'),
                            'price' => zen_get_products_display_price($dup_products_id),
                            'manufacturer' => zen_get_products_manufacturers_name($dup_products_id)
                        ];
                    } else {
                        $messageStack->add(ERROR_COPY_DUPLICATE_NO_DUP_ID, $_POST['products_id'], $_POST['categories_id']);
                        $action = 'find';
                    }
                }
// eof: copy/duplicate

// bof: move from one category to another
                if ($copy_as === 'move') { //if product found
                    $action = 'multiple_product_copy_return'; // used in move_product_confirm.php (core modification required) to bypass default redirect and so allow multiple moves
                    $_POST['products_id'] = $id; // for move_product_confirm
                    $_POST['move_to_category_id'] = $target_category_id;// for move_product_confirm
                    if ($search_category_id === 0) { // 0: search all categories: use the product's master category id as the search/source category
                        $current_category_id = $found_product['master_categories_id'];// for move_product_confirm
                    } else { // a search category is set: the products therein may be linked or master
                        $current_category_id = $search_category_id;// for move_product_confirm
                    }
                    $product_type = zen_get_products_type($id);// for move_product_confirm
                    if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/move_product_confirm.php')) {
                        require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/move_product_confirm.php');
                    } else {
                        require(DIR_WS_MODULES . 'move_product_confirm.php');
                    }
                    //get confirmation messages for display on this page
                    if (isset($_SESSION['messageToStack']) && is_array($_SESSION['messageToStack'])) {
                        foreach ($_SESSION['messageToStack'] as $row) {
                            $messageStack->add($row['text'], $row['type']);
                        }
                        $_SESSION['messageToStack'] = '';
                    }
                    $products_modified[] = [
                        'id' => (int)$found_product->fields['products_id'],
                        'model' => $found_product->fields['products_model'],
                        'name' => $found_product->fields['products_name'],
                        'category' => $target_category_id,
                        'master_category' => $target_category_id,
                        'quantity' => $found_product->fields['products_quantity'],
                        'price' => zen_get_products_display_price($found_product->fields['products_id']),
                        'manufacturer' => $found_product->fields['manufacturers_name']
                    ];
                }

// eof: move from one category to another

// bof: delete specials
                if ($copy_as === 'delete_specials') {
                    $db->Execute("DELETE FROM " . TABLE_SPECIALS . " WHERE products_id = " . $id);
                    $products_modified[] = [
                        'id' => (int)$found_product->fields['products_id'],
                        'model' => $found_product->fields['products_model'],
                        'name' => $found_product->fields['products_name'],
                        'category' => $found_product->fields['master_categories_id'],
                        'master_category' => $found_product->fields['master_categories_id'],
                        'quantity' => $found_product->fields['products_quantity'],
                        'price' => zen_get_products_display_price($found_product->fields['products_id']),
                        'manufacturer' => $found_product->fields['manufacturers_name']
                    ];
                }
// bof: delete specials

// bof: delete from one category. Linked products only
                if ($_POST['copy_as'] === 'delete_linked') {
                    $delete_sql = "DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = " . $id . " AND categories_id = " . $categories_selected[$key];
                    $db->Execute($delete_sql);

                    // check for master_categories_id and reset
                    $products_modified[] = [
                        'id' => (int)$found_product->fields['products_id'],
                        'model' => $found_product->fields['products_model'],
                        'name' => $found_product->fields['products_name'],
                        'category' => $categories_selected[$key],
                        'master_category' => $found_product->fields['master_categories_id'],
                        'quantity' => $found_product->fields['products_quantity'],
                        'price' => zen_get_products_display_price($found_product->fields['products_id']),
                        'manufacturer' => $found_product->fields['manufacturers_name']
                    ];
                }
// eof: delete from one category. Linked products only

// bof: delete from all categories
                if ($_POST['copy_as'] === 'delete_all') { //if product found
                    $action = 'multiple_product_copy_return';
                    $_POST['products_id'] = $id; // for delete_product_confirm
                    $delete_linked = 'true';
                    $product_type = zen_get_products_type($id); // for delete_product_confirm

                    $chk_categories = $db->Execute("SELECT products_id, categories_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = " . $id);
                    $product_categories = [];
                    foreach ($chk_categories as $chk_category) {
                        $product_categories[] = $chk_category['categories_id'];
                    }
                    $_POST['product_categories'] = $product_categories;
                    if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/delete_product_confirm.php')) {
                        require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/delete_product_confirm.php');
                    } else {
                        require(DIR_WS_MODULES . 'delete_product_confirm.php');
                    }

                    $products_modified[] = [
                        'id' => (int)$found_product->fields['products_id'],
                        'model' => $found_product->fields['products_model'],
                        'name' => $found_product->fields['products_name'],
                        'category' => $product_categories,
                        'master_category' => $found_product->fields['master_categories_id'],
                        'quantity' => $found_product->fields['products_quantity'],
                        'price' => zen_get_products_display_price($found_product->fields['products_id']),
                        'manufacturer' => $found_product->fields['manufacturers_name']
                    ];
                }
// eof: delete from all categories
            }
        }
        break;
}
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
<head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <link rel="stylesheet" type="text/css" media="print" href="includes/stylesheet_print.css">
    <link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <script src="includes/menu.js"></script>
    <script src="includes/general.js"></script>
    <script>
        function init() {
            cssjsmenu('navbar');
            if (document.getElementById) {
                var kill = document.getElementById('hoverJS');
                kill.disabled = true;
            }
        }
    </script>
    <style>
        .dataTableHeadingContent {
            vertical-align: top !important; /* for results table headings aligned above toggle checkbox. !important required to override .less */
        }

        #tableMPCduplicateOptions {
            margin-bottom: 10px;
        }

        #tableMPCduplicateOptions th, #tableMPCduplicateOptions td {
            padding: 2px 5px;
        }
    </style>
</head>
<body onload="init()">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php
require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof //-->
<!-- body //-->
<div class="container-fluid">
    <!-- body_text //-->
    <h1><?php echo HEADING_TITLE; ?></h1>
    <?php if ($action === 'new') { // initial page load $action is pre-set to "new"
        $target_categories = zen_get_category_tree('0', '', '', [['id' => '', 'text' => PLEASE_SELECT]], '', true); ?>
        <div>
            <?php
            echo zen_draw_form('find_products', FILENAME_MULTIPLE_PRODUCT_COPY, 'action=find'); ?>
            <div>
                <fieldset>
                    <legend><?php echo TEXT_COPY_AS_LINK; ?></legend>
                    <div class="row">
                        <div class="col-sm-12">
                            <label><?php echo zen_draw_radio_field('copy_as', 'link', ($copy_as === 'link')) . ' ' . TEXT_COPY_AS_LINK; ?></label>
                        </div>
                    </div>
                </fieldset>
                <?php echo zen_draw_separator('pixel_black.gif', '75%', '1'); ?><br>
                <fieldset>
                    <legend><?php echo TEXT_COPY_AS_DUPLICATE; ?></legend>
                    <div class="row">
                        <div class="col-sm-6">
                            <label><?php echo zen_draw_radio_field('copy_as', 'duplicate', ($copy_as === 'duplicate')) . ' ' . TEXT_COPY_AS_DUPLICATE; ?></label>
                        </div>

                        <div class="col-sm-6">
                            <table>
                                <tr>
                                    <td><?php echo TEXT_COPY_ATTRIBUTES; ?></td>
                                    <td>
                                        <label><?php echo zen_draw_radio_field('copy_attributes', '$copy_attributes_yes', ($copy_attributes === 'copy_attributes_yes')) . ' ' . TEXT_YES; ?></label>
                                        <label><?php echo zen_draw_radio_field('copy_attributes', '$copy_attributes_no', ($copy_attributes === 'copy_attributes_no')) . ' ' . TEXT_NO; ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo TEXT_COPY_METATAGS; ?></td>
                                    <td>
                                        <label><?php echo zen_draw_radio_field('copy_metatags', 'copy_metatags_yes', ($copy_metatags === 'copy_metatags_yes')) . ' ' . TEXT_YES; ?></label>
                                        <label><?php echo zen_draw_radio_field('copy_metatags', 'copy_metatags_no', ($copy_metatags === 'copy_metatags_no')) . ' ' . TEXT_NO; ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo TEXT_COPY_LINKED_CATEGORIES; ?></td>
                                    <td>
                                        <label><?php echo zen_draw_radio_field('copy_linked_categories', 'copy_linked_categories_yes', ($copy_linked_categories === 'copy_linked_categories_yes')) . ' ' . TEXT_YES; ?></label>
                                        <label><?php echo zen_draw_radio_field('copy_linked_categories', 'copy_linked_categories_no', ($copy_linked_categories === 'copy_linked_categories_no')) . ' ' . TEXT_NO; ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo TEXT_COPY_DISCOUNTS; ?></td>
                                    <td>
                                        <label><?php echo zen_draw_radio_field('copy_discounts', 'copy_discounts_yes', ($copy_discounts === 'copy_discounts_yes')) . ' ' . TEXT_YES; ?></label>
                                        <label><?php echo zen_draw_radio_field('copy_discounts', 'copy_discounts_no', ($copy_discounts === 'copy_discounts_no')) . ' ' . TEXT_NO; ?></label>
                                    </td>
                                </tr>
                                <?php //the following three are not handled by copy_product_confirm ?>
                                <tr>
                                    <td><?php echo TEXT_COPY_FEATURED; ?></td>
                                    <td>
                                        <label><?php echo zen_draw_radio_field('copy_featured', 'copy_featured_yes', ($copy_featured === 'copy_featured_yes')) . ' ' . TEXT_YES; ?></label>
                                        <label><?php echo zen_draw_radio_field('copy_featured', 'copy_featured_no', ($copy_featured === 'copy_featured_no')) . ' ' . TEXT_NO; ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo TEXT_COPY_SPECIALS; ?></td>
                                    <td>
                                        <label><?php echo zen_draw_radio_field('copy_specials', 'copy_specials_yes', ($copy_specials === 'copy_specials_yes')) . ' ' . TEXT_YES; ?></label>
                                        <label><?php echo zen_draw_radio_field('copy_specials', 'copy_specials_no', ($copy_specials === 'copy_specials_no')) . ' ' . TEXT_NO; ?></label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </fieldset>
                <?php echo zen_draw_separator('pixel_black.gif', '75%', '1'); ?><br>
                <fieldset>
                    <legend><?php echo TEXT_MOVE_TO; ?></legend>
                    <div class="row">
                        <div class="col-sm-6">
                            <label><?php echo zen_draw_radio_field('copy_as', 'move', ($copy_as === 'move')) . ' ' . TEXT_MOVE_TO; ?></label>
                        </div>
                        <div class="col-sm-6">
                            <?php echo TEXT_MOVE_PRODUCTS_INFO_SEARCH_CATEGORY . TEXT_MOVE_PRODUCTS_INFO_SEARCH_GLOBAL; ?>
                        </div>
                    </div>
                </fieldset>
                <?php echo zen_draw_separator('pixel_black.gif', '75%', '1'); ?><br>
                <fieldset>
                    <h2><label for="target_category"><?php echo TEXT_TARGET_CATEGORY; ?>
                            <?php echo zen_draw_pull_down_menu('target_category_id', $target_categories, $target_category_id, 'id="target_category"'); ?></label></h2>
                </fieldset>
                <?php echo zen_draw_separator('pixel_black.gif', '100%', '2'); ?><br>
                <fieldset>
                    <legend><?php echo IMAGE_DELETE; ?></legend>
                    <div class="row">
                        <div class="col-sm-12">
                            <label><?php echo zen_draw_radio_field('copy_as', 'delete_specials', ($copy_as === 'delete_specials')) . ' ' . TEXT_COPY_AS_DELETE_SPECIALS; ?></label><br>
                            <label><?php echo zen_draw_radio_field('copy_as', 'delete_linked', ($copy_as === 'delete_linked')) . ' ' . TEXT_COPY_AS_DELETE_LINKED; ?></label><br>
                            <label><?php echo zen_draw_radio_field('copy_as', 'delete_all', ($copy_as === 'delete_all')) . ' ' . TEXT_COPY_AS_DELETE_ALL; ?></label>
                        </div>
                    </div>
                </fieldset>
                <?php echo zen_draw_separator('pixel_black.gif', '100%', '3'); ?><br>
                <fieldset>
                    <legend><?php echo TEXT_ENTER_CRITERIA; ?></legend>
                    <div>
                        <label for="searchCategory"><?php echo TEXT_PRODUCTS_CATEGORY; ?></label>
                        <?php echo zen_draw_pull_down_menu('search_category_id', zen_get_category_tree('0', '', '', [['id' => '0', 'text' => TEXT_ALL_CATEGORIES]], '', true), $search_category_id, 'id="searchCategory"'); ?>
                        <label><?php echo zen_draw_checkbox_field('inc_subcats', '1', $inc_subcats) . TEXT_INCLUDE_SUBCATS; ?></label>
                    </div>
                    <div>
                        <label for="searchKeywords"><?php echo TEXT_ENTER_SEARCH_KEYWORDS; ?></label>
                        <?php echo zen_draw_input_field('keywords', $keywords, 'size="50" id="searchKeywords"'); ?>
                        <label><?php echo TEXT_SEARCH_DESCRIPTIONS . zen_draw_checkbox_field('search_all', 'all', $search_all); ?></label>
                    </div>
                    <div>
                        <?php $manufacturers_array = [['id' => '0', 'text' => TEXT_ALL_MANUFACTURERS]];
                        $manufacturers_query = $db->Execute("SELECT manufacturers_id, manufacturers_name FROM " . TABLE_MANUFACTURERS . " ORDER BY manufacturers_name");
                        foreach ($manufacturers_query as $manufacturer) {
                            $manufacturers_array[] = [
                                'id' => $manufacturer['manufacturers_id'],
                                'text' => $manufacturer['manufacturers_name']
                            ];
                        } ?>
                        <label for="searchManufacturer"><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></label>
                        <?php echo zen_draw_pull_down_menu('manufacturer_id', $manufacturers_array, $manufacturer_id, 'id="searchManufacturer"'); ?>
                    </div>
                    <div>
                        <label><?php echo ENTRY_MIN_PRICE . zen_draw_input_field('min_price', $min_price, 'step="0.01"', '', 'number'); ?></label><br>
                        <label><?php echo ENTRY_MAX_PRICE . zen_draw_input_field('max_price', $max_price, 'step="0.01"', '', 'number'); ?></label><br>
                        <label><?php echo ENTRY_MAX_PRODUCT_QUANTITY . zen_draw_input_field('product_quantity', $product_quantity, 'min="0" step="1"', '', 'number'); ?></label>
                    </div>
                    <div>
                        <p><?php echo ENTRY_SHOW_IMAGES; ?>
                            <label><?php echo zen_draw_radio_field('show_images', '1', $show_images) . '&nbsp;' . TEXT_YES; ?></label>
                            <label><?php echo zen_draw_radio_field('show_images', '0', !$show_images) . '&nbsp;' . TEXT_NO; ?></label></p>
                        <p><label for="resultsOrderBy"><?php echo ENTRY_RESULTS_ORDER_BY; ?></label>
                            <?php echo zen_draw_pull_down_menu('results_order_by', $results_order_by_array, $results_order_by, 'id="resultsOrderBy"'); ?></p>
                        <noscript>
                            <p><?php echo ENTRY_AUTO_CHECK; ?>
                                <label><?php echo zen_draw_radio_field('autocheck', '1', $autocheck) . '&nbsp;' . TEXT_YES; ?></label>
                                <label><?php echo zen_draw_radio_field('autocheck', '0', !$autocheck) . '&nbsp;' . TEXT_NO; ?></label></p>
                        </noscript>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo IMAGE_PREVIEW; ?></button>
                </fieldset>
            </div>
            <?php echo "</form>\n"; ?>
            <br>
            <?php echo zen_draw_separator('pixel_black.gif', '100%', '1'); ?><br>
            <div><?php echo TEXT_TIPS; ?></div>
        </div>
    <?php } elseif ($action === 'find' || $action === 'confirm' || $action === 'multiple_product_copy_return') { // anything except "new" ?>
        <div>
            <?php // bof capture Search Criteria parameters for reuse
            ob_start(); ?>
            <div>
                <p><?php echo sprintf(TEXT_SEARCH_RESULT_CATEGORY, ($search_category_id === 0 ? TEXT_ALL_CATEGORIES : '"' . zen_output_generated_category_path($search_category_id) . '" <span class="bg-danger">ID#' . $search_category_id) . '</span>'); ?></p>
                <?php if ($action === 'find' && $delete_option && $inc_subcats && zen_childs_in_category_count($search_category_id) > 0) { // search_result not available for action == confirm ?>
                    <p><?php echo TEXT_INCLUDED_SUBCATS; ?></p>
                    <?php
                    $included_categories = [];
                    foreach ($search_results as $search_result) {
                        $included_categories[] = $search_result['categories_id'];
                    }
                    $included_categories = array_unique($included_categories);
                    $included_categories_names = [];
                    foreach ($included_categories as $value) {
                        $included_categories_names[zen_output_generated_category_path($value)] = $value;
                    }
                    ksort($included_categories_names);
                    echo '<ul>';
                    foreach ($included_categories_names as $key => $value) {
                        echo "<li>$key ID#$value</li>";
                    }
                    echo '</ul>';
                }
                if ($keywords !== '') { ?>
                    <p><?php echo sprintf(TEXT_SEARCH_RESULT_KEYWORDS, $keywords); ?></p>
                <?php }
                if ($manufacturer_id !== '') { ?>
                    <p><?php echo sprintf(TEXT_SEARCH_RESULT_MANUFACTURER, ($manufacturer_id === 0 ? TEXT_ALL_MANUFACTURERS : '"' . zen_get_manufacturers_name($manufacturer_id) . '"')); ?></p>
                <?php }
                if ($min_price !== '') { ?>
                    <p><?php echo sprintf(TEXT_SEARCH_RESULT_MIN_PRICE, $min_price); ?></p>
                <?php }
                if ($max_price !== '') { ?>
                    <p><?php echo sprintf(TEXT_SEARCH_RESULT_MAX_PRICE, $max_price); ?></p>
                <?php }
                if ($product_quantity !== '') { ?>
                    <p><?php echo sprintf(TEXT_SEARCH_RESULT_QUANTITY, $product_quantity); ?></p>
                <?php } ?>
            </div>
            <?php $search_criteria = ob_get_clean();
            // eof capture Search Criteria parameters for reuse
            switch ($copy_as) {
                case ('link'): ?>
                    <h2><?php echo TEXT_COPY_AS_LINK; ?></h2>
                    <h3><?php echo sprintf(TEXT_SEARCH_RESULT_TARGET, $target_category_id, zen_output_generated_category_path($target_category_id)); ?></h3>
                    <?php
                    echo $search_criteria;
                    if ($action === 'confirm' || $action === 'multiple_product_copy_return') { ?>
                        <h4><?php echo sprintf(TEXT_PRODUCTS_COPIED_TO, count($products_modified), $target_category_id, zen_output_generated_category_path($target_category_id)); ?></h4>
                    <?php }
                    break;

                case ('duplicate'): ?>
                    <div>
                        <h2><?php echo TEXT_COPY_AS_DUPLICATE; ?></h2>
                        <h3><?php echo sprintf(TEXT_SEARCH_RESULT_TARGET, $target_category_id, zen_output_generated_category_path($target_category_id)); ?></h3>
                        <?php echo $search_criteria; ?>
                        <table class="table-bordered" id="tableMPCduplicateOptions">
                            <tr>
                                <td><?php echo TEXT_COPY_ATTRIBUTES; ?></td>
                                <td><?php echo($copy_attributes === 'copy_attributes_yes' ? TEXT_YES : TEXT_NO); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo TEXT_COPY_METATAGS; ?></td>
                                <td><?php echo($copy_metatags === 'copy_metatags_yes' ? TEXT_YES : TEXT_NO); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo TEXT_COPY_LINKED_CATEGORIES; ?></td>
                                <td><?php echo($copy_linked_categories === 'copy_linked_categories_yes' ? TEXT_YES : TEXT_NO); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo TEXT_COPY_DISCOUNTS; ?></td>
                                <td><?php echo($copy_discounts === 'copy_discounts_yes' ? TEXT_YES : TEXT_NO); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo TEXT_COPY_SPECIALS; ?></td>
                                <td><?php echo($copy_specials === 'copy_specials_yes' ? TEXT_YES : TEXT_NO); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo TEXT_COPY_FEATURED; ?></td>
                                <td><?php echo($copy_featured === 'copy_featured_yes' ? TEXT_YES : TEXT_NO); ?></td>
                            </tr>
                        </table>
                    </div>
                    <?php
                    if ($action === 'confirm' || $action === 'multiple_product_copy_return') { ?>
                        <h4><?php echo sprintf(TEXT_PRODUCTS_COPIED_TO, count($products_modified), $target_category_id, zen_output_generated_category_path($target_category_id)); ?></h4>
                    <?php }
                    break;

                case ('move'): ?>
                    <h2><?php echo TEXT_MOVE_TO; ?></h2>
                    <h3><?php echo sprintf(TEXT_SEARCH_RESULT_TARGET, $target_category_id, zen_output_generated_category_path($target_category_id)); ?></h3>
                    <?php echo $search_criteria;
                    echo($search_category_id > 0 ? TEXT_MOVE_PRODUCTS_INFO_SEARCH_CATEGORY : TEXT_MOVE_PRODUCTS_INFO_SEARCH_GLOBAL);
                    if ($action === 'confirm' || $action === 'multiple_product_copy_return') { ?>
                        <h4><?php echo sprintf(TEXT_PRODUCTS_COPIED_TO, count($products_modified), $target_category_id, zen_output_generated_category_path($target_category_id)); ?></h4>
                    <?php }
                    break;

                case ('delete_specials'): ?>
                    <h2><?php echo TEXT_COPY_AS_DELETE_SPECIALS; ?></h2>
                    <?php echo $search_criteria;
                    if ($action === 'confirm') { ?>
                        <h4><?php echo sprintf(TEXT_SPECIALS_DELETED_FROM, count($products_modified)); ?></h4>
                    <?php }
                    break;

                case ('delete_linked'): ?>
                    <h2><?php echo TEXT_COPY_AS_DELETE_LINKED; ?></h2>
                    <?php echo $search_criteria;
                    if ($action === 'confirm') { ?>
                        <h4><?php echo sprintf(TEXT_PRODUCTS_DELETED, count($products_modified)); ?></h4>
                    <?php }
                    break;

                case ('delete_all'): ?>
                    <h2><?php echo TEXT_COPY_AS_DELETE_ALL; ?></h2>
                    <h2><?php echo TEXT_COPY_AS_DELETE_ALL_INFO; ?></h2>
                    <?php echo $search_criteria;
                    if ($action === 'confirm') { ?>
                        <h4><?php echo sprintf(TEXT_PRODUCTS_DELETED, count($products_modified)); ?></h4>
                    <?php }
                    break;

                default:
                    break;
            } ?>
        </div>
    <?php }
    if ($action === 'find') { //Preview, page 2 ?>
        <div>
            <?php echo zen_draw_form('select_products', FILENAME_MULTIPLE_PRODUCT_COPY, 'action=confirm');
            /* Re-Post all POST'ed variables */
            $key = '';//keep phpstorm EA inspection happy
            foreach ($_POST as $key => $value) {
                if (!is_array($_POST[$key])) {
                    echo zen_draw_hidden_field($key, htmlspecialchars(stripslashes($value), ENT_COMPAT, CHARSET));
                }
            }
            $total_products_found = count($search_results);
            if ($total_products_found > 0) { ?>
                <p><?php echo sprintf(TEXT_PRODUCTS_FOUND, $total_products_found); ?>
                    <?php if (!$delete_option) { // not for Delete ?>
                        <?php echo ' ' . TEXT_EXISTING_PRODUCTS_NOT_SHOWN; ?>
                    <?php } ?></p>
                <table class="table table-striped">
                    <thead>
                    <tr class="dataTableHeadingRow">
                        <th class="dataTableHeadingContent text-center"><?php echo TABLE_HEADING_SELECT; ?>
                            <span id="toggleCheckbox"></span><?php // placeholder for toggle checkbox: no content when javascript disabled ?>
                            <script title="toggle all checkboxes">
                                document.getElementById('toggleCheckbox').innerHTML = '<br><label style="font-weight:normal"><input type="checkbox" onClick="toggle(this)" /><?php echo TEXT_TOGGLE_ALL; ?></label>';

                                function toggle(source) {
                                    let checkboxes = document.getElementsByClassName('checkboxMPC');
                                    for (let i = 0, n = checkboxes.length; i < n; i++) {
                                        checkboxes[i].checked = source.checked;
                                    }
                                }
                            </script>
                        </th>
                        <th class="dataTableHeadingContent text-center"><?php echo TABLE_HEADING_PRODUCTS_ID; ?></th>
                        <?php if ($show_images) { ?>
                            <th class="dataTableHeadingContent text-center"><?php echo TABLE_HEADING_IMAGE; ?></th>
                        <?php } ?>
                        <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODEL; ?></th>
                        <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_NAME; ?></th>
                        <th class="dataTableHeadingContent text-center"><?php echo TABLE_HEADING_CATEGORY; ?></th>
                        <th class="dataTableHeadingContent text-center"><?php echo sprintf(TABLE_HEADING_LINKED_MASTER, zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_LINKED), zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_MASTER)); ?></th>
                        <th class="dataTableHeadingContent text-center"><?php echo TABLE_HEADING_STATUS; ?></th>
                        <th class="dataTableHeadingContent text-right"><?php echo TABLE_HEADING_PRICE; ?></th>
                        <th class="dataTableHeadingContent text-center"><?php echo TABLE_HEADING_QUANTITY; ?></th>
                        <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_MFG; ?></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    $items_found = []; // make array of product ids to pass to Confirm, page 3
                    $cnt = 0; // for checkbox name index
                    foreach ($search_results as $search_result) { // list all matching products
                        $display_product = true;
                        if ($display_product === true) {
                            $items_found[] = (int)$search_result['products_id'];

                            if ($show_images) {
                                if ($search_result['products_image'] === '') {
                                    $product_image = zen_image(DIR_WS_CATALOG_IMAGES . PRODUCTS_IMAGE_NO_IMAGE, $search_result['products_name'], SMALL_IMAGE_WIDTH,
                                        SMALL_IMAGE_HEIGHT);
                                } else {
                                    $product_image = zen_image(DIR_WS_CATALOG_IMAGES . $search_result['products_image'], $search_result['products_name'],
                                        SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
                                }
                            } ?>
                            <tr class="dataTableRow">
                                <td class="dataTableContent text-center">
                                    <?php
                                    if ($copy_as === 'delete_linked' && $search_result['master_categories_id'] === $search_result['categories_id']) { // Do not allow deletion of product from it's master category
                                        echo zen_image(DIR_WS_IMAGES . 'icon_red_off.gif', '', '', '', 'title ="' . IMAGE_ICON_MASTER . '"');
                                    } else {
                                        echo zen_draw_checkbox_field('product[' . $cnt . ']', $search_result['products_id'], $autocheck, '', 'id="product[' . $cnt . ']" class="checkboxMPC"');
                                        echo zen_draw_hidden_field('category[' . $cnt . ']', $search_result['categories_id']);
                                    }
                                    $cnt++;
                                    ?>
                                </td>
                                <td class="dataTableContent text-center"><?php echo $search_result['products_id']; ?></td>
                                <?php if ($show_images) { ?>
                                    <td class="dataTableContent text-center"><?php echo $product_image; ?></td>
                                <?php } ?>
                                <td class="dataTableContent"><?php echo $search_result['products_model']; ?></td>
                                <td class="dataTableContent"><?php echo $search_result['products_name']; ?></td>
                                <td class="dataTableContent text-center"><?php // this category ?>
                                    <a title="<?php echo IMAGE_ICON_LINKED_EDIT_LINKS; ?>"
                                       href="<?php echo zen_href_link(FILENAME_PRODUCTS_TO_CATEGORIES, 'products_filter=' . $search_result['products_id']); ?>">
                                        <?php echo zen_output_generated_category_path($search_result['categories_id']) . '<br>ID#' . $search_result['categories_id']; ?>
                                    </a>
                                </td>
                                <td class="dataTableContent text-center"><?php // product master/linked ?>
                                    <a href="<?php echo zen_href_link(FILENAME_PRODUCTS_TO_CATEGORIES, 'products_filter=' . $search_result['products_id']); ?>">
                                        <?php echo($search_result['master_categories_id'] === $search_result['categories_id'] ?
                                            zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_EDIT_LINKS) :
                                            zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_EDIT_LINKS)); ?>
                                    </a>
                                </td>
                                <td class="dataTableContent text-center"><?php // product status ?>
                                    <a href="<?php echo zen_href_link(FILENAME_PRODUCT, 'cPath=' . zen_get_product_path($search_result['products_id']) . '&amp;product_type=1&amp;pID=' . $search_result['products_id'] . '&amp;action=new_product'); ?>">
                                        <?php echo($search_result['products_status'] === '1' ?
                                            zen_image(DIR_WS_IMAGES . 'icon_green_on.gif', IMAGE_ICON_STATUS_ON_EDIT_PRODUCT) :
                                            zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_STATUS_OFF_EDIT_PRODUCT)); ?></a>
                                </td>
                                <?php
                                $price_info = ($search_result['specials_id'] > 0) ?
                                    '<a title="' . TEXT_PRODUCT_SPECIAL_EDIT . '" href="' . zen_href_link(FILENAME_SPECIALS, 'sID=' . $search_result['specials_id'] . '&action=edit') . '">' . zen_get_products_display_price($search_result['products_id']) . '</a>' :
                                    zen_get_products_display_price($search_result['products_id']);
                                ?>
                                <td class="dataTableContent text-right"><?php echo $price_info; ?></a></td>
                                <td class="dataTableContent text-center"><?php echo $search_result['products_quantity']; ?></td>
                                <td class="dataTableContent"><?php echo $search_result['manufacturers_name']; ?></td>
                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>
                <?php
                echo zen_draw_hidden_field('items_found', implode(',', $items_found));
                echo zen_draw_hidden_field('product_count', $cnt); ?>
                <button type="submit" class="btn btn-danger"><?php echo IMAGE_CONFIRM; ?></button>
                <?php echo "</form>\n";
            } else { // no matching products were found ?>
                <h4><?php echo TEXT_NO_MATCHING_PRODUCTS_FOUND; ?></h4>
            <?php }
            echo zen_draw_form('retry', FILENAME_MULTIPLE_PRODUCT_COPY, 'action=new');
            /* Re-Post all POST'ed variables */
            //$key = '';//keep phpstorm EA inspection happy
            foreach ($_POST as $key => $value) {
                if (!is_array($_POST[$key])) {
                    echo zen_draw_hidden_field($key, htmlspecialchars(stripslashes($value), ENT_COMPAT, CHARSET));
                }
            }
            ?>
            <button type="submit" class="btn btn-primary"><?php echo BUTTON_RETRY; ?></button>
            <a href="<?php echo zen_href_link(FILENAME_CATEGORY_PRODUCT_LISTING, 'cPath=' . $target_category_id) ?>" class="btn btn-default" role="button"><?php echo BUTTON_CATEGORY_LISTING_TARGET; ?></a>
            <?php if ($search_category_id > 0) { // only show if a Search Category was specified ?>
                <a href="<?php echo zen_href_link(FILENAME_CATEGORY_PRODUCT_LISTING, 'cPath=' . $search_category_id) ?>" class="btn btn-default" role="button"><?php echo BUTTON_CATEGORY_LISTING_SEARCH; ?></a>
            <?php } ?>
            <?php echo "</form>\n"; ?>
        </div>

        <?php
    } elseif ($action === 'confirm' || $action === 'multiple_product_copy_return') { //results

        // steve not needed if ($copy_as === 'link' || $copy_as === 'duplicate' || $copy_as === 'move' || $copy_as === 'delete_specials') { ?>
        <div>
            <table class="table">
                <thead>
                <tr class="dataTableHeadingRow">
                    <th class="dataTableHeadingContent text-center"><?php echo TABLE_HEADING_PRODUCTS_ID; ?></th>
                    <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODEL; ?></th>
                    <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_NAME; ?></th>
                    <th class="dataTableHeadingContent text-center"><?php echo TABLE_HEADING_CATEGORY; ?></th>
                    <th class="dataTableHeadingContent text-center"><?php echo TABLE_HEADING_MASTER_CATEGORY; ?></th>
                    <th class="dataTableHeadingContent text-right"><?php echo TABLE_HEADING_PRICE; ?></th>
                    <th class="dataTableHeadingContent text-center"><?php echo TABLE_HEADING_QUANTITY; ?></th>
                    <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_MFG; ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products_modified as $product_modified) { ?>
                    <tr class="dataTableRow">
                        <td class="dataTableContent text-center"><?php echo $product_modified['id']; ?></td>
                        <td class="dataTableContent"><?php echo $product_modified['model']; ?></td>
                        <td class="dataTableContent"><?php echo $product_modified['name']; ?></td>
                        <td class="dataTableContent text-center"><?php
                            if (is_array($product_modified['category'])) { //only with delete all
                                echo implode(", ", $product_modified['category']);
                            } else {
                                echo zen_output_generated_category_path((int)$product_modified['category']) . '<br>ID#' . $product_modified['category'];
                            } ?></td>
                        <td class="dataTableContent text-center"><?php echo zen_output_generated_category_path((int)$product_modified['master_category']) . '<br>ID#' . $product_modified['master_category']; ?></td>
                        <td class="dataTableContent text-right"><?php echo $product_modified['price']; ?></td>
                        <td class="dataTableContent text-center"><?php echo $product_modified['quantity']; ?></td>
                        <td class="dataTableContent"><?php echo $product_modified['manufacturer']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php //steve not needed } ?>
        <div>
            <?php echo zen_draw_form('multi_product_copy', FILENAME_MULTIPLE_PRODUCT_COPY); ?>
            <button type="submit" class="btn btn-primary"><?php echo BUTTON_NEW_SEARCH; ?></button>
            <?php echo "</form>\n"; ?>

            <a href="<?php echo zen_href_link(FILENAME_CATEGORY_PRODUCT_LISTING, 'cPath=' . $target_category_id) ?>" class="btn btn-default" role="button"><?php echo BUTTON_CATEGORY_LISTING_TARGET; ?></a>
            <?php if ($search_category_id > 0) { // only show if a Search Category was specified ?>
                <a href="<?php echo zen_href_link(FILENAME_CATEGORY_PRODUCT_LISTING, 'cPath=' . $search_category_id) ?>" class="btn btn-default" role="button"><?php echo BUTTON_CATEGORY_LISTING_SEARCH; ?></a>
            <?php } ?>

        </div>
    <?php } //end of results ?>
    <!-- body_text_eof //-->
</div>
<!-- body_eof //-->
<!-- footer //-->
<div class="footer-area">
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
