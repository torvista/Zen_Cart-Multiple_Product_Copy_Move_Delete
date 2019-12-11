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
define('ERROR_ENTRY_REQUIRED', 'Entry is missing! Either at least one search term must be entered or else the category or manufacturer drop down must be set to a value or one of the Price fields must have a numeric value!');
define('TEXT_HOW_TO_COPY', '<strong>How do you want to manage Found Products?</strong>');

// Actions
define('TEXT_COPY_AS_DELETED', 'Delete Products permanently from ALL Categories?');
define('TEXT_COPY_AS_DELETE_ONE', 'Delete Linked Products permanently from ONE Category?');
define('TEXT_COPY_AS_DELETE_SPECIALS', 'Delete Specials from Products?');
define('TEXT_MOVE_PRODUCTS_INFO', 'When Moving Products, if you do not select a specific category from the "Search In Category" dropdown, then all found products will be moved/have their Master Category ID reset to the Target Category.');

define('TEXT_MOVE_FROM_LINK', 'Move Products to the Target Category');

define('TEXT_COPY_AS_LINK', 'Copy as Linked Products');
define('TEXT_COPY_AS_DUPLICATE', 'Copy as Duplicate (new) Products');
define('TEXT_COPYING_DUPLICATES', 'When creating Duplicate (new) Products:');
define('TEXT_COPY_ATTRIBUTES', 'Copy existing Attributes?');
define('TEXT_COPY_METATAGS', 'Copy existing Meta Tags?');
define('TEXT_COPY_SPECIALS', 'Copy any existing Special Prices associated with selected products:');
define('TEXT_COPY_FEATURED', 'Copy any existing Featured settings associated with selected products:');
define('TEXT_COPY_DISCOUNTS', 'Copy any existing Quantity Discounts associated with selected products:');
define('TEXT_COPY_MEDIA_MANAGER', 'Copy any Media Manager collections associated with selected products:');

define('ENTRY_COPY_TO', '<strong>Copy or Move Found Products to Target Category:</strong> ');
define('ENTRY_COPY_TO_NOTE', '(<strong>* not</strong> used for Deleting Products)');

// Search
define('TEXT_ENTER_CRITERIA', 'SEARCH CRITERIA:');
define('TEXT_PRODUCTS_CATEGORY', 'Search In Category:');
define('TEXT_ANY_CATEGORY', 'any category');
define('ENTRY_INC_SUBCATS', 'include subcategories');
define('ENTRY_DELETE_TO_NOTE', ' (<strong>* used</strong> for Deleting Products)');
define('TEXT_ENTER_SEARCH_KEYWORDS', 'Find products containing the following keywords:');
define('TEXT_NAME_ONLY', 'Search in product Name, Model & Manufacturer only');
define('TEXT_DESCRIPTIONS', 'Search In Product Descriptions Also');
define('TEXT_PRODUCTS_MANUFACTURER', 'Manufacturer: ');
define('TEXT_ANY_MANUFACTURER', 'any manufacturer');
define('TEXT_OPTIONAL', ' Optional');
define('ENTRY_MIN_PRICE', 'Store (displayed) Price &gt;=: ');
define('ENTRY_MAX_PRICE', 'Store (displayed) Price &lt;=: ');
define('ENTRY_MAX_PRODUCT_QUANTITY', ' Product Quantity &lt;= ');
define('ENTRY_AUTO_CHECK', 'Automatically select matching products:');

