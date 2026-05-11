module.exports = {
	module: {
		rules: [{
			test: /\.(jpg|png|gif|bmp|jpeg)$/,
			use: 'url-loader'
		}, ]
	},
}
