# Zen-Cart_Multiple-Products-Copy-Move-Delete

Based on
https://www.zen-cart.com/showthread.php?180447-Multiple-Products-(Copy-Move-Delete)

Current Status:
Fileset has not (yet) been prepared as a complete plugin, intended as a quick and dirty solution for users who already have this installed.
Apparently it is working on php 7.3.10 and Zen Cart 1.5.6. but NOT TESTED.

USE AT OWN RISK - TEST THOROUGHLY ON YOUR DEVELOPMENT SERVER

Please report all bugs here.

The plan is to clean this up and submit to Zen Cart 1.5.7, time permitting....

-----------------------------
ORIGINAL README:

Originally written for osCommerce as:
Multi-Product Copy Utility v1.1 by Kevin L. Shelton 7/28/2010


Multi-Product Copy Utility for Zen Cart ver 1.392 by Linda McGrath 2011-11-15

Changes to v1392
Added Delete from Linked Category
Added Delete Specials


This utility allows you to quickly:
Copy multiple products to another category at one time as Linked or New Products. 
Or, to Delete multiple products at the same time. 
Or, to Move multiple products at the same time.

Choose the destination category from the list and search for products to copy by keyword, category, manufacturer or price. 

You will be presented with a list of products matching your search terms. Products that are already linked to the destination category will not be listed. 

Look over the list and check the boxes for any product that should be copied; or uncheck the boxes of products that should not be copied if you had the utility automatically check the boxes for you. 

Click the Confirm button and all of the checked products will be now linked as Linked Products or Copied as New Duplicate Products to the destination category and a list of products copied will be displayed. 

Clicking the "Return To Catalog Entry" button after the copy is done will place you in the category you chose as the destination for the copy so you can check your work.

Instructions:

All new files can be loaded without overwritting any of the original files.


============================================================================

NOTE: for Zen Cart v1.5

This file is not used and does not need to be loaded to the server:
/admin/includes/boxes/extra_boxes/multiple_product_copy_catalog_dhtml.php

Instead, install the file:

/v15 SQL/multiple_product_copy.sql

============================================================================

NOTE: this file is not used for Zen Cart v1.3.9h

/v15 SQL/multiple_product_copy.sql

============================================================================


Copy the files in:
/admin

to your admin directory.


Required File Modifications:

Three file modifications are requried.

You will find the examples of these changes in these files:
/admin/includes/modules/copy_to_confirm.php_multiple_product_copy
/admin/includes/modules/move_product_confirm.php_multiple_product_copy
/admin/includes/modules/product_music/copy_to_confirm.php_multiple_product_copy


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
---------------------
Changelog

2019 10 torvista: put on Github.
2011 v1391: made public in above-reference thread.



