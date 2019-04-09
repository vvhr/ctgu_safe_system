package com.tony.utils;

import java.math.BigDecimal;

public class MathUtils {
	/**
	 * 获取gMinThr 值
	 */
	public static double getMinThr(double dp) {
		if (dp <= 500)
			return 50;
		else if (dp <= 1000 && dp > 500)
			return 70;
		else if (dp <= 2000 && dp > 1000)
			return 100;
		else
			return dp * 0.01;
	}
	
	
	/**
	 * 获得有功功率
	 * 
	 * @param nP
	 *            最新的有功
	 * @param nV
	 *            当前电压
	 * @param oV
	 *            指纹电压
	 * @return
	 */
	public static double getP(double nP, float nV, float oV) {
		return Math.pow(oV / nV, 2) * nP;
	}
	
	public static void main(String args [] ){
		double d = calP(573,556.9F,3F);
		System.out.println(d);
	}
	
	/**
	 * 计算隶属度的
	 */
	public static double calP(double value, float Ave, float Std) {
		float nsigma = 1;
		double p = 0;
		float k = 5; // 20% to zeros
		value = Math.abs(value);
		if (value > (Ave - nsigma * Std) && value < (Ave + nsigma * Std))
			p = 1;
		else if (value < (Ave - nsigma * Std))
			p = (1 - k * ((Ave - nsigma * Std) - value) / Math.abs(Ave));
		else
			p = (1 - k * (value - (Ave + nsigma * Std)) / Math.abs(Ave));

		if (p < 0)
			p = 1 / 100;

		return p;
	}
	
	/**
	 * 计算平均值
	 * @param population
	 * @return
	 */
	public static float avg(float[] population) {
		float average = 0.0f;
		for (float p : population) {
			average += p;
		}
		average /= population.length;
		return average;
	}
	/**
	 * 计算方差
	 * @param population
	 * @return
	 */
	public static float sqrtVal(float[] population) {
		float average = 0.0f;
		for (float p : population) {
			average += p;
		}
		average /= population.length;

		float variance = 0.0f;
		for (float p : population) {
			variance += (p - average) * (p - average);
		}
		BigDecimal pfa2 = new BigDecimal(Math.sqrt(variance / population.length));
		return pfa2.setScale(4, BigDecimal.ROUND_HALF_UP).floatValue();
	}
	
	/**
	 * 谐波叠加计算
	 * @param sharmonic
	 * @param eharmonic
	 * @return
	 */
	public static float harmonic(float sharmonic, float eharmonic) {
		float Is = (float) Math.pow(eharmonic, 2);
		float Ic = (float) Math.pow(sharmonic, 2);
		float Isc = (float) (eharmonic * sharmonic * Math.cos(Math.PI * (eharmonic - sharmonic) / 180.0));
		return (float) Math.sqrt(Is + Ic + 2 * Isc);
	}
	
}
