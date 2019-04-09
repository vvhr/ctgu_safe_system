package com.tony.utils;

import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;

public class DataFormatUtils {
	
	  /**
	  * 日期格式化对象，保证线程安全
	  */
	  private static final ThreadLocal<DateFormat> sdf = ThreadLocal.withInitial(() -> new SimpleDateFormat("yyyy-MM-dd HH:mm:ss"));
	   
	   /**
	    * 获得日期格式对象
	    * @return
	    */
	   public static DateFormat getDateFormat(){
	   	return sdf.get();
	   }

	   public static long getTimeStamp(String date) throws ParseException {
		   return sdf.get().parse(date).getTime();
	   }
	   /**
	    * 获得字符串类型的日期信息
	    * @param times
	    * @return
	    */
	   public static String format(final long times){
		   return sdf.get().format(times);
	   }
}
