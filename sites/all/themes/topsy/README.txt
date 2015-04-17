This theme is based on the bootstrap theme starter kit in its simplest configuration
with a couple of minor changes to support the Tops Module.

The changes for Tops are
1) In template.php,
     the topsy_status_messages function inserts a KnockoutJS component element for ViewModel pages that is required
     for display of service response messages.
     This is a required change.

2) In templates\page.tpl.php,
    Added one line at the end of the file to display trace messages.
    This is an optional change.
