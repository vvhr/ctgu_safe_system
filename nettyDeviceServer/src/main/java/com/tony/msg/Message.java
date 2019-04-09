package com.tony.msg;

import io.netty.buffer.Unpooled;
import io.netty.channel.socket.DatagramPacket;
import io.netty.util.CharsetUtil;
import org.apache.commons.codec.DecoderException;
import org.apache.commons.codec.binary.Hex;

import java.net.InetSocketAddress;

public class Message {
    private String start = "ffff";
    private String length = "0102";
    private String msgType = "01";
    private String deviceType = "01";
    private String deviceId = "808080808080808080808080";
    private String checkSum = "0102";
    private String v1 ="0102";
    private String v2 ="0102";
    private String v3 ="0102";
    private String v4 ="0102";
    private String c1 ="0102";
    private String c2 ="0102";
    private String c3 ="0102";
    private String c4 ="0102";
    private String lc1 ="0102";
    private String lc2 ="0102";
    private String lc3 ="0102";
    private String lc4 ="0102";
    private String t1 ="0102";
    private String t2 ="0102";
    private String t3 ="0102";
    private String t4 ="0102";
    private String wrong1 ="0000";
    private String wrong2 ="0000";
    private String alarm ="0000";
    private String data = v1+v2+v3+v4+c1+c2+c3+c4+lc1+lc2+lc3+lc4+t1+t2+t3+t4+wrong1+wrong2+alarm;
    private String hexStr = start + length + msgType + deviceType +deviceId+data+checkSum;

    public String getHexStr(){
        System.err.println("16进制数为："+ hexStr);
        return hexStr;
    }
    // 不同的构造方法重载，接收不同类型的参数
    public static DatagramPacket getMessage(String text, InetSocketAddress address){
        return new DatagramPacket(
                // 数据内容
                Unpooled.copiedBuffer(text, CharsetUtil.UTF_8),
                // 接收人
                address
        );
    }

    // 不同的构造方法重载，接收不同类型的参数
    public static DatagramPacket getMessage(byte[] bytes, InetSocketAddress address){
        return new DatagramPacket(
                // 数据内容
                Unpooled.copiedBuffer(bytes),
                // 接收人
                address
        );
    }

    // 不同的构造方法重载，接收不同类型的参数
    public static DatagramPacket getMessage(String text, InetSocketAddress address, Boolean isHex) throws DecoderException {
        DatagramPacket msg = null;
        if(isHex){
            msg = new DatagramPacket(
                    // 数据内容
                    Unpooled.copiedBuffer(Hex.decodeHex(text)),
                    // 接收人
                    address
            );
        }else{
            msg = new DatagramPacket(
                    // 数据内容
                    Unpooled.copiedBuffer(text, CharsetUtil.UTF_8),
                    // 接收人
                    address
            );
        }
        return msg;
    }
}
