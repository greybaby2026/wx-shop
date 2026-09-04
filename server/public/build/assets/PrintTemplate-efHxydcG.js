import{j as w}from"./element-plus-B9SQrWMQ.js";import{G as N,M as s,P as t,a0 as x,W as y,$ as e,_ as r,Z as m,F as z,ae as C,c as p,L as i,m as T}from"./vue-vendor-BGi9lcTA.js";import{_ as B}from"./app-DMHLzZwq.js";const F={key:0,class:"print-container"},M={class:"print-toolbar"},P={class:"print-header"},q={class:"print-meta"},L={class:"print-info"},V={key:0},$={key:1},A={key:2},S={class:"print-table"},j={class:"print-footer"},D={class:"print-time"},E=N({__name:"PrintTemplate",props:{visible:{type:Boolean},type:{},data:{}},emits:["update:visible"],setup(l,{emit:H}){const d=l,f=T(),b=p(()=>({purchase:"采购单",sale:"销售单",transfer:"调拨单"})[d.type]||""),c=p(()=>({draft:"草稿",pending:"待审核",approved:"已审核",partial_stock:"部分入库",completed:"已完成",cancelled:"已作废"})[d.data?.status]||d.data?.status||"-"),v=p(()=>(d.data?.items||[]).reduce((a,n)=>a+Number(n.quantity||0),0)),_=p(()=>(d.data?.items||[]).reduce((n,u)=>n+Number(u.price||0)*Number(u.quantity||0),0).toFixed(2)),h=p(()=>new Date().toLocaleString("zh-CN"));function k(){if(!f.value)return;const a=window.open("","_blank");a&&(a.document.write(`
    <html>
    <head>
      <title>${b.value}</title>
      <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Microsoft YaHei", sans-serif; padding: 20px; color: #333; }
        .print-header { text-align: center; margin-bottom: 20px; }
        .print-header h1 { font-size: 20px; margin-bottom: 8px; }
        .print-meta { font-size: 13px; color: #666; }
        .print-meta span { margin: 0 16px; }
        .print-info { width: 100%; margin-bottom: 16px; }
        .print-info td { padding: 4px 8px; font-size: 13px; width: 50%; }
        .print-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .print-table th, .print-table td { border: 1px solid #333; padding: 6px 8px; font-size: 12px; text-align: center; }
        .print-table th { background: #f5f5f5; font-weight: 600; }
        .print-table tfoot td { font-weight: 600; }
        .print-footer { margin-top: 20px; }
        .sign-area { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 13px; }
        .print-time { font-size: 12px; color: #999; }
        @media print { body { padding: 0; } }
      </style>
    </head>
    <body>${f.value.innerHTML}</body>
    </html>
  `),a.document.close(),a.focus(),setTimeout(()=>{a.print(),a.close()},250))}return(a,n)=>{const u=w;return l.visible?(i(),s("div",F,[t("div",M,[x(u,{type:"primary",onClick:k},{default:y(()=>[...n[1]||(n[1]=[r("打印",-1)])]),_:1}),x(u,{onClick:n[0]||(n[0]=o=>l.visible=!1)},{default:y(()=>[...n[2]||(n[2]=[r("关闭",-1)])]),_:1})]),t("div",{ref_key:"printArea",ref:f,class:"print-area"},[t("div",P,[t("h1",null,e(b.value),1),t("div",q,[t("span",null,"单号："+e(l.data.order_no),1),t("span",null,"日期："+e(l.data.created_at),1)])]),t("table",L,[l.type==="purchase"?(i(),s("tr",V,[t("td",null,[n[3]||(n[3]=t("strong",null,"供应商：",-1)),r(e(l.data.supplier?.name||"-"),1)]),t("td",null,[n[4]||(n[4]=t("strong",null,"仓库：",-1)),r(e(l.data.warehouse?.name||"-"),1)])])):m("",!0),l.type==="sale"?(i(),s("tr",$,[t("td",null,[n[5]||(n[5]=t("strong",null,"门店：",-1)),r(e(l.data.store?.name||"-"),1)]),t("td",null,[n[6]||(n[6]=t("strong",null,"会员：",-1)),r(e(l.data.member?.name||"-"),1)])])):m("",!0),l.type==="transfer"?(i(),s("tr",A,[t("td",null,[n[7]||(n[7]=t("strong",null,"调出：",-1)),r(e(l.data.out_name||"-"),1)]),t("td",null,[n[8]||(n[8]=t("strong",null,"调入：",-1)),r(e(l.data.in_name||"-"),1)])])):m("",!0),t("tr",null,[t("td",null,[n[9]||(n[9]=t("strong",null,"备注：",-1)),r(e(l.data.remark||"-"),1)]),t("td",null,[n[10]||(n[10]=t("strong",null,"状态：",-1)),r(e(c.value),1)])])]),t("table",S,[n[13]||(n[13]=t("thead",null,[t("tr",null,[t("th",{width:"50"},"序号"),t("th",null,"商品名称"),t("th",null,"SKU编码"),t("th",{width:"80"},"颜色"),t("th",{width:"80"},"尺码"),t("th",{width:"80"},"条码"),t("th",{width:"80"},"单价"),t("th",{width:"60"},"数量"),t("th",{width:"100"},"金额")])],-1)),t("tbody",null,[(i(!0),s(z,null,C(l.data.items||[],(o,g)=>(i(),s("tr",{key:g},[t("td",null,e(g+1),1),t("td",null,e(o.sku?.goods?.name||o.goods_name||"-"),1),t("td",null,e(o.sku?.sku_no||"-"),1),t("td",null,e(o.sku?.color_name||"-"),1),t("td",null,e(o.sku?.size_name||"-"),1),t("td",null,e(o.sku?.barcode||"-"),1),t("td",null,e(Number(o.price||0).toFixed(2)),1),t("td",null,e(o.quantity),1),t("td",null,e((Number(o.price||0)*Number(o.quantity||0)).toFixed(2)),1)]))),128))]),t("tfoot",null,[t("tr",null,[n[11]||(n[11]=t("td",{colspan:"6",style:{"text-align":"right"}},[t("strong",null,"合计")],-1)),n[12]||(n[12]=t("td",null,null,-1)),t("td",null,[t("strong",null,e(v.value),1)]),t("td",null,[t("strong",null,e(_.value),1)])])])]),t("div",j,[n[14]||(n[14]=t("div",{class:"sign-area"},[t("span",null,"制单人：__________"),t("span",null,"审核人：__________"),t("span",null,"收货人：__________")],-1)),t("div",D,"打印时间："+e(h.value),1)])],512)])):m("",!0)}}}),K=B(E,[["__scopeId","data-v-98eef1a2"]]);export{K as P};
