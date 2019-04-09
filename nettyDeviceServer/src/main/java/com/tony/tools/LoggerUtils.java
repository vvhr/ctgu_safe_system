package com.tony.tools;

import io.netty.util.internal.logging.InternalLogger;
import io.netty.util.internal.logging.InternalLoggerFactory;

public class LoggerUtils {
    private static InternalLogger logger = InternalLoggerFactory.getInstance(LoggerUtils.class);
    private static boolean isDebug = false;
    private static void setIsDebug(Boolean value){
        isDebug = value;
    }
    /**
     *
     * @param msg msg
     */
    public static void info(String msg){
        if(isDebug){
            logger.debug(msg);
        }else{
            logger.info(msg);
        }
    }

    /**
     * 异常错误打印
     * @param msg msg
     */
    public static void error(String msg){
        logger.error(msg);
    }
}
