const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const {WebpackManifestPlugin} = require('webpack-manifest-plugin');

module.exports = (env, argv) => {
    return {
        entry: ['./assets/app.js'],
        output: {
            filename: "[name].[contenthash].js",
            path: path.resolve(__dirname, 'public/dist'),
            publicPath: '/dist/',
            clean: true
        },
        devtool: 'source-map',
        devServer: {
            port: 9000,
            allowedHosts: 'all'
        },
        plugins: [
            new MiniCssExtractPlugin({filename: "[name].[contenthash].css"}),
            new WebpackManifestPlugin({})
        ]
        , module: {
            rules: [
                {
                    test: /\.(scss)$/,
                    use: [
                        argv.mode === 'production' ? MiniCssExtractPlugin.loader : "style-loader",
                        "css-loader",
                        "sass-loader"
                    ],
                }
            ],
        }
    }
};
