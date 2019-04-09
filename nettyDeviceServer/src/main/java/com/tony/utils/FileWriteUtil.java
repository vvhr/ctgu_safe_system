package com.tony.utils;

import java.io.*;

public class FileWriteUtil {

	/**
	 * 按字符缓冲写入 BufferedWriter  处理换行
	 * 
	 * @param path 写入文件路径
	 * @param content 写入字符串
	 */
	public void bufferedWrite(String content,String path) {
	    File f = new File(path);
	    OutputStreamWriter writer = null;
	    BufferedWriter bw = null;
	    try {
	    	if(!f.getParentFile().exists()){
	    		f.getParentFile().mkdirs();
	    	}
	    	
	    	if(!f.exists()){
	    		f.createNewFile();
	    	}
	        OutputStream os = new FileOutputStream(f,true);
	        writer = new OutputStreamWriter(os);
	        bw = new BufferedWriter(writer);
	        bw.write(content);
            bw.newLine();
	        bw.flush();
	    } catch (FileNotFoundException e) {
	        e.printStackTrace();
	    } catch (IOException e) {
	        e.printStackTrace();
	    } finally {
	        try {
	        	if(bw!=null)
	        		bw.close();
	        } catch (IOException e) {
	            e.printStackTrace();
	        }
	    }
	}
	
	/**
	 * 按字符缓冲写入 BufferedWriter 不处理换行
	 * 
	 * @param path 写入文件路径
	 * @param content 写入字符串
	 */
	public void bufferedWriteNotRn(String content,String path) {
	    File f = new File(path);
	    OutputStreamWriter writer = null;
	    BufferedWriter bw = null;
	    try {
	    	if(f.exists()){
	    		f.getAbsoluteFile().mkdirs();
	    		f.createNewFile();
	    	}
	        OutputStream os = new FileOutputStream(f,true);
	        writer = new OutputStreamWriter(os);
	        bw = new BufferedWriter(writer);
	        bw.write(content);
	        bw.flush();
	    } catch (FileNotFoundException e) {
	        e.printStackTrace();
	    } catch (IOException e) {
	        e.printStackTrace();
	    } finally {
	        try {
	        	if(bw!=null)
	        		bw.close();
	        } catch (IOException e) {
	            e.printStackTrace();
	        }
	    }
	}
	
	/*public static void main(String args[]){
		String str = "insert into (imei,volteger,e,t,h,t,ttttt,hhhhh,vvvvv,llll,ccccc) values('2345352342',220,33,44,53,333,2222,43444,2322,4444,"+System.currentTimeMillis()+");";
		FileWriteUtil fwu = new FileWriteUtil();
		long st = System.currentTimeMillis();
		int line=0;
		StringBuffer sub = new StringBuffer();
		for(int i=0;i<2000000;i++){
			sub.append(str);
			line++;
			if(line==5000){
				fwu.bufferedWrite(sub.toString(),"D:/data/report/reportHarmonic/666666.sql");
				line=0;
			}
		}
		
		System.out.println("用时："+(System.currentTimeMillis()-st));
	}*/
}
