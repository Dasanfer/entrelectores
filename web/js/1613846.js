/*!
 * @overview es6-promise - a tiny implementation of Promises/A+.
 * @copyright Copyright (c) 2014 Yehuda Katz, Tom Dale, Stefan Penner and contributors (Conversion to ES6 API by Jake Archibald)
 * @license   Licensed under MIT license
 *            See https://raw.githubusercontent.com/jakearchibald/es6-promise/master/LICENSE
 * @version   2.0.0
 */

(function(){function r(a,b){n[l]=a;n[l+1]=b;l+=2;2===l&&A()}function s(a){return"function"===typeof a}function F(){return function(){process.nextTick(t)}}function G(){var a=0,b=new B(t),c=document.createTextNode("");b.observe(c,{characterData:!0});return function(){c.data=a=++a%2}}function H(){var a=new MessageChannel;a.port1.onmessage=t;return function(){a.port2.postMessage(0)}}function I(){return function(){setTimeout(t,1)}}function t(){for(var a=0;a<l;a+=2)(0,n[a])(n[a+1]),n[a]=void 0,n[a+1]=void 0;
l=0}function p(){}function J(a,b,c,d){try{a.call(b,c,d)}catch(e){return e}}function K(a,b,c){r(function(a){var e=!1,f=J(c,b,function(c){e||(e=!0,b!==c?q(a,c):m(a,c))},function(b){e||(e=!0,g(a,b))});!e&&f&&(e=!0,g(a,f))},a)}function L(a,b){1===b.a?m(a,b.b):2===a.a?g(a,b.b):u(b,void 0,function(b){q(a,b)},function(b){g(a,b)})}function q(a,b){if(a===b)g(a,new TypeError("You cannot resolve a promise with itself"));else if("function"===typeof b||"object"===typeof b&&null!==b)if(b.constructor===a.constructor)L(a,
b);else{var c;try{c=b.then}catch(d){v.error=d,c=v}c===v?g(a,v.error):void 0===c?m(a,b):s(c)?K(a,b,c):m(a,b)}else m(a,b)}function M(a){a.f&&a.f(a.b);x(a)}function m(a,b){void 0===a.a&&(a.b=b,a.a=1,0!==a.e.length&&r(x,a))}function g(a,b){void 0===a.a&&(a.a=2,a.b=b,r(M,a))}function u(a,b,c,d){var e=a.e,f=e.length;a.f=null;e[f]=b;e[f+1]=c;e[f+2]=d;0===f&&a.a&&r(x,a)}function x(a){var b=a.e,c=a.a;if(0!==b.length){for(var d,e,f=a.b,g=0;g<b.length;g+=3)d=b[g],e=b[g+c],d?C(c,d,e,f):e(f);a.e.length=0}}function D(){this.error=
null}function C(a,b,c,d){var e=s(c),f,k,h,l;if(e){try{f=c(d)}catch(n){y.error=n,f=y}f===y?(l=!0,k=f.error,f=null):h=!0;if(b===f){g(b,new TypeError("A promises callback cannot return that same promise."));return}}else f=d,h=!0;void 0===b.a&&(e&&h?q(b,f):l?g(b,k):1===a?m(b,f):2===a&&g(b,f))}function N(a,b){try{b(function(b){q(a,b)},function(b){g(a,b)})}catch(c){g(a,c)}}function k(a,b,c,d){this.n=a;this.c=new a(p,d);this.i=c;this.o(b)?(this.m=b,this.d=this.length=b.length,this.l(),0===this.length?m(this.c,
this.b):(this.length=this.length||0,this.k(),0===this.d&&m(this.c,this.b))):g(this.c,this.p())}function h(a){O++;this.b=this.a=void 0;this.e=[];if(p!==a){if(!s(a))throw new TypeError("You must pass a resolver function as the first argument to the promise constructor");if(!(this instanceof h))throw new TypeError("Failed to construct 'Promise': Please use the 'new' operator, this object constructor cannot be called as a function.");N(this,a)}}var E=Array.isArray?Array.isArray:function(a){return"[object Array]"===
Object.prototype.toString.call(a)},l=0,w="undefined"!==typeof window?window:{},B=w.MutationObserver||w.WebKitMutationObserver,w="undefined"!==typeof Uint8ClampedArray&&"undefined"!==typeof importScripts&&"undefined"!==typeof MessageChannel,n=Array(1E3),A;A="undefined"!==typeof process&&"[object process]"==={}.toString.call(process)?F():B?G():w?H():I();var v=new D,y=new D;k.prototype.o=function(a){return E(a)};k.prototype.p=function(){return Error("Array Methods must be provided an Array")};k.prototype.l=
function(){this.b=Array(this.length)};k.prototype.k=function(){for(var a=this.length,b=this.c,c=this.m,d=0;void 0===b.a&&d<a;d++)this.j(c[d],d)};k.prototype.j=function(a,b){var c=this.n;"object"===typeof a&&null!==a?a.constructor===c&&void 0!==a.a?(a.f=null,this.g(a.a,b,a.b)):this.q(c.resolve(a),b):(this.d--,this.b[b]=this.h(a))};k.prototype.g=function(a,b,c){var d=this.c;void 0===d.a&&(this.d--,this.i&&2===a?g(d,c):this.b[b]=this.h(c));0===this.d&&m(d,this.b)};k.prototype.h=function(a){return a};
k.prototype.q=function(a,b){var c=this;u(a,void 0,function(a){c.g(1,b,a)},function(a){c.g(2,b,a)})};var O=0;h.all=function(a,b){return(new k(this,a,!0,b)).c};h.race=function(a,b){function c(a){q(e,a)}function d(a){g(e,a)}var e=new this(p,b);if(!E(a))return (g(e,new TypeError("You must pass an array to race.")), e);for(var f=a.length,h=0;void 0===e.a&&h<f;h++)u(this.resolve(a[h]),void 0,c,d);return e};h.resolve=function(a,b){if(a&&"object"===typeof a&&a.constructor===this)return a;var c=new this(p,b);
q(c,a);return c};h.reject=function(a,b){var c=new this(p,b);g(c,a);return c};h.prototype={constructor:h,then:function(a,b){var c=this.a;if(1===c&&!a||2===c&&!b)return this;var d=new this.constructor(p),e=this.b;if(c){var f=arguments[c-1];r(function(){C(c,d,f,e)})}else u(this,d,a,b);return d},"catch":function(a){return this.then(null,a)}};var z={Promise:h,polyfill:function(){var a;a="undefined"!==typeof global?global:"undefined"!==typeof window&&window.document?window:self;"Promise"in a&&"resolve"in
a.Promise&&"reject"in a.Promise&&"all"in a.Promise&&"race"in a.Promise&&function(){var b;new a.Promise(function(a){b=a});return s(b)}()||(a.Promise=h)}};"function"===typeof define&&define.amd?define(function(){return z}):"undefined"!==typeof module&&module.exports?module.exports=z:"undefined"!==typeof this&&(this.ES6Promise=z)}).call(this);

