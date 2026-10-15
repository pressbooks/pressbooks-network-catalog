<?php

/**
 * Minimal stand-in for pressbooks-aldine's post-PSR-4 (theme PR #588)
 * admin_init callback, used only to verify that PressbooksNetworkCatalog can
 * still remove it by its new array-callable identity. Real pressbooks-aldine
 * is not loaded in this plugin's test environment.
 */

namespace Aldine;

class Actions
{
	public static function hideCatalogContentEditor()
	{
		// No-op: only the callback identity matters for this test, not its body.
	}
}
