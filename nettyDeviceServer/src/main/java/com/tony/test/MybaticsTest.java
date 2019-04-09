package com.tony.test;

import com.tony.model.HomePortrait;
import com.tony.model.mapper.HomePortraitMapper;
import org.apache.ibatis.session.SqlSession;
import org.apache.ibatis.session.SqlSessionFactory;

import java.util.List;


public class MybaticsTest {
    static SqlSessionFactory sqlSessionFactory = null;
    static {
        sqlSessionFactory = MyBatisUtil.getSqlSessionFactory();
    }

    public static void main(String[] args) {
//        testAdd();
//        getUser();
        getHomePortraits();

    }

    private static void getHomePortraits() {
        try (SqlSession sqlSession = sqlSessionFactory.openSession()) {
            HomePortraitMapper mapper = sqlSession.getMapper(HomePortraitMapper.class);
            List<HomePortrait> list = mapper.find("180033001157345432383820_1");
            System.out.println("name: " + list.get(1).getAppName());
        }
    }

//    private static void testAdd() {
//        /*使用sql会话工厂产生一个sql会话*/
//        try (SqlSession sqlSession = sqlSessionFactory.openSession()) {
//            /*会话实例通过操作集接口类获取一个操作集*/
//            UserMapper userMapper = sqlSession.getMapper(UserMapper.class);
//            /*定义一条记录*/
//            User user = new User("lisi", 25);
//            /*调用操作集方法*/
//            userMapper.insertUser(user);
//            /*提交*/
//            sqlSession.commit();// 这里一定要提交，不然数据进不去数据库中
//        }
//    }
//
//    private static void getUser() {
//        try (SqlSession sqlSession = sqlSessionFactory.openSession()) {
//            UserMapper userMapper = sqlSession.getMapper(UserMapper.class);
//            User user = userMapper.getUser("zhangsan");
//            System.out.println("name: " + user.getName() + "|age: " + user.getAge());
//        }
//    }
}
