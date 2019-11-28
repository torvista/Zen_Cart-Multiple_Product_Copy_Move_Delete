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
 * language definitions
*/

define('HEADING_TITLE', "Multi-Product Copy Utility");
define('ERROR_ENTRY_REQUIRED', 'Entry is missing! Either at least one search term must be entered or else the category or manufacturer drop down must be set to a value or one of the Price fields must have a numeric value!');
define('HEADING_NEW', 'New Copy as Duplicate, Copy as Linked or Delete');
define('TEXT_TIPS', '<b>Notes:</b><ul>
<li>Products already listed in the destination category are automatically excluded from the search.</li>
<li>Search terms may be left blank if either a category or a manufacturer is selected or one of the Store Price fields has an entry.</li>
<li>Leave the Store Price entries blank if you are not searching for products based on price.</li>
<li>Leave the Product Quantity set to any if you are not searching for products based on quantity.</li>
<li>You may search using only one of the Store Price entries if you want to find all products with prices greater/less than an amount.</li>
<li>The decimal separator in the Store Price entries must be a \'.\' (decimal-point), example: <b>49.99</b></li>
<br />
<b>When copying selected Products as NEW Duplicate Products:</b>
<li>Attributes with Downloads, the Download filename is NOT copied.</li>
<li>Product Specials can be optionally copied.</li>
<li>Product Featured can be optionally copied.</li>
<li>Product Discount Quantities can be optionally copied.</li>
<li>Music Products Media Manager collections can be optionally copied.</li>
<li>Reviews are NOT copied.</li>
<br />
<b>When Deleting Products:</b>
<li>Deletions are permanent and cannot be undone</li>
<li>If the Main Image is unique, it will be removed, as will the Main Image Medium and Large Image</li>
<li>Additional Images and Additional Large Images will NOT be removed</li>
</ul>
<br /><br />
<strong>Examples:</strong><br />
To copy as NEW Duplicate Products from one Category to another, select the <strong>Copy or Move Found Products to Destination Category</strong> and select the <strong>Search From Category</strong>; You can optionally include subcategories.<br /><br />
To search for Products, you can enter search words in the <strong>Find products matching the following terms</strong>; and, optionally select a Category to search from.<br />And base the search on:  Search On Product Name, Model & Manufacturer Only -or- Search In Product Descriptions Also.<br /><br />
To search based on Manufacturer, select a Manufacturer from the drop down; this can then be limited to a Category or by using the search terms.
');

define('TEXT_SELECT_PRODUCTS', 'Please make sure that only those products that should be COPIED have the checkbox marked.');
define('TEXT_SELECT_PRODUCTS_DELETED', 'Please make sure that only those products that should be DELETED have the checkbox marked.<br /><span class="alert">WARNING: Selected Products will be completely deleted and cannot be recovered!</span>');
define('HEADING_SELECT_PRODUCT', 'Select Products To Copy');
define('HEADING_SELECT_PRODUCT_DELETED', 'Select Products To Delete');
define('TEXT_ENTER_CRITERIA', 'SEARCH CRITERIA:');
define('TEXT_ENTER_TERMS', 'Find products matching the following terms:');
define('TEXT_PRODUCTS_MANUFACTURER', 'Manufacturer: ');
define('TEXT_ANY_MANUFACTURER', 'any manufacturer');
define('TEXT_PRODUCTS_CATEGORY', 'Search From Category: ');
define('TEXT_ANY_CATEGORY', 'any category');
define('TEXT_SELECT', '--Please Select--');
define('ENTRY_COPY_TO', '<strong>Copy or Move Found Products to Destination Category:</strong> ');
define('ENTRY_COPY_TO_NOTE', ' <strong>*</strong> not used for Deleting Products');
define('ENTRY_DELETE_TO_NOTE', ' <strong>*</strong> used for Deleting Products');
define('ENTRY_COPY_TO_DUPLICATE', '<strong>Copy Found Products as NEW Duplicate products to Category:</strong> ');
define('ENTRY_COPY_TO_LINK', '<strong>Copy Found Products as Linked Products to Category:</strong> ');
define('ENTRY_DELETED', 'Delete Found Products: ');
define('ENTRY_AUTO_CHECK', '<strong>Automatically checkmark Found Products:</strong> ');

define('TEXT_HOW_TO_COPY', '<strong>How do you want to manage Found Products?</strong>');
define('TEXT_COPY_AS_LINK', 'Copy as Linked Products');
define('TEXT_COPY_AS_DUPLICATE', 'Copy as NEW Duplicate Products');
define('TEXT_COPY_AS_DELETED', 'Delete Products permanently from ALL Categories?');
define('TEXT_COPYING_DUPLICATES', '&nbsp;&nbsp&nbsp;&nbsp<strong>When creating NEW Duplicate Products:</strong>');
define('TEXT_COPY_ATTRIBUTES', '&nbsp;&nbsp&nbsp;&nbsp' . 'Copy existing Attributes?');
define('TEXT_COPY_ATTRIBUTES_YES', 'Yes');
define('TEXT_COPY_ATTRIBUTES_NO', 'No');
define('TEXT_COPY_SPECIALS','&nbsp;&nbsp&nbsp;&nbsp' . 'Copy any existing Special Prices associated with selected products:');
define('TEXT_COPY_FEATURED','&nbsp;&nbsp&nbsp;&nbsp' . 'Copy any existing Featured settings associated with selected products:');
define('TEXT_COPY_DISCOUNTS','&nbsp;&nbsp&nbsp;&nbsp' . 'Copy any existing Quantity Discounts associated with selectedproducts:');
define('TEXT_COPY_MEDIA_MANAGER','&nbsp;&nbsp&nbsp;&nbsp' . 'Copy any Media Manager collections associated with selected products:');

define('TEXT_YES', 'Yes');
define('TEXT_NO', 'No');
define('TEXT_NAME_ONLY', 'Search On Product Name, Model & Manufacturer Only');
define('TEXT_DESCRIPTIONS', 'Search In Product Descriptions Also');
define('TEXT_NOT_FOUND', 'No products were found to match the search terms given.');
define('ERROR_INVALID_KEYWORDS', 'Invalid keywords.');
define('TABLE_HEADING_SELECT', 'Selected');
define('TABLE_HEADING_PRODUCTS_ID', 'ID#');
define('TABLE_HEADING_NAME', 'Product Name');
define('TABLE_HEADING_MFG', 'Manufacturer');
define('TABLE_HEADING_MODEL', 'Product Model');
define('TABLE_HEADING_PRICE', 'Store Price');
define('BUTTON_RETRY', 'Try Again');
define('TEXT_DETAILS', 'Copy Details:');
define('TEXT_DETAILS_LINK', 'Copy as Linked Products Details:');
define('TEXT_DETAILS_DELETED', 'Delete Products Details: ');
define('BUTTON_PRODUCT_ENTRY', 'Return to Catalog Entry');
define('BUTTON_ANOTHER_COPY', ' Copy More Products');
define('HEADING_PRODUCT_COPIED','Products That Were Copied To The Destination Category');
define('ERROR_NONE_VALID', 'No products were found that could be copied!');
define('ERROR_UNEXPLAIN', 'Unexpected and unexplainable form validation error found! Please try again.');
define('ERROR_NOT_SELECTED', 'At least one product from the list must be selected to copy!');
define('ENTRY_MIN_PRICE', 'Store Price At Least: ');
define('ENTRY_MAX_PRICE', 'Store Price Not More Than: ');
define('ENTRY_PRODUCT_QUANTITY', ' Product Quantity <= ');
define('TEXT_OPTIONAL', ' Optional');
define('TEXT_CHANGES_MADE', 'The following products were copied:');
define('ERROR_NO_LOCATION', 'You failed to specify the category to which the products are supposed to be copied!');
define('ERROR_SAME_LOCATION', 'The category to which the products are supposed to be copied is the same as the category from which they are to be copied!');
define('ERROR_NOT_FOUND', 'Destination category was not found!');
define('ERROR_NAME_NOT_FOUND', 'Unexpected error! A name for the destination category was not found!');
define('TEXT_PRODUCTS_FOUND', ' Products Found');
define('TEXT_PRODUCTS_COPIED', ' Products Copied');
define('TEXT_ONLY_NOT_DEST', 'Only matching products not already linked to the destination category are listed.');
define('ENTRY_INC_SUBCATS', 'include subcategories');

define('TEXT_ATTRIBUTE_COPY_INSERTING','<strong>Inserting New Attribute from </strong>');
define('TEXT_WARNING_CATEGORY_SUB', '<strong>Warning:</strong> Destination Category holds subcategories!<br />Categories should hold either Categories or Products, not both.<br />Products should NOT be Linked or Copied to Categories with subcategories.');

define('TEXT_DUPLICATE_ATTRIBUTES', 'Copy Attributes: ');
define('TEXT_DUPLICATE_SPECIALS', 'Copy Special Prices: ');
define('TEXT_DUPLICATE_FEATURED', 'Copy Featured settings: ');
define('TEXT_DUPLICATE_QUANTITY_DISCOUNTS', 'Copy Quantity Discounts: ');
define('TEXT_DUPLICATE_MEDIA', 'Copy Media Components: ');

define('TEXT_MOVE_FROM_LINK', 'Move Products to another Category');
define('TEXT_MOVE_PRODUCTS_CATEGORIES', 'NOTE: You have selected Products from multiple categories ...<br /> This will result in Products being moved from their Master Category to the NEW Category with the NEW Category being set as the Master Category ID');
define('TEXT_MOVE_PRODUCTS_INFO', '<strong>
&nbsp;&nbsp&nbsp;&nbsp;&nbsp;When Moving Products, if you do not pick the Search From Category,<br />
&nbsp;&nbsp&nbsp;&nbsp;&nbsp;then the Products will be moved from their current Master Category ID<br />
&nbsp;&nbsp&nbsp;&nbsp;&nbsp;to the New Category which will then be set as the new Master Category ID:</strong>');
define('HEADING_SELECT_PRODUCT_MOVE_FROM', 'Select Products To Move');
