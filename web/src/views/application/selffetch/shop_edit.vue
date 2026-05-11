<template>
    <div class="ls-add-admin" ref="page">
        <!-- Header -->
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" :content="pageTitle" />
        </div>

        <!-- Form -->
        <div class="ls-card m-t-16">
            <el-form :rules="rules" ref="form" :model="form" label-width="120px" size="small">
                <!-- 门店名称 -->
                <el-form-item label="门店名称" prop="name">
                    <el-input
                        class="ls-input"
                        v-model="form.name"
                        show-word-limit
                        placeholder="请输入门店名称"
                    ></el-input>
                </el-form-item>

                <!-- 门店LOGO -->
                <el-form-item label="门店LOGO" prop="image">
                    <material-select :limit="1" v-model="form.image" />
                    <div class="muted xs m-r-16">建议尺寸：100*100像素，jpg，jpeg，png图片类型</div>
                </el-form-item>

                <!-- 联系人 -->
                <el-form-item label="联系人" prop="contact">
                    <el-input
                        class="ls-input"
                        v-model="form.contact"
                        show-word-limit
                        placeholder="请输入联系人"
                    ></el-input>
                </el-form-item>

                <!-- 联系电话 -->
                <el-form-item label="联系电话" prop="mobile">
                    <el-input
                        class="ls-input"
                        v-model="form.mobile"
                        show-word-limit
                        placeholder="请输入联系电话"
                    ></el-input>
                </el-form-item>

                <!-- 门店地址 -->
                <el-form-item label="门店地址" prop="province">
                    <area-select :province.sync="form.province" :city.sync="form.city" :district.sync="form.district" />
                </el-form-item>

                <!-- 详情地址 -->
                <el-form-item label="详情地址" prop="address">
                    <el-input class="ls-input" v-model="form.address" show-word-limit placeholder="请输入详情地址">
                        <el-button slot="append" @click="onMapSearch">搜索地图</el-button>
                    </el-input>
                </el-form-item>

                <!-- 地图定位 -->
                <el-form-item label="地图定位" prop="password_confirm">
                    <!-- <el-input
                        class="ls-input"
                        v-model="form.password_confirm"
                        show-word-limit
                        placeholder="请输入地图定位"
                    ></el-input> -->
                    <div id="tencent-map"></div>
                </el-form-item>

                <!-- 营业周天 -->
                <el-form-item label="营业周天" prop="weekdays">
                    <el-checkbox-group v-model="weekdayChecked">
                        <el-checkbox v-for="weekday in weekdayList" :label="weekday.key" :key="weekday.key">{{
                            weekday.label
                        }}</el-checkbox>
                    </el-checkbox-group>
                </el-form-item>

                <!-- 营业时段 -->
                <el-form-item label="营业时段" prop="business_start_time">
                    <el-time-picker
                        class="ls-input"
                        is-range
                        v-model="businessTime"
                        range-separator="至"
                        start-placeholder="开始时间"
                        end-placeholder="结束时间"
                        placeholder="选择时间范围"
                    >
                    </el-time-picker>
                </el-form-item>

                <!-- 门店详情 -->
                <el-form-item label="门店详情" prop="remark">
                    <el-input
                        class="ls-input"
                        v-model="form.remark"
                        type="textarea"
                        :autosize="{ minRows: 3, maxRows: 6 }"
                        show-word-limit
                    />
                </el-form-item>

                <!-- 门店状态 -->
                <el-form-item label="门店状态" prop="status">
                    <el-radio-group v-model="form.status">
                        <el-radio :label="0">停用</el-radio>
                        <el-radio :label="1">启用</el-radio>
                    </el-radio-group>
                </el-form-item>
            </el-form>
        </div>

        <!-- Footer -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import {
    apiSelffetchShopAdd,
    apiSelffetchShopDetail,
    apiSelffetchShopEdit,
    apiMapRegionSearch
} from '@/api/application/selffetch'
import AreaSelect from '@/components/area-select.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import { PageMode } from '@/utils/type'
import { timeFormat } from '@/utils/util'
import { importCDN } from '@/utils/importCDN'
import config from '@/config'
import { apiMapGet } from '@/api/setting/map'

@Component({
    components: {
        AreaSelect,
        MaterialSelect
    }
})
export default class SelffetchShopEdit extends Vue {
    /** S Data **/
    mode: string = PageMode.ADD // 当前页面【add: 添加 | edit: 编辑】
    identity: number | null = null // 当前编辑用户的身份ID  valid: mode = 'edit'
    tencentMapKey?: string
    // 表单数据
    form: any = {
        name: '', // 门店名称
        image: '', // 门店LOGO
        contact: '', // 联系人
        mobile: '', // 联系电话
        province: '', // 省
        city: '', // 市
        district: '', // 区
        address: '', // 详细地址
        longitude: '', // 经度
        latitude: '', // 纬度
        business_start_time: '', // 营业开始时间
        business_end_time: '', // 营业结束时间
        weekdays: '', // 营业周天,逗号隔开如 1,2,3,4,5,6,7
        status: 1, // 门店状态:1-启用;0-停用;
        remark: '' // 门店简介
    }

