# Zen-Cart_Multiple-Products-Copy-Move-Delete

Based on
https://www.zen-cart.com/showthread.php?180447-Multiple-Products-(Copy-Move-Delete)

-----------------------------

2020 02 28 Current Status: tested on php 7.3.11 with Zen Cart 1.5.7
NOTE: Copy-Duplicate of meta-tags requires the ZC157 version of /modules/products_copy_confirm.php

USE AT OWN RISK - TEST THOROUGHLY ON YOUR DEVELOPMENT SERVER

Please report all bugs on Github.

The plan is to submit as a core file for Zen Cart 1.5.7...

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
DO NOT TEST ON YOUR PRODUCTION SERVER!!!!
DO NOT TEST ON YOUR PRODUCTION SERVER!!!!
DO NOT TEST ON YOUR PRODUCTION SERVER!!!!

0) Remove the previous version of this mod as per that version's instructions.

1) Copy the files in:
/ADMIN FOLDER
to YOUR admin directory.
All are new files/no core files should be overwritten.

2) Refresh the Admin screen.

The menu entry should be added to the Admin->Catalog section aut0matically.

3) Required File Modifications: 2019 THIS SECTION IS UNDER REVIEW

Multiple Product copy makes use of three core module files for copy and move. These files (normally) operate on one product/run once and then redirect to the category listing page.
To allow them to be be run multiple times (for multiple copy/move) and return back to multiple_product_copy, a clause needs to be added around the redirect at the end of each file.

For example:
Locate the line at the bottom of the file:

        zen_redirect(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $categories_id . '&pID=' . $products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));

and change to read:

// bof: change for multiple_product_copy
        if ($action != 'multiple_product_copy_return') {
          zen_redirect(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $categories_id . '&pID=' . $products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
        }
// eof: change for multiple_product_copy


Three modifications are required.

You will find the examples of these changes in these files:
/ADMIN FOLDER/includes/modules/copy_to_confirm.php_multiple_product_copy
/ADMIN FOLDER/includes/modules/move_product_confirm.php_multiple_product_copy
/ADMIN FOLDER/includes/modules/product_music/copy_to_confirm.php_multiple_product_copy

Compare them to the core files and merge in the changes at the end of each file.

If you are using other custom files on your Product Types for the files:
copy_to_confirm.php
move_product_confirm.php

you will need to add the similar IF statement around the redirect on the last line of the file. 

4) Uninstall
a) Use the backup you kept of this installation fileset to compare and delete the files from your site.
b) Copy, paste and run the uninstall SQL in the Admin->SQLpatch tool or phpMyAdmin to remve the admin menu page item.


---------------------
Changelog
2020 05 15 torvista: update of main file and removal of extra functions
2020 02 28 torvista: bugfix to allow language selector dropdown
2020 01 torvista: complete revision
remove table layout
conversion to html with embedded php
expanded error checking for search critera

Copy-Duplicate
Allow copy-duplicate to the same category
Added duplication of meta tags
Removed duplication of media collection (original version showed option, but was not implemented in code)

added option show images

Results page 2
check/uncheck all selectboxes with javascript

strict comparisons
html validation

2019 10 torvista: put on Github.

2013 Changes to v1392
Added Delete from Linked Category. Added Delete Specials

2011 Multi-Product Copy Utility for Zen Cart ver 1.392 by Linda McGrath 2011-11-15

Originally written for osCommerce as:
Multi-Product Copy Utility v1.1 by Kevin L. Shelton 7/28/2010



