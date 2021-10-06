// const path = require("path")
// const UglifyJsPlugin = require("uglifyjs-webpack-plugin")
// const glob = require("glob")

//     module.exports = {
//       mode: "production",
//       entry: 
//         // "bundle.js": glob.sync("build/static/?(js|css)/main.*.?(js|css)").map(f => path.resolve(__dirname, f)),
//         path.resolve(__dirname, 'src/index.tsx')
//       ,
//       output: {
//         path: path.resolve(__dirname, "../js"),
//         filename: "passwd-react.js",
//       },
//       module: {
//         rules: [
//           {
//             test: /\.css$/,
//             use: ["style-loader", "css-loader"],
//           },
//           {
//             test: /\.tsx?$/,
//             use: 'ts-loader',
//             exclude: /node_modules/,
//           },
//         ],
//       },
//       resolve: {
//         extensions: ['.tsx', '.ts', '.js'],
//       },
//       plugins: [new UglifyJsPlugin()],
//     }

const path = require('path');

module.exports = {
  mode: 'production',
  entry: './src/index.tsx',
  module: {
    rules: [
      {
        test: /\.tsx?$/,
        use: 'ts-loader',
        exclude: /node_modules/,
      },
      {
        test: /\.css$/,
        use: ["style-loader", "css-loader"],
      },
    ],
  },
  resolve: {
    extensions: ['.tsx', '.ts', '.js'],
  },
  output: {
    filename: 'passwd-react.js',
    path: path.resolve(__dirname, '../js'),
  },
};
