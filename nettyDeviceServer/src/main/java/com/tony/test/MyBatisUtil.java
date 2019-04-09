package com.tony.test;

import org.apache.ibatis.io.Resources;
import org.apache.ibatis.session.SqlSessionFactory;
import org.apache.ibatis.session.SqlSessionFactoryBuilder;

import java.io.IOException;
import java.io.Reader;

public class MyBatisUtil {
    private final static SqlSessionFactory sqlSessionFactory;
    /*静态构造方法，构造SQL会话工厂*/
    static {
        String resource = "mybatis-config.xml";
        Reader reader = null;
        try {
            reader = Resources.getResourceAsReader(resource);
        } catch (IOException e) {
            System.out.println(e.getMessage());

        }
        /*使用读取到的XML配置文件作为参数，来构造SQL会话工厂*/
        sqlSessionFactory = new SqlSessionFactoryBuilder().build(reader);
    }

    /*返回SQL会话工厂*/
    public static SqlSessionFactory getSqlSessionFactory() {
        return sqlSessionFactory;
    }
}