    // 营业时间段
    businessTime: any[] = [new Date().setHours(6, 0, 0), new Date().setHours(18, 0, 0)]

    // 营业周天选中列表
    weekdayChecked = []
    // 营业周天列表
    weekdayList = [
        {
            key: 1,
            label: '周一'
        },
        {
            key: 2,
            label: '周二'
        },
        {
            key: 3,
            label: '周三'
        },
        {
            key: 4,
            label: '周四'
        },
        {
            key: 5,
            label: '周五'
        },
        {
            key: 6,
            label: '周六'
        },
        {
            key: 7,
            label: '周天'
        }
    ]

    // 表单校验
    rules = {
        name: [
            {
                required: true,
                message: '请输入门店名称',
                trigger: ['blur', 'change']
            }
        ],
        image: [
            {
                required: true,
                message: '请选择门店LOGO',
                trigger: ['blur', 'change']
            }
        ],
        contact: [
            {
                required: true,
                message: '请输入联系人',
                trigger: ['blur', 'change']
            }
        ],
        mobile: [
            {
                required: true,
                message: '请输入联系电话',
                trigger: ['blur', 'change']
            }
        ],
        province: [
            {
                required: true,
                message: '请选择门店地址',
                trigger: ['blur', 'change']
            }
        ],
        address: [
            {
                required: true,
                message: '请输入详细地址',
                trigger: ['blur', 'change']
            }
        ],
        weekdays: [
            {
                required: true,
                message: '请选择营业周天',
                trigger: ['blur', 'change']
            }
        ],
        status: [
            {
                required: true,
                message: '请选择门店状态',
                trigger: ['blur', 'change']
            }
        ],
        business_start_time: [
            {
                required: true,
                message: '请选择营业时段',
                trigger: ['blur', 'change']
            }
        ]
    }

    // 腾讯地图对象
    tencentMap: any = undefined
    // 腾讯地图锚点对象
    markerLayer: any = undefined

    /** E Data **/

    /** S Computed **/
    get pageTitle() {
        switch (this.mode) {
            case PageMode.ADD:
                return '新增自提门店'
            case PageMode.EDIT:
                return '编辑自提门店'
        }
    }
    /** S Computed **/

    /** S Methods **/
    // 点击表单提交
    onSubmit(formName: string) {
        // 验证表单格式是否正确
        const refs = this.$refs[formName] as HTMLFormElement
        refs.validate((valid: boolean): any => {
            if (!valid) {
                return
            }

            // 请求发送
            switch (this.mode) {
                case PageMode.ADD:
                    return this.handleAdd()
                case PageMode.EDIT:
                    return this.handleEdit()
            }
        })
    }

