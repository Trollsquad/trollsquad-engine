(function(e){e.event.special.mousewheel={setup:function(){var t=e.event.special.mousewheel.handler;if(e.browser.mozilla)e(this).bind("mousemove.mousewheel",function(t){e.data(this,"mwcursorposdata",{pageX:t.pageX,pageY:t.pageY,clientX:t.clientX,clientY:t.clientY})});if(this.addEventListener)this.addEventListener(e.browser.mozilla?"DOMMouseScroll":"mousewheel",t,false);else this.onmousewheel=t},teardown:function(){var t=e.event.special.mousewheel.handler;e(this).unbind("mousemove.mousewheel");if(this.removeEventListener)this.removeEventListener(e.browser.mozilla?"DOMMouseScroll":"mousewheel",t,false);else this.onmousewheel=function(){};e.removeData(this,"mwcursorposdata")},handler:function(t){var n=Array.prototype.slice.call(arguments,1);t=e.event.fix(t||window.event);e.extend(t,e.data(this,"mwcursorposdata")||{});var r=0,i=true;if(t.wheelDelta)r=t.wheelDelta/120;if(t.detail)r=-t.detail/3;t.data=t.data||{};t.type="mousewheel";n.unshift(r);n.unshift(t);return e.event.handle.apply(this,n)}};e.fn.extend({mousewheel:function(e){return e?this.bind("mousewheel",e):this.trigger("mousewheel")},unmousewheel:function(e){return this.unbind("mousewheel",e)}})})(jQuery)