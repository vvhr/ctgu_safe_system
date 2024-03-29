package com.tony.app;

import com.tony.model.DeviceReportNew;
import com.tony.model.mapper.DeviceReportNewMapper;
import com.tony.msg.AnalysisUtils;
import com.tony.msg.MessageBase;
import com.tony.msg.MessageType;
import com.tony.utils.DebugUtil;
import com.tony.utils.MysqlUtil;
import io.netty.buffer.ByteBuf;
import io.netty.buffer.Unpooled;
import io.netty.channel.ChannelHandlerContext;
import io.netty.channel.SimpleChannelInboundHandler;
import io.netty.channel.socket.DatagramPacket;
import io.netty.util.CharsetUtil;
import org.apache.commons.codec.DecoderException;
import org.apache.commons.codec.binary.Hex;
import org.apache.ibatis.session.SqlSession;

import java.net.InetSocketAddress;
import java.util.ArrayList;

public class UdpServerHandler extends SimpleChannelInboundHandler<DatagramPacket> {
    private int udpDataReceiveCount = 0;
    // 并发Map

    /**
     * 接收数入口
     * @param ctx 通信环境
     * @param udpOriginDataObj 原生UDP数据包
     */
    @Override
    public void channelRead0(ChannelHandlerContext ctx, DatagramPacket udpOriginDataObj) throws DecoderException {
        udpDataReceiveCount++;
        DebugUtil.println("\r\nchannelRead0第几次UPD请求:---------------------------------------------->" + udpDataReceiveCount);
        /*从接收到的UDP数据对象得到ByteBuf*/
        ByteBuf udpByteBuf = udpOriginDataObj.content();

        /*将UDP数据对象的ByteBuf转为byte[]:注意：首先是创建空数组时指定长度。然后是将buf传入这个空数组*/
        byte[] udpByteArr = new byte[udpByteBuf.readableBytes()];

        //？udpByteArr像一个缓冲寄存器，readBytes将读到的字节逐个存入该字节数组。
        udpByteBuf.readBytes(udpByteArr);

        // 然后击转成16进制，打印
        String udpHexString = Hex.encodeHexString(udpByteArr);

        // 解析原始字符到mb列表
        ArrayList<MessageBase> messageBases = this.parseRawDataToMBs(udpHexString);

        // 解析mb合法数据
        for(MessageBase mb:messageBases){
            this.routeMessageBase(mb, ctx, udpOriginDataObj.sender());
        }
        // 1byte = 8bit = 2 x 4bit = 2个0x 元字符
        // 1-F 4bit

        // // DebugUtil.println("-->本次请求周期结束，向客户端发送一个消息");
        ctx.write(
                new DatagramPacket(
                    // 数据内容
                    Unpooled.copiedBuffer("数据接收成功", CharsetUtil.UTF_8),
                    // 接收人
                    udpOriginDataObj.sender()
                )
        );
    }

    @Override
    public void channelReadComplete(ChannelHandlerContext ctx) {
        ctx.flush();
    }

    @Override
    public void exceptionCaught(ChannelHandlerContext ctx, Throwable cause) {
        cause.printStackTrace();
        // We don't c
    }
    /*--------------------------------------启动解析时初始化解析的方法--------------------------------------*/
    /**
     * 解析原始UDP包，并验证合法性，把合法的记录放到一个列表中。这个作为初始化解析的第1步
     * @param udpHexString 原始UDP字符串Hex
     * @return 返回验证通过的数据列表
     */
    private ArrayList<MessageBase> parseRawDataToMBs(String udpHexString){
        // 解析原生数据
        ArrayList<MessageBase> messageBases = new ArrayList<>();

        try{
            // 判断起始符
            if(udpHexString.indexOf("ffff") == 0){
                // 将内容分段,连包的情况适合此法处理
                String[] udpHexStringPieces = udpHexString.split("ffff");
                // 遍历分段
                for(String udpHexStringPiece : udpHexStringPieces){
                    if(udpHexStringPiece.length()>=36){
                        udpHexStringPiece = "ffff"+udpHexStringPiece;
                        // 如果报文中给出的长度==报文的实际长度，那么表示可进一步处理
                        String hexLength = AnalysisUtils.HexS2B(udpHexStringPiece.substring(4,8));
                        if((udpHexStringPiece.length()/2)==Integer.valueOf(hexLength,16)){
                            MessageBase msgBase = MessageBase.read(udpHexStringPiece);
                            if(msgBase != null){
                                messageBases.add(msgBase);
                            }
                        }
                    }
                }
            }
        }catch(Exception e){
            e.printStackTrace();
        }
        return messageBases;
    }

    /*--------------------------------------路由--------------------------------------*/
    /**
     * 解析验证成功的合法数据MessageBase对象,并按解析出来的数据类型执行相关动作。这个作为初始化解析的第2步
     * @param mb 合法的单条上报数据
     * @param ctx 通讯环境
     * @param address UDP上报的来源地址
     */
    private void routeMessageBase(MessageBase mb, ChannelHandlerContext ctx, InetSocketAddress address) throws DecoderException {
        /*报文类型*/
        int msgType = mb.messageType;
        if (msgType == MessageType.ReqAlert) {
            reqAlertController(mb);
        }
    }

    /*--------------------------------------控制器--------------------------------------*/

    /**
     * 实时上传/故障报警报文处理控制器，实时上报与谐波存到一张表中
     * @param mb MessageBase
     */
    private void reqAlertController(MessageBase mb){
        DebugUtil.println("实时上报数据处理imei:" + mb.deviceId);
        ArrayList<DeviceReportNew> modelList = DeviceReportNew.parseMbToModelList(mb);
        for (DeviceReportNew model : modelList) {
            String uuid = model.getUuid();
            // 存表
            try (SqlSession session = MysqlUtil.createSession()) {
                DeviceReportNewMapper mapper = session.getMapper(DeviceReportNewMapper.class);
                // 实时上报存表
                model.setP(model.getV()/100 * model.getC()/100);
                mapper.insertOrUpdate(model);
                session.commit();
            }
        }
    }
}
