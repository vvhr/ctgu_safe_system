package com.tony.msg;

import org.apache.commons.codec.binary.Hex;


public class AnalysisUtils {

    public static boolean checksum(byte[] b, byte[] checksum) {
        // 获取crc校验int值
        int crc = CRC_16.alex_crc16(b, b.length);
        int crc2 = byteToInt(checksum);
        return crc == crc2 ? true : false;
    }

    public static byte[] hexStringToByte(String src) {
        int m = 0, n = 0;
        int l = src.length() / 2;
        System.out.println(l);
        byte[] ret = new byte[l];
        for (int i = 0; i < l; i++) {
            m = i * 2 + 1;
            n = m + 1;
            //16进制转10进制，并且无符号byte 转有符号byte
            int temp = Integer.decode("0x" + src.substring(i * 2, m) + src.substring(m, n));
            if(temp>127)ret[i]=(byte)(temp-256);else ret[i]=(byte)temp;
        }
        return ret;
    }

    public static String bytes2HexString(byte[] b) {
        String ret = "";
        for (int i = 0; i < b.length; i++) {
            String hex = Integer.toHexString(b[ i ] & 0xFF);
            if (hex.length() == 1) {
                //hex =  hex;
            }
            ret += hex.toUpperCase();
        }
        return ret;
    }


    public static byte[] unsigned_short_2byte(int length){
        byte[] targets = new byte[2];
        for (int i = 0; i < targets.length; i++) {
            int offset = (targets.length-1-i)*8;
            targets[i] = (byte)((length >>> offset) & 0xff);
        }
        return targets;
    }

    public static short byte2Short(byte[] b) {
        b = reverse(b);
        short s = 0;
        short s0 = (short) (b[0] & 0xff);// 最低位
        short s1 = (short) (b[1] & 0xff);
        s1 <<= 8;
        s = (short) (s0 | s1);
        return s;
    }


    public static byte[] reverse(byte[] b){
        int len = b.length;
        byte[] r = new byte[len];
        for (int i = 0; i < r.length; i++) {
            r[i] = b[len-1-i];
        }
        return r;
    }

    private static int toInt(byte[] bytes) {
        int ret = ((bytes[0] & 0xff) << 8) | (bytes[1] & 0xff);
        return ret;
    }

    private static int toRInt(byte[] bytes) {
        int ret = ((bytes[1] & 0xff) << 8) | (bytes[0] & 0xff);
        return ret;
    }

    /**
     * java short (1 short == 2 byte == 16 bit) (-2^15~2^15-1 : -32768~32767) to unsigned short(0~2^16-1 : 0~65535)
     *
     * @param data
     * @return
     */
    public static int getUnsignedShort (short data){
        return data & 0x0FFFF;
    }

    public static short getShort (int data){
        return (short)(data & 0x7fff);
    }

    private static void show(int num){
        System.out.println("=============转化演示=============");
        System.out.println("原来:"+num);
        System.out.println("二进制:"+Integer.toBinaryString(num));

        System.out.println("强转short:"+(short)(num));
        if(num>65535){
            System.out.println("该数超过了16位，强转只会保留后16位");
        }
        System.out.println("二进制:"+Integer.toBinaryString((short)(num)));
        System.out.println("强转short后与原int数是否相等:"+((short)(num)==num));
        System.out.println("还原:"+((short)(num) & 0xFFFF));

        System.out.println("强转char:"+(char)(num));



    }

    /**
     * 低转高（小端转大端）
     * @param bb
     * @return
     */
    public static String byteToStr_LToH(byte [] bb){
        StringBuffer sub = new StringBuffer(bb.length);
        for(int i=0;i<bb.length;i=i+2){
            if(i+1<bb.length){
                byte[] w = new byte[2];
                w[0] = bb[i];
                w[1]=bb[i+1];
                int n = byteToInt(w);
                sub.append(Hex.encodeHexString(intToByte2(n)));
            }
        }
        return sub.toString();
    }

    /**
     * 将高低位形式16进制字节数组转成低高位(大端模式转小端)
     * @param bb
     * @return
     */
    public static String byteToStr_HToL(byte [] bb){
        StringBuffer sub = new StringBuffer(bb.length);
        for(int i=0;i<bb.length;i=i+2){
            if(i+1<bb.length){
                byte[] w = new byte[2];
                w[0] = bb[i];
                w[1]=bb[i+1];
                int n = byteToInt(w);
                byte[] bt = new byte[2];
                bt[0] = (byte) ((0xff00 & n) >> 8);
                bt[1] = (byte) (0xff & n);
                sub.append(Hex.encodeHexString(bt));
            }
        }
        return sub.toString();
    }



    public static String decToHex(int dec) {
        String hex = "";
        while(dec != 0) {
            String h = Integer.toString(dec & 0xff, 16);
            if((h.length() & 0x01) == 1)
                h = '0' + h;
            hex = hex + h;
            dec = dec >> 8;
        }
        return hex;
    }

    /**
     * hex 二进制转int
     * 高位在前，低位在后
     * @param src
     * @return
     */
    public static int byteToInt(byte[] src) {
        assert src.length == 2;
        return (int) ((src[0] & 0xFF) | ((src[1] & 0xFF) << 8));
    }

    /**
     * 高位在前，低位在后
     * @param src
     * @return
     */
    public static int byteToInt2(byte[] src) {
        assert src.length == 2;
        return (int) (((src[1] & 0xff) << 8) | (src[0] & 0xFF));
    }

    /**
     * 转义成1be71111
     *
     * @param in
     * @return
     */
    public static String encodeFFFF(byte[] in) {
        String code = Hex.encodeHexString(in).replaceAll("ffff", "e71b1111");
        return code = code.replaceAll("e71b1111e71b1111", "e71b22221111");
    }

