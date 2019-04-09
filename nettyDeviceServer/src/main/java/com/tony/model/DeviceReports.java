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
 * 该对象不对应任何数据表，是新老协议的一个转换工具类，不建议使用
 */
public class DeviceReports {
    public ArrayList<DeviceReportOne> reportList = new ArrayList<>();
    public ArrayList<HarmonicReportOne> harmonicList = new ArrayList<>();

    /**
     * 将基础数据解析到模列表，以备Mysql存表
     * @param mb 基础数据
     * @return model列表
     */
    public static DeviceReports parseMbToDeviceReports(MessageBase mb){
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
        DeviceReports deviceReports = new DeviceReports();

        String now = DataFormatUtils.format(System.currentTimeMillis());
        for(int i=0;i<channelCount;i++){
            // 初始化实时上报模型列表
            DeviceReportOne modelReport = new DeviceReportOne();
            modelReport.setUuid(mb.deviceId + "_" + (i+1));
            modelReport.setImei(mb.deviceId);
            modelReport.setChannel(i+1);
            modelReport.seteHexL(eHexL_);
            modelReport.seteHexH(eHexH_);
            modelReport.setaSignHex(aSignHex_);
            modelReport.setEnable(1);
            modelReport.setReportTime(now);
            deviceReports.reportList.add(modelReport);
            // 初始化谐波上报模型列表
            HarmonicReportOne modelHarmonic = new HarmonicReportOne();
            modelHarmonic.setUuid(mb.deviceId + "_" + (i+1));
            modelHarmonic.setImei(mb.deviceId);
            modelHarmonic.setChannel(i+1);
            modelHarmonic.setReportTime(now);
            deviceReports.harmonicList.add(modelHarmonic);
        }

        // 读正常电量参数值
        for(int i=0;i<=electricityData.length()-4;i+=4){
            value = Integer.valueOf(electricityData.substring(i,i+4),16);
            dataOrder = i/4;
            dataFiledIndex = dataOrder%17;
            channelIndex = dataOrder/17;
            DeviceReportOne modelReport = deviceReports.reportList.get(channelIndex);
            HarmonicReportOne modelHarmonic = deviceReports.harmonicList.get(channelIndex);
            switch (dataFiledIndex){
                case 0:
                    modelReport.setV(value);
                    break;
                case 1:
                    modelReport.setC((value));
                    break;
                case 2:
                    modelReport.setLc(value);
                    break;
                case 3:
                    modelReport.setT(value);
                    break;
                case 4:
                    modelHarmonic.setH1(value);
                    break;
                case 5:
                    modelHarmonic.setA1(value);
                    break;
                case 6:
                    modelHarmonic.setH3(value);
                    break;
                case 7:
                    modelHarmonic.setA3(value);
                    break;
                case 8:
                    modelHarmonic.setH5(value);
                    break;
                case 9:
                    modelHarmonic.setA5(value);
                    break;
                case 10:
                    modelHarmonic.setH7(value);
                    break;
                case 11:
                    modelHarmonic.setA7(value);
                    break;
                case 12:
                    modelHarmonic.setH9(value);
                    break;
                case 13:
                    modelHarmonic.setA9(value);
                    break;
                case 14:
                    modelHarmonic.setP(value);
                    break;
                case 15:
                    modelHarmonic.setNp(value);
                    break;
                case 16:
                    modelHarmonic.setRate(value);
                    break;
            }
        }

        // 读异常标志参数
        try {
            parseExceptionToModelList(eHexL_,aSignHex_,deviceReports.reportList);
        } catch (DecoderException e) {
            e.printStackTrace();
        }
        return deviceReports;
    }

    private static void parseExceptionToModelList(String markMalfunctionsL, String warningSigns, ArrayList<DeviceReportOne> modelList) throws DecoderException {
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
                DeviceReportOne model = modelList.get(exceptionChannel - 1);
                model.seteType(Integer.valueOf(exceptionInfo[1]));
                model.seteDetailType(Integer.valueOf(exceptionInfo[2]));
                model.seteComment(exceptionInfo[3]);
                // DebugUtil.println(">>>>>>>>>>>异常>>>>>>");
                // DebugUtil.println("-->异常通道:" + Integer.valueOf(exceptionInfo[0]));
                // DebugUtil.println("-->异常类型:" + Integer.valueOf(exceptionInfo[1]));
                // DebugUtil.println("-->异常具体类型:" + Integer.valueOf(exceptionInfo[2]));
                // DebugUtil.println("-->异常描述:" + exceptionInfo[3]);
            }
        }
    }

}
