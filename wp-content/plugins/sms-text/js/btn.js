(function() {
    tinymce.PluginManager.add('sms_mce_button', function(editor, url) {
        editor.addButton('sms_mce_button', {
            icon: false,
            text: "GroundSource",
            onclick: function() {
                editor.windowManager.open({
                    title: "Insert Phone Number and Keywords",
                    body: [
                    {
                        type: 'textbox',
                        name: 'keywords',
                        label: 'Keywords',
                        value: ''
                    },
					{
                        type: 'textbox',
                        name: 'phonenumber',
                        label: 'Phone Number',
                        value: ''
                    }],
                    onsubmit: function(e) {
                        editor.insertContent(
                            '[groundsource keywords="'+ e.data.keywords +'"  phonenumber="' + e.data.phonenumber + '" ]'
                        );
                    }
                });
            }
        });
    });
})();