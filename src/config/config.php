<?php

return [
    // 验证码存储驱动
    'driver' => 'file',  // 支持: file, redis, memcache, memcached, database, cookie

    // 驱动配置
    'stores' => [
        'file' => [
            'path' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'captcha'
        ],
        'redis' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => '',
            'database' => 0,
            'prefix' => 'captcha:'
        ],
        'memcache' => [
            'host' => '127.0.0.1',
            'port' => 11211,
            'prefix' => 'captcha:'
        ],
        'database' => [
            'table' => 'captcha',
            'expire_field' => 'expire_time',
            'code_field' => 'code'
        ]
    ],

    // 验证码配置
    'config' => [
        // 验证码字符集
        'characters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        
        // 验证码类型
        'type' => 'default', // 支持: default(字母数字), chinese(中文), slide(滑块), rotate(旋转), click(点击文字)
        
        // 验证码图片宽度
        'width' => 300,
        
        // 验证码图片高度
        'height' => 200,
        
        // 验证码位数
        'length' => 4,
        
        // 验证码有效期（秒）
        'expire' => 300,
        
        // 是否添加混淆曲线
        'useCurve' => true,
        
        // 混淆曲线数量
        'curveNumber' => 6,
        
        // 混淆曲线复杂度
        'curveComplexity' => 1,
        
        // 是否添加噪点
        'useNoise' => true,
        
        // 噪点数量
        'noiseNumber' => 50,
        
        // 文字扭曲度范围
        'textTwist' => [-80, 80],
        
        // 字体文件路径
        'fontFile' => __DIR__ . '/../assets/fonts/zhttfs/1.ttf',
        
        // 背景图片目录
        'bgPath' => __DIR__ . '/../assets/bg',
        
        // 中文验证码字符集
        'zhSet' => '们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借',
        
        // 滑块验证码配置
        'slide' => [
            'block_size' => 50,  // 滑块大小
            'offset' => 5,      // 允许的误差像素
            'shapes' => ['triangle', 'diamond', 'heart'], // 可选的图形形状
            'opacity' => 0.8    // 图形透明度
        ],
        
        // 旋转验证码配置
        'rotate' => [
            'min_angle' => 0,    // 最小旋转角度
            'max_angle' => 360,  // 最大旋转角度
            'offset' => 10,      // 允许的误差角度
            'block_size' => 60   // 旋转图形大小
        ],
        
        // 点击文字验证码配置
        'click' => [
            'word_count' => 4,   // 文字数量
            'font_size' => [20, 40], // 字体大小范围
            'colors' => ['#ff0000', '#00ff00', '#0000ff', '#000000'], // 可选文字颜色
            'opacity' => [0.6, 1.0], // 文字透明度范围
            'twist' => [-85, 85],  // 文字旋转角度范围
            'tolerance' => 30,     // 点击位置允许的误差像素
            'code_image' => [      // 小验证码图片设置
                'width' => 150,    // 宽度
                'height' => 50,    // 高度
                'font_size' => 20, // 字体大小
                'use_curve' => true, // 是否使用干扰线
                'curve_number' => 2, // 干扰线数量
                'use_noise' => true, // 是否使用噪点
                'noise_number' => 10 // 噪点数量
            ]
        ]
    ]
]; 