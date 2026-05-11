(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["components/widgets/navigation/item"],{"24a1":function(t,n,e){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var i=e("2947"),u={props:{content:{type:[Object,Array]},styles:{type:[Object,Array]},item:{type:Object}},data:function(){return{}},methods:{navigate:function(){var t=this.item.link,n=t.path,e=t.params;n&&this.$Router.push({path:n,query:e})},handleClick:function(t){(0,i.navigateTo)(t)}},computed:{}};n.default=u},"376b2":function(t,n,e){"use strict";e.d(n,"b",(function(){return u})),e.d(n,"c",(function(){return a})),e.d(n,"a",(function(){return i}));var i={uImage:function(){return e.e("components/uview-ui/components/u-image/u-image").then(e.bind(null,"cabf"))}},u=function(){var t=this.$createElement,n=(this._self._c,1==this.content.style?this.$px2rpx(this.styles.img_border_radius):null),e=1==this.content.style?this.$getImageUri(this.item.url):null;this.$mp.data=Object.assign({},{$root:{m0:n,m1:e}})},a=[]},"55d4":function(t,n,e){"use strict";e.r(n);var i=e("24a1"),u=e.n(i);for(var a in i)["default"].indexOf(a)<0&&function(t){e.d(n,t,(function(){return i[t]}))}(a);n["default"]=u.a},a18c:function(t,n,e){"use strict";e.r(n);var i=e("376b2"),u=e("55d4");for(var a in u)["default"].indexOf(a)<0&&function(t){e.d(n,t,(function(){return u[t]}))}(a);var r=e("828b"),o=Object(r["a"])(u["default"],i["b"],i["c"],!1,null,null,null,!1,i["a"],void 0);n["default"]=o.exports}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'components/widgets/navigation/item-create-component',
    {
        'components/widgets/navigation/item-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('df3c')['createComponent'](__webpack_require__("a18c"))
        })
    },
    [['components/widgets/navigation/item-create-component']]
]);
