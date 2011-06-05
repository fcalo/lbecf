CKEDITOR.plugins.add('link_issue',
{
    init: function(editor)
    {
        var pluginName = 'link_issue';
        CKEDITOR.dialog.add(pluginName, this.path + 'dialogs/link_issue.js');
        editor.addCommand(pluginName, new CKEDITOR.dialogCommand(pluginName));
        editor.ui.addButton('Link_issue',
            {
                label: 'Link Issuu',
                command: pluginName
            });
    }
});