//     Underscore.js 1.7.0
//     http://underscorejs.org
//     (c) 2009-2014 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
//     Underscore may be freely distributed under the MIT license.
(function(){var n=this,t=n._,r=Array.prototype,e=Object.prototype,u=Function.prototype,i=r.push,a=r.slice,o=r.concat,l=e.toString,c=e.hasOwnProperty,f=Array.isArray,s=Object.keys,p=u.bind,h=function(n){return n instanceof h?n:this instanceof h?void(this._wrapped=n):new h(n)};"undefined"!=typeof exports?("undefined"!=typeof module&&module.exports&&(exports=module.exports=h),exports._=h):n._=h,h.VERSION="1.7.0";var g=function(n,t,r){if(t===void 0)return n;switch(null==r?3:r){case 1:return function(r){return n.call(t,r)};case 2:return function(r,e){return n.call(t,r,e)};case 3:return function(r,e,u){return n.call(t,r,e,u)};case 4:return function(r,e,u,i){return n.call(t,r,e,u,i)}}return function(){return n.apply(t,arguments)}};h.iteratee=function(n,t,r){return null==n?h.identity:h.isFunction(n)?g(n,t,r):h.isObject(n)?h.matches(n):h.property(n)},h.each=h.forEach=function(n,t,r){if(null==n)return n;t=g(t,r);var e,u=n.length;if(u===+u)for(e=0;u>e;e++)t(n[e],e,n);else{var i=h.keys(n);for(e=0,u=i.length;u>e;e++)t(n[i[e]],i[e],n)}return n},h.map=h.collect=function(n,t,r){if(null==n)return[];t=h.iteratee(t,r);for(var e,u=n.length!==+n.length&&h.keys(n),i=(u||n).length,a=Array(i),o=0;i>o;o++)e=u?u[o]:o,a[o]=t(n[e],e,n);return a};var v="Reduce of empty array with no initial value";h.reduce=h.foldl=h.inject=function(n,t,r,e){null==n&&(n=[]),t=g(t,e,4);var u,i=n.length!==+n.length&&h.keys(n),a=(i||n).length,o=0;if(arguments.length<3){if(!a)throw new TypeError(v);r=n[i?i[o++]:o++]}for(;a>o;o++)u=i?i[o]:o,r=t(r,n[u],u,n);return r},h.reduceRight=h.foldr=function(n,t,r,e){null==n&&(n=[]),t=g(t,e,4);var u,i=n.length!==+n.length&&h.keys(n),a=(i||n).length;if(arguments.length<3){if(!a)throw new TypeError(v);r=n[i?i[--a]:--a]}for(;a--;)u=i?i[a]:a,r=t(r,n[u],u,n);return r},h.find=h.detect=function(n,t,r){var e;return t=h.iteratee(t,r),h.some(n,function(n,r,u){return t(n,r,u)?(e=n,!0):void 0}),e},h.filter=h.select=function(n,t,r){var e=[];return null==n?e:(t=h.iteratee(t,r),h.each(n,function(n,r,u){t(n,r,u)&&e.push(n)}),e)},h.reject=function(n,t,r){return h.filter(n,h.negate(h.iteratee(t)),r)},h.every=h.all=function(n,t,r){if(null==n)return!0;t=h.iteratee(t,r);var e,u,i=n.length!==+n.length&&h.keys(n),a=(i||n).length;for(e=0;a>e;e++)if(u=i?i[e]:e,!t(n[u],u,n))return!1;return!0},h.some=h.any=function(n,t,r){if(null==n)return!1;t=h.iteratee(t,r);var e,u,i=n.length!==+n.length&&h.keys(n),a=(i||n).length;for(e=0;a>e;e++)if(u=i?i[e]:e,t(n[u],u,n))return!0;return!1},h.contains=h.include=function(n,t){return null==n?!1:(n.length!==+n.length&&(n=h.values(n)),h.indexOf(n,t)>=0)},h.invoke=function(n,t){var r=a.call(arguments,2),e=h.isFunction(t);return h.map(n,function(n){return(e?t:n[t]).apply(n,r)})},h.pluck=function(n,t){return h.map(n,h.property(t))},h.where=function(n,t){return h.filter(n,h.matches(t))},h.findWhere=function(n,t){return h.find(n,h.matches(t))},h.max=function(n,t,r){var e,u,i=-1/0,a=-1/0;if(null==t&&null!=n){n=n.length===+n.length?n:h.values(n);for(var o=0,l=n.length;l>o;o++)e=n[o],e>i&&(i=e)}else t=h.iteratee(t,r),h.each(n,function(n,r,e){u=t(n,r,e),(u>a||u===-1/0&&i===-1/0)&&(i=n,a=u)});return i},h.min=function(n,t,r){var e,u,i=1/0,a=1/0;if(null==t&&null!=n){n=n.length===+n.length?n:h.values(n);for(var o=0,l=n.length;l>o;o++)e=n[o],i>e&&(i=e)}else t=h.iteratee(t,r),h.each(n,function(n,r,e){u=t(n,r,e),(a>u||1/0===u&&1/0===i)&&(i=n,a=u)});return i},h.shuffle=function(n){for(var t,r=n&&n.length===+n.length?n:h.values(n),e=r.length,u=Array(e),i=0;e>i;i++)t=h.random(0,i),t!==i&&(u[i]=u[t]),u[t]=r[i];return u},h.sample=function(n,t,r){return null==t||r?(n.length!==+n.length&&(n=h.values(n)),n[h.random(n.length-1)]):h.shuffle(n).slice(0,Math.max(0,t))},h.sortBy=function(n,t,r){return t=h.iteratee(t,r),h.pluck(h.map(n,function(n,r,e){return{value:n,index:r,criteria:t(n,r,e)}}).sort(function(n,t){var r=n.criteria,e=t.criteria;if(r!==e){if(r>e||r===void 0)return 1;if(e>r||e===void 0)return-1}return n.index-t.index}),"value")};var m=function(n){return function(t,r,e){var u={};return r=h.iteratee(r,e),h.each(t,function(e,i){var a=r(e,i,t);n(u,e,a)}),u}};h.groupBy=m(function(n,t,r){h.has(n,r)?n[r].push(t):n[r]=[t]}),h.indexBy=m(function(n,t,r){n[r]=t}),h.countBy=m(function(n,t,r){h.has(n,r)?n[r]++:n[r]=1}),h.sortedIndex=function(n,t,r,e){r=h.iteratee(r,e,1);for(var u=r(t),i=0,a=n.length;a>i;){var o=i+a>>>1;r(n[o])<u?i=o+1:a=o}return i},h.toArray=function(n){return n?h.isArray(n)?a.call(n):n.length===+n.length?h.map(n,h.identity):h.values(n):[]},h.size=function(n){return null==n?0:n.length===+n.length?n.length:h.keys(n).length},h.partition=function(n,t,r){t=h.iteratee(t,r);var e=[],u=[];return h.each(n,function(n,r,i){(t(n,r,i)?e:u).push(n)}),[e,u]},h.first=h.head=h.take=function(n,t,r){return null==n?void 0:null==t||r?n[0]:0>t?[]:a.call(n,0,t)},h.initial=function(n,t,r){return a.call(n,0,Math.max(0,n.length-(null==t||r?1:t)))},h.last=function(n,t,r){return null==n?void 0:null==t||r?n[n.length-1]:a.call(n,Math.max(n.length-t,0))},h.rest=h.tail=h.drop=function(n,t,r){return a.call(n,null==t||r?1:t)},h.compact=function(n){return h.filter(n,h.identity)};var y=function(n,t,r,e){if(t&&h.every(n,h.isArray))return o.apply(e,n);for(var u=0,a=n.length;a>u;u++){var l=n[u];h.isArray(l)||h.isArguments(l)?t?i.apply(e,l):y(l,t,r,e):r||e.push(l)}return e};h.flatten=function(n,t){return y(n,t,!1,[])},h.without=function(n){return h.difference(n,a.call(arguments,1))},h.uniq=h.unique=function(n,t,r,e){if(null==n)return[];h.isBoolean(t)||(e=r,r=t,t=!1),null!=r&&(r=h.iteratee(r,e));for(var u=[],i=[],a=0,o=n.length;o>a;a++){var l=n[a];if(t)a&&i===l||u.push(l),i=l;else if(r){var c=r(l,a,n);h.indexOf(i,c)<0&&(i.push(c),u.push(l))}else h.indexOf(u,l)<0&&u.push(l)}return u},h.union=function(){return h.uniq(y(arguments,!0,!0,[]))},h.intersection=function(n){if(null==n)return[];for(var t=[],r=arguments.length,e=0,u=n.length;u>e;e++){var i=n[e];if(!h.contains(t,i)){for(var a=1;r>a&&h.contains(arguments[a],i);a++);a===r&&t.push(i)}}return t},h.difference=function(n){var t=y(a.call(arguments,1),!0,!0,[]);return h.filter(n,function(n){return!h.contains(t,n)})},h.zip=function(n){if(null==n)return[];for(var t=h.max(arguments,"length").length,r=Array(t),e=0;t>e;e++)r[e]=h.pluck(arguments,e);return r},h.object=function(n,t){if(null==n)return{};for(var r={},e=0,u=n.length;u>e;e++)t?r[n[e]]=t[e]:r[n[e][0]]=n[e][1];return r},h.indexOf=function(n,t,r){if(null==n)return-1;var e=0,u=n.length;if(r){if("number"!=typeof r)return e=h.sortedIndex(n,t),n[e]===t?e:-1;e=0>r?Math.max(0,u+r):r}for(;u>e;e++)if(n[e]===t)return e;return-1},h.lastIndexOf=function(n,t,r){if(null==n)return-1;var e=n.length;for("number"==typeof r&&(e=0>r?e+r+1:Math.min(e,r+1));--e>=0;)if(n[e]===t)return e;return-1},h.range=function(n,t,r){arguments.length<=1&&(t=n||0,n=0),r=r||1;for(var e=Math.max(Math.ceil((t-n)/r),0),u=Array(e),i=0;e>i;i++,n+=r)u[i]=n;return u};var d=function(){};h.bind=function(n,t){var r,e;if(p&&n.bind===p)return p.apply(n,a.call(arguments,1));if(!h.isFunction(n))throw new TypeError("Bind must be called on a function");return r=a.call(arguments,2),e=function(){if(!(this instanceof e))return n.apply(t,r.concat(a.call(arguments)));d.prototype=n.prototype;var u=new d;d.prototype=null;var i=n.apply(u,r.concat(a.call(arguments)));return h.isObject(i)?i:u}},h.partial=function(n){var t=a.call(arguments,1);return function(){for(var r=0,e=t.slice(),u=0,i=e.length;i>u;u++)e[u]===h&&(e[u]=arguments[r++]);for(;r<arguments.length;)e.push(arguments[r++]);return n.apply(this,e)}},h.bindAll=function(n){var t,r,e=arguments.length;if(1>=e)throw new Error("bindAll must be passed function names");for(t=1;e>t;t++)r=arguments[t],n[r]=h.bind(n[r],n);return n},h.memoize=function(n,t){var r=function(e){var u=r.cache,i=t?t.apply(this,arguments):e;return h.has(u,i)||(u[i]=n.apply(this,arguments)),u[i]};return r.cache={},r},h.delay=function(n,t){var r=a.call(arguments,2);return setTimeout(function(){return n.apply(null,r)},t)},h.defer=function(n){return h.delay.apply(h,[n,1].concat(a.call(arguments,1)))},h.throttle=function(n,t,r){var e,u,i,a=null,o=0;r||(r={});var l=function(){o=r.leading===!1?0:h.now(),a=null,i=n.apply(e,u),a||(e=u=null)};return function(){var c=h.now();o||r.leading!==!1||(o=c);var f=t-(c-o);return e=this,u=arguments,0>=f||f>t?(clearTimeout(a),a=null,o=c,i=n.apply(e,u),a||(e=u=null)):a||r.trailing===!1||(a=setTimeout(l,f)),i}},h.debounce=function(n,t,r){var e,u,i,a,o,l=function(){var c=h.now()-a;t>c&&c>0?e=setTimeout(l,t-c):(e=null,r||(o=n.apply(i,u),e||(i=u=null)))};return function(){i=this,u=arguments,a=h.now();var c=r&&!e;return e||(e=setTimeout(l,t)),c&&(o=n.apply(i,u),i=u=null),o}},h.wrap=function(n,t){return h.partial(t,n)},h.negate=function(n){return function(){return!n.apply(this,arguments)}},h.compose=function(){var n=arguments,t=n.length-1;return function(){for(var r=t,e=n[t].apply(this,arguments);r--;)e=n[r].call(this,e);return e}},h.after=function(n,t){return function(){return--n<1?t.apply(this,arguments):void 0}},h.before=function(n,t){var r;return function(){return--n>0?r=t.apply(this,arguments):t=null,r}},h.once=h.partial(h.before,2),h.keys=function(n){if(!h.isObject(n))return[];if(s)return s(n);var t=[];for(var r in n)h.has(n,r)&&t.push(r);return t},h.values=function(n){for(var t=h.keys(n),r=t.length,e=Array(r),u=0;r>u;u++)e[u]=n[t[u]];return e},h.pairs=function(n){for(var t=h.keys(n),r=t.length,e=Array(r),u=0;r>u;u++)e[u]=[t[u],n[t[u]]];return e},h.invert=function(n){for(var t={},r=h.keys(n),e=0,u=r.length;u>e;e++)t[n[r[e]]]=r[e];return t},h.functions=h.methods=function(n){var t=[];for(var r in n)h.isFunction(n[r])&&t.push(r);return t.sort()},h.extend=function(n){if(!h.isObject(n))return n;for(var t,r,e=1,u=arguments.length;u>e;e++){t=arguments[e];for(r in t)c.call(t,r)&&(n[r]=t[r])}return n},h.pick=function(n,t,r){var e,u={};if(null==n)return u;if(h.isFunction(t)){t=g(t,r);for(e in n){var i=n[e];t(i,e,n)&&(u[e]=i)}}else{var l=o.apply([],a.call(arguments,1));n=new Object(n);for(var c=0,f=l.length;f>c;c++)e=l[c],e in n&&(u[e]=n[e])}return u},h.omit=function(n,t,r){if(h.isFunction(t))t=h.negate(t);else{var e=h.map(o.apply([],a.call(arguments,1)),String);t=function(n,t){return!h.contains(e,t)}}return h.pick(n,t,r)},h.defaults=function(n){if(!h.isObject(n))return n;for(var t=1,r=arguments.length;r>t;t++){var e=arguments[t];for(var u in e)n[u]===void 0&&(n[u]=e[u])}return n},h.clone=function(n){return h.isObject(n)?h.isArray(n)?n.slice():h.extend({},n):n},h.tap=function(n,t){return t(n),n};var b=function(n,t,r,e){if(n===t)return 0!==n||1/n===1/t;if(null==n||null==t)return n===t;n instanceof h&&(n=n._wrapped),t instanceof h&&(t=t._wrapped);var u=l.call(n);if(u!==l.call(t))return!1;switch(u){case"[object RegExp]":case"[object String]":return""+n==""+t;case"[object Number]":return+n!==+n?+t!==+t:0===+n?1/+n===1/t:+n===+t;case"[object Date]":case"[object Boolean]":return+n===+t}if("object"!=typeof n||"object"!=typeof t)return!1;for(var i=r.length;i--;)if(r[i]===n)return e[i]===t;var a=n.constructor,o=t.constructor;if(a!==o&&"constructor"in n&&"constructor"in t&&!(h.isFunction(a)&&a instanceof a&&h.isFunction(o)&&o instanceof o))return!1;r.push(n),e.push(t);var c,f;if("[object Array]"===u){if(c=n.length,f=c===t.length)for(;c--&&(f=b(n[c],t[c],r,e)););}else{var s,p=h.keys(n);if(c=p.length,f=h.keys(t).length===c)for(;c--&&(s=p[c],f=h.has(t,s)&&b(n[s],t[s],r,e)););}return r.pop(),e.pop(),f};h.isEqual=function(n,t){return b(n,t,[],[])},h.isEmpty=function(n){if(null==n)return!0;if(h.isArray(n)||h.isString(n)||h.isArguments(n))return 0===n.length;for(var t in n)if(h.has(n,t))return!1;return!0},h.isElement=function(n){return!(!n||1!==n.nodeType)},h.isArray=f||function(n){return"[object Array]"===l.call(n)},h.isObject=function(n){var t=typeof n;return"function"===t||"object"===t&&!!n},h.each(["Arguments","Function","String","Number","Date","RegExp"],function(n){h["is"+n]=function(t){return l.call(t)==="[object "+n+"]"}}),h.isArguments(arguments)||(h.isArguments=function(n){return h.has(n,"callee")}),"function"!=typeof/./&&(h.isFunction=function(n){return"function"==typeof n||!1}),h.isFinite=function(n){return isFinite(n)&&!isNaN(parseFloat(n))},h.isNaN=function(n){return h.isNumber(n)&&n!==+n},h.isBoolean=function(n){return n===!0||n===!1||"[object Boolean]"===l.call(n)},h.isNull=function(n){return null===n},h.isUndefined=function(n){return n===void 0},h.has=function(n,t){return null!=n&&c.call(n,t)},h.noConflict=function(){return n._=t,this},h.identity=function(n){return n},h.constant=function(n){return function(){return n}},h.noop=function(){},h.property=function(n){return function(t){return t[n]}},h.matches=function(n){var t=h.pairs(n),r=t.length;return function(n){if(null==n)return!r;n=new Object(n);for(var e=0;r>e;e++){var u=t[e],i=u[0];if(u[1]!==n[i]||!(i in n))return!1}return!0}},h.times=function(n,t,r){var e=Array(Math.max(0,n));t=g(t,r,1);for(var u=0;n>u;u++)e[u]=t(u);return e},h.random=function(n,t){return null==t&&(t=n,n=0),n+Math.floor(Math.random()*(t-n+1))},h.now=Date.now||function(){return(new Date).getTime()};var _={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},w=h.invert(_),j=function(n){var t=function(t){return n[t]},r="(?:"+h.keys(n).join("|")+")",e=RegExp(r),u=RegExp(r,"g");return function(n){return n=null==n?"":""+n,e.test(n)?n.replace(u,t):n}};h.escape=j(_),h.unescape=j(w),h.result=function(n,t){if(null==n)return void 0;var r=n[t];return h.isFunction(r)?n[t]():r};var x=0;h.uniqueId=function(n){var t=++x+"";return n?n+t:t},h.templateSettings={evaluate:/<%([\s\S]+?)%>/g,interpolate:/<%=([\s\S]+?)%>/g,escape:/<%-([\s\S]+?)%>/g};var A=/(.)^/,k={"'":"'","\\":"\\","\r":"r","\n":"n","\u2028":"u2028","\u2029":"u2029"},O=/\\|'|\r|\n|\u2028|\u2029/g,F=function(n){return"\\"+k[n]};h.template=function(n,t,r){!t&&r&&(t=r),t=h.defaults({},t,h.templateSettings);var e=RegExp([(t.escape||A).source,(t.interpolate||A).source,(t.evaluate||A).source].join("|")+"|$","g"),u=0,i="__p+='";n.replace(e,function(t,r,e,a,o){return i+=n.slice(u,o).replace(O,F),u=o+t.length,r?i+="'+\n((__t=("+r+"))==null?'':_.escape(__t))+\n'":e?i+="'+\n((__t=("+e+"))==null?'':__t)+\n'":a&&(i+="';\n"+a+"\n__p+='"),t}),i+="';\n",t.variable||(i="with(obj||{}){\n"+i+"}\n"),i="var __t,__p='',__j=Array.prototype.join,"+"print=function(){__p+=__j.call(arguments,'');};\n"+i+"return __p;\n";try{var a=new Function(t.variable||"obj","_",i)}catch(o){throw o.source=i,o}var l=function(n){return a.call(this,n,h)},c=t.variable||"obj";return l.source="function("+c+"){\n"+i+"}",l},h.chain=function(n){var t=h(n);return t._chain=!0,t};var E=function(n){return this._chain?h(n).chain():n};h.mixin=function(n){h.each(h.functions(n),function(t){var r=h[t]=n[t];h.prototype[t]=function(){var n=[this._wrapped];return i.apply(n,arguments),E.call(this,r.apply(h,n))}})},h.mixin(h),h.each(["pop","push","reverse","shift","sort","splice","unshift"],function(n){var t=r[n];h.prototype[n]=function(){var r=this._wrapped;return t.apply(r,arguments),"shift"!==n&&"splice"!==n||0!==r.length||delete r[0],E.call(this,r)}}),h.each(["concat","join","slice"],function(n){var t=r[n];h.prototype[n]=function(){return E.call(this,t.apply(this._wrapped,arguments))}}),h.prototype.value=function(){return this._wrapped},"function"==typeof define&&define.amd&&define("underscore",[],function(){return h})}).call(this);
//# sourceMappingURL=underscore-min.map
var exists = function(x) { return x != null && typeof x != 'undefined'; };

var truthy = function(x) {
  return (x !== false) && exists(x);
};

(function(appbundle, $, undefined ) {


    appbundle.setupRating = function(element, rate){

        if(typeof element == 'string')
            element = $(element);

        var stars = element.find('li');
        stars.each(function(ix,el){$(el).removeClass('half whole').addClass('empty');});
        for(var i = 0; rate > 0; rate = rate - 1){
            if(i % 2 == 0){
                $(stars[i/2]).removeClass('empty');
                $(stars[i/2]).addClass('half');
            } else {
                $(stars[(i-1)/2]).removeClass('half');
                $(stars[(i-1)/2]).addClass('whole');
            }
            i = i + 1;
        }
    }

    appbundle.starHover = function(el){
        el.closest('ul').find('li').removeClass('rover halfrover');

        if(el.hasClass('r'))
            el.parent().addClass('rover');
        else
            el.parent().addClass('halfrover');

        var prev = el.parent().prev();
        while(prev.is('li')){
            prev.removeClass('rover halfrover');
            prev.addClass('rover');
            prev = prev.prev();
        }
    }

    appbundle.ratingHoverOut = function(el){
        el.find('li').each(function(ix,el){$(el).removeClass('halfrover rover')})
    }

    appbundle.sendRating = function(element,value,bookId){
        var route = Routing.generate('rate_book',{id: bookId});
        $.post(
            route,
            {value:value},
            function(){
                element.attr('data-rating',value);
                appbundle.setupRating(element,value);
            }
        );
    }

    appbundle.sendReview = function(form){
        var data = form.serializeArray();
        form.find('.form_errors').remove();

        if(!_.some(data,function(val){return val['name'] == 'review_public[spoiler]';}))
            data['review_public[spoiler]'] = false;

        var route = Routing.generate('post_review');
        $.ajax(
            {
                method:'POST',
                url: route,
                data: data,
                error: function(xhr,txtstatus,error){
                    $.each(xhr.responseJSON.form.children,function(name,val){
                        if(exists(val.errors)){
                            var list = $('<ul class="form_errors">');

                            $.each(val.errors,function(ix,val){
                                list.append('<li>'+val+'</li>');
                            });

                            form.find('[name="review_public['+name+']"]').before(list);
                        }
                    });
                },
                success: function(){
                    form.hide(200);
                    book.reloadReviews();
                }
            }
        );
    }

    appbundle.addBookRelation = function(relation,bookId,button){
        var url = Routing.generate('user_add_'+relation);
        $.ajax(
            {
                url: url,
                method:'POST',
                data: {book:bookId},
                error: function(){
                    console.log(arguments);
                },
                success: function(){
                    button.parent().find('a').removeClass('boton_celeste');
                    button.addClass('boton_celeste');
                    if(relation == 'read'){
                        $('.book-tab').removeClass('activo');
                        $('#goto-reviews').addClass('activo');
                        book.bookId = bookId;
                        book.gotoReviews();
                        $('html, body').animate({
                            scrollTop: $("#publicar-resena").offset().top
                        }, 1000);
                    }
                }
            }
        );
    }


    appbundle.checkBookRelation = function(){
        if(user_id == 'anon')
            return;

        if($('.js-reading-buttons').length <= 0)
            return;

        var bookId = $('.js-reading-buttons').attr('data-book-id');
        var url = Routing.generate('user_book_relation',{id:bookId});
        $.ajax({
            url: url,
            method: 'GET',
            error: console.log,
            success: function(data){
                if(data.read)
                    $('#js-set-read').addClass('boton_celeste');
                if(data.reading)
                    $('#js-set-reading').addClass('boton_celeste');
                if(data.wants)
                    $('#js-set-want').addClass('boton_celeste');
            }
        });

    }

    appbundle.poll_vote = function(element){
        var selected = element.closest('form').find('input:checked');
        if(selected.length > 0){
            var value = selected.val();
            console.log('poll option',value);
            $.ajax({
                url: Routing.generate('poll_vote'),
                data:{option: value},
                method: 'POST',
                success: function(data){
                    element.closest('.single_poll').html(data);
                },
                error: console.log
            });
        }
    }

    appbundle.showRegisterModal = function(){
        $('#register-modal').show();
    }


    appbundle.setErrors = function (errors, container,formName) {
        $('span.validation-error').remove();
        var fields = errors.children;
        _.each(fields,function(error,name){
            _.each(error.errors,function(error){
                container.find('[name="'+formName+'['+name+']"]').after('<span class="validation-error">'+error+'</span>');}
            );
        });
    }

    appbundle.setupRatings = function(){
        $('[data-rating]').each(function(ix,el){
            el = $(el);

            if($(el).attr('setup'))
                return;

            $(el).attr('setup',true);

            appbundle.setupRating(el,parseFloat(el.attr('data-rating')));

            if(!el.parent().hasClass('disabled')){
                el.find('span').hover(
                    function(){
                        appbundle.starHover($(this));
                    }
                );
                el.find('span').click(
                    function(event){
                        event.preventDefault();
                        event.stopPropagation();
                        var value = $(this).attr('data-value');
                        var bookId = $(this).closest('ul').attr('data-id');
                        appbundle.sendRating($(el),value,bookId);

                        $('ul[data-id="'+bookId+'"]').each(function(ix,el){
                            if(!$(el).hasClass('disabled')){
                                $(el).attr('data-rating',value);
                            }
                        });
                    }
                );
                el.hover(
                    function(){},
                    function(){
                    appbundle.ratingHoverOut(el);
                    }
                );
            }

        });
    }

    $( document ).ready(function() {
        appbundle.setupRatings();
        appbundle.checkBookRelation();
        $('.js-trigger-panel').click( function(event){
            event.preventDefault();
            var its_id = $(this).attr('id');
            $('.js-panel').each( function() {
                if ( $(this).attr('id') != 'panel-' + its_id ) {
                    $(this).slideUp();
                }
            });
            $('#panel-' + its_id ).slideToggle();
        });

        if(exists(location.hash))
            $(window).trigger('navigate_'+location.hash)
    });

    $( document ).ajaxError(function(event,xhr,settings,error) {
        if(xhr.status == 401 || xhr.status == 403){
            appbundle.showRegisterModal();
        }
    });

    $(window).on('hashchange', function(event) {
        $(window).trigger('navigate_'+location.hash)
    });

    appbundle.mostrado = function() {
        $.cookie("visto_143015", true, { expires: 10000, path: '/' } );
        $('.overlay.pop_up').fadeOut();
    };

    $('html').on('click.panel',function(event){
        if($(event.target).closest('.js-trigger-panel').length == 0)
            $('.js-panel').hide(100);
    })
}( window.appbundle = window.appbundle || {}, jQuery ));



(function(lists, $, undefined ) {

    lists.addModal = function(lists){
        var input_template = _.template($('#input-lista-template').text());
        _.each(lists,function(list){
            $('form.listas').prepend(input_template(list));
        });
    }

    lists.submit = function(form){
        var list_id = form.find(":checked").val();
        var new_list_name = null;
        var post_data = {book: $('#list-book-input').val()};

        if(list_id == null || list_id < 0){
            var new_list_name = $('#nuevalista').val();
        }

        if(list_id > 0)
            post_data['list'] = list_id;
        else if(new_list_name != '')
            post_data['newName'] = new_list_name;
        else
            return;

        $.ajax({
            method: 'post',
            url: Routing.generate('add_to_list'),
            data: post_data,
            success: function(data){
                $('#overlay_add_to_list').hide();
                $('#btn_listas_pop_up').hide();
                if(post_data['newName']){
                    var newUrl = Routing.generate('edit_list',{id:data.list});
                    window.location = newUrl;
                }
            },
            error: function(){
                console.log(arguments);
            }
        });
    }

    lists.addToList = function(bookId){
        $('.list-input').remove();
        $('#list-book-input').val(bookId);
        var lists_route = Routing.generate('my_lists');
        $.ajax({
            method: 'get',
            dataType: 'json',
            url: lists_route,
            success: lists.addModal,
            error: function(){
                console.error(arguments);
            }
        });
    }

    lists.changeToPrivate = function(){
        $('#edit_booklist_publicFlag').val(0);
        $('#public-list').hide();
        $('#private-list').show();
        $('#overlay_privacidad').hide();
    }

    lists.changeToPublic = function(){
        $('#edit_booklist_publicFlag').val(1);
        $('#public-list').show();
        $('#private-list').hide();
    }

    lists.removeBook = function(book_id,list_id){
        var prom = new Promise(function(resolve,reject){
            $.ajax({
                method: 'DELETE',
                url: Routing.generate('remove_from_list'),
                data: {list:list_id, book:book_id},
                success: resolve,
                error: reject
            });
        });
        return prom;
    }

    lists.loadBooks = function(){
        var offset = $('.js-book-list-entry').length;
        var count = 10;
        var listId = $('#all-list-books').attr('data-list-id');
        ajaxBooks(listId,count,offset).then(function(html){return loadNewBooks(html,count)});
    }

    var loadNewBooks = function(newHtml,count){
        var newEntriesCount = (newHtml.match(/js-book-list-entry/g) || []).length;
        if(newEntriesCount < count){
            $('#all-list-books').find('.js-load-more').hide();
        }
        else {
            $('#all-list-books').find('.js-load-more').show();
        }
        $('#all-list-books .js-entries-container').append(newHtml);
        lists.attachRemove($('#all-list-books'));
    }

    var ajaxBooks = function(listId,count,offset){
        return new Promise(function(resolve,reject){
            $.ajax({
                url: Routing.generate('books_in_list',{id:listId,count:count,offset:offset}),
                method: 'GET',
                success: resolve,
                error: function(){
                    reject();
                }
            });
        });

    }

    lists.setupBookSection = function(){
        if($('#all-list-books').attr('data-setup'))
            return;

        $('#all-list-books').attr('data-setup',true);
        var moreButton = $('#all-list-books').find('.js-load-more');
        moreButton.on('click.load-books',function(event){event.preventDefault();lists.loadBooks()});
        lists.loadBooks();
    }

    lists.attachRemove = function(baseEl){
        _.each(baseEl.find('.list-remove-button'),function(el){
            el = $(el);
            el.unbind('click.remove');
            var book_id = el.attr('data-id');
            var list_id = el.attr('data-list-id');
            el.on('click.remove',getRemoveClickFunc(el,book_id,list_id));
        });
    }

    var getRemoveClickFunc = function(button,book_id,list_id){
        return function(event){
            event.preventDefault();
            lists.removeBook(book_id,list_id).then(button.closest('.libro_header').remove());
        }
    }

    $('#convertir_lista_publica').on('click.list',function(event){
        event.preventDefault();
        lists.changeToPublic();
    });

    $('#convertir_lista_privada').on('click.list',function(event){
        $('#overlay_privacidad').show();
    });

    $(window).on('navigate_#list-books',function(event){
        event.preventDefault();
        $('.book-tab').removeClass('activo');
        $('#goto-list-books').addClass('activo');
        $('.js-general-content').hide();
        $('#all-list-books').show();
        lists.setupBookSection();
    });

    $(window).on('navigate_#list-info',function(event){
        event.preventDefault();
        $('.book-tab').removeClass('activo');
        $('#goto-list-info').addClass('activo');
        $('.js-general-content').hide();
        $('#list-general-content').show();
    });

    $(window).on('navigate_#list-followers',function(event){
        event.preventDefault();
        $('.book-tab').removeClass('activo');
        $('#goto-list-followers').addClass('activo');
        $('.js-general-content').hide();
        $('.js-followers').show();
    });

    $(window).on('navigate_#list-conversation',function(event){
        event.preventDefault();
        $('.book-tab').removeClass('activo');
        $('#goto-list-conversation').addClass('activo');
        $('.js-general-content').hide();
        $('.conversation-section').show();
        var element_id = $('#goto-list-conversation').attr('data-id');
        var type = $('#goto-list-conversation').attr('data-type');
        comments.gotoComments(element_id,type);

    });

}( window.lists = window.lists || {}, jQuery ));

(function(book, $, undefined ) {

    book.bookId = null;

    book.gotoReviews = function(){
        book.loadReviews();
        $('.general-content').hide();
        $('.conversation-section').hide();
        $('.resenas-container').show();
    }

    book.reloadReviews = function(){
        $('.single_resena').remove();
        book.loadReviews();
    }

    book.loadReviews = function(){
        var url = Routing.generate('ajax_book_review',{
            id:book.bookId,
            offset:$('.resena_entry').length
        });

        $.ajax({
            method:'GET',
            url: url,
            success: function(reviewsHtml){
                if(reviewsHtml != ""){
                    $('#review-container').append(reviewsHtml);
                    book.attachReviewRating();

                    $('#review-container [data-rating]').each(function(ix,el){
                        el = $(el);
                        appbundle.setupRating(el,parseFloat(el.attr('data-rating')));
                    });
                }

                var newEntriesCount = (reviewsHtml.match(/single_resena/g) || []).length;
                if(newEntriesCount< 10)
                    $('#more-reviews').hide();
            },
            error: function(){
                console.log(arguments);
            }
        });
    }

    book.attachReviewRating = function(){
        _.each($('span.ion-thumbsup'),function(el){
            el = $(el);
            var binded = el.data('click.rate');
            if(!binded){
                el.data('click.rate',true);
                el.on('click.rate',function(event){
                    event.preventDefault();
                    book.rateReview($(event.target).attr('data-review'),true);
                });
            }
        });

        _.each($('span.ion-thumbsdown'),function(el){
            el = $(el);
            var binded = el.data('click.rate');
            if(!binded){
                el.data('click.rate',true);
                el.on('click.rate',function(event){
                    event.preventDefault();
                    book.rateReview($(event.target).attr('data-review'),false);
                });
            }
        });
    }

    book.rateReview = function(reviewId,up){
        if(up)
            var url = Routing.generate('review_rate_up',{id:reviewId});
        else
            var url = Routing.generate('review_rate_down',{id:reviewId});

        $.ajax({
            method:'POST',
            url: url,
            success: function(reviewRates){
                book.refreshReviewRates(reviewId,reviewRates);
            },
            error: function(){
                console.log(arguments);
            }
        });
    }

    book.refreshReviewRates = function(reviewId,rates){
        $('#review-'+reviewId+' .th_up .cantidad').text(rates.positive);
        $('#review-'+reviewId+' .th_down .cantidad').text(rates.negative);
    }

    book.attachMoreAuthorBooks = function(){
        $('.js-load-more-author').unbind('click.more');
        _.each($('.js-load-more-author'),function(el){
            var loadFunc = book.loadAuthorBooks($(el).attr('data-id'),$(el));
            $(el).on('click.more',loadFunc);
            loadFunc();
        });
    }

    book.loadAuthorBooks = function(authorId,element){
        return function(event){
            if(exists(event))
                event.preventDefault();

            var offset = $('.js-author-book-entry').length;
            var url = Routing.generate('author_books',{
                authorId:authorId,
                offset:offset,
                count:5
            });
            $.ajax({
                method:'get',
                url: url,
                success:function(html){
                    $('#all-author-books .js-book-entries').append(html);
                    follow.attachButtons();
                    if($('.js-author-book-entry').length < offset+5)
                        element.hide();
                    else
                        element.show();
                }
            })
        }
    }


    $('.goto-reviews').click(function(event){
        $('.book-tab').removeClass('activo');
        $('#goto-reviews').addClass('activo');
        book.bookId = $(event.target).attr('data-id');
        book.gotoReviews();
    });

    $('.goto-info').click(function(event){
        $('.book-tab').removeClass('activo');
        $('#goto-info').addClass('activo');
        $('.js-general-content').hide();
        $('.js-general-info').show();
    });

    $(window).on('navigate_#author-books',function(event){
        $('.book-tab').removeClass('activo');
        $('#goto-author-books').addClass('activo');
        $('.js-general-content').hide();
        book.attachMoreAuthorBooks();
        $('#all-author-books').show();
    });

    $('.goto-followers').click(function(event){
        $('.book-tab').removeClass('activo');
        $('#goto-followers').addClass('activo');
        $('.js-general-content').hide();
        $('.js-followers').show();
    });

    $(window).on('navigate_#book-conversation',function(event){
        event.preventDefault();
        $('.book-tab').removeClass('activo');
        $('#goto-book-conversation').addClass('activo');
        var element_id = $('#goto-book-conversation').attr('data-id');
        var type = $('#goto-book-conversation').attr('data-type');
        $('.js-general-content').hide();
        $('.conversation-section').show();
        comments.gotoComments(element_id,type);
    });

    $(window).on('navigate_#author-conversation',function(event){
        event.preventDefault();
        $('.book-tab').removeClass('activo');
        $('#goto-author-conversation').addClass('activo');
        var element_id = $('#goto-author-conversation').attr('data-id');
        var type = $('#goto-author-conversation').attr('data-type');
        $('.js-general-content').hide();
        $('.conversation-section').show();
        comments.gotoComments(element_id,type);
    });

    book.attachReviewRating();

}( window.book = window.book || {}, jQuery ));

(function(comments, $, undefined ) {

    comments.element_id = null;
    comments.element_type = null;

    comments.getElementComments = function(){
        var offset = $('#comments-list-'+comments.element_id+' li.conversation-entry').length;
        var url = Routing.generate(
           'element_conversation',
           {id: comments.element_id,type:comments.element_type,offset:offset});

        $.ajax({
            method: 'GET',
            url: url,
            success: function(html,textStatus,request){
                $('#comments-list-'+comments.element_id).append(html);
                $('a.js-abre-form-respuesta').each(function(ix,el){comments.attachNewCommentEvent($(el))});
                if(html.length == 0 || parseInt(request.getResponseHeader('total')) <= $('#comments-list-'+comments.element_id+' li.js-element-comment').length){
                    $('#load-more-comments-'+comments.element_id).hide();
                }
            },
            error: function(){
                console.log(arguments);
            }
        });
    }

    comments.submitForm = function(form,parentId){
        var url = Routing.generate('post_comment');
        var array_data = form.serializeArray();
        var data = {};

        _.each(array_data,function(element){
            data[element.name] = element.value;
        });

        if(typeof parentId != 'undefined')
            data['parent'] = parentId;

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(){
                $('#comments-list-'+comments.element_id+' li.conversation-entry').remove();
                comments.getElementComments();
                comments.cancelComment(form.closest('.form-holder'));
                console.log(arguments)
            },
            error: function(xhr){
                if(xhr.status == 400)
                    appbundle.setErrors(xhr.responseJSON.errors,form);
            }
        });
    }

    comments.newComment=function(element,parentId){
        var form_holder = element.parent().find('.form-holder');
        form_holder.html($('#comment-form-'+comments.element_id).text());

        form_holder.find('form').on('submit',function(event){
            event.preventDefault();
            comments.submitForm($(this),parentId);
        });

        element.off('click.comment');
        element.removeClass('no_activo');
        element.data('data-prev-text',element.text());
        element.text('cancelar');

        element.addClass('activo').on('click.cancel',
            function(event){
                event.preventDefault();
                comments.cancelComment(form_holder);
            }
        );
        form_holder.show(300);
    }

    comments.cancelComment = function(form_holder){
        var button = form_holder.parent().find('.js-abre-form-comentario');
        button.off('click.cancel');
        button.removeClass('activo');
        button.addClass('no_activo');
        button.text(button.data('data-prev-text'));
        form_holder.hide(300);
        comments.attachNewCommentEvent(button);
    }

    comments.gotoComments = function(element_id,type){

        if($('#comments-list-'+element_id+' li.conversation-entry').length > 0 )
            return;

        comments.element_id = element_id;
        comments.element_type = type;
        comments.getElementComments();

        $('#load-more-comments-'+element_id).on('click.comments',function(event){
            event.preventDefault();
            comments.getElementComments();
        });
    }

    comments.attachNewCommentEvent = function(element){
        element.on('click.comment',
            function(event){
                event.preventDefault();
                comments.newComment(element,element.attr('data-parent-comment'));
            }
        );
    }

    $('a.js-abre-form-comentario').each(function(ix,el){comments.attachNewCommentEvent($(el))});

}( window.comments = window.comments || {}, jQuery ));

