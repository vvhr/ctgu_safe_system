package com.tony.tools;

public class FormatTool {
    public static String bytes2String(byte[] bytes){
        StringBuilder udpByteArrToString = new StringBuilder();
        for(byte b:bytes){
            udpByteArrToString.append("[").append(b).append("] ");
        }
        return udpByteArrToString.toString();
    }
}
