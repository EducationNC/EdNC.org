// Created from SMS Link Repo 
// https://github.com/smeeckaert/sms-link 
var SMSLink = SMSLink || {};
SMSLink.detector = SMSLink.detector || function() {
    var a, b, c, d = navigator.userAgent;
    return d.match(/iPad/i) || d.match(/iPhone/i) ? (a = "iOS", c = d.indexOf("OS ")) : d.match(/Android/i) ? (a = "Android", c = d.indexOf("Android ")) : a = null, "iOS" === a && c > -1 ? b = d.substr(c + 3, 3).replace("_", ".") : "Android" === a && c > -1 ? a = d.substr(c + 8, 3) : ver = null, {
        version: function() {
            return b
        },
        os: function() {
            return a
        },
        isMobile: function() {
            return null !== a
        }
    }
};
var SMSLink = SMSLink || {};
SMSLink.link = SMSLink.link || function() {
    function a() {
        SMSLink.linkDetector || (SMSLink.linkDetector = new SMSLink.detector)
    }
    return a(), {
        replaceAll: function() {
            this.replaceWithSelector("[href^='sms:']")
        },
        replaceWithSelector: function(a) {
            elements = document.querySelectorAll(a);
            for (i in elements) this.replace(elements[i])
        },
        replace: function(a) {
            if (a.href) {
                switch (replaceBody = !1, SMSLink.linkDetector.os()) {
                    case "iOS":
                        parseFloat(SMSLink.linkDetector.version()) <= 8 ? replaceBody = ";" : replaceBody = "&"
                }
                replaceBody && (a.href = a.href.replace(/\?body=/, replaceBody + "body="))
            }
        }
    }
};
document.addEventListener('DOMContentLoaded', (function () {
    link = new SMSLink.link();
    link.replaceAll();
}), false);


var container = document.getElementsByClassName("phonegroup")[0];
container.onkeyup = function(e) {
    var target = e.srcElement;
    var maxLength = parseInt(target.attributes["maxlength"].value, 10);
    var myLength = target.value.length;
    if (myLength >= maxLength) {
        var next = target;
        while (next = next.nextElementSibling) {
            if (next == null)
                break;
            if (next.tagName.toLowerCase() == "input") {
                next.focus();
                break;
            }
        }
    }
}

$(document).ready(function(){

   $(".submit-button").click(function(event){
        event.preventDefault();
        phoneNumberReceiver=$('#phoneNumberReceiver').val();
		
        phoneNumberSender=$('#phoneNumberSender').val();
        areaCodeSender=$('#areaCodeSender').val();
		area_phone = areaCodeSender + '' + phoneNumberSender;
		
        keywords=$('#keywords').val();
        if (area_phone.length < 6){
            alert("Invalid phone number")
            return;
        }
        gs_url = "https://www.groundsource.co/surveys/sms/received/?MessageSid=1&To="+phoneNumberReceiver+"&From=+" + "+1" +area_phone+"&Body="+keywords;
        $.ajax({url:gs_url, 
            async: true, 
            beforeSend: function(msg){
                $(".submit-button").prop('disabled', true);
              },
            success: function(result){
               // alert(" Message sent! " +phoneNumberReceiver+ " "+area_phone+ " "+keywords);
                alert(" Message sent! ");
				location.reload();
            },
            error: function() {
                alert('Sorry, an error occured');
            }
        });

    });

});

