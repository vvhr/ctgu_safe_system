package com.tony.test;

import com.tony.utils.DataFormatUtils;

import java.text.ParseException;
import java.util.ArrayList;

public class ArrayListTest {
    public static void main(String[] args){
        ArrayList<Integer> listInt = new ArrayList<>();
        listInt.add(10);
        listInt.add(11);
        listInt.add(12);
        listInt.add(13);
        System.out.println(listInt.get(0));
        listInt.remove(0);
        System.out.println(listInt.get(0));
        try {
            long time = DataFormatUtils.getTimeStamp("2019-03-22 15:36:42");
            System.out.println(time);
            System.out.println(System.currentTimeMillis());
        } catch (ParseException e) {
            e.printStackTrace();
        }
    }
}
