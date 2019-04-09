package com.tony.test;

import com.tony.msg.AnalysisUtils;
import com.tony.msg.CRC_16;
import com.tony.tools.FormatTool;
import com.tony.utils.DebugUtil;
import org.apache.commons.codec.DecoderException;
import org.apache.commons.codec.binary.Hex;

public class Crc16Test {
    public static void main(String[] arg) throws DecoderException {
        String hexStr = "6706" + "0111b235810313329cf2cb2c";
        DebugUtil.println("hexStr:" + hexStr);
        int crcInt = CRC_16.alex_crc16(Hex.decodeHex(hexStr), Hex.decodeHex(hexStr).length);
        DebugUtil.println("crcInt:" + crcInt);
        byte[] crcBin = AnalysisUtils.intToByte(crcInt);
        DebugUtil.println(FormatTool.bytes2String(crcBin));
        String crcHex = Hex.encodeHexString(crcBin);
        DebugUtil.println("crcHex:" + crcHex);
        String dataHex = "ffff0014" + hexStr + crcHex.toLowerCase();
        DebugUtil.println("dataHex:" + dataHex);
    }
}
