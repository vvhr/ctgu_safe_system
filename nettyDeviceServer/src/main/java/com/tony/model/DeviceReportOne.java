package com.tony.model;

import org.bson.Document;
import java.util.ArrayList;

/**
 * 该对象对应老协议实时上报数据，对应device_report_one数据表
 */
public class DeviceReportOne {
    private String uuid;
    private String imei;
    private int channel;
    private String eHexL;
    private String eHexH;
    private String aSignHex;
    private int v;
    private int c;
    private int lc;
    private int t;
    private int eType;
    private int eDetailType;
    private String eComment;
    private String reportTime;
    private int enable;

    public static ArrayList<Document> convertListOfModelToDoc(ArrayList<DeviceReportOne> modelList){
        ArrayList<Document> docList = new ArrayList<>();
        for (DeviceReportOne model:modelList) {
            Document doc = new Document();
            doc.put("uuid", model.uuid);
            doc.put("imei", model.imei);
            doc.put("channel", model.channel);
            doc.put("v", model.v);
            doc.put("c", model.c);
            doc.put("lc", model.lc);
            doc.put("t", model.t);
            doc.put("eType", model.eType);
            doc.put("eDetailType", model.eDetailType);
            doc.put("eComment", model.eComment);
            doc.put("eHexL", model.eHexL);
            doc.put("eHexH", model.eHexH);
            doc.put("aSignHex", model.aSignHex);
            doc.put("reportTime", model.reportTime);
            docList.add(doc);
        }
        return docList;
    }
    /*---------------------------------------*get or set 方法集合*-----------------------------------------------------------*/
    
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

    public String geteHexL() {
        return eHexL;
    }

    public void seteHexL(String eHexL) {
        this.eHexL = eHexL;
    }

    public String geteHexH() {
        return eHexH;
    }

    public void seteHexH(String eHexH) {
        this.eHexH = eHexH;
    }

    public String getaSignHex() {
        return aSignHex;
    }

    public void setaSignHex(String aSignHex) {
        this.aSignHex = aSignHex;
    }

    public int getV() {
        return v;
    }

    public void setV(int v) {
        this.v = v;
    }

    public int getC() {
        return c;
    }

    public void setC(int c) {
        this.c = c;
    }

    public int getLc() {
        return lc;
    }

    public void setLc(int lc) {
        this.lc = lc;
    }

    public int getT() {
        return t;
    }

    public void setT(int t) {
        this.t = t;
    }

    public int geteType() {
        return eType;
    }

    public void seteType(int eType) {
        this.eType = eType;
    }

    public int geteDetailType() {
        return eDetailType;
    }

    public void seteDetailType(int eDetailType) {
        this.eDetailType = eDetailType;
    }

    public String geteComment() {
        return eComment;
    }

    public void seteComment(String eComment) {
        this.eComment = eComment;
    }

    public String getReportTime() {
        return reportTime;
    }

    public void setReportTime(String reportTime) {
        this.reportTime = reportTime;
    }

    public int getEnable() {
        return enable;
    }

    public void setEnable(int enable) {
        this.enable = enable;
    }
    }
