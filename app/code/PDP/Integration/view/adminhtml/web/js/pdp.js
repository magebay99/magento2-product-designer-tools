/**
 *
 */

define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, alert, $t) {
		'use strict';
		
		return function (config, element) {
			var alertModal = function(title, message) {
					alert({
						title: $.mage.__("!"+title),
						content: $.mage.__(message),
						autoOpen: true,
						clickableOverlay: false,
						focus: "",
						actions: {
							always: function(){
						    }
						}
					});
			};
			$(element).click(function(){
				var url = config.url;
				if(url != '#') {
					$.ajax({
						url: url,
						type: 'GET',
						beforeSend: function() {                  
							$('body').trigger('processStart');                 
						},
						success : function(_res) {
						    $('body').trigger('processStop');
							var res = $.parseJSON(_res);
							var data = res.data;
							if(res.status == 'success') {
								var url_download = data.baseUrl+''+data.file;
								window.location.href = url_download;
							} else {
								alertModal('error', 'can\'t zip design');
							}
						},
						error: function(xhr, ajaxOptions, thrownError){
							$('body').trigger('processStop');
							alertModal('error', thrownError);
						}
					});					
				} else {
					alertModal('error', 'can\'t zip design');
				}				
			});
		};	
});