(function(messages, $, undefined ) {

    messages.user_id = null;

    messages.attachNewMessageEvent = function(element){
        element.on('click.comment',
            function(event){
                event.preventDefault();
                messages.newMessage(element,element.attr('data-parent-comment'));
            }
        );
    }



    messages.getUserMessages = function(){
        var offset = $('li.js-message-entry').length;
        var url = Routing.generate(
           'user_messages',
           {user_id: messages.user_id, offset:offset});

        $.ajax({
            method: 'GET',
            url: url,
            success: function(html){
                if(html.length == 0){
                    $('#js-more-messages-'+messages.user_id).hide();
                }
                $('#message-list-'+messages.user_id).append(html);
                $('a.js-abre-form-mensaje').each(function(ix,el){messages.attachNewMessageEvent($(el))});
            },
            error: function(){
                console.log(arguments);
            }
        });
    }

    messages.submitForm = function(form, parentId){
        var url = Routing.generate('post_message');
        var array_data = form.serializeArray();
        var data = {};

        _.each(array_data,function(element){
            data[element.name] = element.value;
        });

        if(typeof parentId != 'undefined')
            data['parent'] = parentId;

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(){
                $('.js-message-entry').remove();
                messages.getUserMessages();
                messages.cancelMessage(form.closest('.form-holder'));
            },
            error: function(){
                console.log(arguments)
            }
        });
    }

    messages.newMessage=function(element, parentId){
        var form_holder = element.parent().find('.form-holder');
        form_holder.html($('#comment-form').text());

        form_holder.find('form').on('submit',function(event){
            event.preventDefault();
            messages.submitForm($(this),parentId);
        });

        element.off('click.comment');
        element.removeClass('no_activo');
        element.data('data-prev-text',element.text());
        element.text('cancelar');

        element.addClass('activo').on('click.cancel',
            function(event){
                event.preventDefault();
                messages.cancelMessage(form_holder);
            }
        );
        form_holder.show(300);
    }

    messages.cancelMessage = function(form_holder){
        var button = form_holder.parent().find('.js-abre-form-mensaje');
        button.off('click.cancel');
        button.removeClass('activo');
        button.addClass('no_activo');
        button.text(button.data('data-prev-text'));
        form_holder.hide(300);
        messages.attachNewMessageEvent(button);
    }

    $('a.js-abre-form-mensaje').each(function(ix,el){messages.attachNewMessageEvent($(el))});

    if($('#conversation-user-id').length > 0)
    {
        messages.user_id = $('#conversation-user-id').attr('userid');
        $('#js-more-messages-'+messages.user_id).on('click.more',messages.getUserMessages);
    }


}( window.messages = window.messages || {}, jQuery ));

