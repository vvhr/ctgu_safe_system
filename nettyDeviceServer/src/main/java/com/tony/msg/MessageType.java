package com.tony.msg;


public interface MessageType {
    //上行
    //    报警故障复位报文
    int ReqReset = 0x01;
    //    心跳报文
    int ReqHeart = 0x21;
    //    自检报文
    int ReqCheck = 0x03;
    //    脱扣报文
    int ReqTripping = 0x04;
    //    故障报警/实时数据报文
    int ReqAlert = 0x22;
    //	    谐波报文
    int ReqHer=0x23;
    //    设备参数报文
    int ReqParam = 0x65;

    int reqCcid = 0x37;

    //下行

    //    报警故障复位报文
    int ResAlertReset = 0x61;
    //    自检报文
    int ResReset = 0x62;
    //    脱扣报文
    int ResTrip = 0x63;
    //    设备参数设置报文
    int ResParam = 0x64;
    //    UTC设置报文
    int ResUtc =0x66;

    //    UTC设置报文
    int ResCCID =0x67;
    // 转义符
    int esc = 0x00;

}
