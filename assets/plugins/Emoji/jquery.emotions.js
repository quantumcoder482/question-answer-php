(function($){	
	$.fn.emotions = function(options){
		$this = $(this);
		var opts = $.extend({}, $.fn.emotions.defaults, options);
		return $this.each(function(i,obj){
			var o = $.meta ? $.extend({}, opts, $this.data()) : opts;					   	
			var x = $(obj);
			// Entites Encode 
			var encoded = [];
			for(i=0; i<o.s.length; i++){
				encoded[i] = String(o.s[i]).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
			}
			for(j=0; j<o.s.length; j++){
				var repls = x.html();
				if(repls.indexOf(o.s[j]) || repls.indexOf(encoded[j])){
					var imgr = o.a+o.b[j]+"."+o.c;			
					var rstr = "<img src='"+imgr+"' border='height:18px' />";	
					//Escape the ')' and '(' brackets
var tempStrSmiley1 = o.s[j].replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
var tempStrSmiley2 = encoded[j].replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");

x.html(repls.replace(new RegExp(tempStrSmiley1, 'g'), rstr));
x.html(repls.replace(new RegExp(tempStrSmiley2, 'g'), rstr));

x.html(repls.replace(new RegExp(tempStrSmiley1, 'g'), rstr));
x.html(repls.replace(new RegExp(tempStrSmiley2, 'g'), rstr));
//alert(tempStrSmiley2)
				}
			}
		});
	}	
	// Defaults
	$.fn.emotions.defaults = {
		a : PATH+"assets/plugins/Emoji/emotions/",			// Emotions folder
		b : new Array("sick","angel","colonthree","confused","confused","cry","devil","frown","gasp","glasses","grin","grumpy","heart","kiki","kiss","pacman","smile","unsure","sunglasses","tongue","upset","wink"),			// Emotions Type
		s : new Array(":S","o:)",":3","o.O","O.o",":'(","3:)",":(",":O","8|",":D",":|","<3","^_^",":*","(y)",":)","-_-","8)",":P",">:O",";)"),
		c : "gif"					// Emotions Image format
	};
})(jQuery);


// Notes
// a - icon folder
// b - emotions name array
// c - image format
// x - current selector
// d - type of selector
// o - options 
