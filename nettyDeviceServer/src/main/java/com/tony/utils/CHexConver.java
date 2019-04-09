package com.tony.utils;


import java.io.ByteArrayOutputStream;
import java.io.UnsupportedEncodingException;
import java.util.Locale;



/**
 * 16进制值与String/Byte之间的转换
 * @author tiger
 * @data 2018-04-20
 * */
public class CHexConver
{
    private final static char[] mChars = "0123456789ABCDEF".toCharArray();
    private final static String mHexStr = "0123456789ABCDEF";
    /**
     * 检查16进制字符串是否有效
     * @param sHex String 16进制字符串
     * @return boolean
     */
    public static boolean checkHexStr(String sHex){
        String sTmp = sHex.toString().trim().replace(" ", "").toUpperCase(Locale.US);
        int iLen = sTmp.length();

        if (iLen > 1 && iLen%2 == 0){
            for(int i=0; i<iLen; i++)
                if (!mHexStr.contains(sTmp.substring(i, i+1)))
                    return false;
            return true;
        }
        else
            return false;
    }

    /**
     * 字符串转换成十六进制字符串
     * @param str String 待转换的ASCII字符串
     * @return String 每个Byte之间空格分隔，如: [61 6C 6B]
     */
    public static String str2HexStr(String str){
        StringBuilder sb = new StringBuilder();
        byte[] bs = str.getBytes();

        for (int i = 0; i < bs.length; i++){
            sb.append(mChars[(bs[i] & 0xFF) >> 4]);
            sb.append(mChars[bs[i] & 0x0F]);
            //sb.append(' ');
        }
        return sb.toString().trim();
    }

    /**
     * 十六进制字符串转换成 ASCII字符串
     * @return String 对应的字符串
     */
    public static String hexStr2Str(String hexStr){
        hexStr = hexStr.toString().trim().replace(" ", "").toUpperCase(Locale.US);
        char[] hexs = hexStr.toCharArray();
        byte[] bytes = new byte[hexStr.length() / 2];
        int iTmp = 0x00;;

        for (int i = 0; i < bytes.length; i++){
            iTmp = mHexStr.indexOf(hexs[2 * i]) << 4;
            iTmp |= mHexStr.indexOf(hexs[2 * i + 1]);
            bytes[i] = (byte) (iTmp & 0xFF);
        }
        return new String(bytes);
    }

    /**
     * bytes转换成十六进制字符串
     * @param b byte[] byte数组
     * @param iLen int 取前N位处理 N=iLen
     * @return String 每个Byte值之间空格分隔
     */
    public static String byte2HexStr(byte[] b, int iLen){
        StringBuilder sb = new StringBuilder();
        for (int n=0; n<iLen; n++){
            sb.append(mChars[(b[n] & 0xFF) >> 4]);
            sb.append(mChars[b[n] & 0x0F]);
            sb.append(' ');
        }
        return sb.toString().trim().toUpperCase(Locale.US);
    }

    /**
     * bytes字符串转换为Byte值
     * @param src String Byte字符串，每个Byte之间没有分隔符(字符范围:0-9 A-F)
     * @return byte[]
     */
    public static byte[] hexStr2Bytes(String src){
        /*对输入值进行规范化整理*/
        src = src.trim().replace(" ", "").toUpperCase(Locale.US);
        //处理值初始化
        int m=0,n=0;
        int iLen=src.length()/2; //计算长度
        byte[] ret = new byte[iLen]; //分配存储空间

        for (int i = 0; i < iLen; i++){
            m=i*2+1;
            n=m+1;
            ret[i] = (byte)(Integer.decode("0x"+ src.substring(i*2, m) + src.substring(m,n)) & 0xFF);
        }
        return ret;
    }

    public static byte[] hexStringToByte(String hex) {
        int len = (hex.length() / 2);
        byte[] result = new byte[len];
        char[] achar = hex.toCharArray();
        for (int i = 0; i < len; i++) {
            int pos = i * 2;
            result[i] = (byte) (toByte(achar[pos]) << 4 | toByte(achar[pos + 1]));
        }
        return result;
    }

    private static byte toByte(char c) {
        byte b = (byte) "0123456789abcdef".indexOf(c);
        return b;
    }

