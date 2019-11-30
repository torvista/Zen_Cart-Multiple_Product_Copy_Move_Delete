<?php //steve TO CHANGE SORT ORDER go to line 338
/**
 * @package admin
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 *
 * $Id: multi_product_copy.php ver 1.11 by Kevin L. Shelton 2010-10-15
 * $Id: multi_product_copy.php ver 1.393 by Linda McGrath 2013-01-17
 * $Id: multi_product_copy.php ver 1.394 by torvista 2019
 */
/**PROGRESS

 **/

require('includes/application_top.php');

$show_images = false;//steve added todo

require(DIR_WS_CLASSES . 'currencies.php');

$currencies = new currencies();

function list_subcategories($parent_id)
{
    global $db;

    $list_categories = $db->Execute("SELECT categories_id FROM " . TABLE_CATEGORIES . " WHERE parent_id = " . (int)$parent_id);
    $list = '';
    while (!$list_categories->EOF) {
        $list .= ',' . $list_categories->fields['categories_id'] . list_subcategories($list_categories->fields['categories_id']);
        $list_categories->MoveNext();
    }
    return $list;
}

$action = (!empty($_GET['action']) ? $_GET['action'] : 'new');
$_POST['copy_as'] = !empty($_POST['copy_as']) ? $_POST['copy_as'] : 'link';//steve for php warning and set default action/radio button

