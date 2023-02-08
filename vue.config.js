module.exports = {
    chainWebpack(config) {
        config.resolve.alias
            .set("lottie-player", "@vue/composition-api");
    }
};