    public static void main(String args[]) throws UnsupportedEncodingException{

    	/*byte[] a={0x01,0x02,0x03};
    	byte[] b =  CHexConver.hexStr2BinArr("0531FF006B064D4B37321240".toUpperCase());
    	byte[] c= byteMerger(a,b);
    	System.out.println(byte2HexStr(c,c.length));*/
    	/*byte[] b = {0x01,0x02};
    	CrcUtils cu = new CrcUtils();
    	cu.update(b, b.length);
    	int n = cu.value;
    	System.out.println(Integer.toHexString(n)+"----"+n);
    	String str =Integer.toHexString(n);
    	byte[] bb = {0x01,0x02};
    	byte[] cc = byteMerger(bb, CHexConver.hexStr2BinArr(str.toUpperCase()));
    	System.out.println(byte2HexStr(cc,cc.length));
    	System.out.println(CrcUtils.checkBuf(cc));*/
        byte[] b = {0x5B,(byte)0xAC,0x00,0x01,0x56,0x01,0x54,0x01,0x56,0x01,0x52,0x01,0x36,0x01,0x1D,0x01,0x45,0x01,0x36,0x00,0x00,0x00,0x00,0x00,0x0F,0x00,0x00,(byte)0xCE,0x31,(byte)0xFF,(byte)0xFF,0x00,0x36,0x06,0x22,0x00,(byte)0xFF,0x00,(byte)0xFF,0x00,(byte)0xFF,0x00,(byte)0xFF,0x00,(byte)0xFF,0x00,(byte)0xFF,0x5B,(byte)0xAC,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x01,0x55,0x01,0x56,0x01,0x57,0x01,0x53,0x01,0x36,0x01,0x1D,0x01,0x45,0x01,0x36,0x00,0x00,0x00,0x00,0x00,0x0F,0x00,0x00};
        String str = CHexConver.byte2HexStr(b, b.length);

        //byte [] b = CHexConver.hexStr2BinArr("73c1ffff0036062200ff00ff00ff00ff00ff00ff5ab");
        System.out.println(str);
    }


    static String rev(String ox){
        String s = ox;
        byte b[] = s.getBytes();
        byte result[] = new byte[b.length];
        for (int i= b.length-1, j=0; i>=0;i=i-2,j=j+2){
            result[j]= b[i-1];
            result[j+1]= b[i];
        }
        return  new String(result);
    }

    private static String hexString="0123456789ABCDEF";
    public static String decode(String bytes)
    {
        ByteArrayOutputStream baos=new ByteArrayOutputStream(bytes.length()/2);
        //将每2位16进制整数组装成一个字节
        for(int i=0;i<bytes.length();i+=2)
            baos.write((hexString.indexOf(bytes.charAt(i))<<4 |hexString.indexOf(bytes.charAt(i+1))));
        return new String(baos.toByteArray());
    }

    public static String encode(String str)
    {
        //根据默认编码获取字节数组
        byte[] bytes=str.getBytes();
        StringBuilder sb=new StringBuilder(bytes.length*2);
        //将字节数组中每个字节拆解成2位16进制整数
        for(int i=0;i<bytes.length;i++)
        {
            sb.append(hexString.charAt((bytes[i]&0xf0)>>4));
            sb.append(hexString.charAt((bytes[i]&0x0f)>>0));
        }
        return sb.toString();
    }

