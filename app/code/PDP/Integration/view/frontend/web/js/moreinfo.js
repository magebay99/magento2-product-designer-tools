/**
*/
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
	'mage/template'
], function ($, modal, $t, mageTemplate) {
	'use strict';
	
	return function (config, element) {
		var title = $t(config.title), template_html = mageTemplate(config.template_id);
		$(element).click(function(){
			var mifmodal = $('[id=modal-name-number]').modal({
				title: title,
				buttons: [],
				wrapperClass: 'moreinfo-modal',
				opened: function(){
					$('[id=modal-name-number] > [class=block-content]').html(template_html);
				},
				closed: function(){
					$('[id=modal-name-number] > [class=block-content]').html('');
				}
			});
			mifmodal.modal('openModal');
		});
	};
})