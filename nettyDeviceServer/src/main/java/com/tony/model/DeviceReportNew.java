package com.tony.model;

import com.tony.msg.MessageBase;
import com.tony.proto.Proto;
import com.tony.utils.CHexConver;
import com.tony.utils.DataFormatUtils;
import com.tony.utils.DebugUtil;
import org.apache.commons.codec.DecoderException;
import org.apache.commons.codec.binary.Hex;
import org.bson.Document;
import java.util.ArrayList;

/**
 * 此对象对应新协议实时上报记录，对应device_report_new数据表
 */
public class DeviceReportNew {
    private String uuid;
    private String imei;
    private int channel;
    private int v;
    private int c;
    private int lc;
    private int t;
    private int h1;
    private int h3;
    private int h5;
    private int h7;
    private int h9;
    private int a1;
    private int a3;
    private int a5;
    private int a7;
    private int a9;
    private int p;
    private int np;
    private int rate;
    private int eType;
    private int eDetailType;
    private String eComment;
    private String eHexL;
    private String eHexH;
    private String aSignHex;
    private int enable;
    private String reportTime;
    private int diffLc;
    private int diffT;
    private int diffP;
    private int diffNp;

/*--------------------------------------------业务逻辑--------------------------------------------*/
    /**
     * 将基础数据解析到模列表，以备Mysql存表.如查使用DeviceReportNew，则使用此方法解析数据
     * @param mb 基础数据
     * @return model列表
     */
    public static ArrayList<DeviceReportNew> parseMbToModelList(MessageBase mb){
        String data = mb.data;
        String channelSettingHex = data.substring(0,4);

        String eHexL_ = data.substring(12,16);
        String eHexH_ = data.substring(16,20);
        String aSignHex_ = data.substring(20,24);
        String electricityData = data.substring(24);
        // 一个通道17个值，32个字节，64个字母
        // 通道个数
        int channelCount = electricityData.length()/64;
        // channelIndex：第几个通道
        int channelIndex = 0;
        // dataFiledIndex：通道中第几个值（哪个字段）
        int dataFiledIndex = 0;
        // dataOrder：第几个值
        int dataOrder = 0;
        int value;

        // 初始化通道列表
        ArrayList<DeviceReportNew> modelList = new ArrayList<>();
        for(int i=0;i<channelCount;i++){
            DeviceReportNew model = new DeviceReportNew();
            model.uuid = mb.deviceId + "_" + (i+1);
            model.imei = mb.deviceId;
            model.channel = i+1;
            model.eHexL = eHexL_;
            model.eHexH = eHexH_;
            model.aSignHex = aSignHex_;
            model.enable = 1;
            model.reportTime = DataFormatUtils.format(System.currentTimeMillis());
            modelList.add(model);
        }

        // 读正常电量参数值
        for(int i=0;i<=electricityData.length()-4;i+=4){
            // System.out.println(electricityData.substring(i,i+4));
            String valueHex = electricityData.substring(i,i+4);
            dataOrder = i/4;
            dataFiledIndex = dataOrder%17;
            channelIndex = dataOrder/17;
            DeviceReportNew model = modelList.get(channelIndex);
            switch (dataFiledIndex){
                case 0:
                    model.v=Integer.valueOf(valueHex,16);
                    break;
                case 1:
                    model.c=Integer.valueOf(valueHex,16);
                    break;
                case 2:
                    model.lc=Integer.valueOf(valueHex,16);
                    break;
                case 3:
                    model.t=Integer.valueOf(valueHex,16);
                    break;
                case 4:
                    model.h1=Integer.valueOf(valueHex,16);
                    break;
                case 5:
                    model.a1=(short)(Integer.parseInt(valueHex,16));
                    break;
                case 6:
                    model.h3=Integer.valueOf(valueHex,16);
                    break;
                case 7:
                    model.a3=(short)(Integer.parseInt(valueHex,16));
                    break;
                case 8:
                    model.h5=Integer.valueOf(valueHex,16);
                    break;
                case 9:
                    model.a5=(short)(Integer.parseInt(valueHex,16));
                    break;
                case 10:
                    model.h7=Integer.valueOf(valueHex,16);
                    break;
                case 11:
                    model.a7=(short)(Integer.parseInt(valueHex,16));
                    break;
                case 12:
                    model.h9=(short)(Integer.parseInt(valueHex,16));
                    break;
                case 13:
                    model.a9=Integer.valueOf(valueHex,16);
                    break;
                case 14:
                    model.p=(short)(Integer.parseInt(valueHex,16));
                    break;
                case 15:
                    model.np=(short)(Integer.parseInt(valueHex,16));
                    break;
                case 16:
                    model.rate=Integer.valueOf(valueHex,16);
                    break;
            }
        }

        // 读异常标志参数
        try {
            parseExceptionToModelList(eHexL_,aSignHex_,modelList);
        } catch (DecoderException e) {
            e.printStackTrace();
        }
        return modelList;
    }
    private static void parseExceptionToModelList(String markMalfunctionsL, String warningSigns, ArrayList<DeviceReportNew> modelList) throws DecoderException {
        // DebugUtil.println("-->开始解析异常标志：");
        char[] chars = new char[0];
        String[][] proto = new String[0][];
        String exceptionString = "无异常";

        if(!"0000".equals(markMalfunctionsL)){ // 如果是故障,看故障通道与类型
            exceptionString = CHexConver.bytes2BinStr(Hex.decodeHex(markMalfunctionsL));
            chars = exceptionString.toCharArray();
            proto = Proto.FAULTL;

        }else if(!"0000".equals(warningSigns)){ // 如果是警报，看警报通道与类型
            exceptionString = CHexConver.bytes2BinStr(Hex.decodeHex(warningSigns));
            chars = exceptionString.toCharArray();
            proto = Proto.ALARM;

        }

        for(int i=0;i<chars.length;i++){
            // 第几个值为1
            if(chars[i]=='1'){
                String[] exceptionInfo = proto[i];
                int exceptionChannel = Integer.valueOf(exceptionInfo[0]);
                DeviceReportNew model = modelList.get(exceptionChannel - 1);
                model.seteType(Integer.valueOf(exceptionInfo[1]));
                model.seteDetailType(Integer.valueOf(exceptionInfo[2]));
                model.seteComment(exceptionInfo[3]);
                DebugUtil.println(">>>>>>>>>>>异常>>>>>>");
                DebugUtil.println("-->异常通道:" + Integer.valueOf(exceptionInfo[0]));
                DebugUtil.println("-->异常类型:" + Integer.valueOf(exceptionInfo[1]));
                DebugUtil.println("-->异常具体类型:" + Integer.valueOf(exceptionInfo[2]));
                DebugUtil.println("-->异常描述:" + exceptionInfo[3]);
            }
        }
    }
    public static ArrayList<Document> convertListOfModelToDoc(ArrayList<DeviceReportNew> modelList){
        ArrayList<Document> docList = new ArrayList<>();
        for (DeviceReportNew model:modelList) {
            Document doc = new Document();
            doc.put("uuid", model.uuid);
            doc.put("imei", model.imei);
            doc.put("channel", model.channel);
            doc.put("v", (double)model.v/100);
            doc.put("c", (double)model.c/100);
            doc.put("lc", (double)model.lc);
            doc.put("t", (double)model.t/10);
            doc.put("h1", (double)model.h1/100);
            doc.put("a1", (double)model.a1/1000);
            doc.put("h3", (double)model.h3/100);
            doc.put("a3", (double)model.a3/1000);
            doc.put("h5", (double)model.h5/100);
            doc.put("a5", (double)model.a5/1000);
            doc.put("h7", (double)model.h7/100);
            doc.put("a7", (double)model.a7/1000);
            doc.put("h9", (double)model.h9/100);
            doc.put("a9", (double)model.a9/1000);
            doc.put("p", model.p);
            doc.put("np", model.np);
            doc.put("rate", (double)model.rate/1000);
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

    public static Document convertModelToDoc(DeviceReportNew model){
        Document doc = new Document();
        doc.put("uuid", model.uuid);
        doc.put("imei", model.imei);
        doc.put("channel", model.channel);
        doc.put("v", (double)model.v/100);
        doc.put("c", (double)model.c/100);
        doc.put("lc", (double)model.lc);
        doc.put("t", (double)model.t/10);
        doc.put("h1", (double)model.h1/100);
        doc.put("a1", (double)model.a1/1000);
        doc.put("h3", (double)model.h3/100);
        doc.put("a3", (double)model.a3/1000);
        doc.put("h5", (double)model.h5/100);
        doc.put("a5", (double)model.a5/1000);
        doc.put("h7", (double)model.h7/100);
        doc.put("a7", (double)model.a7/1000);
        doc.put("h9", (double)model.h9/100);
        doc.put("a9", (double)model.a9/1000);
        doc.put("p", model.p);
        doc.put("np", model.np);
        doc.put("rate", (double)model.rate/1000);
        doc.put("eType", model.eType);
        doc.put("eDetailType", model.eDetailType);
        doc.put("eComment", model.eComment);
        doc.put("eHexL", model.eHexL);
        doc.put("eHexH", model.eHexH);
        doc.put("aSignHex", model.aSignHex);
        doc.put("reportTime", model.reportTime);
        return doc;
    }
/*--------------------------------------------get/set方法--------------------------------------------*/
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

    public int getEnable() {
        return enable;
    }

    public void setEnable(int enable) {
        this.enable = enable;
    }

    public String getReportTime() {
        return reportTime;
    }

    public void setReportTime(String reportTime) {
        this.reportTime = reportTime;
    }
    public int getDiffLc() { return diffLc; }

    public void setDiffLc(int diffLc) { this.diffLc = diffLc; }
    public int getDiffT() {
        return diffT;
    }

    public void setDiffT(int diffT) {
        this.diffT = diffT;
    }

    public int getDiffP() {
        return diffP;
    }

    public void setDiffP(int diffP) {
        this.diffP = diffP;
    }

    public int getDiffNp() {
        return diffNp;
    }

    public void setDiffNp(int diffNp) {
        this.diffNp = diffNp;
    }
}
