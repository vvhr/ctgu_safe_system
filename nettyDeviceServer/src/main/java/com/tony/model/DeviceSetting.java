package com.tony.model;

import com.tony.msg.MessageBase;
import com.tony.utils.DataFormatUtils;

/**
 * 该对象对应设备参数，对应device_setting数据表
 */
public class DeviceSetting {
    private int id;
    private String imei;
    // 配置密码
    private int pwd;
    // 电压变比
    private int pt;
    //通道对应电流变比
    private int ct1;
    private int ct2;
    private int ct3;
    private int ct4;
    // 电压报警值
    private int v1;
    private int v2;
    private int v3;
    private int v4;
    // 电流报警值
    private int e1;
    private int e2;
    private int e3;
    private int e4;
    // 漏电流报警值
    private int lc1;
    private int lc2;
    private int lc3;
    private int lc4;
    // 温度报警值
    private int t1;
    private int t2;
    private int t3;
    private int t4;
    // 通道开启标志
    private int cef;
    // 外设开启标志（蜂鸣器，继电器，电压模式：0单相，1三相）
    private int epem;


    // 报警延时
    private int ad;
    // 实时上报周期
    private int rttrc;
    // 谐波上报周期
    private int chrp;
    // 心跳上报周期
    private int hc;
    // 更新时间
    private String update_time;
    // 是否系统更改
    private int verified;


/*----------------------------业务逻辑----------------------------*/
    public static DeviceSetting parseMbToMode(MessageBase mb){
        String imei = mb.deviceId;
        // 转义
        String dataBodyHex = mb.data;
        // 转化
        DeviceSetting deviceSetting = new DeviceSetting();
        deviceSetting.imei = imei;
        deviceSetting.update_time = DataFormatUtils.format(System.currentTimeMillis());
        deviceSetting.verified = 1;

        deviceSetting.pwd = Integer.valueOf(dataBodyHex.substring(0,4),16);
        deviceSetting.pt = Integer.valueOf(dataBodyHex.substring(4,8),16);
        deviceSetting.ct1 = Integer.valueOf(dataBodyHex.substring(8,12),16);
        deviceSetting.ct2 = Integer.valueOf(dataBodyHex.substring(12,16),16);
        deviceSetting.ct3 = Integer.valueOf(dataBodyHex.substring(16,20),16);
        deviceSetting.ct4 = Integer.valueOf(dataBodyHex.substring(20,24),16);
        deviceSetting.v1 = Integer.valueOf(dataBodyHex.substring(24,28),16);
        deviceSetting.v2 = Integer.valueOf(dataBodyHex.substring(28,32),16);
        deviceSetting.v3 = Integer.valueOf(dataBodyHex.substring(32,36),16);
        deviceSetting.v4 = Integer.valueOf(dataBodyHex.substring(36,40),16);
        deviceSetting.e1 = Integer.valueOf(dataBodyHex.substring(40,44),16);
        deviceSetting.e2 = Integer.valueOf(dataBodyHex.substring(44,48),16);
        deviceSetting.e3 = Integer.valueOf(dataBodyHex.substring(48,52),16);
        deviceSetting.e4 = Integer.valueOf(dataBodyHex.substring(52,56),16);
        deviceSetting.lc1 = Integer.valueOf(dataBodyHex.substring(56,60),16);
        deviceSetting.lc2 = Integer.valueOf(dataBodyHex.substring(60,64),16);
        deviceSetting.lc3 = Integer.valueOf(dataBodyHex.substring(64,68),16);
        deviceSetting.lc4 = Integer.valueOf(dataBodyHex.substring(68,72),16);
        deviceSetting.t1 = Integer.valueOf(dataBodyHex.substring(72,76),16);
        deviceSetting.t2 = Integer.valueOf(dataBodyHex.substring(76,80),16);
        deviceSetting.t3 = Integer.valueOf(dataBodyHex.substring(80,84),16);
        deviceSetting.t4 = Integer.valueOf(dataBodyHex.substring(84,88),16);
        deviceSetting.cef = Integer.valueOf(dataBodyHex.substring(88,92),16);

        deviceSetting.ad = Integer.valueOf(dataBodyHex.substring(92,96),16);
        deviceSetting.rttrc = Integer.valueOf(dataBodyHex.substring(96,100),16);
        deviceSetting.chrp = Integer.valueOf(dataBodyHex.substring(100,104),16);
        deviceSetting.hc = Integer.valueOf(dataBodyHex.substring(104,108),16);
        return deviceSetting;
    }
/*----------------------------get and set----------------------------*/
    public DeviceSetting() {

    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getPwd() {
        return pwd;
    }

    public void setPwd(int pwd) {
        this.pwd = pwd;
    }

    public int getPt() {
        return pt;
    }

    public void setPt(int pt) {
        this.pt = pt;
    }

    public int getCt1() {
        return ct1;
    }

    public void setCt1(int ct1) {
        this.ct1 = ct1;
    }

    public int getCt2() {
        return ct2;
    }

    public void setCt2(int ct2) {
        this.ct2 = ct2;
    }

    public int getCt3() {
        return ct3;
    }

    public void setCt3(int ct3) {
        this.ct3 = ct3;
    }

    public int getCt4() {
        return ct4;
    }

    public void setCt4(int ct4) {
        this.ct4 = ct4;
    }

    public int getV1() {
        return v1;
    }

    public void setV1(int v1) {
        this.v1 = v1;
    }

    public int getV2() {
        return v2;
    }

    public void setV2(int v2) {
        this.v2 = v2;
    }

    public int getV3() {
        return v3;
    }

    public void setV3(int v3) {
        this.v3 = v3;
    }

    public int getV4() {
        return v4;
    }

    public void setV4(int v4) {
        this.v4 = v4;
    }

    public int getE1() {
        return e1;
    }

    public void setE1(int e1) {
        this.e1 = e1;
    }

    public int getE2() {
        return e2;
    }

    public void setE2(int e2) {
        this.e2 = e2;
    }

    public int getE3() {
        return e3;
    }

    public void setE3(int e3) {
        this.e3 = e3;
    }

    public int getE4() {
        return e4;
    }

    public void setE4(int e4) {
        this.e4 = e4;
    }

    public int getLc1() {
        return lc1;
    }

    public void setLc1(int lc1) {
        this.lc1 = lc1;
    }

    public int getLc2() {
        return lc2;
    }

    public void setLc2(int lc2) {
        this.lc2 = lc2;
    }

    public int getLc3() {
        return lc3;
    }

    public void setLc3(int lc3) {
        this.lc3 = lc3;
    }

    public int getLc4() {
        return lc4;
    }

    public void setLc4(int lc4) {
        this.lc4 = lc4;
    }

    public int getT1() {
        return t1;
    }

    public void setT1(int t1) {
        this.t1 = t1;
    }

    public int getT2() {
        return t2;
    }

    public void setT2(int t2) {
        this.t2 = t2;
    }

    public int getT3() {
        return t3;
    }

    public void setT3(int t3) {
        this.t3 = t3;
    }

    public int getT4() {
        return t4;
    }

    public void setT4(int t4) {
        this.t4 = t4;
    }

    public int getCef() {
        return cef;
    }

    public void setCef(int cef) {
        this.cef = cef;
    }

    public int getEpem() {
        return epem;
    }

    public void setEpem(int epem) {
        this.epem = epem;
    }

    public String getImei() {
        return imei;
    }

    public void setImei(String imei) {
        this.imei = imei;
    }

    public int getAd() {
        return ad;
    }

    public void setAd(int ad) {
        this.ad = ad;
    }

    public int getRttrc() {
        return rttrc;
    }

    public void setRttrc(int rttrc) {
        this.rttrc = rttrc;
    }

    public int getChrp() {
        return chrp;
    }

    public void setChrp(int chrp) {
        this.chrp = chrp;
    }

    public int getHc() {
        return hc;
    }

    public void setHc(int hc) {
        this.hc = hc;
    }

    public String getUpdate_time() {
        return update_time;
    }

    public void setUpdate_time(String update_time) {
        this.update_time = update_time;
    }

    public int getVerified() {
        return verified;
    }

    public void setVerified(int verified) {
        this.verified = verified;
    }
}
