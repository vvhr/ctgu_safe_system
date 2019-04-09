package com.tony.msg;

import com.tony.tools.LoggerUtils;
import org.apache.commons.codec.binary.Hex;

public class MessageBase {

    public String head;

    public int messageType;

    public int length;

    public String deviceType;

    public String deviceId;

    public String data;
    public String dataSmallModel;

    public byte[] checksum;

    private boolean isPass=false;


    public boolean isPass() {
        return isPass;
    }

    public static MessageBase read(String rawDataHex){
        int rawHexLength = rawDataHex.length();

        // bytes的长度必须在20个元素以上
        if (rawHexLength<=40) {
            return null;
        }
        try {
            MessageBase msg = new MessageBase();

            msg.head = rawDataHex.substring(0,4);
            msg.length = Integer.valueOf(AnalysisUtils.HexS2B(rawDataHex.substring(4,8)),16);
            msg.messageType = Integer.valueOf(rawDataHex.substring(8,10),16);
            msg.deviceType = rawDataHex.substring(10,12);
            msg.deviceId = rawDataHex.substring(12,36);
            String baseDataRaw = rawDataHex.substring(36,rawHexLength-4);
            String baseData = AnalysisUtils.decodeFFFF(baseDataRaw);
            msg.dataSmallModel = baseData;
            msg.data = AnalysisUtils.HexS2B(baseData);
            msg.checksum = Hex.decodeHex(rawDataHex.substring(rawHexLength-4));

            byte[] checkSumBaseData = Hex.decodeHex(rawDataHex.substring(8,rawHexLength-4));

            //开始校验
            // System.out.println("-->校验成功");
            msg.isPass = AnalysisUtils.checksum(checkSumBaseData, msg.checksum);

            return msg;
        } catch (Exception e) {
            LoggerUtils.error(" MessageBase read exception: "+e.getMessage());
            return null;
        }
    }
}
