<?php
/**
 * @package admin
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * $Id: multi_product_copy.php ver 1.11 by Kevin L. Shelton 2010-10-15
 * $Id: multi_product_copy.php ver 1.392 by Linda McGrath 2011-11-15
 * $Id: multi_product_copy.php ver 1.394 by torvista 2019
*/

define('HEADING_TITLE', 'Multiple Product Copy/Move/Delete');

// Actions
define('TEXT_COPY_AS_LINK', 'Copy Products as Linked ');
define('TEXT_COPY_AS_DUPLICATE', 'Copy Products as Duplicates (new products)');
define('TEXT_COPY_AS_DUPLICATE_ENABLE', 'Enable the new products');
define('TEXT_COPY_ATTRIBUTES', 'Copy Attributes');
define('TEXT_COPY_METATAGS', 'Copy Meta Tags');
define('TEXT_COPY_LINKED_CATEGORIES', 'Copy Linked Categories');
define('TEXT_COPY_DISCOUNTS', 'Copy Quantity Discounts');
define('TEXT_COPY_SPECIALS', 'Copy Special Prices');
define('TEXT_COPY_FEATURED', 'Copy Featured settings');
define('TEXT_COPY_MEDIA_MANAGER', 'Copy Media Manager collections');
define('TEXT_MOVE_TO', 'Move Products');
define('TEXT_MOVE_PRODUCTS_INFO', '"Moving" products means the products Master Category ID\'s will be set to that of the Target Category.');
define('TEXT_TARGET_CATEGORY', 'Target Category (for Copy/Move):');

define('TEXT_COPY_AS_DELETE_ONE', 'Delete Linked Products permanently from ONE Category');
define('TEXT_COPY_AS_DELETE_ALL', 'Delete Products permanently from ALL Categories?');
define('TEXT_COPY_AS_DELETE_SPECIALS', 'Delete Specials from Products?');

// Search
define('TEXT_ENTER_CRITERIA', 'Search/Filter Criteria:');
define('TEXT_PRODUCTS_CATEGORY', 'Search in Category:');
define('TEXT_ALL_CATEGORIES', 'All Categories');
define('TEXT_INCLUDE_SUBCATS', ' include subcategories (only used for <em>deleting</em> products)');
define('TEXT_ENTER_SEARCH_KEYWORDS', 'Find products containing the following keywords (in product\'s Model, Name and Manufacturer name) ');
define('TEXT_SEARCH_DESCRIPTIONS', 'Also search in product Descriptions ');
define('TEXT_PRODUCTS_MANUFACTURER', 'Manufacturer ');
define('TEXT_ALL_MANUFACTURERS', 'All Manufacturers');
define('ENTRY_MIN_PRICE', 'Store (displayed) Price &gt;= ');
define('ENTRY_MAX_PRICE', 'Store (displayed) Price &lt;= ');
define('ENTRY_MAX_PRODUCT_QUANTITY', 'Product Quantity &lt;= ');
define('ENTRY_SHOW_IMAGES', 'Show images? ');
define('ENTRY_AUTO_CHECK', 'Automatically select all matching products? ');
define('ENTRY_RESULTS_ORDER_BY', 'Order results by ');
define('TEXT_ORDER_BY_ID', 'Product ID');
define('TEXT_ORDER_BY_MANUFACTURER', 'Manufacturer');
define('TEXT_ORDER_BY_MODEL', 'Model');
define('TEXT_ORDER_BY_NAME', 'Name');
define('TEXT_ORDER_BY_PRICE', 'Price');
define('TEXT_ORDER_BY_QUANTITY', 'Quantity');
define('TEXT_ORDER_BY_STATUS', 'Status');

