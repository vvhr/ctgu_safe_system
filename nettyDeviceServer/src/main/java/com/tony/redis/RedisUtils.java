package com.tony.redis;

import org.apache.commons.pool2.impl.GenericObjectPoolConfig;

import com.tony.utils.PropertiesUtil;

import redis.clients.jedis.Jedis;
import redis.clients.jedis.JedisPool;

public class RedisUtils {

    static JedisPool pool = null;
    static Jedis jedis = null;

    /*static 静态代码块。有些代码必须在项目启动的时候就执行的时候,需要使用静态代码块,这种代码是主动执行的;需要在项目启动的时候就初始化,在不创建对象的情况下,其他程序来调用的时候,需要使用静态方法,这种代码是被动执行的. 静态方法在类加载的时候 就已经加载 可以用类名直接调用*/
    static {
        init();
    }


    private static void init(){
        GenericObjectPoolConfig config = new GenericObjectPoolConfig();
        config.setTestOnBorrow(true);
        config.setTestOnReturn(false);
        config.setTestWhileIdle(true);
        config.setMaxTotal(100);
        config.setMaxIdle(100);
        config.setMaxWaitMillis(2000);
        pool = new JedisPool(config, PropertiesUtil.getValue("redisIp"), PropertiesUtil.getIntValue("redisPort"), PropertiesUtil.getIntValue("redisTimeout"),PropertiesUtil.getValue("redisPwd"));// 在应用初始化的时候生成连接池
        // pool = new JedisPool(config, PropertiesUtils.getValue("redisIp"), PropertiesUtils.getIntValue("redisPort"), PropertiesUtils.getIntValue("redisTimeout"));// 在应用初始化的时候生成连接池
    }

    public static void setToRedis(String key,String value){
        if(pool!=null){
            jedis = pool.getResource();
            jedis.set(key, value);
            jedis.close();
        }
    }

    public static String getValue(String key){
        try{
            if(pool!=null){
                if(jedis==null)
                    jedis = pool.getResource();
                return jedis.get(key);
            }
        }catch(Exception e){
            init();
        }
        return null;
    }

    public static void remove(String key){
        if(pool!=null){
            jedis = pool.getResource();
            jedis.del(key);
            jedis.close();
        }
    }

    /**
     * 设置存储对象，带有效时间
     * @param key
     * @param value
     * @param timeOut
     */
    public static void setValueByOutTime(String key,String value,int timeOut){
        if(pool!=null){
            jedis = pool.getResource();
            jedis.set(key, value);
            jedis.expire(key, timeOut);//设置key的生存时间,单位秒
            jedis.close();
        }
    }
}

