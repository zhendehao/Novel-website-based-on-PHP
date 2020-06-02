function calculateCollapseSpec(el) {
    var maxLines = Number(el.data("max-lines")) || Ark.abstractMaxLines,
        lineHeight = parseFloat(el.data("line-height")) || 24;
    return {
        foldedHeight: (maxLines - 3) * lineHeight - 1,
        maxHeight: maxLines * lineHeight - 1
    }
}
function expandAndFoldCollapseContext(spec) {
    return function() {
        var el = $(this),
            context = el.parents(".collapse-context"),
            beExpand = !context.hasClass(EXPANDED_CLASS);
        context.toggleClass(EXPANDED_CLASS), el.text(beExpand ? "收起" : "展开"), context.find(".collapse-content").css({
            "max-height": beExpand ? "none" : spec.foldedHeight
        })
    }
}
function getAjaxUrl(f,d) {
    return ["", f, "j"].join("/")+".php?do="+d
}
function getHeaderToken() {
    var x = window.XMLHttpRequest ? new window.XMLHttpRequest() : new ActiveXObject( "Microsoft.XMLHTTP ");
    x.open( "GET", window.location.href, false);
    x.send();
    return x.getResponseHeader('Token');
}
!function($){$.fn.replaceClass=function(oldClass,newClass){$(this).removeClass(oldClass).addClass(newClass)}}(jQuery);
(function($){$.extend({myTime:{CurTime:function(){return Date.parse(new Date())/1000},DateToUnix:function(string){var f=string.split(' ',2);var d=(f[0]?f[0]:'').split('-',3);var t=(f[1]?f[1]:'').split(':',3);return(new Date(parseInt(d[0],10)||null,(parseInt(d[1],10)||1)-1,parseInt(d[2],10)||null,parseInt(t[0],10)||null,parseInt(t[1],10)||null,parseInt(t[2],10)||null)).getTime()/1000},UnixToDate:function(unixTime,isFull,timeZone){if(typeof(timeZone)=='number'){unixTime=parseInt(unixTime)+parseInt(timeZone)*60*60}var time=new Date(unixTime*1000);var ymdhis="";ymdhis+=time.getUTCFullYear()+"-";ymdhis+=(time.getUTCMonth()+1)+"-";ymdhis+=time.getUTCDate();if(isFull===true){ymdhis+=" "+time.getUTCHours()+":";ymdhis+=time.getUTCMinutes()+":";ymdhis+=time.getUTCSeconds()}return ymdhis}}})})(jQuery);
var Saxue = {
    uid: 0,
    token: "",
    popTip: function(b,e) {
        e = e||window.event;
        0 == $("#J_FIXED").length && $("body").prepend('<div id="J_FIXED" class="m-tip" style="display:none;"><div id="J_TIPS" class="inner"></div></div>');
        var a = $("#J_TIPS");
        a.empty().html(b);
        a.parent().fadeIn(300).delay(3E3).fadeOut(300)
    },
    popTipClose: function(b) {
        var a = $("#J_FIXED_X");
        0 == a.length && (a = $('<div id="J_FIXED_X" class="m-tip" style="display:none;"><div id="J_TIPS_X" class="inner"><p></p></div></div>').appendTo($("body")));
        var c = $("#J_TIPS_X p");
        c.empty().html(b);
        c.append('<a class="close" href="javascript:;"></a>');
        $(".close", a).click(function() {
            a.fadeOut(300)
        });
        $(".link", a).click(function() {
            a.fadeOut(300)
        });
        a.fadeIn(500)
    },
    setStyle: function (u,id) {
        var lk = document.createElement("LINK");
        lk.type = "text/css";
        lk.rel = "stylesheet";
        lk.id = typeof id === "undefined" ? "" : id;
        lk.href = u + "?v=" + (new Date().getTime());
        var head = document.getElementsByTagName("head")[0];
        head.appendChild(lk);
    },
    doClick: function(id,f,d,callback,e) {
        var t = typeof id;
        if(t === "undefined"){
            return;
        }
        var param = t === "object" ? id : { "aid":id };
        $.post(getAjaxUrl(f,d), param, function(data){
            Saxue.popTip(data.msg,e);
            if (data.flag == 1 && typeof callback === "function"){
                callback(data);
            }
        }, "json")
    },
    postForm: function(fid,f,d,callback) {
        var el = $('#'+fid);
        if(el.length==0){
            return;
        }
        var param = el.serialize() + "&token=" + Saxue.token;
        $.post(getAjaxUrl(f,d), param, function(data){
            if (typeof callback === "function"){
                callback(data);
            }
        }, "json")
    },
    showLogin: function() {
        var text = ''
            +'<div class="login-bd clearfix">'
            +'	<ul class="clearfix">'
            +'		<li class="sina-login"><a href="/member/oauthlogin.php?site=weibo">微博账号登录</a></li>'
            +'		<li class="qq-login"><a href="/member/oauthlogin.php?site=qq">QQ账号登录</a></li>'
            +'	</ul>'
            +'</div>';
        Saxue.Dialog.show({"title":"会员登录","body":text})
    },
    pageInit: function(){
        $.getScript("/api/uinfo.php", function() {
            if(user.id){
                Saxue.uid = user.id;
                var info = '<li><a class="name">'+user.name+'</a></li><li><a href="/member/logout.php" class="logout">退出</a></li>';
                $('.user-info').empty().html(info);
                //$('.myAvatar').attr('src',user.avatar);
            }else{
                $('.isLogin, .requireLogin').click(function(){
                    Saxue.showLogin();
                });
            }
        });
        Saxue.token = getHeaderToken()
    }
};
Saxue.Dialog = {
    options: {
        title:'',
        body:'',
        width:360,
        mask:true
    },
    opts: {},
    tmpl: '<div id="saxue-dialog"><div class="dialog-hd"><h3 class="clearfix"><span class="k-title"></span><i class="closealert"></i></h3></div><div class="dialog-bd clearfix"></div></div>',
    mask: '<div id="dialog-mask"></div>',
    show: function (opts) {
        $.extend(this.opts, this.options, opts), this.render();
        $('#dialog-mask, .closealert').one("click",function(){
            $('#saxue-dialog, #dialog-mask').hide();
        });
        $("#saxue-dialog, #dialog-mask").show()
    },
    render: function() {
        if ($("#saxue-dialog").length == 0) {
            Saxue.setStyle('/public/index/css/dialog.css');
            var el = $(this.tmpl);
            this.opts.title && el.find(".k-title").html(this.opts.title), el.find(".dialog-bd").html(this.opts.body), el.appendTo("body"), $(this.mask).appendTo("body")
        } else {
            this.opts.title && $("#saxue-dialog .k-title").html(this.opts.title), $("#saxue-dialog .dialog-bd").html(this.opts.body)
        }
        if (this.opts.width){
            $("#saxue-dialog").css({"width":this.opts.width+80, "margin-left":-(this.opts.width+80)/2}), $("#saxue-dialog .dialog-bd").css("width",this.opts.width)
        }
    }
};
$(function() {
    Saxue.pageInit(), $('#btn-search').click(function(){
        if($('#input-query').val().trim()=="") return false
    });
    var doc = document;
    $(doc).on("click", 'a[href="#"]', function(e) {
        e.preventDefault()
    });
    try {
        doc.execCommand("BackgroundImageCache", !1, !0)
    } catch (err) {}
})