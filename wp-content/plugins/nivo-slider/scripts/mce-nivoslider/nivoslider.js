(function() {
    tinymce.create('tinymce.plugins.NivoSlider', {
        createControl : function(n, cm) {
            switch (n) {
                case 'nivoslider':
                    var mlb = cm.createListBox('nivoslider', {
                        title : 'Nivo Sliders',
                        onselect : function(v) {
                            if(v != ''){
                                tinyMCE.execCommand('mceInsertContent', false, '[nivoslider id="' + v + '"]');
                            }
                            return false;
                        }
                    });

                    var sliders = NivoSlider.sliders.replace(new RegExp("&quot;", "g"), String.fromCharCode(34));
                    sliders = jQuery.parseJSON(sliders);
                    for(var i in sliders){
                        mlb.add(sliders[i].name, sliders[i].id);
                    }

                    // Return the new listbox instance
                    return mlb;
            }

            return null;
        },
        getInfo : function() {
            return {
                longname : 'NivoSlider Shortcode',
                author : 'Gilbert Pellegrom',
                authorurl : 'http://gilbert.pellegrom.me',
                infourl : 'http://nivo.dev7studios.com/wordpress',
                version : '1.0'
            };
        }
    });
    tinymce.PluginManager.add('nivoslider', tinymce.plugins.NivoSlider);
})();