package com.tony.client;

import com.tony.tools.FormatTool;
import com.tony.utils.DebugUtil;
import io.netty.buffer.ByteBuf;
import io.netty.channel.ChannelHandlerContext;
import io.netty.channel.SimpleChannelInboundHandler;
import io.netty.channel.socket.DatagramPacket;
import io.netty.util.CharsetUtil;
import org.apache.commons.codec.binary.Hex;

public class UdpClientHandler extends SimpleChannelInboundHandler<DatagramPacket> {
    @Override
    public void channelRead0(ChannelHandlerContext ctx, DatagramPacket packet) throws Exception {
        // 打印接收到的UDP数据包
        // DebugUtil.println("-->receive from server :" + packet.content().toString());

        /*从接收到的UDP数据对象得到ByteBuf*/
        ByteBuf udpByteBuf = packet.content();

        /*将UDP数据对象的ByteBuf转为byte[]:注意：首先是创建空数组时指定长度。然后是将buf传入这个空数组*/
        byte[] udpByteArr = new byte[udpByteBuf.readableBytes()];
        //？udpByteArr像一个缓冲寄存器，readBytes将读到的字节逐个存入该字节数组。
        udpByteBuf.readBytes(udpByteArr);

        /*调试打印udp字节组*/
        DebugUtil.println("-->UDP原始字节数组:" + FormatTool.bytes2String(udpByteArr));

        // 然后击转成16进制，打印
        String udpHexString = Hex.encodeHexString(udpByteArr);
        DebugUtil.println("-->UDP原始Hex字符串:" + udpHexString);
        // 关闭服务
        DebugUtil.println("-->ctx close");
        ctx.close();
    }

    @Override
    public void channelReadComplete(ChannelHandlerContext ctx) {
    }

    @Override
    public void exceptionCaught(ChannelHandlerContext ctx, Throwable cause) {
        cause.printStackTrace();
    }
}
