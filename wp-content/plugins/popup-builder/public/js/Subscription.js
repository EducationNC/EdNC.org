function SGPBSubscription(){this.expiryTime=365,this.submissionPopupId=0,this.newWindow=null,this.init()}SGPBSubscription.cookieName="SGPBSubscription",SGPBSubscription.prototype.init=function(){this.livePreview(),this.formSubmission()},SGPBSubscription.prototype.formSubmission=function(){var e=this;if("undefined"==typeof sgAddEvent)return!1;sgAddEvent(window,"sgpbDidOpen",function(s){var i=s.detail.popupId,t=SGPBPopup.getPopupOptionsById(i),o={},r=jQuery("#sgpb-popup-dialog-main-div .sgpb-subs-form-"+i+" form"),n=r.find(".js-subs-submit-btn");if("object"!=typeof sgpbSubsValidateObj)return!1;jQuery.validator.setDefaults({errorPlacement:function(e,s){var t=jQuery(s).attr("data-error-message-class");jQuery(".sgpb-subs-form-"+i+" ."+t).html(e)}}),sgpbSubsValidateObj.submitHandler=function(){var s=r.serialize(),u={action:"sgpb_subscription_submission",nonce:SGPB_JS_PARAMS.nonce,beforeSend:function(){n.val(n.attr("data-progress-title")),"redirectToURL"==t["sgpb-subs-success-behavior"]&&t["sgpb-subs-success-redirect-new-tab"]&&(e.newWindow=window.open(t["sgpb-subs-success-redirect-URL"]))},formData:s,popupPostId:i};jQuery.post(SGPB_JS_PARAMS.ajaxUrl,u,function(s){e.submissionPopupId=i,jQuery(".sgpb-subs-form-"+i+" .sgpb-alert").addClass("sg-hide-element"),n.val(n.attr("data-title")),o.res=s,e.showMessages(o),e.processAfterSuccessfulSubmission(u)})},r.validate(sgpbSubsValidateObj),jQuery.extend(jQuery.validator.messages,{email:t["sgpb-subs-invalid-message"],required:t["sgpb-subs-validation-message"]})}),jQuery(window).on("sgpbDidClose",function(e){var s=e.detail.popupId;jQuery(".sgpb-subs-form-"+s+" form label.error").hide()})},SGPBSubscription.prototype.processAfterSuccessfulSubmission=function(e){if(jQuery.isEmptyObject(e))return!1;e.action="sgpb_process_after_submission",jQuery.post(SGPB_JS_PARAMS.ajaxUrl,e,function(e){})},SGPBSubscription.prototype.showMessages=function(e){return 1==e.res?this.subscriptionSuccessBehavior():(null!=this.newWindow&&this.newWindow.close(),this.showErrorMessage()),window.dispatchEvent(new Event("resize")),!0},SGPBSubscription.prototype.showErrorMessage=function(){var e=parseInt(this.submissionPopupId);jQuery(".sgpb-subs-form-"+e+" .sgpb-alert-danger").removeClass("sg-hide-element")},SGPBSubscription.prototype.subscriptionSuccessBehavior=function(){var e={popupId:this.submissionPopupId,eventName:"sgpbSubscriptionSuccess"};jQuery(window).trigger("sgpbFormSuccess",e);var s=parseInt(this.submissionPopupId),i=SGPBPopup.getPopupOptionsById(s),t="showMessage";switch(jQuery(".sgpb-subs-form-"+s+" form").remove(),this.setSubscriptionCookie(s),void 0!==i["sgpb-subs-success-behavior"]&&(t=i["sgpb-subs-success-behavior"]),this.resetFieldsValues(),t){case"showMessage":jQuery(".sgpb-subs-form-"+s+" .sgpb-alert-success").removeClass("sg-hide-element");break;case"redirectToURL":this.redirectToURL(i);break;case"openPopup":this.openSuccessPopup(i);break;case"hidePopup":SGPBPopup.closePopupById(this.submissionPopupId)}},SGPBSubscription.prototype.openSuccessPopup=function(e){var s=this;setTimeout(function(){SGPBPopup.closePopupById(s.submissionPopupId)},0),void 0!==e["sgpb-subs-success-popup"]&&sgAddEvent(window,"sgpbDidClose",this.openPopup(e))},SGPBSubscription.prototype.openPopup=function(e){if(void 0===e["sgpb-subs-success-popup"])return!1;var s=parseInt(e["sgpb-subs-success-popup"]),i=SGPBPopup.getPopupOptionsById(s),t=new SGPBPopup;t.setPopupId(s),t.setPopupData(i),setTimeout(function(){t.prepareOpen()},500)},SGPBSubscription.prototype.setSubscriptionCookie=function(e){var s=window.location.href,i=SGPBSubscription.cookieName+e,t=this.expiryTime;if(""==SGPopup.getCookie(i)){var o=[s];SGPBPopup.setCookie(i,JSON.stringify(o),t)}},SGPBSubscription.prototype.redirectToURL=function(e){var s=e["sgpb-subs-success-redirect-URL"],i=e["sgpb-subs-success-redirect-new-tab"];if(SGPBPopup.closePopupById(this.submissionPopupId),i)return!0;window.location.href=s},SGPBSubscription.prototype.resetFieldsValues=function(){if(!jQuery(".js-subs-text-inputs").length)return!1;jQuery(".js-subs-text-inputs").each(function(){jQuery(this).val("")})},SGPBSubscription.prototype.livePreview=function(){this.binding(),this.changeLabels(),this.changeButtonTitle(),this.changeColor(),this.changeOpacity(),this.changePadding(),this.changeDimension(),this.preventDefaultSubmission(),"function"==typeof SGPBBackend&&SGPBBackend.makeContactAndSubscriptionFieldsRequired()},SGPBSubscription.prototype.preventDefaultSubmission=function(){var e=jQuery('.sgpb-subscription-admin-wrapper input[type="submit"]');if(!e.length)return!1;e.bind("click",function(e){e.preventDefault()})},SGPBSubscription.prototype.changeDimension=function(){var e=this;jQuery(".js-subs-dimension").change(function(){var s=jQuery(this),i=e.changeDimensionMode(s.val()),t=s.attr("data-style-type"),o=s.attr("data-field-type"),r=s.attr("data-subs-rel");"input"==o&&(jQuery(".sgpb-gdpr-label-wrapper").css("width",i),jQuery(".sgpb-gdpr-info").css("width",i));var n={};n[t]=i,jQuery("."+r).css(n)})},SGPBSubscription.prototype.changePadding=function(){jQuery(".js-sgpb-form-padding").on("change keydown keyup",function(){var e=jQuery(this).val();jQuery(".sgpb-subscription-admin-wrapper").css("padding",e+"px")})},SGPBSubscription.prototype.changeColor=function(){var e=this;if(void 0===jQuery.wp||"function"!=typeof jQuery.wp.wpColorPicker)return!1;jQuery(".js-subs-color-picker").each(function(){jQuery(this).wpColorPicker({change:function(){e.colorPickerChange(jQuery(this))}})}),jQuery(".wp-picker-holder").mouseover(function(){var s=jQuery(this).prev().find(".js-subs-color-picker");e.colorPickerChange(s)}),jQuery(".wp-picker-holder").bind("click",function(){var s=jQuery(this).prev().find(".js-subs-color-picker");e.colorPickerChange(s)})},SGPBSubscription.prototype.changeOpacity=function(){var e=this;jQuery(".js-subs-bg-opacity").next().find(".range-handle").on("change mousemove",function(){e.colorPickerChange(jQuery("input[name=sgpb-subs-form-bg-color]"))})},SGPBSubscription.prototype.setupPlaceholderColor=function(e,s){jQuery("."+e).each(function(){jQuery("#sgpb-placeholder-style").remove();var i="."+e+"::-webkit-input-placeholder {color: "+s+" !important;}";i+="."+e+"::-moz-placeholder {color: "+s+" !important;}";var t='<style id="sgpb-placeholder-style">'+(i+="."+e+"::-ms-placeholder {color: "+s+" !important;}")+"</style>";jQuery("head").append(t)})},SGPBSubscription.prototype.colorPickerChange=function(e){var s=jQuery("input[name=sgpb-subs-form-bg-opacity]").val(),i=e.val();i=SGPBBackend.hexToRgba(i,s);var t=e.attr("data-style-type"),o=e.attr("data-subs-rel");if("placeholder"==t)return this.setupPlaceholderColor(o,i),!1;var r={};r[t]=i,jQuery("."+o).each(function(){jQuery(this).css(r)})},SGPBSubscription.prototype.changeButtonTitle=function(){jQuery(".js-subs-btn-title").bind("input",function(){var e=jQuery(this).attr("data-subs-rel"),s=jQuery(this).val();jQuery("."+e).val(s)})},SGPBSubscription.prototype.changeLabels=function(){jQuery("#sgpb-subs-gdpr-text").on("keyup",function(){var e=jQuery(this).val();jQuery(this).text(""),jQuery(this).text(e),jQuery(".sgpb-gdpr-text-js").text(e)}),jQuery(".js-subs-field-placeholder").each(function(){jQuery(this).bind("input",function(){var e=jQuery(this).attr("data-subs-rel"),s=jQuery(this).val();"js-subs-gdpr-label"==e?jQuery("."+e).next().text(s):jQuery("."+e).attr("placeholder",s)})})},SGPBSubscription.prototype.binding=function(){var e=this;jQuery(".js-checkbox-field-status").bind("click",function(){var s=jQuery(this).is(":checked"),i=jQuery(this).attr("data-subs-field-wrapper"),t=jQuery("."+i);e.toggleVisible(t,s)}),jQuery(".js-checkbox-acordion").each(function(){var s=jQuery(this).is(":checked"),i=jQuery(this).attr("data-subs-rel"),t=jQuery("."+i);e.toggleVisible(t,s)})},SGPBSubscription.prototype.toggleVisible=function(e,s){s?e.css({display:"block"}):e.css({display:"none"})},SGPBSubscription.prototype.changeDimensionMode=function(e){var s;return s=parseInt(e)+"px",-1==e.indexOf("%")&&-1==e.indexOf("px")||(s=e),s},SGPBSubscription.prototype.allowToOpen=function(e){var s=!0,i=SGPBSubscription.cookieName+e;return""!=SGPopup.getCookie(i)&&(s=!1),s},jQuery(document).ready(function(){new SGPBSubscription});
