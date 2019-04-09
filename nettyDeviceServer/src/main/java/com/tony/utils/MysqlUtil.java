package com.tony.utils;

import com.tony.test.MyBatisUtil;
import org.apache.ibatis.session.SqlSession;
import org.apache.ibatis.session.SqlSessionFactory;

public class MysqlUtil {
    private static SqlSessionFactory sqlSessionFactory = MyBatisUtil.getSqlSessionFactory();
    public static SqlSession createSession(){
        return sqlSessionFactory.openSession();
    }
}
