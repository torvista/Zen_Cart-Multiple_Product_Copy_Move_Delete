<?php

declare(strict_types=1);

/**
 * part of the Multiple Product Copy Plugin
 * @link https://github.com/torvista/Zen_Cart-Multiple_Products_Copy_Move_Delete
 * @copyright Copyright 2025 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @author torvista, Ajeh
 * @version $Id: torvista 2025-04-20
 */

use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    protected function executeInstall(): void
    {
        zen_deregister_admin_pages(['multipleProductCopy']);//remove original plugin adminpage name
        zen_register_admin_page(
            'catalogMultipleProductCopy', 'BOX_CATALOG_MULTIPLE_PRODUCT_COPY', 'FILENAME_MULTIPLE_PRODUCT_COPY', '', 'catalog', 'Y');
    }

    protected function executeUninstall(): void
    {
        zen_deregister_admin_pages(['catalogMultipleProductCopy']);
    }
}
