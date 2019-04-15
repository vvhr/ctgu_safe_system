package com.tony.client;

import com.tony.msg.Message;
import com.tony.msg.MockMessage;
import com.tony.utils.DebugUtil;
import io.netty.bootstrap.Bootstrap;
import io.netty.channel.Channel;
import io.netty.channel.ChannelOption;
import io.netty.channel.EventLoopGroup;
import io.netty.channel.nio.NioEventLoopGroup;
import io.netty.channel.socket.DatagramPacket;
import io.netty.channel.socket.nio.NioDatagramChannel;
import io.netty.util.internal.SocketUtils;
import java.net.InetSocketAddress;

public final class UdpClient {
    /*服务器的通信端口*/
    private static final int PORT = Integer.parseInt(System.getProperty("port", "2060"));
    /*通信地址对象：主机名，端口*/
    private static final InetSocketAddress ADDRESS = SocketUtils.socketAddress("localhost", PORT);
    // 模拟客户端
    public static void main(String[] args) throws Exception {
        for(int i=1;i<200;i++){
            int index = (int)System.currentTimeMillis()%12;
            System.out.println(index);
            String messageContent = MockMessage.newProtoMessageArray[index];
            client(i, messageContent);
            Thread.sleep(800);
        }
    }
    // 客户端发送UDP包
    private static void client(int i, String messageContent) throws Exception {
        DebugUtil.println("-->ctx " + i + " start");
        /*分组循环查询事件-事件处理器*/
        EventLoopGroup group = new NioEventLoopGroup();
        try {
            /*启动器*/
            Bootstrap bootstraper = new Bootstrap();
            /*通过启动器进行初始化配置，并启动服务进程*/
            bootstraper
                    /*设置通信分组工具*/
                    .group(group)
                    /*设置通信通道工具*/
                    .channel(NioDatagramChannel.class)
                    /*设置通信选项*/
                    .option(ChannelOption.SO_BROADCAST, true)
                    /*设置业务逻辑处理器*/
                    .handler(new UdpClientHandler());

            // 绑定0是表示向所有端口广播？？？因为如果用7686，则会提示端口被占用，因为这里，用的是bind而不是connect
            /*绑定接收监听的端口，设置为异步，使用channel()方法获得通道实例*/
            // 绑定端口0表示，系统内核会自动分配一个可用端口给他。这样就可以同时使用多个客户端了。
            Channel ch = bootstraper.bind(0).sync().channel();
            /*发送的内容*/
            DebugUtil.println("-->send to server : " + messageContent);
            /*内容打包*/
            DatagramPacket msg = Message.getMessage(messageContent, ADDRESS, true);
            /*通道对象IO操作*/
            ch.writeAndFlush(msg).sync();
            // 设置通道异步关闭的等待时间，如果设置了等待时间，那么超时则会强制关闭
            if (!ch.closeFuture().await(5000)) {
                DebugUtil.println("-->5秒内没有接到关闭通知，强制关闭");
            }
        } finally {
            // 工作组关闭
            group.shutdownGracefully();
        }
    }
}