(function(follow, $, undefined ) {

    var user_follows_data = {};
    var store_url = user_id+'follows_sotre';

    var attachFollow = function(button){
        var type = button.attr('data-type');
        var id = button.attr('data-id');
        button.unbind('click.follow');
        button.on('click.follow',function(event){
            event.preventDefault();
            follow.setFollow(type,id,true).then(function(){
                follow.recoverUserFollows().then();
                $('.follow-button[data-id="'+id+'"][data-type="'+type+'"]').hide();
                $('.unfollow-button[data-id="'+id+'"][data-type="'+type+'"]').show();
            });
        });
    }

    var attachUnfollow = function(button){
        var type = button.attr('data-type');
        var id = button.attr('data-id');
        button.unbind('click.unfollow');
        button.on('click.unfollow',function(event){
            event.preventDefault();
            follow.setFollow(type,id,false).then(function(){
                follow.recoverUserFollows().then();
                $('.follow-button[data-id="'+id+'"][data-type="'+type+'"]').show();
                $('.unfollow-button[data-id="'+id+'"][data-type="'+type+'"]').hide();
            });
        });
    }

    follow.attachButtons = function(){
        $('.follow-button').each(function(ix,el){attachFollow($(el))});
        $('.unfollow-button').each(function(ix,el){attachUnfollow($(el))});
    }

    follow.setFollow = function(type,id,follow){
        return new Promise(function(resolve,reject){
            if(follow)
                var url = Routing.generate('follow_element',{type:type,id:id});
            else
                var url = Routing.generate('unfollow_element',{type:type,id:id});

            $.ajax({
                url: url,
                method: 'POST',
                success: function(){
                    resolve(true);
                    console.log(arguments)
                },
                error: function(){console.log(arguments)}
            });
        });
    }

    follow.setupButtons = function(){
        _.each($('span.js-follow'),function(el){
            el = $(el);
            if(el.attr('data-type')  != 'user' || el.attr('data-id') != user_id)
                follow.doesUserFollow(el.attr('data-type'),el.attr('data-id'))
                    .then(function(data){
                        if(data){
                            el.find('.unfollow-button').show();
                        } else {
                            el.find('.follow-button').show();
                        }
                    })
        });

        follow.attachButtons();
    }

    follow.doesUserFollow = function(type,id){
        var object_list = user_follows_data[type+'s_followed'];
        return new Promise(
            function(resolve,reject){
                resolve(_.some(object_list,function(el){
                    return el.id == id;
                }));
            }
        );
    }

    follow.getUserFollows = function(){
        return new Promise(
            function(resolve,reject){
                var url = Routing.generate('user_follow_objects');
                $.ajax({
                    url:url,
                    method: 'GET',
                    success: function(data){
                        resolve(data);
                    },
                    error: function(){
                        resolve({});
                    }
                });
            }
        );
    }

    follow.recoverUserFollows = function(resolve,reject){
        return new Promise(function(resolve,rejec){
                follow.getUserFollows().then(function(data){
                user_follows_data = data;
                window.localStorage.setItem(store_url,JSON.stringify(data));
                resolve();
            });
        })
    }

    follow.checkUserFollows = function(resolve,reject){
        var data_string = window.localStorage.getItem(store_url);
        if(!exists(data_string)){
            return follow.recoverUserFollows();
        }
        else{
            return new Promise(function(resolve,reject){
                user_follows_data = JSON.parse(data_string);
                resolve();
            });
        }
    }

    follow.checkUserFollows().then(follow.setupButtons);


}( window.follow = window.follow || {}, jQuery ));

