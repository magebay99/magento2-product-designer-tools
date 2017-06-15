/**
 *
 */

define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, alert, confirmation, $t) {
		'use strict';
    var confirmModal = function (title, msg,onOkay,onCancel)
    {
      var emptyFunc = function (){ };
      title = title | 'Confirmation Question Dialog';
      onOkay = typeof onOkay === 'function' ? onOkay : emptyFunc;
      onCancel = typeof onCancel === 'function' ? onCancel : emptyFunc;
      var modal = confirmation({
                                  title: title,
                                  content: msg,
                                  actions: {
                                    confirm: onOkay,
                                    cancel: onCancel,
                                    always: emptyFunc
                                  },
                                  autoOpen : true,
                                  clickableOverlay : false
                                });
    };
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
        console.log(config);
				var url = config.url+'&zip=1';
        var reload = config['update-svg-url'];
        var returnUri = window.location.href;
        reload += '&return-uri=' + encodeURIComponent(returnUri);
        if(url != '#') {
					$.ajax({
						url: url,
						type: 'GET',
						beforeSend: function() {                  
							$('body').trigger('processStart');                 
						},
						success : function(_res) {
						    $('body').trigger('processStop');
							if(typeof _res === 'object') {
								var res = _res;
							} else {
								res = $.parseJSON(_res);
							}
							var data = res.data;
							if(res.status === 'success') {
                window.location.href = data.baseUrl + '' + data.file;
							} else {
                if(res.message)
                {
                  var msg = res.message;
                }else{
                  msg = 'Cant zip design , the source SVG file missing.';
                }
                if(res.errorCode && res.errorCode === 15)
                {
                  msg += '<br /><strong>We need create it again from Design Editor . Press "<span style="color: #0fa7ff">OK</span>" then just wait , all done automatically !.</strong>';
                  confirmModal('Error',$t(msg),
                               function ()
                               {
                                 window.location.href = reload;
                               });
                }else{
                  alertModal('Error', $t(msg));
                }
							}
						},
						error: function(xhr, ajaxOptions, thrownError){
							$('body').trigger('processStop');
//							alertModal('error', thrownError);
              if(xhr.responseJSON && xhr.responseJSON.message)
              {
                var msg = xhr.responseJSON.message;
              }else{
                msg = 'Cant zip design , the source SVG file missing.';
              }
              if(xhr.responseJSON && xhr.responseJSON.errorCode && xhr.responseJSON.errorCode === 15)
              {
                msg += '<br /><strong>We need create it again from Design Editor . Press "<span style="color: #0fa7ff">OK</span>" then just wait , all done automatically !.</strong>';
                confirmModal('Error',$t(msg),
                             function ()
                             {
                               window.location.href = reload;
                             });
              }else{
                alertModal('Error', $t(msg));
              }
						}
					});					
				} else {
					alertModal('error', 'can\'t zip design');
				}				
			});
		};	
});