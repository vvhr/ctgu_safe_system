package com.tony.test;

import com.tony.redis.RedisUtils;

public class RedisTest {
    public static void main(String[] arg){
        System.err.println("test Redis");
        RedisUtils.setToRedis("name", "tony");
        System.out.println("-->RedisUtils.getValue(\"name\"):" + RedisUtils.getValue("name"));
    }
}
