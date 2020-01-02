测试: dev.wind.slogger.cn

正式: wind.slogger.cn


数据库: movie
用户-表(movie_user): id -int
        id       - id(int)
        unionid  -唯一id
        name     -用户名(varchar 10)
        password -用户密码(int)
        icon     -用户头像(varchar 10)
        role     -用户权限

电影-表(movie_item): id -int
        name       -电影名称(varchar 20)
        poster     -海报(varchar 100)
        uri        -播放地址(varchar 200)
        series     -剧集(varchar 20)
        describe   -详情(varchar 200)
        star       -主演(varchar 100)
        score      -评分(varchar 10)

会员-表(movie_member): id-int
       id       -id(int)
       unionid  -唯一id
       name     -会员名
       password -会员密码
       icon     -会员头像
       level    -会员等级
       time     -注册时间

上报-表(movie_report): id-int
       id       -id(int)
       sceneid  -场景id(varchar 20)
       userid   -用户id(varchar 20)
       acttype  -行为id(varchar 20)
       time     -时间id(int 100)

