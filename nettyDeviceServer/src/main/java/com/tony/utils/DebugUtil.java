package com.tony.utils;

public class DebugUtil {
    private static Boolean debugModel = true;
    public static void println(String arg){
        if(debugModel){
            System.out.println(arg);
        }
    }

    public static void print(String arg){
        if(debugModel){
            System.out.print("  [ " + arg + " ] ");
        }
    }
}
