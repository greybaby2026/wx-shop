const path = require('path')
module.exports = {
  publicPath: '/kefu',
  lintOnSave: false,
  css: {
    loaderOptions: {
      sass: {
        prependData: `@import "@/styles/variables.scss";`
      },
    }
  },
  transpileDependencies: [
    'vue-echarts',
    'resize-detector'
  ],
}