    /**
     * 转义成FFFF
     *
     * @param in
     * @return
     */
    public static String decodeFFFF(byte[] in) {
        String code = Hex.encodeHexString(in).replaceAll("e71b22221111", "e71b1111e71b1111");
        return code = code.replaceAll("e71b1111", "ffff");
    }

    public static String decodeFFFF(String dataHex) {
        String code = dataHex.replaceAll("e71b22221111", "e71b1111e71b1111");
        return code.replaceAll("e71b1111", "ffff");
    }

    /**
     * int转byte[]
     * 低位在前，高位在后
     * @param i
     * @return
     */
    public static byte[] intToByte(int i) {
        byte[] bt = new byte[2];
        bt[0] = (byte) (0xff & i);
        bt[1] = (byte) ((0xff00 & i) >> 8);
        return bt;
    }
    /**
     * 高位在前，低位在后
     * @param i
     * @return
     */
    public static byte[] intToByte2(int i) {
        byte[] bt = new byte[2];
        bt[0] = (byte) ((0xff00 & i) >> 8);
        bt[1] = (byte) (0xff & i);
        return bt;
    }

    /**
     * int转换为小端byte[]（高位放在高地址中） 低位在前，高位在后
     * @param iValue
     * @return
     */
    public static byte[] Int2Bytes_LE(long iValue){
        byte[] rst = new byte[4];
        // 先写int的最后一个字节
        rst[0] = (byte)(iValue & 0xFF);
        // int 倒数第二个字节
        rst[1] = (byte)((iValue & 0xFF00) >> 8 );
        // int 倒数第三个字节
        rst[2] = (byte)((iValue & 0xFF0000) >> 16 );
        // int 第一个字节
        rst[3] = (byte)((iValue & 0xFF000000) >> 24 );
        return rst;
    }

    /**
     * int 转为大端模式，高位在前，低位在后
     * @param iValue
     * @return
     */
    public static byte[] Int2Bytes_HE(long iValue){
        byte[] rst = new byte[4];
        // 先写int的最后一个字节
        rst[3] = (byte)(iValue & 0xFF);
        // int 倒数第二个字节
        rst[2] = (byte)((iValue & 0xFF00) >> 8 );
        // int 倒数第三个字节
        rst[1] = (byte)((iValue & 0xFF0000) >> 16 );
        // int 第一个字节
        rst[0] = (byte)((iValue & 0xFF000000) >> 24 );
        return rst;
    }


    /**
     * 将16进制字符串转成字符串
     * @param hexStr
     * @return
     */
    public static String hexStr2Str(String hexStr) {

        String str = "0123456789ABCDEF";
        char[] hexs = hexStr.toCharArray();
        byte[] bytes = new byte[hexStr.length() / 2];
        int n;
        for (int i = 0; i < bytes.length; i++) {
            n = str.indexOf(hexs[2 * i]) * 16;
            n += str.indexOf(hexs[2 * i + 1]);
            bytes[i] = (byte) (n & 0xff);
        }
        return new String(bytes);
    }
    //转成小端int
    public static long bytes2Int_LE(byte[] bytes){
        if(bytes.length < 4)
            return -1;
        long iRst = (bytes[0] & 0xFF);
        iRst |= (bytes[1] & 0xFF) << 8;
        iRst |= (bytes[2] & 0xFF) << 16;
        iRst |= (bytes[3] & 0xFF)<< 24;
        return iRst;
    }

    /**
     * 转换byte数组为int（大端）
     * @return
     * @note 数组长度至少为4，按小端方式转换，即传入的bytes是大端的，按这个规律组织成int
     */
    public static long bytes2Int_BE(byte[] bytes){
        if(bytes.length < 4)
            return -1;
        long iRst = (bytes[0] << 24) & 0xFF;
        iRst |= (bytes[1] << 16) & 0xFF;
        iRst |= (bytes[2] << 8) & 0xFF;
        iRst |= bytes[3] & 0xFF;
        return iRst;
    }

    // 16进制字串小端到大端转整数值最终转为ASCII字符串
    // 前提条件：每个整数值必须是16位。
    // 原理：每个16bit的值分成2个8bit字节，4个0x16进制字母。
    // 两个字节调换顺序，等同于四个字母的后两个字母与前两个字母调换顺序即可
    public static String HexSmall2ASCII(String dataHex){
        //大小端转换
        StringBuilder stringBuffer = new StringBuilder();
        for(int i=0;i<=dataHex.length()-4;i+=4){
            String tempString = dataHex.substring(i,i+4);

            String tempAsc2Hex = tempString.substring(2);
            int tempInt = Integer.valueOf(tempAsc2Hex,16);
            char tempChar = (char)tempInt;
            stringBuffer.append(tempChar);

            tempAsc2Hex = tempString.substring(0,2);
            tempInt = Integer.valueOf(tempAsc2Hex,16);
            tempChar = (char)tempInt;
            stringBuffer.append(tempChar);
        }
        return stringBuffer.toString();
    }

    // 小端转大端
    // 在我们的设备中，只要是16bit，都是以8bit为一个单位整体。大小端转换只需要将后8bit与前8bit调换即可
    public static String HexS2B(String dataHex){
        //大小端转换
        StringBuilder stringBuffer = new StringBuilder();
        for(int i=0;i<=dataHex.length()-4;i+=4){
            String tempString = dataHex.substring(i,i+4);

            String tempAsc2Hex = tempString.substring(2);
            stringBuffer.append(tempAsc2Hex);

            tempAsc2Hex = tempString.substring(0,2);
            stringBuffer.append(tempAsc2Hex);
        }
        return stringBuffer.toString();
    }
}
