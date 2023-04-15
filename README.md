# Multiple Products-Copy-Move-Delete for Zen Cart

This awesome utility allows you to perform multiple actions in one process.

* Copy (as linked or duplicate) multiple products from one category to another
* Delete multiple products
* Move multiple products

Based on
https://www.zen-cart.com/showthread.php?180447-Multiple-Products-(Copy-Move-Delete)

-----------------------------
2020 11 04 Current Status: tested on php 7.3.11 with Zen Cart 157a

USE AT OWN RISK - TEST THOROUGHLY ON YOUR DEVELOPMENT SERVER.

Please report all bugs on GitHub.

-----------------------------

## How to Use

1. Choose an action.
1. Choose a target/destination category (not for delete)
1. Search for products to copy/delete by keyword, category, manufacturer or price.

You will be presented with a list of products matching your search terms.
Products that are already linked to the destination category will not be listed. 

Look over the list and tick the boxes for any product that should be copied, (or uncheck the boxes of products that should not be copied if you had the utility automatically check the boxes for you). 

Click the Confirm button and all the checked products will be now linked as Linked (or Duplicated) products in the destination category and a list of products copied will be displayed. 

Clicking the "Return To Catalog Entry" button after the copy is done will open the destination category for review.

## Installation
TEST ON YOUR DEVELOPMENT SERVER FIRST!

1. Remove any previous version of this mod as per that version's instructions.

1. Copy the files in "ADMIN FOLDER" to YOUR admin directory.
All are new files/no core files should be overwritten.

1. Refresh the Admin screen.
The menu entry should be added to the Admin->Catalog section automatically.
1. REQUIRED CORE FILE MODIFICATIONS

    Multiple Product copy makes use of two core module files for copy and move. These files are normally used for one product/ are only run once and then redirect to the category listing page.

    To allow them to be run multiple times (for multiple copy/move) and to return back to the Multiple Product Copy results, a clause needs to be added around the redirect at the end of each file.

    The changes required are in these files, which you should have already copied to ADMIN/includes/modules

    /ADMIN FOLDER/includes/modules/copy_to_confirm.php_multiple_product_copy
    /ADMIN FOLDER/includes/modules/move_product_confirm.php_multiple_product_copy

    Compare each file to yours and merge the differences to yours.

    If you are using other custom files on your Product Types for the files:
    copy_to_confirm.php
    move_product_confirm.php

    you will need to add the similar IF statement around the redirect on the last line of the file.
   
1. Uninstall

a) Use the backup you kept of this installation fileset to compare and delete the files from your site.

b) Copy, paste and run the uninstall SQL in the Admin->SQLpatch tool or phpMyAdmin to remove the admin menu page item.

---------------------
### Changelog
2023 04 23: minor IDE fettling, no functional changes
2020 06 13 torvista: limitation of search results to not exceed php input_max_vars  
2020 05 15 torvista: update of main file and removal of extra functions  
2020 02 28 torvista: bugfix to allow language selector dropdown  
2020 01 torvista: complete rework  
remove table layout  
conversion to html with embedded php  
expanded error checking for search criteria  

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
