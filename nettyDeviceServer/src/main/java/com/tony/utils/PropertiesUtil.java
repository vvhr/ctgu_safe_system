package com.tony.utils;

import java.io.IOException;
import java.io.InputStream;
import java.util.HashMap;
import java.util.Properties;

public class PropertiesUtil {

	private static HashMap<String,String> configMap = new HashMap<>();
	
	public static String getValue(String key){
		if(configMap == null || configMap.get(key) == null){
            resetProperties();
            return configMap.get(key);
        }else{
            return configMap.get(key);
		}
	}
	
	public static int getIntValue(String key){
		if(configMap != null && configMap.get(key) != null){
			return Integer.valueOf(configMap.get(key));
		}else{
			resetProperties();
			return Integer.valueOf(configMap.get(key));
		}
	}
	
	private static void resetProperties(){
		try{
			Properties properties = new Properties();
		    // 使用ClassLoader加载properties配置文件生成对应的输入流
 			// InputStream in = new FileInputStream(new File("/data/config/config.properties"));
		    InputStream in = PropertiesUtil.class.getClassLoader().getResourceAsStream("config.properties");

			properties.load(in);
			configMap.put("servicePort", properties.getProperty("service_port"));
			configMap.put("serviceIp", properties.getProperty("service_ip"));
			configMap.put("algoritServiceUrl",  properties.getProperty("algorithm_service_url"));
			configMap.put("jdbcUrl",  properties.getProperty("jdbc_url"));
			configMap.put("jdbcUsername",  properties.getProperty("jdbc_username"));
			configMap.put("jdbcPwd",  properties.getProperty("jdbc_password"));
			configMap.put("mongoHost",  properties.getProperty("mongodb_host"));
			configMap.put("mongoPort",  properties.getProperty("mongodb_port"));
			configMap.put("mongoHost2",  properties.getProperty("mongodb_host2"));
			configMap.put("mongoPort2",  properties.getProperty("mongodb_port2"));
			configMap.put("mongoUsername",  properties.getProperty("mongodb_username"));
			configMap.put("mongoPwd",  properties.getProperty("mongodb_password"));
			configMap.put("mongoDatabase",  properties.getProperty("mongodb_database"));
			configMap.put("replSetName", properties.getProperty("mongodb_replsetname"));
			configMap.put("redisPort", properties.getProperty("redis_port"));
			configMap.put("redisIp", properties.getProperty("redis_ip"));
			configMap.put("redisPwd", properties.getProperty("redis_pwd"));
			configMap.put("redisTimeout", properties.getProperty("redis_timeout"));
			configMap.put("apiUrl", properties.getProperty("api_url"));
			configMap.put("leakageCurrent",  properties.getProperty("leakageCurrent"));
		}catch(IOException io){
			io.printStackTrace();
		}
	}
}