define('TEXT_TIPS', '<b>Notes:</b>
<ul><li>Products currently existing in the Target Category are automatically excluded from the search (results).</li>
<li>Search terms may be left blank if either a Category or a Manufacturer is selected,  or one of the Store Price fields has a value.</li>
<li>Leave the Store Price fields blank if you are not searching for products based on price.</li>
<li>Leave the Product Quantity field set to any if you are not searching for products based on quantity.</li>
<li>You may search using only <strong>one</strong> of the Store Price fields if you want to find all products with prices greater/less than a specific amount.</li>
<li>The decimal separator in the Store Price entries <strong>must</strong> be a \'.\' (decimal-point), example: <b>49.99</b></li></ul>
<b>When copying Products as Duplicate (new) Products:</b>
<ul><li>for Attributes with Downloads, the Download filename is NOT copied.</li>
<li>Product Specials can be optionally copied.</li>
<li>Product Featured can be optionally copied.</li>
<li>Product Discount Quantities can be optionally copied.</li>
<li>Music Products Media Manager collections can be optionally copied.</li>
<li>Reviews are NOT copied.</li></ul>
<b>When Deleting Products:</b><br>
<b>Delete Products permanently from ALL Categories?</b>
<ul><li>Deletions are permanent and cannot be undone</li>
<li>If the product Main Image is unique, it will be deleted, as will the Main Image Medium and Large Image. Additional Images and Additional Large Images will NOT be removed</li></ul>
<b>Delete Linked Products permanently from ONE Category?</b>
<ul><li>Deleting from ONE Category will unlink a Product from that Category.</li>
<li>If that Category is the master_categories_id, the master_categories_id will be reset.</li></ul>
<strong>Examples:</strong>
<p>To copy products as Duplicate (new) Products, from one Category to another, select the <strong>Copy or Move Found Products to Target Category</strong> and select the <strong>Search From Category</strong>. You can optionally include subcategories.<br></p>
<p>To search for Products, you can enter search words in the <strong>Find products matching the following terms</strong>; and, optionally select a Category to search from.<br>And base the search on:  Search On Product Name, Model & Manufacturer Only -or- Search In Product Descriptions Also.<br></p>
<p>To search based on Manufacturer, select a Manufacturer from the drop down; this can then be limited to a Category or by using the search terms.</p>');

//Results
define('ERROR_NO_TARGET_CATEGORY', 'No target category selected!');

//Results Delete
//Results Move
//Results Copy Linked

//Results Copy Duplicates

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
define('HEADING_SELECT_PRODUCT_DELETE_ONE', 'Select Products To Delete from ');
define('HEADING_SELECT_PRODUCT_DELETE_SPECIALS', 'Select Products To Delete Specials from ');

define('ENTRY_COPY_TO_DUPLICATE', '<strong>Copy Found Products as Duplicate (new) products to Category:</strong> ');
define('ENTRY_COPY_TO_LINK', '<strong>Copy Found Products as Linked Products to Category:</strong> ');
define('ENTRY_DELETED', 'Delete Found Products: ');
define('ENTRY_DELETE_ONE', 'Delete Found Products from ONE Category: ');
define('ENTRY_DELETE_SPECIALS', 'Delete Specials from Found Products: ');

define('TEXT_COPY_ATTRIBUTES_YES', 'Yes');
define('TEXT_COPY_ATTRIBUTES_NO', 'No');

define('TEXT_NOT_FOUND', 'No products were found to match the search terms given. Or, Products are already in this Category.');
define('ERROR_INVALID_KEYWORDS', 'Invalid keywords.');
define('TABLE_HEADING_SELECT', 'Selected');
define('TABLE_HEADING_PRODUCTS_ID', 'ID#');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_LINKED', 'Linked');
define('TABLE_HEADING_NAME', 'Product Name');
define('TABLE_HEADING_MFG', 'Manufacturer');
define('TABLE_HEADING_MODEL', 'Product Model');//remove for ZC157 (in english.php)
define('TABLE_HEADING_PRICE', 'Store Price');
define('BUTTON_RETRY', 'Try Again');
define('TEXT_DETAILS', 'Copy Details:');
define('TEXT_DETAILS_LINK', 'Copy as Linked Products Details:');
define('TEXT_DETAILS_DELETED', 'Delete Products Details: ');
define('TEXT_DETAILS_DELETE_ONE', 'Delete Products from ONE Category Details: ');
define('TEXT_DETAILS_DELETE_SPECIALS', 'Delete Specials from Products Details: ');
define('BUTTON_GO_TO_CATEGORY', 'View Category Listing: %2$s (#%1$u)');
define('BUTTON_ANOTHER_COPY', ' Return (Copy/Delete more products)');
define('HEADING_PRODUCT_COPIED','Products That Were Copied To The Target Category');
define('ERROR_NONE_VALID', 'No products were found that could be copied!');
define('ERROR_UNEXPLAIN', 'Unexpected and unexplainable form validation error found! Please try again.');
define('ERROR_NOT_SELECTED', 'At least one product from the list must be selected to copy!');

define('TEXT_CHANGES_MADE', 'The following products were copied:');



define('ERROR_NO_LOCATION_DELETE_ONE', 'You failed to specify the specific category from which the products are supposed to be deleted!');
define('ERROR_SAME_LOCATION', 'The category to which the products are supposed to be copied is the same as the category from which they are to be copied!');
define('ERROR_SAME_LOCATION_DELETE_ONE', 'The category to delete products from is not selected!');
define('ERROR_NOT_FOUND', 'Destination category was not found!');
define('ERROR_NAME_NOT_FOUND', 'Unexpected error! A name for the Target Category was not found!');
define('TEXT_PRODUCTS_FOUND', ' Products Found');
define('TEXT_PRODUCTS_COPIED', ' Products Copied');
define('TEXT_ONLY_NOT_DEST', 'Only matching products not already linked to the Target Category are listed.');

//define('TEXT_ATTRIBUTE_COPY_INSERTING','<strong>Inserting New Attribute from </strong>');//removed in ZC157
define('TEXT_WARNING_CATEGORY_SUB', '<strong>Warning:</strong> Target Category holds subcategories!<br>Categories should hold either Categories or Products, not both.<br>Products should NOT be Linked or Copied to Categories with subcategories.');

define('TEXT_DUPLICATE_ATTRIBUTES', 'Copy Attributes: ');
define('TEXT_DUPLICATE_SPECIALS', 'Copy Special Prices: ');
define('TEXT_DUPLICATE_FEATURED', 'Copy Featured settings: ');
define('TEXT_DUPLICATE_QUANTITY_DISCOUNTS', 'Copy Quantity Discounts: ');
define('TEXT_DUPLICATE_MEDIA', 'Copy Media Components: ');

define('TEXT_MOVE_PRODUCTS_CATEGORIES', 'NOTE: You have selected Products from multiple categories ...<br> This will result in Products being moved from their Master Category to the NEW Category with the NEW Category being set as the Master Category ID');

define('HEADING_SELECT_PRODUCT_MOVE_FROM', 'Select Products To Move');

//define('HEADING_NEW', 'New Copy as Duplicate, Copy as Linked or Delete');
