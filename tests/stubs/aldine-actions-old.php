<?php

/**
 * Minimal stand-in for pressbooks-aldine's pre-PSR-4 (dev branch) admin_init
 * callback, used only to verify that PressbooksNetworkCatalog can still
 * remove it by its old plain function-string identity. Real pressbooks-aldine
 * is not loaded in this plugin's test environment.
 */

namespace Aldine\Actions;

function hide_catalog_content_editor()
{
    // No-op: only the callback identity matters for this test, not its body.
}
