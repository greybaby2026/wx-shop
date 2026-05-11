(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["components/tab/tab"],{"512d":function(t,n,e){"use strict";var i=e("e99e"),a=e.n(i);a.a},"6d88":function(t,n,e){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var i={props:{dot:{type:Boolean},name:{type:[Number,String],value:""}},inject:["tabs"],data:function(){return{active:!1,shouldShow:!1,shouldRender:!1}},created:function(){this.tabs.childrens.push(this)},mounted:function(){this.update()},methods:{getComputedName:function(){return""!==this.data.name?this.data.name:this.index},updateRender:function(t,n){this.inited=this.inited||t,this.active=t,this.shouldRender=this.inited,this.shouldShow=t},update:function(){this.tabs&&this.tabs.updateTabs()}},computed:{changeData:function(){var t=this.dot,n=this.info;return{dot:t,info:n}}},watch:{changeData:function(t){this.update()},name:function(t){this.update()}}};n.default=i},8560:function(t,n,e){"use strict";e.r(n);var i=e("6d88"),a=e.n(i);for(var u in i)["default"].indexOf(u)<0&&function(t){e.d(n,t,(function(){return i[t]}))}(u);n["default"]=a.a},"95ec":function(t,n,e){"use strict";e.d(n,"b",(function(){return i})),e.d(n,"c",(function(){return a})),e.d(n,"a",(function(){}));var i=function(){var t=this.$createElement;this._self._c},a=[]},bbe1:function(t,n,e){"use strict";e.r(n);var i=e("95ec"),a=e("8560");for(var u in a)["default"].indexOf(u)<0&&function(t){e.d(n,t,(function(){return a[t]}))}(u);e("512d");var o=e("828b"),d=Object(o["a"])(a["default"],i["b"],i["c"],!1,null,null,null,!1,i["a"],void 0);n["default"]=d.exports},e99e:function(t,n,e){}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'components/tab/tab-create-component',
    {
        'components/tab/tab-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('df3c')['createComponent'](__webpack_require__("bbe1"))
        })
    },
    [['components/tab/tab-create-component']]
]);
