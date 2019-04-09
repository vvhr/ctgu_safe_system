package com.tony.model;

import org.bson.Document;
import java.util.ArrayList;

/**
 * 该对象对应老协议谐波上报数据据，对应harmonic_report_one表
 */
public class HarmonicReportOne {
    // 所有通道中，谐波(1,3,5,7,9)，相角(1,3,5,7,9)，有功，无功，功率因率的Hex字符串总长度。单个通道是60个Hex字符，30字节
    // public static int dataHexLength;
    // 通道数 channelCount = dataHexLength / 60
    // public static int channelCount;
    // 谐波
    private String uuid;
    private String imei;
    private int channel;
    private String reportTime;
    private int h1;
    private int h3;
    private int h5;
    private int h7;
    private int h9;
    // 相角
    private int a1;
    private int a3;
    private int a5;
    private int a7;
    private int a9;
    // 有功
    private int p;
    // 无功
    private int np;
    // 功率因素
    private int rate;
    public static ArrayList<Document> convertListOfModelToDoc(ArrayList<HarmonicReportOne> modelList){
        ArrayList<Document> docList = new ArrayList<>();
        for (HarmonicReportOne model:modelList) {
            Document doc = new Document();
            doc.put("uuid", model.uuid);
            doc.put("imei", model.imei);
            doc.put("channel", model.channel);
            doc.put("h1", model.h1);
            doc.put("a1", model.a1);
            doc.put("h3", model.h3);
            doc.put("a3", model.h3);
            doc.put("h5", model.h5);
            doc.put("a5", model.a5);
            doc.put("h7", model.h7);
            doc.put("a7", model.a7);
            doc.put("h9", model.h9);
            doc.put("a9", model.a9);
            doc.put("p", model.p);
            doc.put("np", model.np);
            doc.put("rate", model.rate);
            doc.put("reportTime", model.reportTime);
            docList.add(doc);
        }
        return docList;
    }

/*get set 方法------------------------------------------*/
    public String getUuid() {
        return uuid;
    }

    public void setUuid(String uuid) {
        this.uuid = uuid;
    }

    public String getImei() {
        return imei;
    }

    public void setImei(String imei) {
        this.imei = imei;
    }

    public int getChannel() {
        return channel;
    }

    public void setChannel(int channel) {
        this.channel = channel;
    }

    public String getReportTime() {
        return reportTime;
    }

    public void setReportTime(String reportTime) {
        this.reportTime = reportTime;
    }

    public int getH1() {
        return h1;
    }

    public void setH1(int h1) {
        this.h1 = h1;
    }

    public int getH3() {
        return h3;
    }

    public void setH3(int h3) {
        this.h3 = h3;
    }

    public int getH5() {
        return h5;
    }

    public void setH5(int h5) {
        this.h5 = h5;
    }

    public int getH7() {
        return h7;
    }

    public void setH7(int h7) {
        this.h7 = h7;
    }

    public int getH9() {
        return h9;
    }

    public void setH9(int h9) {
        this.h9 = h9;
    }


    public int getA1() {
        return a1;
    }

    public void setA1(int a1) {
        this.a1 = a1;
    }

    public int getA3() {
        return a3;
    }

    public void setA3(int a3) {
        this.a3 = a3;
    }

    public int getA5() {
        return a5;
    }

    public void setA5(int a5) {
        this.a5 = a5;
    }

    public int getA7() {
        return a7;
    }

    public void setA7(int a7) {
        this.a7 = a7;
    }

    public int getA9() {
        return a9;
    }

    public void setA9(int a9) {
        this.a9 = a9;
    }
    public int getP() {
        return p;
    }

    public void setP(int p) {
        this.p = p;
    }

    public int getNp() {
        return np;
    }

    public void setNp(int np) {
        this.np = np;
    }

    public int getRate() {
        return rate;
    }

    public void setRate(int rate) {
        this.rate = rate;
    }
}
