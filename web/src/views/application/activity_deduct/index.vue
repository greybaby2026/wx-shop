<template>
  <div class="ls-add-admin">
    <div class="ls-card ls-coupon-edit__form m-t-10">
      <div class="nr weight-500 m-b-20">活动配置</div>
      <el-form ref="form" :model="form" label-width="120px" size="small">
        <el-form-item label="活动开关">
          <el-radio v-model="form.switch" :label="1">开启</el-radio>
          <el-radio v-model="form.switch" :label="0">关闭</el-radio>
          <span class="desc">开启后用户下单时余额自动抵扣订单金额</span>
        </el-form-item>
        <el-form-item label="充值到活动余额">
          <el-radio v-model="form.recharge_to_activity" :label="1">开启</el-radio>
          <el-radio v-model="form.recharge_to_activity" :label="0">关闭</el-radio>
          <span class="desc">开启后活动期间的充值进入活动余额，仅用于比例抵扣</span>
        </el-form-item>
        <el-form-item label="活动名称">
          <el-input v-model="form.activity_name" placeholder="如父亲节充值有礼" style="width:300px"></el-input>
        </el-form-item>
        <el-form-item label="抵扣比例">
          <el-input-number v-model="form.ratio" :min="1" :max="100"></el-input-number>
          <span class="desc">用户下单时余额最多可抵扣订单金额的百分比</span>
        </el-form-item>
        <el-form-item label="开始时间">
          <el-date-picker v-model="form.start_time" type="datetime" placeholder="不填表示无开始限制" value-format="yyyy-MM-dd HH:mm:ss"></el-date-picker>
        </el-form-item>
        <el-form-item label="结束时间">
          <el-date-picker v-model="form.end_time" type="datetime" placeholder="不填表示无结束限制" value-format="yyyy-MM-dd HH:mm:ss"></el-date-picker>
        </el-form-item>
      </el-form>
    </div>

    <div class="ls-card ls-coupon-edit__form m-t-10">
      <div class="nr weight-500 m-b-20">主题设置</div>
      <el-form ref="themeForm" :model="form" label-width="120px" size="small">
        <el-form-item label="预设主题">
          <el-select v-model="form.current_theme" placeholder="选择主题" @change="onThemeChange" style="width:300px">
            <el-option v-for="preset in themePresets" :key="preset.name" :label="preset.name" :value="preset.name"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="主色">
          <el-input v-model="form.theme_config.primary_color" placeholder="#1a3a5c" style="width:200px"></el-input>
        </el-form-item>
        <el-form-item label="强调色">
          <el-input v-model="form.theme_config.accent_color" placeholder="#c9a96e" style="width:200px"></el-input>
        </el-form-item>
        <el-form-item label="口号">
          <el-input v-model="form.theme_config.slogan" placeholder="感恩父爱充值有礼" style="width:400px"></el-input>
        </el-form-item>
        <el-form-item label="角标文字">
          <el-input v-model="form.theme_config.badge_text" placeholder="父亲节" style="width:200px"></el-input>
        </el-form-item>
        <el-form-item label="横幅图片">
          <div class="flex">
            <el-input v-model="form.theme_config.banner_image" placeholder="上传图片或输入链接" style="width:300px"></el-input>
            <el-upload
              class="m-l-10"
              :action="uploadAction"
              :headers="{ token: $store.getters.token }"
              :show-file-list="false"
              :on-success="handleBannerUploadSuccess"
              :before-upload="beforeUpload">
              <el-button size="small" type="primary">上传</el-button>
            </el-upload>
            <div v-if="form.theme_config.banner_image" class="m-l-10">
              <img :src="form.theme_config.banner_image" style="width:32px;height:32px;border-radius:4px;object-fit:cover;">
            </div>
          </div>
        </el-form-item>
        <el-form-item label="角标图标">
          <div class="flex">
            <el-input v-model="form.theme_config.badge_icon" placeholder="输入图片链接或上传" style="width:300px"></el-input>
            <el-upload
              class="m-l-10"
              :action="uploadAction"
              :headers="{ token: $store.getters.token }"
              :show-file-list="false"
              :on-success="handleUploadSuccess"
              :before-upload="beforeUpload">
              <el-button size="small" type="primary">上传</el-button>
            </el-upload>
            <div v-if="form.theme_config.badge_icon" class="m-l-10">
              <img :src="form.theme_config.badge_icon" style="width:32px;height:32px;border-radius:4px;object-fit:cover;">
            </div>
          </div>
        </el-form-item>
        <el-form-item label="装饰元素">
          <el-select v-model="form.theme_config.deco_element" placeholder="选择装饰" style="width:200px">
            <el-option label="无" value="none"></el-option>
            <el-option label="爆裂" value="burst"></el-option>
            <el-option label="云彩" value="cloud"></el-option>
            <el-option label="花瓣" value="petal"></el-option>
            <el-option label="烟花" value="firework"></el-option>
            <el-option label="彩纸" value="confetti"></el-option>
            <el-option label="灯笼" value="lantern"></el-option>
            <el-option label="星星" value="star"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="按钮风格">
          <el-select v-model="form.theme_config.button_style" placeholder="选择风格" style="width:200px">
            <el-option label="圆角" value="rounded"></el-option>
            <el-option label="药丸" value="pill"></el-option>
            <el-option label="直角" value="square"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="动效风格">
          <el-select v-model="form.theme_config.animation" placeholder="选择动效" style="width:200px">
            <el-option label="无" value="none"></el-option>
            <el-option label="脉冲" value="pulse"></el-option>
            <el-option label="淡入" value="fade"></el-option>
            <el-option label="柔和" value="gentle"></el-option>
            <el-option label="烟花" value="firework"></el-option>
            <el-option label="闪烁" value="flash"></el-option>
            <el-option label="浮动" value="float"></el-option>
            <el-option label="花瓣" value="petal"></el-option>
            <el-option label="弹跳" value="bounce"></el-option>
          </el-select>
        </el-form-item>
      </el-form>
    </div>

    <div class="ls-card ls-coupon-edit__form m-t-10">
      <div class="nr weight-500 m-b-20">预览</div>
      <div class="preview-area" :style="{background:form.theme_config.primary_color||'#1a3a5c'}">
        <div class="preview-slogan" :style="{color:form.theme_config.accent_color||'#c9a96e'}">{{ form.theme_config.slogan || '充值享抵扣' }}</div>
        <div class="preview-desc">充值即享 {{ form.ratio||30 }}% 订单抵扣</div>
        <div class="preview-badge">{{ form.theme_config.badge_text||'抵扣' }}</div>
      </div>
    </div>

    <div class="ls-fixed-footer">
      <el-button type="primary" @click="onSave" :loading="saving">保存</el-button>
      <el-button @click="onCancel">取消</el-button>
    </div>
  </div>
