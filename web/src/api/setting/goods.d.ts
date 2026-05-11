/** S 商品 **/
// 获取商品设置
export interface GoodsSettings_Res {
    image: string //商品主图
    is_show: 0 | 1 //是否显示:0-否;1-是;
    show_price: 0 | 1 //是否显示商品划线价
    sales_num: 0 | 1 //是否显示商品销量
    click_num: 0 | 1 //是否显示商品点击量
}
// 商品设置
export interface GoodsSettings_Req {
    image: string //商品主图
    is_show: 0 | 1 //是否显示:0-否;1-是;
    show_price: 0 | 1 //是否显示商品划线价
    sales_num: 0 | 1 //是否显示商品销量
    click_num: 0 | 1 //是否显示商品点击量
}
/** E 商品 **/
