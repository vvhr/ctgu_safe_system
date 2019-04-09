package com.tony.proto;

import com.tony.msg.AnalysisUtils;
import com.tony.msg.CRC_16;
import com.tony.utils.DebugUtil;
import io.netty.buffer.Unpooled;
import io.netty.channel.ChannelHandlerContext;
import io.netty.channel.socket.DatagramPacket;
import org.apache.commons.codec.DecoderException;
import org.apache.commons.codec.binary.Hex;

import java.net.InetSocketAddress;

public class ProtoCommand {
    // 查询报文类型Hex字符
    // CCID查询
    public static final String QUERY_TYPE_CCID = "3706";
    // 设置参数查询
    public static final String QUERY_TYPE_SETTING = "3506";
    // 脱扣
    public static final String QUERY_TYPE_CLOSE = "6306";
    // 复位
    public static final String QUERY_TYPE_RESET = "3106";
    // 静音
    public static final String QUERY_TYPE_MUTE = "3806";
    // 自检
    public static final String QUERY_TYPE_CHECK = "6206";

    public static void send(String msg, InetSocketAddress receiver, ChannelHandlerContext ctx) throws DecoderException {
        msg = msg.replaceAll("\"", "");
        ctx.writeAndFlush(new DatagramPacket(Unpooled.copiedBuffer(Hex.decodeHex(msg.toLowerCase())),receiver));
    }

    public static void sendQuery(String queryTypeHEX,String deviceId,ChannelHandlerContext ctx, InetSocketAddress address) throws DecoderException{
        // 发送ccid查询命令
        String hex2 = queryTypeHEX + deviceId;
        int crc2 = CRC_16.alex_crc16(Hex.decodeHex(hex2), Hex.decodeHex(hex2).length);
        String suc2 = Hex.encodeHexString(AnalysisUtils.intToByte(crc2));
        hex2 = "ffff1400" + hex2 + suc2.toLowerCase();
        // DebugUtil.println("查询命令类型：" + hex2);
        send(hex2,address,ctx);
    }
}