    // 添加
    handleAdd() {
        const form = this.form
        apiSelffetchShopAdd(form).then(() => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    // 编辑
    handleEdit() {
        const params = this.form
        const id: number = this.identity as number
        apiSelffetchShopEdit({ ...params, id }).then(() => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    // 表单初始化数据 [编辑模式] mode => edit
    initFormDataForEdit() {
        return new Promise((resolve, reject) => {
            apiSelffetchShopDetail({
                id: this.identity as number
            })
                .then(data => {
                    Object.keys(data).map(key => {
                        this.$set(this.form, key, data[key])
                    })
                    // 初始化营业周天表单
                    this.weekdayChecked = data.weekdays.split(',').map((item: string) => Number(item))
                    // 初始化营业时段表单
                    const date = new Date().toDateString()
                    const dateFormat = timeFormat(Date.parse(date), 'yyyy-mm-dd')
                    this.businessTime = [
                        new Date(dateFormat + ' ' + data.business_start_time),
                        new Date(dateFormat + ' ' + data.business_end_time)
                    ]

                    resolve(data)
                })
                .catch(err => {
                    this.$message.error('数据初始化失败，请刷新重载！')
                    reject(err)
                })
        })
    }

    // 初始化腾讯地图
    initTencentMap() {
        const TencentMap = (window as any).TMap

        if (this.mode === PageMode.ADD) {
            this.form.latitude = '39.954104'
            this.form.longitude = '116.357503'

            this.getCurrentLocation().then((data: any) => {
                this.form.latitude = data.lat
                this.form.longitude = data.lng

                this.tencentMap.setCenter({ lat: data.lat, lng: data.lng })
                this.markerLayer.updateGeometries([
                    {
                        styleId: 'me',
                        id: '1',
                        position: new TencentMap.LatLng(data.lat, data.lng)
                    }
                ])
            })
        }

        const latLng = {
            lat: this.form.latitude,
            lng: this.form.longitude
        }

        //定义地图中心点坐标
        const center = new TencentMap.LatLng(latLng.lat, latLng.lng)

        //定义map变量，调用 TencentMap.Map() 构造函数创建地图
        this.tencentMap = new TencentMap.Map(document.getElementById('tencent-map'), {
            center: center, //设置地图中心点坐标
            zoom: 17.2, //设置地图缩放级别
            pitch: 43.5, //设置俯仰角
            rotation: 45 //设置地图旋转角度
        })

        //创建并初始化MultiMarker
        this.markerLayer = new TencentMap.MultiMarker({
            map: this.tencentMap, //指定地图容器
            styles: {
                //创建一个styleId为"myStyle"的样式（styles的子属性名即为styleId）
                me: new TencentMap.MarkerStyle({
                    width: 34, // 点标记样式宽度（像素）
                    height: 34, // 点标记样式高度（像素）
                    src: require('@/assets/images/icon_marker.png'), //图片路径
                    //焦点在图片中的像素位置，一般大头针类似形式的图片以针尖位置做为焦点，圆形点以圆心位置为焦点
                    anchor: { x: 17, y: 34 }
                })
            },
            //点标记数据数组
            geometries: [
                {
                    //点标记唯一标识，后续如果有删除、修改位置等操作，都需要此id
                    id: '1',
                    //指定样式id
                    styleId: 'me',
                    //点标记坐标位置
                    position: new TencentMap.LatLng(latLng.lat, latLng.lng)
                    // properties: {
                    //     //自定义属性
                    //     title: 'marker',
                    // },
                }
            ]
        })

        // 监听地图点击事件
        this.tencentMap.on('click', ({ latLng, poi }: any) => {
            this.markerLayer.updateGeometries([
                {
                    styleId: 'me',
                    id: '1',
                    position: new TencentMap.LatLng(latLng.lat, latLng.lng)
                }
            ])

            this.form.latitude = latLng.lat
            this.form.longitude = latLng.lng
        })

        //
        //
    }

    // 导入腾讯地图API
    loadTencentMapScript() {
        const TENTCENT_MAP_API = `https://map.qq.com/api/gljs?v=1.exp&key=${this.tencentMapKey}`
        importCDN(TENTCENT_MAP_API).then(() => this.initTencentMap())
    }

    // 获取当前位置
    getCurrentLocation() {
        return new Promise((resolve: Function, reject: Function) => {
            const URL = 'https://mapapi.qq.com/web/mapComponents/geoLocation/v/geolocation.min.js'

            importCDN(URL)
                .then(() => {
                    const QQ = (window as any).qq
                    const geolocation = new QQ.maps.Geolocation(this.tencentMapKey, 'admin')
                    geolocation.watchPosition((data: any) => {
                        geolocation.clearWatch()
                        resolve(data)
                    })
                })
                .catch(err => reject(err))
        })
    }

    // 地图搜索
    onMapSearch() {
        if (!this.tencentMap) {
            return this.$message.error('地图未加载完成')
        }

        apiMapRegionSearch({
            keyword: encodeURI(this.form.address),
            boundary: `region(${this.form.district ?? this.form.city}, 0)`,
            key: this.tencentMapKey!
        })
            .then(({ data }) => {
                if (!data.length) {
                    return this.$message.info(`没有找到“${this.form.address}”的相关地点`)
                }

                const TencentMap = (window as any).TMap
                const geometries: any = []

                // 清除所有标点
                this.markerLayer.setGeometries([])
                // 添加自己位置的标点
                this.markerLayer.add([
                    {
                        id: '1',
                        styleId: 'me',
                        position: new TencentMap.LatLng(this.form.latitude, this.form.longitude)
                    }
                ])

                data.forEach((item: any) => {
                    const { lat, lng } = item.location
                    geometries.push({
                        id: item.id,
                        styleId: 'marker',
                        position: new TencentMap.LatLng(lat, lng),
                        properties: {
                            title: item.title
                        }
                    })
                })

                // 更改地图中心点
                const { lat, lng } = data[0].location
                this.tencentMap.setCenter({ lat, lng })
                // 添加标点
                this.markerLayer.add(geometries)
            })
            .catch(err => {
                // this.$message.error('地图搜索出现了点问题，请重新尝试。')
            })
    }
    async getMapKey() {
        const data = await apiMapGet()
        return data.tencent_map_key
    }
    /** E Methods **/

    /** S Life Cycle **/
    async created() {
        const query: any = this.$route.query

        if (query.mode) {
            this.mode = query.mode
        }

        // 编辑模式：初始化数据
        if (this.mode === PageMode.EDIT) {
            this.identity = query.id * 1
            await this.initFormDataForEdit()
        }
        this.tencentMapKey = await this.getMapKey()
        this.loadTencentMapScript()
        ;(window as any).handleSearchMap = (data: any) => {}
    }

    @Watch('weekdayChecked')
    handleWeekdays(value: Array<number>) {
        this.form.weekdays = value.join(',')
    }

    @Watch('businessTime')
    handleBusinessTime(value: Array<string>) {
        this.form.business_start_time = timeFormat(Date.parse(value[0]), 'hh:MM:ss')
        this.form.business_end_time = timeFormat(Date.parse(value[1]), 'hh:MM:ss')
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-add-admin {
    padding-bottom: 80px;

    .ls-input {
        width: 380px;
    }

    #tencent-map {
        width: 580px;
        height: 380px;
    }
}
</style>
