<?php

declare(strict_types=1);
// Plugin Multiple Product Copy
// English
// https://github.com/torvista/Zen_Cart-Multiple_Products_Copy_Move_Delete
// @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
$define = [
    'HEADING_TITLE' => 'Multiple Product Copy/Move/Delete',

// SELECTIONS page 1
    'TEXT_COPY_AS_LINK' => 'Copy Products as Linked ',
    'TEXT_COPY_AS_DUPLICATE' => 'Copy Products as Duplicates (new products)',
    'TEXT_COPY_AS_DUPLICATE_ENABLE' => 'Enable the new products',
    'TEXT_COPY_ATTRIBUTES' => 'Copy Attributes',
    'TEXT_COPY_METATAGS' => 'Copy Meta Tags',
    'TEXT_COPY_LINKED_CATEGORIES' => 'Copy Linked Categories',
    'TEXT_COPY_DISCOUNTS' => 'Copy Quantity Discounts',
    'TEXT_COPY_SPECIALS' => 'Copy Special Prices',
    'TEXT_COPY_FEATURED' => 'Copy Featured settings',
    'TEXT_ALL_CATEGORIES' => 'All Categories', // this constant declared earlier to be used subsequently

    'TEXT_MOVE_TO' => 'Move Products',
    'TEXT_MOVE_PRODUCTS_INFO_SEARCH_CATEGORY' => '<p>When the search is restricted to a Search Category:<br>Linked Products will be unlinked from that current category and linked to the Target Category.<br>Products in their Master Category will have their Master Category ID changed to that of the Target Category.</p>',

    'TEXT_MOVE_PRODUCTS_INFO_SEARCH_GLOBAL' => '<p>When the search is <strong>not</strong> restricted ("%%TEXT_ALL_CATEGORIES%%"):<br>All selected products will have their Master Category ID changed to that of the Target Category. Product links will be unchanged.',
    'TEXT_TARGET_CATEGORY' => 'Target Category (for Copy/Move):',

    'TEXT_COPY_AS_DELETE_SPECIALS' => 'Delete Specials from Products',
    'TEXT_COPY_AS_DELETE_LINKED' => 'Delete Linked Products',
    'TEXT_COPY_AS_DELETE_ALL' => 'Delete Any Products',
    'TEXT_COPY_AS_DELETE_ALL_INFO' => 'This option allows the multiple permanent deletion of products. Selection of any product (whether linked/master) will delete <span style="text-decoration: underline">ALL INSTANCES</span> (both master and linked) of that product. USE WITH CARE and ensure you have a verified backup of your database first!',

// Search Criteria
    'TEXT_ENTER_CRITERIA' => 'Search/Filter Criteria:',
    'TEXT_PRODUCTS_CATEGORY' => 'Search in Category:',
    'TEXT_INCLUDE_SUBCATS' => ' include subcategories',
    'TEXT_ENTER_SEARCH_KEYWORDS' => 'Find products containing the following keywords (in product\'s Model, Name and Manufacturer name) ',
    'TEXT_SEARCH_DESCRIPTIONS' => 'Also search in product Descriptions ',
    'TEXT_PRODUCTS_MANUFACTURER' => 'Manufacturer ',
    'TEXT_ALL_MANUFACTURERS' => 'All Manufacturers',
    'ENTRY_MIN_PRICE' => 'Store (displayed) Price &gt;= ',
    'ENTRY_MAX_PRICE' => 'Store (displayed) Price &lt;= ',
    'ENTRY_MAX_PRODUCT_QUANTITY' => 'Product Quantity &lt;= ',
    'ENTRY_SHOW_IMAGES' => 'Show images? ',
    'ENTRY_AUTO_CHECK' => 'Automatically select all matching products? ',
    'ENTRY_RESULTS_ORDER_BY' => 'Order results by ',
//constant name suffix TEXT_ORDER_BY_?? auto-defined by option name
    'TEXT_ORDER_BY_ID' => 'Product ID',
    'TEXT_ORDER_BY_MANUFACTURER' => 'Manufacturer',
    'TEXT_ORDER_BY_MODEL' => 'Model',
    'TEXT_ORDER_BY_NAME' => 'Name',
    'TEXT_ORDER_BY_PRICE' => 'Price',
    'TEXT_ORDER_BY_QUANTITY' => 'Quantity',
    'TEXT_ORDER_BY_STATUS' => 'Status',

    'TEXT_TIPS' => '<h2>Notes:</h2>
<h3>Searching:</h3>
<ul><li>Products currently existing in the Target Category are automatically excluded from the search results.</li>
<li>Search keywords may be left blank if either a Category or a Manufacturer is selected,  or one of the Store Price fields has a value.</li>
<li>You may search using only <strong>one</strong> of the Store Price fields if you want to find all products with prices greater/less than a specific amount.</li>
<li>The decimal separator in the Store Price entries <strong>must</strong> be a \'.\' (decimal-point), example: <b>49.99</b></li></ul>
<h3>Copy as Duplicate (new) Products:</h3>
<ul><li>Attributes can be optionally copied. But for Downloads, the Download filename is NOT copied.</li>
<li>Reviews are NOT copied.</li></ul>
<h3>Deleting Products</h3>
<h4>Delete Products permanently from ALL Categories:</h4>
<ul><li>Deletions are permanent and cannot be undone</li>
<li>If the product\'s Main Image is unique, it will be deleted, as will the Main Image Medium and Large Image. Additional Images and Additional Large Images will NOT be removed</li></ul>
<h4>Delete Linked Products from ONE Category:</h4>
<ul><li>Deleting from ONE Category will unlink a Product from that Category.</li>
<li>If that category is a product\'s master_categories_id, the product will not be deleted.</li></ul>',

//RESULTS page 2
    'TEXT_PRODUCTS_FOUND' => '%u matching product(s) found.',
    'WARNING_MAX_INPUT_VARS_LIMIT' => 'WARNING: search results have been limited to %1$u products. The PHP environment parameter "max_input_vars" (currently "%2$u") will need to be increased if you wish to select more products.',
// Search Critera summary
    'TEXT_SEARCH_RESULT_CATEGORY' => 'Search category: %s',
    'TEXT_SEARCH_RESULT_KEYWORDS' => 'Search keywords: "%s"',
    'TEXT_SEARCH_RESULT_MANUFACTURER' => 'Search manufacturer: %s',
    'TEXT_SEARCH_RESULT_MIN_PRICE' => 'Search price > %s',
    'TEXT_SEARCH_RESULT_MAX_PRICE' => 'Search price < %s',
    'TEXT_SEARCH_RESULT_QUANTITY' => 'Search quantity < %u',
    'TEXT_SEARCH_RESULT_TARGET' => 'Target Category: "%2$s" ID#%1$u',
    'TEXT_EXISTING_PRODUCTS_NOT_SHOWN' => 'Only matching products <strong>not already present</strong> in the Target Category are listed.',
    'TABLE_HEADING_SELECT' => 'Selected',
    'TEXT_TOGGLE_ALL' => 'toggle all',
    'TABLE_HEADING_PRODUCTS_ID' => 'ID',
    'TABLE_HEADING_IMAGE' => 'Image',
    'TABLE_HEADING_STATUS' => 'Status',
    'IMAGE_ICON_STATUS_ON_EDIT_PRODUCT' => 'product is enabled -> Edit Product',
    'IMAGE_ICON_STATUS_OFF_EDIT_PRODUCT' => 'product is disabled -> Edit Product',

    'TABLE_HEADING_CATEGORY' => 'In Category',
    'TABLE_HEADING_LINKED_MASTER' => 'Linked %1$s<br>Master %2$s',
    'TABLE_HEADING_MASTER_CATEGORY' => 'Master Category',
    'IMAGE_ICON_MASTER' => 'Product in Master Category',
    'IMAGE_ICON_LINKED_EDIT_LINKS' => 'product is linked -> Edit in Link Manager',
    'IMAGE_ICON_NOT_LINKED_EDIT_LINKS' => 'product is not linked -> Edit in Link Manager',

    'TEXT_PRODUCT_MASTER_CATEGORY_CHANGE' => 'move this product/change master category',
    'TEXT_PRODUCT_SPECIAL_EDIT' => 'edit this Special Price',

    'TABLE_HEADING_NAME' => 'Name',
    'TABLE_HEADING_PRICE' => 'Store Price',
    'TABLE_HEADING_QUANTITY' => 'Quantity',
    'TABLE_HEADING_MFG' => 'Manufacturer',

    'IMAGE_ICON_EDIT_LINKS' => 'Edit Link/Master Category',
//'IMAGE_ICON_CATEGORY_LINKED' => 'linked category: %2$s ID#%1$u . Edit in Link Manager',

    'BUTTON_RETRY' => 'Modify Search',
    'BUTTON_CATEGORY_LISTING_SEARCH' => 'Product Listing - Search Category',
    'BUTTON_CATEGORY_LISTING_TARGET' => 'Product Listing - Target Category',

//RESULTS
    'TEXT_DELETE_LINKED' => 'Category "%2$s" ID#%1$u',
    'TEXT_DELETE_LINKED_INFO' => '',
    'TEXT_INCLUDED_SUBCATS' => 'Included subcategories:',
    'TEXT_DISABLED' => 'disabled',
//CONFIRM page 3
    'BUTTON_NEW_SEARCH' => 'New Search',

//Confirm Copy Linked
    'TEXT_PRODUCTS_COPIED_TO' => '%1$u product(s) copied to Category "%3$s" ID#%2$u ',
//Confirm Moved
//'TEXT_PRODUCTS_MOVED_TO' => '%1$u product(s) moved to Category "%3$s" ID#%2$u ',

//Confirm Copy Duplicates
//these four constants used in copy_product_confirm
//if (!defined('TEXT_DUPLICATE_IDENTIFIER')) {
    'TEXT_DUPLICATE_IDENTIFIER' => '[COPY]',
//}
    'TEXT_COPY_AS_DUPLICATE_ATTRIBUTES' => 'Attributes copied from Product ID#%1$u to duplicate Product ID#%2$u',
    'TEXT_COPY_AS_DUPLICATE_METATAGS' => 'Metatags for Language ID#%1$u copied from Product ID#%2$u to duplicate Product ID#%3$u',
    'TEXT_COPY_AS_DUPLICATE_CATEGORIES' => 'Linked Category ID#%1$u copied from Product ID#%2$u to duplicate Product ID#%3$u',
    'TEXT_COPY_AS_DUPLICATE_DISCOUNTS' => 'Discounts copied from Product ID#%1$u to duplicate Product ID#%2$u',
//these two constants used in move_product_confirm
    'TEXT_PRODUCT_MOVED' => 'Product ID#%1$u moved to Category ID#%2$u',
    'TEXT_PRODUCT_MASTER_CATEGORY_RESET' => 'Product ID#%1$u Master Category ID changed to Category ID#%2$u',

    'TEXT_COPY_AS_DUPLICATE_SPECIALS' => 'Special price copied from Product ID#%1$u to duplicate Product ID#%2$u',
    'TEXT_COPY_AS_DUPLICATE_FEATURED' => 'Featured settings copied from Product ID#%1$u to duplicate Product ID#%2$u',

//Confirm Move
    'TEXT_PRODUCTS_MOVED_TO' => '%1$u product(s) moved to Category ID#%2$u "%3$s"',

//Confirm Delete Specials
    'TEXT_SPECIALS_DELETED_FROM' => 'Special price(s) deleted from %u product(s).',

//Confirm Delete
    'TEXT_PRODUCTS_DELETED' => '%u product(s) deleted.',

// Errors
    'ERROR_ILLEGAL_OPTION' => 'Invalid option/no option set.',
    'ERROR_NO_TARGET_CATEGORY' => 'No Target Category selected!',
    'ERROR_TARGET_CATEGORY_HAS_SUBCATEGORY' => 'Copy/Move not allowed: Target Category "%2$s" ID#%1$u contains a subcategory',
    'ERROR_SEARCH_CATEGORY_HAS_SUBCATEGORY' => 'Copy/Move not allowed: Search Category "%2$s" ID#%1$u contains a subcategory',
    'ERROR_SAME_CATEGORIES' => 'The Search and Target Categories are the same: "%2$s" ID#%1$u!',
    'ERROR_NO_PRODUCTS_IN_CATEGORY' => 'No products found in category "%2$s" ID#%1$u',
    'ERROR_OR_SUBS' => ', or subcategories.',
    'ERROR_INVALID_KEYWORDS' => 'Invalid keywords',
    'ERROR_NO_PRODUCTS_FOUND' => 'No products found in "%2$s" ID#%1$u',
    'ERROR_SEARCH_CRITERIA_REQUIRED' => 'No Search critera set! Set a Search category / keyword / manufacturer / price field.',
    'ERROR_ARRAY_COUNTS' => 'The POST value for the total count of products selected from the search was not set. This was most likely due to the PHP limit max_input vars (currently %u) being insufficient for the products selected. This limit may be increased by your hosting and needs to be more than double the products selected.',
    'ERROR_NO_SELECTION' => 'No products selected. At least one product from the list must be selected!',
    'ERROR_CHECKBOXES_NOT_ARRAY' => 'Selected checkboxes not an array.',
    'ERROR_CHECKBOX_ID' => 'A selected checkbox references product ID#%u. This is not a found product ID!',
    'ERROR_COPY_DUPLICATE_NO_DUP_ID' => 'The duplicate/new product ID was not returned from "copy_product_confirm.php" for copy-duplicate of product ID#%1$u to category ID#%2$u.',
    'TEXT_NO_MATCHING_PRODUCTS_FOUND' => 'No products were found that matched the search criteria or all matching products already exist in the target category.',
];

return $define;