    public static String toStringHex2(String s) {
        byte[] baKeyword = new byte[s.length() / 2];
        for (int i = 0; i < baKeyword.length; i++) {
            try {
                baKeyword[i] = (byte) (0xff & Integer.parseInt(s.substring(
                        i * 2, i * 2 + 2), 16));
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
        try {
            s = new String(baKeyword, "utf-8");// UTF-16le:Not
        } catch (Exception e1) {
            e1.printStackTrace();
        }
        return s;
    }

    public static  byte[] byteMerger(byte[] byte_1, byte[] byte_2){
        byte[] byte_3 = new byte[byte_1.length+byte_2.length];
        System.arraycopy(byte_1, 0, byte_3, 0, byte_1.length);
        System.arraycopy(byte_2, 0, byte_3, byte_1.length, byte_2.length);
        return byte_3;
    }

    public static byte[] appendTobyte(byte[] obj,int len){
        byte [] bu = new byte[len];
        for(int i=0;i<len;i++){
            bu[i] =0x00;
        }
        return  byteMerger(obj, bu);
    }

    /**
     * 10进制转16进制byte数组
     * @param len
     * @return
     */
    public static byte[] intToByte(int len){
        int num = len/255;
        int low = len%255;
        StringBuffer sbu = new StringBuffer();
        for(int i=0;i<num;i++){
            sbu.append("FF");
        }
        if(low!=0)
            sbu.append(String.format("%02x", low));

        return hexStringToByte(sbu.toString());
    }
    /**
     * 十进制转16进制数组  移位形式：530->212 ->0212
     * @param len
     * @return
     */
    public static byte[] intToByte2(int len){
        String st =String.format("%04x", len);
        return  hexStringToByte(st);
    }


    private static String hexStr =  "0123456789ABCDEF";
    private static String[] binaryArray =
            {"0000","0001","0010","0011",
                    "0100","0101","0110","0111",
                    "1000","1001","1010","1011",
                    "1100","1101","1110","1111"};

    /**
     *
     * @param str
     * @return 二进制数组转换为二进制字符串   2-2
     */
    public static String bytes2BinStr(byte[] bArray){

        String outStr = "";
        int pos = 0;
        for(byte b:bArray){
            //高四位
            pos = (b&0xF0)>>4;
            outStr+=binaryArray[pos];
            //低四位
            pos=b&0x0F;
            outStr+=binaryArray[pos];
        }
        return outStr;
    }

    /**
     *
     * @param bytes
     * @return 将二进制数组转换为十六进制字符串  2-16
     */
    public static String bin2HexStr(byte[] bytes){

        String result = "";
        String hex = "";
        for(int i=0;i<bytes.length;i++){
            //字节高4位
            hex = String.valueOf(hexStr.charAt((bytes[i]&0xF0)>>4));
            //字节低4位
            hex += String.valueOf(hexStr.charAt(bytes[i]&0x0F));
            result +=hex;  //+" "
        }
        return result;
    }

    /**
     *
     * @param hexString
     * @return 将十六进制转换为二进制字节数组   16-2
     */
    public static byte[] hexStr2BinArr(String hexString){
        //hexString的长度对2取整，作为bytes的长度
        int len = hexString.length()/2;
        byte[] bytes = new byte[len];
        byte high = 0;//字节高四位
        byte low = 0;//字节低四位
        for(int i=0;i<len;i++){
            //右移四位得到高位
            high = (byte)((hexStr.indexOf(hexString.charAt(2*i)))<<4);
            low = (byte)hexStr.indexOf(hexString.charAt(2*i+1));
            bytes[i] = (byte) (high|low);//高地位做或运算
        }
        return bytes;
    }

    /**
     *
     * @param hexString
     * @return 将十六进制转换为二进制字符串   16-2
     */
    public static String hexStr2BinStr(String hexString){
        return bytes2BinStr(hexStr2BinArr(hexString));
    }

    /**
     * String的字符串转换成unicode的String
     * @param strText String 全角字符串
     * @return String 每个unicode之间无分隔符
     * @throws Exception
     */
    public static String strToUnicode(String strText)
            throws Exception
    {
        char c;
        StringBuilder str = new StringBuilder();
        int intAsc;
        String strHex;
        for (int i = 0; i < strText.length(); i++){
            c = strText.charAt(i);
            intAsc = (int) c;
            strHex = Integer.toHexString(intAsc);
            if (intAsc > 128)
                str.append("\\u");
            else // 低位在前面补00
                str.append("\\u00");
            str.append(strHex);
        }
        return str.toString();
    }

    /**
     * unicode的String转换成String的字符串
     * @param hex String 16进制值字符串 （一个unicode为2byte）
     * @return String 全角字符串
     * @see CHexConver.unicodeToString("\\u0068\\u0065\\u006c\\u006c\\u006f")
     */
    public static String unicodeToString(String hex){
        int t = hex.length() / 6;
        int iTmp = 0;
        StringBuilder str = new StringBuilder();
        for (int i = 0; i < t; i++){
            String s = hex.substring(i * 6, (i + 1) * 6);
            // 将16进制的string转为int
            iTmp = (Integer.valueOf(s.substring(2, 4), 16) << 8) | Integer.valueOf(s.substring(4), 16);
            // 将int转换为字符
            str.append(new String(Character.toChars(iTmp)));
        }
        return str.toString();
    }

    public static String toStringHex(String s) {
        byte[] baKeyword = new byte[s.length() / 2];
        for (int i = 0; i < baKeyword.length; i++) {
            try {
                baKeyword[i] = (byte) (0xff & Integer.parseInt(s.substring(i * 2, i * 2 + 2), 16));
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
        try {
            s = new String(baKeyword, "utf-8");// UTF-16le:Not
        } catch (Exception e1) {
            e1.printStackTrace();
        }
        return s;
    }

    public static byte[] intToByte(int i,int len) {
        byte[] abyte=null;
        if(len==1){
            abyte = new byte[len];
            abyte[0] = (byte) (0xff & i);
        }else{
            abyte = new byte[len];
            abyte[0] = (byte) ((i >>> 24) & 0xff);
            abyte[1] = (byte) ((i >>> 16) & 0xff);
        }
        return abyte;
    }

    public  static int bytesToInt(byte[] bytes) {
        int addr=0;
        if(bytes.length==1){
            addr = bytes[0] & 0xFF;
        }else{
            addr = bytes[0] & 0xFF;
            addr = (addr << 8) | (bytes[1] & 0xff) ;
        }
        return addr;
    }

}