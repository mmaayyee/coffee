module.exports = {
  assetsDir: 'dashboard',
  productionSourceMap: false,
  devServer: {
    proxy: 'http://erpdev.coffee08test.com'
  }
}
