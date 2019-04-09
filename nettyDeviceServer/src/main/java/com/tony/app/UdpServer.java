package com.tony.app;

import io.netty.bootstrap.Bootstrap;
import io.netty.channel.ChannelOption;
import io.netty.channel.EventLoopGroup;
import io.netty.channel.nio.NioEventLoopGroup;
import io.netty.channel.socket.nio.NioDatagramChannel;

public class UdpServer
{
    // 端口
//    private static final int PORT = Integer.parseInt(System.getProperty("port", "7786"));
    private static final int PORT = Integer.parseInt(System.getProperty("port", "2060"));

    public static void main( String[] args ) throws InterruptedException {
        System.out.println("-->service start：配置服务，并启动服务监听进程，监听端口" + PORT);
        EventLoopGroup group = new NioEventLoopGroup();
        try {
            Bootstrap b = new Bootstrap();
            // 一系列的配置。服务器的主要工作是轮循监听.
            b.group(group)
                    // 什么通道
                    .channel(NioDatagramChannel.class)
                    // 什么选项
                    .option(ChannelOption.SO_BROADCAST, true)
                    // 什么处理器
                    .handler(new UdpServerHandler());

            // closeFuture表示由未来状态决定关闭动作，也就是说ctx环境会下达这个状态通知
            b.bind(PORT).sync().channel().closeFuture().await();
        } finally {
            group.shutdownGracefully();
        }
    }
}
