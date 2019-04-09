package com.tony.test;

public class MathTest {
    public static void main(String[] arg){
        short i = -2;
        String hex = Integer.toHexString(i);
        System.out.println(hex);
        int s = (short)Integer.parseInt("f016",16);
        System.out.println(s);

        short x=1131;
        System.out.println((float) x/1000);
    }
}
