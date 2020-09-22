module.exports = function(grunt) {
    grunt.initConfig({
        pot: {
            options: {
                text_domain: 'mangofp', //Your text domain. Produces mangofp.pot
                dest: 'languages/', //directory to place the pot file
                keywords: ['gettext', '__', 'esc_html__', 'esc_html_e'], //functions to look for
            },
            files: {
                src: ['**/*.php'], //Parse all php files
                expand: true,
            }
        },
    });
    grunt.loadNpmTasks('grunt-pot');
    grunt.registerTask('gettext', [
        'pot',
    ]);
};
