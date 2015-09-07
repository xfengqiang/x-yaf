
## [类的分包规则]
library目录下: 
    Common放通用性强，不依赖于Common包以外的对象;
    Http放特定于http服务器的类以及基类；
    
## [部署方式]
可以按照以下步骤来部署和运行程序:
1.请确保机器已经安装了Yaf框架, 并且已经加载入PHP;
2.把XYaf目录Copy到Webserver的DocumentRoot目录下;
3.需要在php.ini里面启用如下配置，生产的代码才能正确运行：
	yaf.environ="product"
4.重启Webserver;
5.访问http://yourhost/XYaf/,出现Hellow Word!, 表示运行成功,否则请查看php错误日志;
