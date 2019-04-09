package com.tony.app;

import io.netty.channel.ChannelHandlerContext;

import java.net.InetSocketAddress;

class IMEICache {
    ChannelHandlerContext ctx;
    InetSocketAddress address;
    String ccid;
    // 上次上报的0-漏电流，1-温度，2-有功，3-无功(abcd代表四个通道)
    // 每个imei保存一个设备的四个通道历史信息，每个通道保存几个关键历史值
    int[][] preValues = new int[4][4];

}
