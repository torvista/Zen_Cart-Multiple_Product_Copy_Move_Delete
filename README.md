# Zen-Cart_Multiple-Products-Copy-Move-Delete

Based on
https://www.zen-cart.com/showthread.php?180447-Multiple-Products-(Copy-Move-Delete)

-----------------------------
2019 12 Current Status: being used on php 7.3.11 with Zen Cart 1.5.6
copy as link: tested and working
copy as duplicate: not working for multiple product duplication, specials, featured
move: not tested
delete optiones: not tested

This fileset is CURRENTLY being revised and heavily modified. It is "probably" still working but is not tested!

USE AT OWN RISK - TEST THOROUGHLY ON YOUR DEVELOPMENT SERVER

Please report all bugs on Github ONLY for the functions listed above as tested and working

The plan is to clean this up and submit as a core file for Zen Cart 1.5.7...

-----------------------------
This utility allows you to perform multiple actions in one process.

Copy (as linked or duplicate) multiple products from one category to another. 
Delete multiple products. 
Move multiple products.

How to Use
Choose the destination category from the list, and search for products to copy/delete by keyword, category, manufacturer or price. 

You will be presented with a list of products matching your search terms.
Products that are already linked to the destination category will not be listed. 

Look over the list and tick the boxes for any product that should be copied, (or uncheck the boxes of products that should not be copied if you had the utility automatically check the boxes for you). 

Click the Confirm button and all of the checked products will be now linked as Linked (or Duplicated) products in the destination category and a list of products copied will be displayed. 

Clicking the "Return To Catalog Entry" button after the copy is done will open the destination category for review.

Install
1) Copy the files in:
/ADMIN FOLDER
to YOUR admin directory.
All are new files/no core files should be overwritten.

2) Copy, paste and run the install SQL.

The menu entry should be added to the Admin->Catalog section.

3) Required File Modifications: 2019 THIS SECTION IS UNDER REVIEW

Three modifications are required to core files.

You will find the examples of these changes in these files:
/ADMIN FOLDER/includes/modules/copy_to_confirm.php_multiple_product_copy
/ADMIN FOLDER/includes/modules/move_product_confirm.php_multiple_product_copy
/ADMIN FOLDER/includes/modules/product_music/copy_to_confirm.php_multiple_product_copy

Files to change:
/admin/includes/modules/copy_to_confirm.php

Locate the line at the bottom of the file:

        zen_redirect(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $categories_id . '&pID=' . $products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));

and change to read:

// bof: change for multiple_product_copy
        if ($action != 'multiple_product_copy_return') {
          zen_redirect(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $categories_id . '&pID=' . $products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
        }
// eof: change for multiple_product_copy


/admin/includes/modules/product_music/copy_to_confirm.php

Locate the line at the bottom of the file:

        zen_redirect(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $categories_id . '&pID=' . $products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));

and change to read:

// bof: change for multiple_product_copy
        if ($action != 'multiple_product_copy_return') {
          zen_redirect(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $categories_id . '&pID=' . $products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
        }
// eof: change for multiple_product_copy


Files to change:
/admin/includes/modules/move_product_confirm.php

Locate the line at the bottom of the file:

          zen_redirect(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $new_parent_id . '&pID=' . $products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));

and change to read:

// bof: change for multiple_product_copy
        if ($action != 'multiple_product_copy_return') {
          zen_redirect(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $new_parent_id . '&pID=' . $products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
        }
// eof: change for multiple_product_copy


If you are using other custom files on your Product Types for the files:
copy_to_confirm.php
move_product_confirm.php

you will need to add the similar IF statement around the redirect on the last line of the file. 

4) Uninstall
a) Copy, paste and run the uninstall SQL in the Admin->SQLpatch tool or phpMyAdmin.
b) Use the backup you kept of this fileset to compare and delete the files copied as part of the installation process.


---------------------
Changelog

2019 12 torvista: complete revision
remove table layout
strict comparisons
conversion to html with embedded php
html validation
expanded error checking for search critera

Copy-Link

Copy-Duplicate
Allow copy-duplicate to the same category
Added duplication of meta tags
Removed duplication of media collection (original version showed option, but was not implemented in code)

Move

Delete

Results
check/uncheck all selectboxes with javascript
added option show images

2019 10 torvista: put on Github.

2013 Changes to v1392
Added Delete from Linked Category. Added Delete Specials

2011 Multi-Product Copy Utility for Zen Cart ver 1.392 by Linda McGrath 2011-11-15

Originally written for osCommerce as:
Multi-Product Copy Utility v1.1 by Kevin L. Shelton 7/28/2010