define('TEXT_TIPS', '<h2>Notes:</h2>
<ul><li>Products currently existing in the Target Category are automatically excluded from the search (results).</li>
<li>Search terms may be left blank if either a Category or a Manufacturer is selected,  or one of the Store Price fields has a value.</li>
<li>Leave the Store Price fields blank if you are not searching for products based on price.</li>
<li>Leave the Product Quantity field set to any if you are not searching for products based on quantity.</li>
<li>You may search using only <strong>one</strong> of the Store Price fields if you want to find all products with prices greater/less than a specific amount.</li>
<li>The decimal separator in the Store Price entries <strong>must</strong> be a \'.\' (decimal-point), example: <b>49.99</b></li></ul>
<h3>When copying Products as Duplicate (new) Products:</h3>
<ul><li>for Attributes with Downloads, the Download filename is NOT copied.</li>
<li>Product Specials can be optionally copied.</li>
<li>Product Featured can be optionally copied.</li>
<li>Product Discount Quantities can be optionally copied.</li>
<li>Music Products Media Manager collections can be optionally copied.</li>
<li>Reviews are NOT copied.</li></ul>
<h3>Deleting Products</h3>
<h4>Delete Products permanently from ALL Categories:</h4>
<ul><li>Deletions are permanent and cannot be undone</li>
<li>If the product Main Image is unique, it will be deleted, as will the Main Image Medium and Large Image. Additional Images and Additional Large Images will NOT be removed</li></ul>
<h4>Delete Linked Products permanently from ONE Category:</h4>
<ul><li>Deleting from ONE Category will unlink a Product from that Category.</li>
<li>If that Category is the master_categories_id, the master_categories_id will be reset.</li></ul>
<h3>Examples:</h3>
<p>To copy products as Duplicate (new) Products, from one Category to another, select the <strong>Copy or Move Found Products to Target Category</strong> and select the <strong>Search From Category</strong>. You can optionally include subcategories.<br></p>
<p>To search for Products, you can enter search words in the <strong>Find products matching the following terms</strong>; and, optionally select a Category to search from.<br>And base the search on:  Search On Product Name, Model & Manufacturer Only -or- Search In Product Descriptions Also.<br></p>
<p>To search based on Manufacturer, select a Manufacturer from the drop down; this can then be limited to a Category or by using the search terms.</p>');

//Results
define('TEXT_SEARCH_RESULT_CATEGORY', 'Search category: %s');
define('TEXT_SEARCH_RESULT_KEYWORDS', 'Search keywords: "%s"');
define('TEXT_SEARCH_RESULT_MANUFACTURER', 'Search manufacturer: %s');
define('TEXT_SEARCH_RESULT_MIN_PRICE', 'Search price > %s');
define('TEXT_SEARCH_RESULT_MAX_PRICE', 'Search price < %s');
define('TEXT_SEARCH_RESULT_QUANTITY', 'Search quantity < %u');
define('TEXT_SEARCH_RESULT_TARGET', 'Target Category: "%2$s" ID#%1$u');
define('TABLE_HEADING_SELECT', 'Selected');
define('TEXT_TOGGLE_ALL', 'Toggle All');
define('TABLE_HEADING_PRODUCTS_ID', 'ID');
if (strpos(PROJECT_VERSION_MINOR, '5.7') === false) define('TABLE_HEADING_MODEL', 'Model');//remove for ZC157 (in english.php)
define('TABLE_HEADING_IMAGE', 'Image');
define('TABLE_HEADING_STATUS', 'Status');
define('IMAGE_ICON_STATUS_ON_EDIT_PRODUCT', 'product is enabled -> Edit Product');
define('IMAGE_ICON_STATUS_OFF_EDIT_PRODUCT', 'product is disabled -> Edit Product');

define('TABLE_HEADING_LINKED', 'Linked');
define('IMAGE_ICON_LINKED_EDIT_LINKS', 'product is linked -> Edit in Link Manager');
define('IMAGE_ICON_NOT_LINKED_EDIT_LINKS', 'product is not linked -> Edit in Link Manager');


define('TABLE_HEADING_MASTER_CATEGORY', 'Master Category ID');
define('TABLE_HEADING_NAME', 'Name');
define('TABLE_HEADING_PRICE', 'Store Price');
define('TABLE_HEADING_QUANTITY', 'Quantity');
define('TABLE_HEADING_MFG', 'Manufacturer');



define('IMAGE_ICON_MASTER_CATEGORY_THIS', 'master category is ID#%u');
define('IMAGE_ICON_MASTER_CATEGORY_ELSE', 'master category is ID#%u');

define('BUTTON_RETRY', 'Modify Search');
define('BUTTON_CATEGORY_LISTING_SEARCH', 'Product Listing - Search Category');
define('BUTTON_CATEGORY_LISTING_TARGET', 'Product Listing - Target Category');
define('TEXT_EXISTING_PRODUCTS_NOT_SHOWN', 'Only matching products <strong>not already present</strong> in the Target Category are listed.');

//Results Delete

define('TEXT_DELETE_FROM_ONE', 'Category "%2$s" ID#%1$u');
define('TEXT_DISABLED', 'disabled');
define('BUTTON_NEW_SEARCH', 'New Search');
define('TEXT_NO_MATCHING_PRODUCTS_FOUND', 'No products were found that matched the search criteria or all matching products already exist in the target category.');
define('TEXT_PRODUCTS_COPIES', 'The following products were copied:');
define('TEXT_PRODUCTS_FOUND', '%u product(s) found.');
define('TEXT_PRODUCTS_COPIED_TO', '%1$u product(s) copied to Category ID#%2$u "%3$s"');
//Results Move
//Results Copy Linked

//Results Copy Duplicates
//these four constants used in copy_product_confirm
define('TEXT_COPY_AS_DUPLICATE_ATTRIBUTES', 'Attributes copied from Product ID#%1$u to duplicate Product ID#%2$u');
define('TEXT_COPY_AS_DUPLICATE_METATAGS', 'Metatags for Language ID#%1$u copied from Product ID#%2$u to duplicate Product ID#%3$u');
define('TEXT_COPY_AS_DUPLICATE_CATEGORIES', 'Linked Category ID#%1$u copied from Product ID#%2$u to duplicate Product ID#%3$u');
define('TEXT_COPY_AS_DUPLICATE_DISCOUNTS', 'Discounts copied from Product ID#%1$u to duplicate Product ID#%2$u');

define('TEXT_COPY_AS_DUPLICATE_SPECIALS', 'Special price copied from Product ID#%1$u to duplicate Product ID#%2$u');
define('TEXT_COPY_AS_DUPLICATE_FEATURED', 'Featured settings copied from Product ID#%1$u to duplicate Product ID#%2$u');

//Not reviewed yet
/////////////////////////////////////////////////////////////
define('TEXT_SELECT_PRODUCTS', 'Please make sure that only those products that should be COPIED have the checkbox marked.');
define('TEXT_SELECT_PRODUCTS_DELETED', 'Please make sure that only those products that should be DELETED have the checkbox marked.<br><span class="alert">WARNING: Selected Products will be completely deleted and cannot be recovered!</span><br><br>');
define('TEXT_SELECT_PRODUCTS_DELETE_ONE', 'Please make sure that only those products that should be DELETED have the checkbox marked.
<br><span class="alert">WARNING: Selected Products will be deleted from ONE Category. If the Products use this Category as their master_categories_id, the master_categories_id will be reset to the next available Category for the Products.</span><br>
<strong>*** Products that are not Linked Products cannot be Deleted from this ONE Category. To Delete them completely, use the other Delete option to delete Products completely.</strong><br><br>');
define('TEXT_SELECT_PRODUCTS_DELETE_SPECIALS', 'Please make sure that only those products that should have the Specials DELETED have the checkbox marked.<br>Only Products with Specials are actually listed.<br>If the Special does not display, then the Special is not enabled.<br><br>');
define('HEADING_SELECT_PRODUCT', 'Select Products To Copy');
define('HEADING_SELECT_PRODUCT_DELETED', 'Select Products To Delete');

define('HEADING_SELECT_PRODUCT_DELETE_SPECIALS', 'Select Products To Delete Specials from ');

define('ENTRY_COPY_TO_DUPLICATE', '<strong>Copy Found Products as Duplicate (new) products to Category:</strong> ');
define('ENTRY_COPY_TO_LINK', '<strong>Copy Found Products as Linked Products to Category:</strong> ');
define('ENTRY_DELETED', 'Delete Found Products: ');
define('ENTRY_DELETE_ONE', 'Delete Found Products from ONE Category: ');
define('ENTRY_DELETE_SPECIALS', 'Delete Specials from Found Products: ');

define('TEXT_COPY_ATTRIBUTES_YES', 'Yes');
define('TEXT_COPY_ATTRIBUTES_NO', 'No');

define('TEXT_NOT_FOUND', 'No products were found to match the search terms given. Or, Products are already in this Category.');


define('TEXT_DETAILS', 'Copy Details:');
define('TEXT_DETAILS_LINK', 'Copy as Linked Products Details:');
define('TEXT_DETAILS_DELETED', 'Delete Products Details: ');
define('TEXT_DETAILS_DELETE_ONE', 'Delete Products from ONE Category Details: ');
define('TEXT_DETAILS_DELETE_SPECIALS', 'Delete Specials from Products Details: ');
define('BUTTON_GO_TO_CATEGORY', 'View Category Listing: %2$s (#%1$u)');
define('BUTTON_ANOTHER_COPY', ' Return (Copy/Delete more products)');
define('HEADING_PRODUCT_COPIED','Products That Were Copied To The Target Category');




define('TEXT_WARNING_CATEGORY_SUB', '<strong>Warning:</strong> Target Category holds subcategories!<br>Categories should hold either Categories or Products, not both.<br>Products should NOT be Linked or Copied to Categories with subcategories.');

define('TEXT_MOVE_PRODUCTS_CATEGORIES', 'NOTE: You have selected Products from multiple categories ...<br> This will result in Products being moved from their Master Category to the NEW Category with the NEW Category being set as the Master Category ID');

define('HEADING_SELECT_PRODUCT_MOVE_FROM', 'Select Products To Move');

// Errors
define('ERROR_ILLEGAL_OPTION', 'Invalid option/no option set.');
define('ERROR_NO_TARGET_CATEGORY', 'No Target Category selected!');
define('ERROR_TARGET_CATEGORY_HAS_SUBCATEGORY', 'Copy/Move not allowed: Target Category "%2$s" ID#%1$u contains a subcategory');
define('ERROR_SEARCH_CATEGORY_HAS_SUBCATEGORY', 'Copy/Move not allowed: Search Category "%2$s" ID#%1$u contains a subcategory');
define('ERROR_SAME_CATEGORIES', 'The Search and Target Categories are the same: "%2$s" ID#%1$u!');
define('ERROR_NO_PRODUCTS_IN_CATEGORY', 'No products found in category "%2$s" ID#%1$u');
define('ERROR_OR_SUBS', ', or subcategories.');
define('ERROR_INVALID_KEYWORDS', 'Invalid keywords');
define('ERROR_NO_PRODUCTS_FOUND', 'No products found in "%2$s" ID#%1$u');
define('ERROR_ENTRY_REQUIRED', 'No Search critera set! Set a Search category / keyword / manufacturer / price field.');
define('ERROR_ARRAY_COUNTS', 'Array of products found and array count not equal.');
define('ERROR_NO_SELECTION', 'No products selected. At least one product from the list must be selected!');
define('ERROR_CHECKBOXES_NOT_ARRAY', 'Selected checkboxes not an array.');
define('ERROR_CHECKBOX_ID', 'A selected checkbox references product ID#%u. This is not a found product ID!');
define('ERROR_COPY_DUPLICATE_NO_DUP_ID', 'The duplicate/new product ID was not returned from "copy_product_confirm.php" for copy-duplicate of product ID#%1$u to category ID#%2$u.');

//not seen
define('ERROR_UNEXPLAIN', 'Unexpected and unexplainable form validation error found! Please try again.');

define('ERROR_NO_LOCATION_DELETE_ONE', 'You failed to specify the specific category from which the products are supposed to be deleted!');

define('ERROR_SAME_LOCATION_DELETE_ONE', 'The category to delete products from is not selected!');
define('ERROR_NOT_FOUND', 'Destination category was not found!');
define('ERROR_NAME_NOT_FOUND', 'Unexpected error! A name for the Target Category was not found!');
