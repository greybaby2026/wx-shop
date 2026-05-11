<template>
    <view class="uploader-container flex wrap">
        <u-upload @on-change="change" :action="action"
            :header="{'token': token,'version': version}" :deletable="deletable" :max-count="maxUpload" :showProgress="showProgress"
            @on-remove="remove" :multiple="mutiple" :custom-btn="true" :width="previewSize" :height="previewSize"
            ref="upload">
            <view slot="addBtn" class="uplader-upload" hover-class="slot-btn__hover" hover-stay-time="150">
                <u-icon size="48" color="#dcdee0" name="camera" />
                <view class="xs m-t-10">
                    {{fileList.length>=1?fileList.length+'/'+maxUpload:tips}}
                </view>
            </view>
        </u-upload>
    </view>
</template>

<script>
    import store from '@/store'
    import {
    	baseURL,
		version
    } from '@/config/app.js'
    export default {
        name: "uploader",
        props: {
            // 默认不允许多选图片
            mutiple: {
                type: Boolean,
                default: false
            },
            // 限制上传文件数量
            maxUpload: {
                type: Number,
                default: 1
            },
            // upload大小
            previewSize: {
                type: String,
                default: "160"
            },
            // 是否可删除
            deletable: {
                type: Boolean,
                default: true,
            },
            // 提示
            tips: {
                type: String,
                default: '上传图片'
            },
            // 是否显示进度条
            showProgress: {
                type: Boolean,
                default: false,
            }
        },
        data() {
            return {
                action: '',
                token: '',
                fileList: [],
				version: version
            }
        },
        mounted() {
            console.log(store.getters.token,baseURL)
            this.action = baseURL + '/shopapi/upload/image';
            this.token = store.getters.token
        },
        methods: {
            // 上传，不管成不成功都返回数据｜提示
            change(event) {
                this.$toast({
                    title: JSON.parse(event.data).msg
                })
                
                if(JSON.parse(event.data).code == 1) {
                    this.fileList.push(JSON.parse(event.data).data.uri)
                    this.$emit('input', this.fileList)
                }
            },
            
            // 删除一个图片
            remove(event) {
                this.fileList.splice(event, 1);
                this.$emit('input', this.fileList)
            }
        }
    }
</script>

<style lang="scss">
    .uploader-container {
        .uplader-upload {
            position: relative;
            width: 160rpx;
            height: 160rpx;
            padding-top: 30rpx;
            text-align: center;
            margin: 11rpx;
            border: 2px dashed #e5e5e5;
            background-color: #FFFFFF;

            >view {
                color: #BBB;
            }
        }
    }
</style>
