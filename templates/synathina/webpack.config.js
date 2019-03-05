const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const cssnano = require('cssnano');

const buildPath = path.resolve(__dirname, 'dist');
const extractSass = new ExtractTextPlugin({
    publicPath:  buildPath,
    filename: 'styles.css'
});
const env = process.env.NODE_ENV;
const isDev = (env !== 'production');
const plugins = [
    extractSass
];

const config = {
    devtool: 'source-map',
    mode: isDev ? 'development' : 'production',
    entry: {
        site: './js/site/index.js',
        styles: './sass/styles.scss'
    },
    output: {
        path: buildPath,
        filename: '[name].js'
    },
    watchOptions: {
        poll: 1000
    },
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: extractSass.extract({
                    use: [{
                        loader: 'css-loader?sourceMap',
                    }, {
                        loader: 'resolve-url-loader'
                    }, {
                        loader: 'sass-loader?sourceMap',
                        options: {
                            sourceMap: true,
                        }
                    }],
                    // use style-loader in development
                    fallback: 'style-loader'
                })
            },
            {
                test: /\.css$/,
                use: [{
                    loader: 'style-loader?sourceMap'
                }, {
                    loader: 'css-loader?sourceMap',
                    options: {
                        sourceMap: true,
                    },
                },{
                    loader: 'postcss-loader'
                }, {
                    loader: 'resolve-url-loader'
                }]
            },
            {
                test: /\.(eot|woff|woff2|ttf|otf)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name].[ext]',
                            outputPath: './fonts/'
                        }
                    }
                ]
            },
            {
                test: /\.(jpg|png|gif|svg)$/,
                use: [
                    'file-loader?name=[name].[ext]&outputPath=./img/',
                    {
                        loader: 'image-webpack-loader',
                        options: !isDev ? {
                            bypassOnDebug: true,
                            pngquant: {
                                quality: '65-90',
                                speed: 4
                            }
                        } : {},
                    }
                ]
            }
        ]
    },
    optimization: {
        minimizer: [
            new UglifyJsPlugin({
                cache: true,
                parallel: true,
                sourceMap: true,
                uglifyOptions: {
                    ecma:8,
                    compress: {
                        warnings: false
                    }
                }
            }),
            new OptimizeCSSAssetsPlugin({
                cssProcessor: cssnano({reduceIdents: false, autoprefixer: false})
            })
        ]
    },
    plugins: plugins
};

module.exports = config;