</template>

<script>
import { apiGetDeductConfig, apiSetDeductConfig } from '@/api/application/activity_deduct'
export default {
  data() {
    return {
      saving: false,
      themePresets: [],
      uploadAction: window.location.origin + '/adminapi/upload/image',
      form: { switch:0, ratio:30, activity_name:'', current_theme:'', start_time:'', end_time:'', recharge_to_activity:0, theme_config:{ primary_color:'#1a3a5c', accent_color:'#c9a96e', banner_image:'', slogan:'', badge_text:'', badge_icon:'', deco_element:'none', button_style:'rounded', animation:'none' } }
    }
  },
  created() { this.getConfig() },
  methods: {
    getConfig() {
      apiGetDeductConfig().then(res => {
        if (res) {
          this.form.switch = res.switch ?? 0
          this.form.ratio = res.ratio ?? 30
          this.form.activity_name = res.activity_name || ''
          this.form.current_theme = res.current_theme || ''
          this.form.start_time = res.start_time || ''
          this.form.end_time = res.end_time || ''
          this.form.recharge_to_activity = res.recharge_to_activity ?? 0
          this.form.theme_config = res.theme_config || this.form.theme_config
          this.themePresets = res.theme_presets || []
        }
      })
    },
        handleBannerUploadSuccess(res) {
      if (res.code == 1 && res.data?.uri) {
        this.form.theme_config.banner_image = res.data.uri
      }
    },
    handleUploadSuccess(res) {
      if (res.code == 1 && res.data?.uri) {
        this.form.theme_config.badge_icon = res.data.uri
      }
    },
    onThemeChange(val) {
      const preset = this.themePresets.find(p => p.name === val)
      if (preset && preset.theme_config) this.form.theme_config = { ...this.form.theme_config, ...preset.theme_config }
    },
    onSave() { this.saving=true; apiSetDeductConfig(this.form).then(()=>{ this.$message.success('保存成功'); this.getConfig() }).finally(()=>{ this.saving=false }) },
    beforeUpload(file) {
      const isImage = file.type.startsWith('image/')
      const isLt2M = file.size / 1024 / 1024 < 2
      if (!isImage) { this.$message.error('只能上传图片'); return false }
      if (!isLt2M) { this.$message.error('图片大小不能超过2MB'); return false }
      return true
    },
    onCancel() { this.getConfig() }
  }
}
</script>
<style scoped>
.preview-area { padding:30px; border-radius:8px; text-align:center; color:#fff; min-height:120px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; }
.preview-slogan { font-size:18px; font-weight:600; }
.preview-desc { font-size:14px; opacity:0.85; }
.preview-badge { font-size:12px; padding:2px 10px; border-radius:10px; background:rgba(255,255,255,0.2); display:inline-block; }
</style>