$messages = array();
$error = false;
if (($action == 'find') || ($action == 'confirm')) { //validate form
    $autocheck = (isset($_POST['autocheck']) && ($_POST['autocheck'] == 'yes'));
    $keywords = (isset($_POST['keywords']) ? zen_db_prepare_input($_POST['keywords']) : '');
    $within = (isset($_POST['within']) ? $_POST['within'] : 'all');
    if (zen_not_null($keywords)) {
        if (!zen_parse_search_string($keywords, $search_keywords)) {
            $error = true;
            $messages[] = ERROR_INVALID_KEYWORDS;
        }
    }
    $category_id = (isset($_POST['category_id']) ? $_POST['category_id'] : '');
    if (!is_numeric($category_id)) {
        $category_id = '';
    }
    $inc_subcats = (isset($_POST['inc_subcats']) && ($_POST['inc_subcats'] == 'yes'));
    $copy_to = zen_db_prepare_input($_POST['copy_to']);

    $copy_as = $_POST['copy_as'];
    if (!is_numeric($copy_to)) {
        $copy_to = '';
    }
    if ($copy_to == '' && $_POST['copy_as'] != 'deleted' && $_POST['copy_as'] != 'delete_one' && $_POST['copy_as'] != 'delete_specials') {
        $error = true;
        $messages[] = ERROR_NO_LOCATION;
    } elseif ($copy_to == $category_id && $_POST['copy_as'] != 'deleted' && $_POST['copy_as'] != 'delete_specials') {
        $error = true;
        if ($copy_as == 'delete_one') {
            $messages[] = ERROR_SAME_LOCATION_DELETE_ONE;
        } else {
            $messages[] = ERROR_SAME_LOCATION;
        }
    } else {
        if ($_POST['copy_as'] != 'deleted' && $_POST['copy_as'] != 'delete_one' && $_POST['copy_as'] != 'delete_specials') {
            $check = $db->Execute('SELECT * FROM ' . TABLE_CATEGORIES . ' WHERE categories_id = ' . (int)$copy_to);
            if ($check->RecordCount() != 1) {
                $error = true;
                $messages[] = ERROR_NOT_FOUND;
            } else {
                $check = 'SELECT categories_name FROM ' . TABLE_CATEGORIES_DESCRIPTION . ' WHERE categories_id = ' . (int)$copy_to . ' AND language_id = ' . (int)$_SESSION['languages_id'];
                $cat = $db->Execute($check);
                $copy_to_name = $cat->fields['categories_name'];
                if (!zen_not_null($copy_to_name)) {
                    $error = true;
                    $messages[] = ERROR_NAME_NOT_FOUND;
                }
            }
        } // deleted
        if ($_POST['copy_as'] == 'delete_one' && ($category_id == 0 || zen_products_in_category_count($category_id,
                    true, false) < 1)) {
            $messages[] = ERROR_NO_LOCATION_DELETE_ONE;
            $error = true;
        }
    }

    $check = $db->Execute("SELECT products_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE categories_id = " . (int)$copy_to);
    $products_in_copyto = array();
    while (!$check->EOF) { // build list of products in destination category
        $products_in_copyto[] = $check->fields['products_id'];
        $check->MoveNext();
    }
    $manufacturer_id = (isset($_POST['manufacturer_id']) ? $_POST['manufacturer_id'] : '');
    if (!is_numeric($manufacturer_id)) {
        $manufacturer_id = '';
    }
    $min_price = zen_db_prepare_input($_POST['min_price']);
    if (!is_numeric($min_price)) {
        $min_price = '';
    }
    $max_price = zen_db_prepare_input($_POST['max_price']);
    if (!is_numeric($max_price)) {
        $max_price = '';
    }
    $product_quantity = zen_db_prepare_input($_POST['product_quantity']);
    if (!is_numeric($product_quantity)) {
        $product_quantity = '';
    }

    if (($keywords == '') && ($category_id == '') && ($manufacturer_id == '') && ($min_price == '') && ($max_price == '') && ($min_msrp == '') && ($max_msrp == '') && ($product_quantity == '')) {
        $error = true;
        $messages[] = ERROR_ENTRY_REQUIRED;
    }
    if ($action == 'confirm') { // perform additional validations
        $cnt = (int)$_POST['product_count'];
        $found = explode(',', $_POST['items_found']);
        if ($cnt != count($found)) {
            $messages[] = ERROR_UNEXPLAIN;
            $action = 'find';
        }
        $set_items = $_POST['product'];//checkboxes
        if (count($set_items) == 0) {
            $messages[] = ERROR_NOT_SELECTED;
            $action = 'find';
        } elseif (!is_array($set_items)) {
            $messages[] = ERROR_UNEXPLAIN;
            $action = 'find';
        } else {
            foreach ($set_items as $item) {
                if (!in_array($item, $found)) {
                    $messages[] = ERROR_UNEXPLAIN;
                    $action = 'find';
                    break;
                }
            }
        }
    }
    if ($error) {  // if error return to entry form
        $action = 'new';
    }
}
if (zen_not_null($action)) {
    switch ($action) {
        case 'confirm':
            $items_set = array();
            foreach ($set_items as $id) {
                if (!in_array($id, $products_in_copyto)) { // product not already in destination
                    $query = $db->Execute("SELECT * FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd WHERE p.products_id = pd.products_id AND pd.language_id =  " . (int)$_SESSION['languages_id'] . ' AND p.products_id = ' . (int)$id);

// bof: move from one category to another
                    if ($_POST['copy_as'] == 'move_from' && $query->RecordCount() == 1) { //if product found
                        $product_multi = $query;
                        $action = 'multiple_product_copy_return';
                        $_POST['products_id'] = $id;
                        $_POST['move_to_category_id'] = $copy_to;
                        if (zen_childs_in_category_count($_POST['category_id']) > 0 || $_POST['category_id'] == 0) {
                            $current_category_id = $product_multi->fields['master_categories_id'];
                        } else {
                            $current_category_id = $category_id;
                        }

                        $product_type = zen_get_products_type($id);
                        if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/move_product_confirm.php')) {
                            require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/move_product_confirm.php');
                        } else {
                            require(DIR_WS_MODULES . 'move_product_confirm.php');
                        }
                    } // eof link
// bof: move from one category to another


// bof: delete specials
                    if ($_POST['copy_as'] == 'delete_specials' && $query->RecordCount() == 1) { //if product found
                        $db->Execute("DELETE FROM " . TABLE_SPECIALS . " WHERE products_id = " . $id);
                        $messages[] = "Special delete for products_id: " . $id . '<br>';
                    } // eof link
// bof: delete specials


// bof: delete from one category
                    if ($_POST['copy_as'] == 'delete_one' && $query->RecordCount() == 1) { //if product found
                        $product_multi = $query;
                        $chk_product = $db->Execute("SELECT * FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = " . $id);
                        if ($chk_product->RecordCount() == 1) {
                            // error cannot delete Product when only in one category
                            $messages[] = 'ERROR cannot delete from ' . $_POST['category_id'] . ' multi ' . $product_multi->RecordCount() . '<br>';
                        } else {
                            // delete Product from seleted category
                            $db->Execute("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = " . $id . " AND categories_id = " . $_POST['category_id']);
                            // fix master_categories_id
                            $new_master_categories_id = '';
                            if ($product_multi->fields['master_categories_id'] == $_POST['category_id']) {
                                // get list of indexes, so they can be excluded temporarily
                                $results = $db->execute("show index from " . TABLE_PRODUCTS_TO_CATEGORIES);
                                $keys = array('PRIMARY');
                                while (!$results->EOF) {
                                    if ($results->fields['Key_name'] != 'PRIMARY') {
                                        if (!in_array($results->fields['Key_name'], $keys)) {
                                            $keys[] = $results->fields['Key_name'];
                                        }
                                    }
                                    $results->MoveNext();
                                }

                                $keys_to_exclude = implode($keys, ',');

                                $sql = "SELECT * FROM " . TABLE_PRODUCTS_TO_CATEGORIES;
                                // append the list of excluded keys to the query
                                $sql .= " IGNORE INDEX (" . $keys_to_exclude . ")";
                                $ignore_sql = $sql;
                                $sql = $ignore_sql . " where products_id='" . $id . "' limit 1";
                                $fix_master_categories_id = $db->Execute($sql);
                                $db->Execute("UPDATE " . TABLE_PRODUCTS . " SET master_categories_id = " . $fix_master_categories_id->fields['categories_id'] . " WHERE products_id = " . $id);
                                // update products_price_sorter
                                zen_update_products_price_sorter($id);
                                $new_master_categories_id = ' New master_categories_id: ' . $fix_master_categories_id->fields['categories_id'];
                            }
                            $messages[] = 'Deleted products_id: ' . $id . ' from categories_id: ' . $_POST['category_id'] . ' master_categories_id: ' . $product_multi->fields['master_categories_id'] . $new_master_categories_id . '<br>';

                            // check for master_categories_id and reset
                        }

                    } // eof link
// bof: delete from one category

                    if ($_POST['copy_as'] == 'link' && $query->RecordCount() == 1) { //if product found
                        $product_multi = $query;
                        $items_set[] = array(
                            'id' => $product_multi->fields['products_id'],
                            'manufacturer' => $product_multi->fields['manufacturers_name'],
                            'model' => $product_multi->fields['products_model'],
                            'name' => $product_multi->fields['products_name'],
                            'price' => zen_get_products_display_price($product_multi->fields['products_id'])
                        );
                        $data_array = array(
                            'products_id' => $id,
                            'categories_id' => $copy_to
                        );
                        zen_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, $data_array);
                    } // eof link
                    if ($_POST['copy_as'] == 'duplicate' && $query->RecordCount() == 1) { //if product found
                        $action = 'multiple_product_copy_return';
                        $_POST['products_id'] = $id;
                        $_POST['categories_id'] = $copy_to;
                        $product_type = zen_get_products_type($id);
                        if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/copy_product_confirm.php')) {
                            require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/copy_product_confirm.php');
                        } else {
                            require(DIR_WS_MODULES . 'copy_product_confirm.php');
                        }

                        if ($_POST['copy_specials'] == 'copy_specials_yes') {
                            $chk_specials = $db->Execute("SELECT * FROM " . TABLE_SPECIALS . " WHERE products_id= " . $id);
                            while (!$chk_specials->EOF) {
                                $db->Execute("INSERT INTO " . TABLE_SPECIALS . "
                              (products_id, specials_new_products_price, specials_date_added, expires_date, status, specials_date_available)
                              VALUES ('" . (int)$dup_products_id . "',
                                      '" . zen_db_input($chk_specials->fields['specials_new_products_price']) . "',
                                      now(),
                                      '" . zen_db_input($chk_specials->fields['expires_date']) . "', '1', '" . zen_db_input($chk_specials->fields['specials_date_available']) . "')");
                                $chk_specials->MoveNext();
                            }
                        }

                        if ($_POST['copy_featured'] == 'copy_featured_yes') {
                            $chk_featured = $db->Execute("SELECT * FROM " . TABLE_FEATURED . " WHERE products_id= " . $id);
                            while (!$chk_featured->EOF) {
                                $db->Execute("INSERT INTO " . TABLE_FEATURED . "
                              (products_id, featured_date_added, expires_date, status, featured_date_available)
                              VALUES ('" . (int)$dup_products_id . "',
                                      now(),
                                      '" . zen_db_input($chk_featured->fields['expires_date']) . "', '1', '" . zen_db_input($chk_featured->fields['featured_date_available']) . "')");

                                $chk_featured->MoveNext();
                            }
                        }

                        // reset products_price_sorter for searches etc.
                        zen_update_products_price_sorter((int)$id);

                        $product_multi = $query;
                        $items_set[] = array(
                            'id' => $product_multi->fields['products_id'],
                            'manufacturer' => $product_multi->fields['manufacturers_name'],
                            'model' => $product_multi->fields['products_model'],
                            'name' => $product_multi->fields['products_name'],
                            'price' => zen_get_products_display_price($product_multi->fields['products_id'])
                        );

                    } // eof duplicate
                    if ($_POST['copy_as'] == 'deleted' && $query->RecordCount() == 1) { //if product found
                        $action = 'multiple_product_copy_return';
                        $_POST['products_id'] = $id;

                        $delete_linked = 'true';
                        $product_type = zen_get_products_type($id);

                        $product_categories = array();
                        $chk_categories = $db->Execute("SELECT products_id, categories_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = " . $id);
                        while (!$chk_categories->EOF) {
                            $product_categories[] = $chk_categories->fields['categories_id'];
                            $chk_categories->MoveNext();
                        }
                        $_POST['product_categories'] = $product_categories;
                        if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/delete_product_confirm.php')) {
                            require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/delete_product_confirm.php');
                        } else {
                            require(DIR_WS_MODULES . 'delete_product_confirm.php');
                        }

                        $product_multi = $query;
                        $items_set[] = array(
                            'id' => $product_multi->fields['products_id'],
                            'manufacturer' => $product_multi->fields['manufacturers_name'],
                            'model' => $product_multi->fields['products_model'],
                            'name' => $product_multi->fields['products_name'],
                            'price' => zen_get_products_display_price($product_multi->fields['products_id'])
                        );
                    } // eof delete
                }
            }
            break;
        case 'find':
            $raw_query = "SELECT * FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " ptoc WHERE p.products_id = pd.products_id AND p.products_id = ptoc.products_id AND pd.language_id =  " . (int)$_SESSION['languages_id'];
            if (count($products_in_copyto) > 0) {
                $raw_query .= ' and (not (p.products_id in (' . implode(',', $products_in_copyto) . ')))';
            }
            if (is_numeric($manufacturer_id)) {
                $raw_query .= ' and p.manufacturers_id = ' . (int)$manufacturer_id;
            }
            if (is_numeric($category_id)) {
                if ($inc_subcats) {
                    $raw_query .= ' and (ptoc.categories_id in (' . (int)$category_id . list_subcategories($category_id) . '))';
                } else {
                    $raw_query .= ' and ptoc.categories_id = ' . (int)$category_id;
                }
            }
            if (is_numeric($min_price)) {
                $raw_query .= ' and p.products_price_sorter >= "' . zen_db_input($min_price) . '"';
            }
            if (is_numeric($max_price)) {
                $raw_query .= ' and p.products_price_sorter <= "' . zen_db_input($max_price) . '"';
            }
            if (is_numeric($product_quantity)) {
                $raw_query .= ' and p.products_quantity <= "' . zen_db_input($product_quantity) . '"';
            }

            $where_str = '';
            if (isset($search_keywords) && (count($search_keywords) > 0)) {
                $where_str .= " and (";
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
                            $where_str .= "(pd.products_name like '%" . zen_db_input($keyword) . "%' or p.products_model like '%" . zen_db_input($keyword) . "%' or m.manufacturers_name like '%" . zen_db_input($keyword) . "%'";
                            if ($within == 'all') {
                                $where_str .= " or pd.products_description like '%" . zen_db_input($keyword) . "%'";
                            }
                            $where_str .= ')';
                            break;
                    }
                }
                $where_str .= " )";
            }
            //$query = $db->Execute($raw_query . $where_str . ' group by p.products_model');//steve change this for results sort order
            //$query = $db->Execute($raw_query . $where_str . ' order by p.products_sort_order');//steve change this for results sort order
            $query = $db->Execute($raw_query . $where_str . ' order by p.products_model');//steve change this for results sort order

            if ($query->EOF) {
                $action = 'new';
                $error = true;
                $messages[] = TEXT_NOT_FOUND;
            }
            break;
    }
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
    <script src ="includes/general.js"></script>
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
        #tableDelete td label {
            font-weight: normal;
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
            <table>
                <tr>
                    <td>
                        <?php
                        if (!empty($messages)) { ?>
                            <div class="errorText">
                                <?php foreach ($messages as $message) { ?>
                                    <div><?php echo $message; ?></div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
                <?php if ($action == 'new') {//default / initial page entry
                    echo '<tr><td class="main"><p class="pageHeading">' . HEADING_NEW . "</p>\n";
                    echo zen_draw_form('sale_entry', FILENAME_MULTI_COPY, 'action=find');
                    echo '<p>' . TEXT_HOW_TO_COPY . "</p>\n";

                    echo zen_draw_radio_field('copy_as', 'deleted',
                            ($_POST['copy_as'] == 'deleted' ? true : false)) . ' ' . TEXT_COPY_AS_DELETED . '<br>' . "<br>\n";
                    echo zen_draw_radio_field('copy_as', 'delete_one',
                            ($_POST['copy_as'] == 'delete_one' || $_POST['copy_as'] == '' ? true : false)) . ' ' . TEXT_COPY_AS_DELETE_ONE . '<br>' . "<br>\n";

                    echo zen_draw_radio_field('copy_as', 'delete_specials',
                            ($_POST['copy_as'] == 'delete_specials' ? true : false)) . ' ' . TEXT_COPY_AS_DELETE_SPECIALS . '<br>' . "<br>\n";

                    echo zen_draw_radio_field('copy_as', 'move_from',
                            ($_POST['copy_as'] == 'move_from' ? true : false)) . ' ' . TEXT_MOVE_FROM_LINK . "<br>\n";
                    echo TEXT_MOVE_PRODUCTS_INFO . "<br>\n";
                    //steve original with default checked echo zen_draw_radio_field('copy_as', 'link', ($_POST['copy_as'] == 'link' || $_POST['copy_as'] == ''  ? true : false)) . ' ' . TEXT_COPY_AS_LINK . "<br>\n";
                    echo zen_draw_radio_field('copy_as', 'link',
                            ($_POST['copy_as'] == 'link' ? true : false)) . ' ' . TEXT_COPY_AS_LINK . "<br>\n";
                    echo zen_draw_radio_field('copy_as', 'duplicate',
                            ($_POST['copy_as'] == 'duplicate' ? true : false)) . ' ' . TEXT_COPY_AS_DUPLICATE . "<br>\n";
                    echo TEXT_COPYING_DUPLICATES . "<br>\n";
                    echo TEXT_COPY_ATTRIBUTES . ' ' . zen_draw_radio_field('copy_attributes', 'copy_attributes_yes',
                            true) . ' ' . TEXT_COPY_ATTRIBUTES_YES . ' ' . zen_draw_radio_field('copy_attributes',
                            'copy_attributes_no') . ' ' . TEXT_COPY_ATTRIBUTES_NO . "<br>\n";
                    echo TEXT_COPY_SPECIALS . ' ' . zen_draw_radio_field('copy_specials', 'copy_specials_yes',
                            true) . ' ' . TEXT_YES . ' ' . zen_draw_radio_field('copy_specials',
                            'copy_specials_no') . ' ' . TEXT_NO . "<br>\n";
                    echo TEXT_COPY_FEATURED . ' ' . zen_draw_radio_field('copy_featured', 'copy_featured_yes',
                            true) . ' ' . TEXT_YES . ' ' . zen_draw_radio_field('copy_featured',
                            'copy_featured_no') . ' ' . TEXT_NO . "<br>\n";
                    echo TEXT_COPY_DISCOUNTS . ' ' . zen_draw_radio_field('copy_discounts', 'copy_discounts_yes',
                            true) . ' ' . TEXT_YES . ' ' . zen_draw_radio_field('copy_discounts',
                            'copy_discounts_no') . ' ' . TEXT_NO . "<br>\n";
                    echo TEXT_COPY_MEDIA_MANAGER . ' ' . zen_draw_radio_field('copy_media', 'on',
                            true) . ' ' . TEXT_YES . ' ' . zen_draw_radio_field('copy_media',
                            'off') . ' ' . TEXT_NO . "<br><br>\n";

                    echo ENTRY_COPY_TO . ' ' . zen_draw_pull_down_menu('copy_to', zen_get_category_tree('0', '', '',
                            array(
                                array('id' => '', 'text' => TEXT_SELECT),
                                array('id' => '0', 'text' => TEXT_TOP)
                            ))) . ENTRY_COPY_TO_NOTE . '<br>' . "<br>\n";

                    echo zen_draw_separator('pixel_black.gif', '90%', '3') . '<br><br>';

                    echo ENTRY_AUTO_CHECK . zen_draw_radio_field('autocheck',
                            'yes') . '&nbsp;' . TEXT_YES . '&nbsp;&nbsp;&nbsp;' . zen_draw_radio_field('autocheck',
                            'no', 'yes') . '&nbsp;' . TEXT_NO . "<br><br>\n";//steve edited
                    //echo ENTRY_AUTO_CHECK . zen_draw_radio_field('autocheck', 'yes', 'yes') . '&nbsp;' . TEXT_YES . '&nbsp;&nbsp;&nbsp;' . zen_draw_radio_field('autocheck', 'no') . '&nbsp;' . TEXT_NO . "<br><br>\n";

                    echo zen_draw_separator('pixel_black.gif', '90%', '3') . '<br><br>';

                    echo '<p><b>' . TEXT_ENTER_CRITERIA . "</b></p>\n";

                    echo "<p>\n";
                    echo TEXT_PRODUCTS_CATEGORY . ' ' . zen_draw_pull_down_menu('category_id', zen_get_category_tree('0', '', '', array(
                                array('id' => '', 'text' => TEXT_ANY_CATEGORY),
                                array('id' => '0', 'text' => TEXT_TOP)
                            )));
                    echo '&nbsp;&nbsp;&nbsp;' . zen_draw_checkbox_field('inc_subcats',
                            'yes') . ENTRY_INC_SUBCATS . ' ' . ENTRY_DELETE_TO_NOTE . "</p>\n";
                    echo '<p><b>' . TEXT_ENTER_TERMS . "</b></p>\n";

                    echo zen_draw_input_field('keywords', '', 'size=50') . "<br>\n";
                    echo zen_draw_radio_field('within', 'name') . '&nbsp;' . TEXT_NAME_ONLY;
                    echo '&nbsp;' . zen_draw_radio_field('within', 'all', 'all') . '&nbsp;' . TEXT_DESCRIPTIONS . "<br>\n";
                    $manufacturers_array = array(array('id' => '', 'text' => TEXT_ANY_MANUFACTURER));
                    $manufacturers_query = $db->Execute("SELECT manufacturers_id, manufacturers_name FROM " . TABLE_MANUFACTURERS . " ORDER BY manufacturers_name");
                    while (!$manufacturers_query->EOF) {
                        $manufacturers_array[] = array(
                            'id' => $manufacturers_query->fields['manufacturers_id'],
                            'text' => $manufacturers_query->fields['manufacturers_name']
                        );
                        $manufacturers_query->MoveNext();
                    }
                    echo TEXT_PRODUCTS_MANUFACTURER . zen_draw_pull_down_menu('manufacturer_id', $manufacturers_array) . "<br>\n";
                    echo ENTRY_MIN_PRICE . zen_draw_input_field('min_price') . TEXT_OPTIONAL . "<br>\n";
                    echo ENTRY_MAX_PRICE . zen_draw_input_field('max_price') . TEXT_OPTIONAL . "<br>\n";
                    echo ENTRY_PRODUCT_QUANTITY . zen_draw_input_field('product_quantity',
                            'any') . TEXT_OPTIONAL . "<br>\n";

                    echo '<p>' . zen_image_submit('button_preview.gif', IMAGE_PREVIEW) . '</p>';
                    echo "</form>\n";

                    echo TEXT_TIPS;
                    ?>
                    <!--</form>--><!--steve for validation-->
                    </td></tr>
                <?php } elseif ($action == 'find') {
                    ?>
                    <tr>
                        <td>
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="pageHeading">
                                        <?php
                                        switch (true) {
                                            case ($copy_as == 'delete_one'):
                                                echo HEADING_SELECT_PRODUCT_DELETE_ONE . ' - ID#' . $_POST['category_id'] . ' ' . zen_get_category_name($_POST['category_id'],
                                                        (int)$_SESSION['languages_id']);
                                                break;
                                            case ($copy_as == 'delete_specials'):
                                                echo HEADING_SELECT_PRODUCT_DELETE_SPECIALS;
                                                break;
                                            case ($copy_as == 'deleted'):
                                                echo HEADING_SELECT_PRODUCT_DELETED;
                                                break;
                                            case ($copy_as == 'move_from'):
                                                echo HEADING_SELECT_PRODUCT_MOVE_FROM;
                                                break;
                                            default:
                                                echo HEADING_SELECT_PRODUCT;
                                                break;
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="main">&nbsp;</td>
                                </tr>
                                <?php if ($copy_as != 'deleted' && $copy_as != 'link' && $copy_as != 'move_from' && $copy_as != 'delete_one' && $copy_as != 'delete_specials') { ?>
                                    <tr>
                                        <td class="main">
                                            <?php
                                            echo TEXT_DUPLICATE_ATTRIBUTES . ($_POST['copy_attributes'] == 'copy_attributes_yes' ? 'YES' : 'NO') . '<br>';
                                            echo TEXT_DUPLICATE_SPECIALS . ($_POST['copy_specials'] == 'copy_specials_yes' ? 'YES' : 'NO') . '<br>';
                                            echo TEXT_DUPLICATE_FEATURED . ($_POST['copy_featured'] == 'copy_featured_yes' ? 'YES' : 'NO') . '<br>';
                                            echo TEXT_DUPLICATE_QUANTITY_DISCOUNTS . ($_POST['copy_discounts'] == 'copy_discounts_yes' ? 'YES' : 'NO') . '<br>';
                                            echo TEXT_DUPLICATE_MEDIA . ($_POST['copy_media'] == 'on' ? 'YES' : 'NO') . '<br><br>';
                                            ?>
                                        </td>
                                    </tr>
                                <?php } // !=deleted ?>
                                <tr>
                                    <td class="main">

                                            <?php
                                            //echo '<pre>'; echo var_dump($_POST); echo '</pre>';
                                            switch ($copy_as) {
                                                case ('delete_one'):
                                                    echo '<p>' . TEXT_DETAILS_DELETE_ONE . "</p>\n";
                                                    echo '<blockquote>' . ENTRY_DELETE_ONE . "<br>\n";
                                                    echo "</blockquote>\n<b>" . TEXT_SELECT_PRODUCTS_DELETE_ONE . "</b>\n";
                                                    break;
                                                case ('delete_specials'):
                                                    echo '<p>' . TEXT_DETAILS_DELETE_SPECIALS . "</p>\n";
                                                    echo '<blockquote>' . ENTRY_DELETE_SPECIALS . "<br>\n";
                                                    echo "</blockquote>\n<b>" . TEXT_SELECT_PRODUCTS_DELETE_SPECIALS . "</b>\n";
                                                    break;
                                                case ('deleted'):
                                                    echo '<p>' . TEXT_DETAILS_DELETED . "</p>\n";
                                                    echo '<blockquote>' . ENTRY_DELETED . "<br>\n";
                                                    echo "</blockquote>\n<b>" . TEXT_SELECT_PRODUCTS_DELETED . "</b>\n";
                                                    break;
                                                case ('link'):
                                                    echo '<p>' . TEXT_DETAILS_LINK . "</p>\n";
                                                    if (zen_childs_in_category_count($copy_to) > 0) {
                                                        echo '<span class="alert">' . TEXT_WARNING_CATEGORY_SUB . '</span>' . "<br>\n";
                                                    }
                                                    echo '<blockquote>' . ENTRY_COPY_TO_LINK . $copy_to . ' - ' . $copy_to_name . "<br>\n";
                                                    echo TEXT_ONLY_NOT_DEST . "<br>\n";
                                                    echo "</blockquote>\n<b>" . TEXT_SELECT_PRODUCTS . "</b>\n";
                                                    break;
                                                default:
                                                    if ($copy_as == 'move_from' && zen_childs_in_category_count($category_id) > 0) {
                                                        echo '<span class="alert">' . TEXT_MOVE_PRODUCTS_CATEGORIES . '</span>' . '<br>' . 'Moving to ID#: ' . $copy_to . ' - ' . zen_get_category_name($copy_to,
                                                                (int)$_SESSION['languages_id']);
                                                    }
                                                    if ($copy_as == 'move_from' && zen_childs_in_category_count($category_id) == 0) {
                                                        echo '<span class="alert">' . TEXT_MOVE_PRODUCTS_CATEGORIES . '</span>' . '<br>' . 'Moving to ID#: ' . $copy_to . ' - ' . zen_get_category_name($copy_to,
                                                                (int)$_SESSION['languages_id']) . '<br>' . 'Products already in or Linked to this category are not listed';
                                                    }
                                                    if ($copy_as != 'move_from') {
// echo 'I SEE: ' . $copy_as . '<br>' . 'Copy from: ' . $category_id . '<br>' . 'Copy To: ' . $copy_to . '<br><br><br>';
                                                        echo '<p>' . TEXT_DETAILS . "</p>\n";
                                                        if (zen_childs_in_category_count($copy_to) > 0) {
                                                            echo '<span class="alert">' . TEXT_WARNING_CATEGORY_SUB . '</span>' . "<br>\n";
                                                        }
                                                        echo '<blockquote>' . ENTRY_COPY_TO_DUPLICATE . $copy_to . ' - ' . $copy_to_name . "<br>\n";
                                                        echo TEXT_ONLY_NOT_DEST . "<br>\n";
                                                        echo "</blockquote>\n<b>" . TEXT_SELECT_PRODUCTS . "</b>\n";
                                                    }
                                            }
                                            echo zen_draw_form('select_products', FILENAME_MULTI_COPY,
                                                'action=confirm');
                                            // repost previous form values
                                            echo zen_draw_hidden_field('copy_to');
                                            echo zen_draw_hidden_field('autocheck');
                                            echo zen_draw_hidden_field('keywords');
                                            echo zen_draw_hidden_field('within');
                                            echo zen_draw_hidden_field('manufacturer_id');
                                            echo zen_draw_hidden_field('category_id');
                                            echo zen_draw_hidden_field('inc_subcats');
                                            echo zen_draw_hidden_field('min_price');
                                            echo zen_draw_hidden_field('max_price');
                                            echo zen_draw_hidden_field('product_quantity');
                                            echo zen_draw_hidden_field('copy_as');
                                            $copy_attributes = $_POST['copy_attributes'];
                                            echo zen_draw_hidden_field('copy_attributes');
                                            $copy_specials = $_POST['copy_specials'];
                                            echo zen_draw_hidden_field('copy_specials');
                                            $copy_featured = $_POST['copy_featured'];
                                            echo zen_draw_hidden_field('copy_featured');
                                            $copy_discounts = $_POST['copy_discounts'];
                                            echo zen_draw_hidden_field('copy_discounts');
                                            $copy_media = $_POST['copy_media'];
                                            echo zen_draw_hidden_field('copy_media');
                                            ?>
                                        <table border="1" id="tableDelete">
                                            <tr class="dataTableHeadingRow">
                                                <td class="dataTableHeadingContent text-center" width="20"><?php echo TABLE_HEADING_SELECT; ?></td>
                                                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODEL; ?></td>
                                                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NAME; ?></td>
                                                <td class="dataTableHeadingContent text-center"><?php echo TABLE_HEADING_PRODUCTS_ID; ?></td>
                                                <td class="dataTableHeadingContent text-center" width="20"><?php echo TABLE_HEADING_STATUS; ?></td>
                                                <td class="dataTableHeadingContent text-center" width="20"><?php echo TABLE_HEADING_LINKED; ?></td>
                                                <td class="dataTableHeadingContent text-right"><?php echo TABLE_HEADING_PRICE; ?></td>
                                                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MFG; ?></td>
                                            </tr>
                                            <?php
                                            $items_found = array();
                                            $cnt = 0;
                                            $product_multi = $query;
                                            while (!$product_multi->EOF) { // list all matching products
                                                ?>
                                                <?php
                                                if ($copy_as == 'delete_specials') {
                                                    $chk_product_special = $db->Execute("SELECT products_id FROM " . TABLE_SPECIALS . " WHERE products_id = " . $product_multi->fields['products_id']);
                                                    if ($chk_product_special->RecordCount() > 0) {
                                                        $show_product = true;
                                                    } else {
                                                        $show_product = false;
                                                    }

                                                } else {
                                                    $show_product = true;
                                                }
                                                if ($show_product == true) {
                                                    if ($show_images) {//bof steve added
                                                        if ($product_multi->fields['products_image'] == '') {
                                                            $product_image = zen_image(DIR_WS_CATALOG_IMAGES . PRODUCTS_IMAGE_NO_IMAGE, $product_multi->fields['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
                                                        } else {
                                                            $product_image = zen_image(DIR_WS_CATALOG_IMAGES . $product_multi->fields['products_image'],$product_multi->fields['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
                                                        }
                                                    } else {
                                                        $product_image = '';
                                                    }
                                                    //eof steve
                                                    ?>
                                                    <tr class="dataTableRow"><?php //steve todo removed mouseover as shows cursor on most columns... to investigte. Was
                                                        //  onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ?>
                                                        <td class="dataTableContent text-center">
                                                            <?php
                                                            if ($copy_as != 'delete_one' || ($copy_as == 'delete_one' && zen_get_product_is_linked($product_multi->fields['products_id']) == 'true')) {
                                                                echo zen_draw_checkbox_field('product[' . $cnt . ']',
                                                                    $product_multi->fields['products_id'], $autocheck,
                                                                    '',
                                                                    'id="product[' . $cnt . ']"');//steve added id to allow label to be attached to it. Note that validator says [] are invalid characters but these are necessary to create the checkbox array for the POST parameter.
                                                            } else {
                                                                echo '&nbsp;***&nbsp;';
                                                            }
                                                            $items_found[] = $product_multi->fields['products_id'];
                                                            $cnt++;
                                                            ?>
                                                        </td>
                                                        <td class="dataTableContent"><label
                                                                    for="product[<?php echo $cnt - 1; ?>]"><?php echo $product_multi->fields['products_model']; ?></label>
                                                        </td>
                                                        <td class="dataTableContent"><label
                                                                    for="product[<?php echo $cnt - 1; ?>]"><?php echo $product_multi->fields['products_name'] . '&nbsp;' . $product_image; ?></label>
                                                        </td>
                                                        <td class="dataTableContent">
                                                            <?php echo $product_multi->fields['products_id']; //steve added/was missing!!??? ?>
                                                        </td>
                                                        <td class="dataTableContent text-center">
                                                            <?php
                                                            //steve made status icons a link to product edit
                                                            echo '<a href="' . zen_href_link(FILENAME_PRODUCT,
                                                                    'cPath=' . zen_get_product_path($product_multi->fields['products_id']) . '&amp;product_type=1&amp;pID=' . $product_multi->fields['products_id'] . '&amp;action=new_product') . '" target="_blank">' . ($product_multi->fields['products_status'] == '1' ? zen_image(DIR_WS_IMAGES . 'icon_green_on.gif',
                                                                    IMAGE_ICON_STATUS_ON . ' -> Edit Product') : zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_STATUS_OFF . ' -> Edit Product')) . '</a>';//steve todo languages constants
                                                            ?>
                                                        </td>
                                                        <td class="dataTableContent text-center">
                                                            <?php
                                                            if (zen_get_product_is_linked($product_multi->fields['products_id']) == 'true') {
                                                                echo '&nbsp;&nbsp;' . zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif',
                                                                        IMAGE_ICON_LINKED) . '<br>';
                                                            } else {
                                                                echo '&nbsp;&nbsp;' . '<br>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="dataTableContent text-right"><?php echo zen_get_products_display_price($product_multi->fields['products_id']); ?>
                                                        </td>
                                                        <td class="dataTableContent"><label
                                                                    for="product[<?php echo $cnt - 1; ?>]"><?php echo $product_multi->fields['manufacturers_name']; ?></label>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                } // $show_product
                                                ?>
                                                <?php
                                                $product_multi->MoveNext();
                                            }
                                            ?>
                                        </table>
                                        <?php
                                        if ($cnt > 0) {
                                            echo zen_draw_hidden_field('items_found', implode(',', $items_found));
                                            echo zen_draw_hidden_field('product_count', $cnt);
                                            echo $cnt . TEXT_PRODUCTS_FOUND . "<br><br>\n";
                                            echo zen_image_submit('button_confirm.gif', IMAGE_CONFIRM) . '&nbsp;&nbsp;';
                                        } else { // no valid products were found
                                            echo '<p class="error">' . ERROR_NONE_VALID . "</p>\n";
                                        }
                                        echo '</form>' . zen_draw_form('retry', FILENAME_MULTI_COPY, 'action=new');
                                        // repost previous form values
                                        echo zen_draw_hidden_field('copy_to');
                                        echo zen_draw_hidden_field('autocheck');
                                        echo zen_draw_hidden_field('keywords');
                                        echo zen_draw_hidden_field('within');
                                        echo zen_draw_hidden_field('manufacturer_id');
                                        echo zen_draw_hidden_field('category_id');
                                        echo zen_draw_hidden_field('inc_subcats');
                                        echo zen_draw_hidden_field('min_price');
                                        echo zen_draw_hidden_field('max_price');
                                        echo zen_draw_hidden_field('product_quantity');

                                        echo zen_draw_hidden_field('copy_as');
                                        echo zen_draw_hidden_field('copy_attributes');
                                        echo zen_draw_hidden_field('copy_specials');
                                        echo zen_draw_hidden_field('copy_featured');
                                        echo zen_draw_hidden_field('copy_discounts');

                                        echo zen_draw_hidden_field('copy_media');

                                        echo zen_draw_input_field('retry', BUTTON_RETRY, 'alt="' . BUTTON_RETRY . '"',
                                            false, 'submit');
                                        echo '&nbsp;&nbsp;<a href="' . zen_href_link(FILENAME_CATEGORY_PRODUCT_LISTING) . '">' . zen_image_button('button_cancel.gif',
                                                IMAGE_CANCEL) . "</a></form>\n";
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php
                }
                else { /* display list of products set */
                ?>
                <?php
                if ($copy_as == 'link' || $copy_as == 'move_from') {
                    ?>
                    <tr>
                        <td class="pageHeading"><?php echo HEADING_PRODUCT_COPIED; ?></td>
                    </tr>
                    <tr>
                        <td class="main">
                            <p>
                                <?php echo TEXT_DETAILS; ?>
                             </p>
                            <?php
                                echo '<blockquote>' . ENTRY_COPY_TO . $copy_to . ' - ' . $copy_to_name . "<br>\n";
                                echo "</blockquote>\n<b>" . TEXT_CHANGES_MADE . "</b>\n";
                                ?>
                            <table>
                                <tr class="dataTableHeadingRow">
                                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_ID; ?></td>
                                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MFG; ?></td>
                                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODEL; ?></td>
                                    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NAME; ?></td>
                                    <td class="dataTableHeadingContent text-right"><?php echo TABLE_HEADING_PRICE; ?>
                                        &nbsp;&nbsp;
                                    </td>
                                </tr>
                                <?php foreach ($items_set as $product_multi) { ?>
                                    <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                        onmouseout="rowOutEffect(this)">
                                        <td class="dataTableContent"><?php echo $product_multi['id']; ?></td>
                                        <td class="dataTableContent"><?php echo $product_multi['manufacturer']; ?></td>
                                        <td class="dataTableContent"><?php echo $product_multi['model']; ?></td>
                                        <td class="dataTableContent"><?php echo $product_multi['name']; ?></td>
                                        <td class="dataTableContent text-right"><?php echo $product_multi['price']; ?>&nbsp;&nbsp;
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td>
                        <?php
                        echo '<p>' . count($items_set) . TEXT_PRODUCTS_COPIED . "</p>\n";
                        echo zen_draw_form('product_entry', FILENAME_CATEGORY_PRODUCT_LISTING, 'cPath=' . $copy_to);
                        echo zen_draw_input_field('cat', sprintf(BUTTON_GO_TO_CATEGORY, $copy_to, $copy_to_name), 'alt="' . sprintf(BUTTON_GO_TO_CATEGORY, $copy_to, $copy_to_name) . '"', false, 'submit');
                        echo '</form>&nbsp;&nbsp;';

                        echo zen_draw_form('multi_product_copy', FILENAME_MULTI_COPY);
                        echo zen_draw_input_field('new', BUTTON_ANOTHER_COPY, 'alt="' . BUTTON_ANOTHER_COPY . '"', false, 'submit');
                        echo "</form>\n";
                        }
                        ?>
            </table>
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
