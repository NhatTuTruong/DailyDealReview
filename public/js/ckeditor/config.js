/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function (config) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';

    // Simplify the dialog windows.
    config.extraPlugins = 'youtube, btgrid, tableresizerowandcolumn, emoji, autocomplete, ajax';

    config.removeDialogTabs = 'image:advanced;link:advanced';
    config.filebrowserBrowseUrl = baseUrl + '/ckfinder/browser';
    config.filebrowserImageBrowseUrl = baseUrl + '/ckfinder/browser?type=Images';

    config.toolbarGroups = [
        {name: 'document', groups: ['mode', 'document', 'doctools']},
        {name: 'clipboard', groups: ['clipboard', 'undo']},
        {name: 'forms', groups: ['forms']},
        {name: 'styles', groups: ['styles']},
        {name: 'colors', groups: ['colors']},
        {name: 'tools', groups: ['tools']},
        {name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing']},

        {name: 'others', groups: ['others']},
        '/',
        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
        {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
        {name: 'links', groups: ['links']},
        {name: 'insert', groups: ['insert']},
        // '/',
        {name: 'about', groups: ['about']}
    ];

    config.removeButtons = 'About,Language,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Print,Find,Preview,NewPage,SelectAll,Scayt,Smiley,SpecialChar,Font,Styles,ExportPdf';

};
