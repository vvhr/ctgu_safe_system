package com.tony.nioTest;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.nio.ByteBuffer;
import java.nio.channels.FileChannel;

public class Test {
    private final static String fileInPath = "C:/Users/tony/javaDir/tempdir/testTextIn.txt";
    private final static String fileOutPath = "C:/Users/tony/javaDir/tempdir/testTextOut.txt";

    public static void main( String[] args ) throws IOException {
        System.out.println("你好，世界");
//        testFileInputChannel();
//        testFileOutputChannel();
        copyFile();
    }

    // 缓冲，程序称为内部。磁盘，显示器，网络称为外部
    // 单独的读取文件
    private static void testFileInputChannel() throws IOException {
        // 创建输入流
        FileInputStream finStream = new FileInputStream(fileInPath);
        // 输入流转为通道
        FileChannel fc = finStream.getChannel();
        // 分配缓冲区为1K字节
        ByteBuffer buffer = ByteBuffer.allocate( 1024 );
        // 将通道数读入缓冲区
        fc.read(buffer);
        System.out.println(buffer.get(0));
    }
    // 单独的写入文件
    private static void testFileOutputChannel() throws IOException {
        // 创建输入流
        FileOutputStream foutStream = new FileOutputStream(fileOutPath);
        // 输入流转为通道
        FileChannel fc = foutStream.getChannel();
        // 分配缓冲区为1K字节
        ByteBuffer buffer = ByteBuffer.allocate( 1024 );
        // 将写缓冲区
        buffer.put((byte) 90);
        buffer.flip();
        fc.write(buffer);
    }

    private static void copyFile() throws IOException {
        FileInputStream finStream = new FileInputStream(fileInPath);
        FileOutputStream foutStream = new FileOutputStream(fileOutPath);

        FileChannel finChannel = finStream.getChannel();
        FileChannel foutChannel = foutStream.getChannel();

        // 一个数据块是1024个字节。这个buff有多大，一次最多就只能写多少数据。
         ByteBuffer buffer = ByteBuffer.allocate( 8 );

         int r = 0;
        // buffer是集贸市场
        // read的过程
        // buffer容量为N个字节
        // 每次从输入通道最多读取N个字节到buffer。返回值r=N
        // 当通道再没有数据时，读取不到数据，返回值r=-1
        while ((r = finChannel.read(buffer))!=-1){
            System.out.println(r);
            // Buffer的index重置
            buffer.flip();
            // 写入到输出通道
            foutChannel.write(buffer);
            // Buffer的index清空重置
            buffer.clear();
        }

        // 以下代码意图不明。


    }


}
