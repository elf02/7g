function w(n){if(Array.isArray(n)){for(var e=0,o=Array(n.length);e<n.length;e++)o[e]=n[e];return o}else return Array.from(n)}var s=!1;if(typeof window<"u"){var f={get passive(){s=!0}};window.addEventListener("testPassive",null,f),window.removeEventListener("testPassive",null,f)}var c=typeof window<"u"&&window.navigator&&window.navigator.platform&&(/iP(ad|hone|od)/.test(window.navigator.platform)||window.navigator.platform==="MacIntel"&&window.navigator.maxTouchPoints>1),i=[],u=!1,v=-1,l=void 0,d=void 0,h=function(e){return i.some(function(o){return!!(o.options.allowTouchMove&&o.options.allowTouchMove(e))})},a=function(e){var o=e||window.event;return h(o.target)||o.touches.length>1?!0:(o.preventDefault&&o.preventDefault(),!1)},g=function(e){if(d===void 0){var o=!!e,t=window.innerWidth-document.documentElement.clientWidth;o&&t>0&&(d=document.body.style.paddingRight,document.body.style.paddingRight=t+"px")}l===void 0&&(l=document.body.style.overflow,document.body.style.overflow="hidden")},y=function(){d!==void 0&&(document.body.style.paddingRight=d,d=void 0),l!==void 0&&(document.body.style.overflow=l,l=void 0)},p=function(e){return e?e.scrollHeight-e.scrollTop<=e.clientHeight:!1},m=function(e,o){var t=e.targetTouches[0].clientY-v;return h(e.target)?!1:o&&o.scrollTop===0&&t>0||p(o)&&t<0?a(e):(e.stopPropagation(),!0)},b=function(e,o){if(!e){console.error("disableBodyScroll unsuccessful - targetElement must be provided when calling disableBodyScroll on IOS devices.");return}if(!i.some(function(r){return r.targetElement===e})){var t={targetElement:e,options:{}};i=[].concat(w(i),[t]),c?(e.ontouchstart=function(r){r.targetTouches.length===1&&(v=r.targetTouches[0].clientY)},e.ontouchmove=function(r){r.targetTouches.length===1&&m(r,e)},u||(document.addEventListener("touchmove",a,s?{passive:!1}:void 0),u=!0)):g(o)}},S=function(e){if(!e){console.error("enableBodyScroll unsuccessful - targetElement must be provided when calling enableBodyScroll on IOS devices.");return}i=i.filter(function(o){return o.targetElement!==e}),c?(e.ontouchstart=null,e.ontouchmove=null,u&&i.length===0&&(document.removeEventListener("touchmove",a,s?{passive:!1}:void 0),u=!1)):i.length||y()};function T(n,e){let o=!1;e.mobileToggle.addEventListener("click",t=>{t.preventDefault(),o=!o,e.mobileToggle.setAttribute("aria-expanded",o),o?b(e.NavigationMobile):S(e.NavigationMobile)})}export{T as default};