(function(user, $, undefined ) {


    user.activity = function(){
        $('.tab-content').hide();
        $('div.timeline.tab-content').show();
        $('.user-tab').removeClass('activo');
        $('#activity-tab').addClass('activo');
    }

    user.follows = function(){
        $('.tab-content').hide();
        $('div.follows.tab-content').show();
        $('.user-tab').removeClass('activo');
        $('#follows-tab').addClass('activo');
    }

    user.followers = function(){
        $('.tab-content').hide();
        $('div.followers.tab-content').show();
        $('.user-tab').removeClass('activo');
        $('#followers-tab').addClass('activo');
    }

    user.lists = function(){
        $('.tab-content').hide();
        $('div.lista-listas-publicas.tab-content').show();
        $('.user-tab').removeClass('activo');
        $('#lists-tab').addClass('activo');
    }

    user.booksRead = function(){
        $('.tab-content').hide();
        $('div.libros-leidos.tab-content').show();
        $('.user-tab').removeClass('activo');
        $('#read-tab').addClass('activo');
    }

    user.load_activity = function(slug){
        var currentCount = $('.timeline ul li.entry').length;
        var url = Routing.generate('user_actions',{slug:slug,offset:currentCount});
        return user.get_new_entries(url).then(user.load_timeline_entries);
    }

    user.load_timeline = function(){
        var currentCount = $('.timeline ul li.entry').length;
        var url = Routing.generate('user_timeline',{offset:currentCount});
        return user.get_new_entries(url).then(user.load_timeline_entries).then(user.get_interview).then(function(html){follow.setupButtons(); return user.append_interview(html);});
    }

    user.get_interview = function(){
        var url = Routing.generate('timeline_interview',{offset:$('li.js-interview-entry').length});

        return new Promise(function(resolve,reject){
            $.ajax({
                url: url,
                method: 'GET',
                success: function(interview){
                    resolve(interview);
                },
                error: function(xhr){
                     reject(arguments);
                }
            });
        });
    }

    user.append_interview = function(interviewHtml){
        if($.trim(interviewHtml) == '')
            return;
        $('.timeline ul').first().append(interviewHtml);
    }

    user.get_new_entries = function(url){
        return new Promise(function(resolve,reject){
            $.ajax({
                url: url,
                method: 'GET',
                success: function(newEntries,textStatus,request){
                    resolve({entries:newEntries,total: parseInt(request.getResponseHeader('total'))});
                },
                error: function(xhr){
                     reject(arguments);
                }
            });
        });
    }

    user.load_timeline_entries = function(data){
        var newEntries = data.entries;
        var total = data.total;
        return new Promise(function(resolve,reject){
            if($.trim(newEntries) != ""){
                $('.timeline ul').first().append(newEntries);

                follow.setupButtons();
                appbundle.setupRatings();

                if($('.timeline ul li.entry').length >= total)
                    $('.load-user-timeline').hide();

                resolve(true);
            } else{
                resolve(false);
            }
        });
    }

    user.get_delete_book_relation = function(relationId){
        return function(event){
            event.preventDefault();
            var element = $(event.target);
            user.deleteBookRelation(relationId).then(element.closest('li').remove(),function(data){console.log(data)});
        }
    }

    user.deleteBookRelation = function(relationId){
        return new Promise(function(resolve,reject){
            $.ajax({
                url: Routing.generate('book_relation_delete',{id:relationId}),
                method:'delete',
                success: resolve,
                error: function(){reject(arguments)}
            });
        });
    }

    user.attachBookRelationDelete = function(){
        _.each($('.js-delete-relation'),function(el){
            el = $(el);
            el.unbind('click.delete');
            el.on('click.delete',user.get_delete_book_relation(el.attr('data-id')));
        });
    }

    user.changeToPrivate = function(){
        $('#user_profile_publicProfile').val(0);
        $('#public-profile').hide();
        $('#private-profile').show();
        $('#overlay_privacidad_perfil').hide();
    }

    user.changeToPublic = function(){
        $('#user_profile_publicProfile').val(1);
        $('#public-profile').show();
        $('#private-profile').hide();
    }

    user.loadBookEntries = function(triggerButton,container,urlName,count){
        var offset = container.find('.js-book-entry').length;
        var userId = triggerButton.attr('data-user-id');
        var url = Routing.generate(urlName,{userId:userId,count:count,offset:offset})
        $.ajax({
            url:url,
            method:'GET',
            success: function(newHtml){
                var newEntriesCount = (newHtml.match(/js-book-entry/g) || []).length;
                if(newEntriesCount < count)
                    triggerButton.hide();
                else
                    triggerButton.show();

                container.append(newHtml);
                follow.setupButtons();
                user.attachBookRelationDelete();
            },
            error: console.log
        });
    }

    user.postStatus = function(form){
        var url = Routing.generate('post_status');
        var array_data = form.serializeArray();
        var data = {};

        _.each(array_data,function(element){
            data[element.name] = element.value;
        });

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(){
                $('.timeline ul li').remove();
                form.find('input, textarea').val('');
                user.load_timeline();
            },
            error: function(xhr){
                if(xhr.status == 400){
                    if(!exists(xhr.responseJSON))
                        var jsonResponse = JSON.parse(xhr.responseText);
                    else
                        var jsonResponse = xhr.responseJSON;

                    appbundle.setErrors(jsonResponse,form,'user_status');
                }
                console.log(arguments)
            }
        });
    }

    $('#convertir_perfil_publico').on('click.list',function(event){
        event.preventDefault();
        user.changeToPublic();
    });

    $('#convertir_perfil_privado').on('click.list',function(event){
        $('#overlay_privacidad_perfil').show();
    });

    $(window).on('navigate_#lists',function(event){
        user.lists();
    });

    $(window).on('navigate_#followers',function(event){
        user.followers();
    });

    $(window).on('navigate_#follows',function(event){
        user.follows();
    });

    $(window).on('navigate_#actividad',function(event){
        user.activity();
    });

    $(window).on('navigate_#booksRead',function(event){
        user.booksRead();
    });

    $('.load-user-activity').on('click.user_activity',function(event){
        event.preventDefault();
        user.load_activity($(event.target).attr('data-slug')).then(function(){},function(){$(event.target).hide()});
    });

    $('.load-user-timeline').on('click.user_timeline',function(event){
        event.preventDefault();
        user.load_timeline().then(function(){},function(){$(event.target).hide()});
    });

    if($('#load-readed-books').length > 0){
        $('#load-readed-books').on('click.more',function(event){
            event.preventDefault();
            user.loadBookEntries($('#load-readed-books'),$('.js-readed-container'),'my_readed_books',9);
        });
        user.loadBookEntries($('#load-readed-books'),$('.js-readed-container'),'my_readed_books',9);
    }

    if($('#load-wanted-books').length > 0){
        $('#load-wanted-books').on('click.more',function(event){
            event.preventDefault();
            user.loadBookEntries($('#load-wanted-books'),$('.js-wanted-container'),'my_wanted_books',9);
        });
        user.loadBookEntries($('#load-wanted-books'),$('.js-wanted-container'),'my_wanted_books',9);
    }

    if($('#load-reading-books').length > 0){
        $('#load-reading-books').on('click.more',function(event){
            event.preventDefault();
            user.loadBookEntries($('#load-reading-books'),$('.js-reading-container'),'my_reading_books',9);
        });
        user.loadBookEntries($('#load-reading-books'),$('.js-reading-container'),'my_reading_books',9);
    }

    if($('.js-user-timeline').length > 0)
        user.load_timeline();

    user.attachBookRelationDelete();

}( window.user = window.user || {}, jQuery ));

(function(banners, $, undefined ) {

  $('a.banner-link').on('click', function(event){
    var bannerZone = $(this).attr('data-zone');
    _gaq.push(['_trackEvent', 'banner-click', bannerZone]);
  });

  if($('.js-banner-adsense').length > 0) {
    $.getScript('//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',function(){
      $('.js-banner-adsense').show();
      (adsbygoogle = window.adsbygoogle || []).push({})
    });
  }

}( window.banners = window.banners || {}, jQuery ));
