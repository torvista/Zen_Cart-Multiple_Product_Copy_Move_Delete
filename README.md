# Multiple Product Copy-Move-Delete for Zen Cart

This awesome utility allows you to perform multiple actions in one process.

* Copy (as linked or duplicate) multiple products from one category to another
* Delete multiple products
* Move multiple products

Based on an orginal work by Ajeh:  
https://www.zen-cart.com/showthread.php?180447-Multiple-Products-(Copy-Move-Delete)

## Compatibility
Zen Cart: 2.1+ Earlier versions not supported/need core modifications.
PHP 8+

This is a complex plugin. Although it has been in use on my site for a long time, you may find a bug, so BEFORE making big changes on your production site TEST THOROUGHLY ON YOUR DEVELOPMENT SERVER.

Please report all bugs on GitHub with steps how to reproduce.

## Installation
TEST ON YOUR DEVELOPMENT SERVER FIRST!

As this is an encapsulated plugin, there are no core-file overwrites.

1. Remove any previous version of this mod as per that version's instructions.

1. Copy the folder `MultipleProductCopy` into the zc_plugins folder

1. Use the Admin->Modules->Plugin Manager to install the plugin.

## How to Use

1. Choose an action.
1. Choose a target/destination category (not for delete)
1. Search for products to copy/delete by keyword, category, manufacturer or price.

You will be presented with a list of products matching your search terms.  
Products that are already linked to the destination category will not be listed. 

Look over the list and tick the boxes for any product that should be copied, (or uncheck the boxes of products that should not be copied if you had the utility automatically check the boxes for you). 

Click the Confirm button and all the checked products will be now linked as Linked (or Duplicated) products in the destination category and a list of the copied products will be displayed. 

Buttons are provided to easily view the source and target category listings to check all is as expected.

## Uninstall

a) Use the backup you kept of this installation fileset to compare and delete the files from your site.

b) Copy, paste and run the uninstall SQL in the Admin->SQLpatch tool or phpMyAdmin to remove the admin menu page item.

## Changelog
2025 04 20 Rework as encapsulated plugin.

2024 10 10: simplify javascript, remove old function, rename custom function, convert defines to lang. file
2024 10 09: php echo short tags  
2023 04 23: minor IDE fettling, fix for confirmation text for moved products, update debug function, update modified core files
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
