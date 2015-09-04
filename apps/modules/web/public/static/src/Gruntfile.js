module.exports = function (grunt) {
    var transport = require('grunt-cmd-transport');
    var style = transport.style.init(grunt);
    var text = transport.text.init(grunt);
    var script = transport.script.init(grunt);

    grunt.initConfig({
        pkg : grunt.file.readJSON("package.json"),

        transport : {
            options : {
                paths : ['./js/'],
                alias: '<%= pkg.spm.alias %>',
                parsers : {
                    '.js' : [script.jsParser],
                    '.css' : [style.css2jsParser],
                    '.html' : [text.html2jsParser]
                }
            },
            app : {
                options : {
                    idleading : ''
                },

                files : [
                    {
                        cwd : 'js/',
                        src : ['**/*',  '!lib/seajs/*/*'],
                        filter : 'isFile',
                        dest : '.build/js'
                    }
                ]
            }
        },
        concat : {
            app : {
                files: [
                    {
                        src: ['js/rootConfig.js', '.build/js/**/*.js', '!.build/js/**/*-debug.js'],
                        dest: '../dist/js/app/all.js'
                    },
                    {
                        src: ['js/rootConfig.js', '.build/js/**/*-debug.js'],
                        dest: '../dist/js/app/all-debug.js'
                    }
                ]
            }
        },
        copy : {
            app : {
                files: [
                    {expand: true, src: ['js/lib/seajs/**'], dest: '../dist/'},
                ]
            }
        },
        cssmin: {
            options: {
                keepSpecialComments: 0
            },
            app: {
                files: {
                    '../dist/css/all.css': [
                        "css/**/*.css"
                    ]
                }
            }
        },
        processhtml: {
            options: {
                process: true
            },
            app : {
                files: [
                    {expand: true, src: ['html/**/*.html'], dest: '../dist/'}
                ]
            }
        },
        uglify : {
            app : {
                files: [
                    {
                        expand: true,
                        cwd: '../dist/',
                        src: ['js/**/*.js', '!js/**/*-debug.js'],
                        dest: '../dist/',
                        ext: '.js'
                    }
                ]
            }
        },

        clean : {
            spm : ['.build']
        }
    });

    grunt.loadNpmTasks('grunt-cmd-transport');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-processhtml-prepend');
    
    grunt.loadNpmTasks('grunt-process-tags');
    
    grunt.registerMultiTask('htmlprocess', 'My asynchronous task.', function() {
        grunt.log.writeln('htmlprocess started');
        var path = require('path');
        
        this.files.forEach(function(fileObj) {
            fileObj.src.forEach(function(fpath) {
                if (grunt.file.isDir(fpath)) {
                    grunt.file.mkdir(fpath);
                    return;
                }
//                grunt.log.writeln(fpath, fileObj.dest, path.extname(fpath));
                var srcData = grunt.file.read(fpath);
                var extName = path.extname(fpath)
                if (extName == '.html'){
                    var reg = /rootConfig/
                    srcData = srcData.replace(reg, 'app/all')
                }
                grunt.file.write(fileObj.dest, srcData);
                grunt.log.writeln(fpath,'->', fileObj.dest);
            });
        });

        grunt.log.writeln('html page process finished.');
    });
    
//    grunt.registerTask('build-styles', ['transport:styles', 'concat:styles', 'uglify:styles', 'clean']);
    grunt.registerTask('default', ['transport:app', 'concat:app', 'copy:app', 'uglify:app', 'cssmin:app', 'processhtml:app', 'clean']);
//    grunt.registerTask('default', ['clean']);
};