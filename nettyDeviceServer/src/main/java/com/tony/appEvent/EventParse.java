package com.tony.appEvent;

import com.tony.model.Device;
import com.tony.model.DeviceReportNew;
import com.tony.model.EventRecord;
import com.tony.model.HomePortrait;
import com.tony.model.mapper.DeviceMapper;
import com.tony.model.mapper.EventRecordMapper;
import com.tony.model.mapper.HomePortraitMapper;
import com.tony.utils.DataFormatUtils;
import com.tony.utils.DebugUtil;
import com.tony.utils.MathUtils;
import com.tony.utils.MysqlUtil;
import org.apache.ibatis.session.SqlSession;

import java.util.ArrayList;
import java.util.List;

public class EventParse {
    private ArrayList<DeviceReportNew> listModel;
    private EventData eventData;

    public EventParse(ArrayList<DeviceReportNew> listModel) {
        this.listModel = listModel;
    }

    public void run(){
        if(listModel.size()==14){
            jKStep0();
            jkStep1();
        }
    }
    // JK算法：第一步，预判事件,事件成立的话，就将事件关键数据放入对象EventData eventData
    private void jKStep0(){
//        DebugUtil.println("-开始JK算法");
        // 求powers有功数组，取14个值
        // 有功做一个数组
        float[] powers = new float[14];
        for(int i=0;i<=13;i++){
            powers[i] = listModel.get(i).getP();
        }

        // 计算样本总方差
        float[] varArray = new float[13];
        for(int k=0;k<13;k++){//第一步：计算前面部分和后面部分方差的总和,并将每一次的方差赋值给数组varArray
            varArray[k] = getVar(k,powers); // 求每个值的总体方差
        }
        // step1-如果中间值为最小值
        if(is6Min(varArray)){
            DebugUtil.print("step1-判定索引6为中间跳变临界值powers[6]->powers[7]:" +powers[6] +" w -> " + powers[7]+ " w");
            // 求门限与抖动
            double advFp; // 前段平均有功
            double advFnp; // 前段平均无功
            double diffP0 = Math.abs(powers[13] - powers[6]);
            double diffP1 = Math.abs(powers[12] - powers[5]);
            double diffPMax = getMax(diffP0,diffP1);
            double adv0 = (powers[13] + powers[12] + powers[11])/3;
            double adv1 = (powers[10] + powers[9] + powers[8])/3;
            double advMax = getMax(adv0, adv1);
            double powerShake = advMax == 0 ? 0 : Math.abs(adv0-adv1)/advMax;
            double diffPowerDev10 = getDiffAllow(diffPMax);

            // 求跳变功率
            double adv0_6 = (powers[0] + powers[1] + powers[2]+powers[3]+powers[4]+powers[5]+powers[6])/7;
            double adv7_13 = (powers[7] + powers[8] + powers[9]+powers[10]+powers[11]+powers[12]+powers[13])/7;
            double powerChange = Math.abs(adv7_13 - adv0_6);

            // step2-判定平均跳变功率是否大于门限。且当功率大于门限时无抖动，判定为事件
            if(powerChange > diffPowerDev10 && (advMax<=diffPowerDev10 || powerShake<=0.2f)){
                DebugUtil.print("step2-判定平均跳变功率是否大于门限。且当功率大于门限时无抖动powerChange:" +powerChange+ " w");
                advFp=(double) (listModel.get(4).getP()+listModel.get(3).getP()+listModel.get(2).getP())/3;
                advFnp=(double) (listModel.get(4).getNp()+listModel.get(3).getNp()+listModel.get(2).getNp())/3;

                float [] arrayDiffP = new float[6];//有功数组
                float [] arrayDiffNP = new float[6];//无功数组
                for (int l=8;l<=13;l++){
                    arrayDiffP[l-8] = Math.abs( listModel.get(l).getP()-(float)advFp );//有功必须为正。
                    arrayDiffNP[l-8] = ( listModel.get(l).getNp()-(float)advFnp );//无功需要正负
                }

                // step3-再次判定功率跳变是否大于门限
                if(MathUtils.avg(arrayDiffP) > diffPowerDev10){
                    DebugUtil.print("step3-再次判定功率跳变是否大于门限powerChange2:" +MathUtils.avg(arrayDiffP)+ " w");
                    eventData = new EventData();
                    eventData.onOff = (listModel.get(13).getP() - advFp > 0) ? 1 : 0;
                    eventData.p = eventData.avgP = MathUtils.avg(arrayDiffP);
                    eventData.stdP = MathUtils.sqrtVal(arrayDiffP);
                    eventData.np = eventData.avgNp = MathUtils.sqrtVal(arrayDiffNP);
                    eventData.stdNp = MathUtils.sqrtVal(arrayDiffNP);
                    eventData.eventTime = listModel.get(7).getReportTime();
                    eventData.v = (float) listModel.get(7).getV()/100;
                }
            }else{
                advFp=(double) (listModel.get(4).getP()+listModel.get(3).getP()+listModel.get(2).getP())/3;
                advFnp=(double) (listModel.get(4).getNp()+listModel.get(3).getNp()+listModel.get(2).getNp())/3;
                listModel.get(6).setP((int)advFp);
                listModel.get(6).setNp((int)advFnp);
            }
        }

    }
    // 匹配指纹
    private void jkStep1(){
        // 如果没有事件，则什么事情都不做
        if(eventData==null) return;


        String uuid = listModel.get(0).getUuid(); // 设备uuid
        DeviceReportNew deviceReportNew = listModel.get(7); // 跳变时的上报记录

        List<HomePortrait> homePortraitList;
        int parseMode;
        try (SqlSession session = MysqlUtil.createSession()) {
            HomePortraitMapper homePortraitMapper = session.getMapper(HomePortraitMapper.class);
            homePortraitList = homePortraitMapper.find(uuid);
            session.commit();

            // 查询设备解析模式
            DeviceMapper deviceMapper = session.getMapper(DeviceMapper.class);
            Device device = deviceMapper.findOne(uuid);
            if(device==null){
                return;
            }
            parseMode = device.getParseMode();
            session.commit();

        }

        double maxLike = 0;
        int likeIndex = 0;
        int i = 0;

        // 0表示高危 1表求详细 2表求详细+自动画像
        switch (parseMode){
            case 0: // 只识别高危电器
                double pRate = Math.sqrt(1/(1+Math.pow(eventData.np/eventData.p,2)));
                if(eventData.p > 200 && pRate>0.999)
                    DebugUtil.println("step4-识别到高危电器onOff:" + eventData.onOff);
                break;
            case 1: // 识别详细电器指纹
                for (HomePortrait homePortrait:homePortraitList){
                    float fingerP = homePortrait.getApAve();
                    float fingerV = homePortrait.getVoltage();
                    float eventV = eventData.v;
                    double modifyP = Math.pow(eventV / fingerV, 2) * fingerP;
                    double likeP = calLike(eventData.p, (float)modifyP, homePortrait.getApStd());//calP(有功差值均值，调整有功，有功方差)

                    float fingerNp = Math.abs(homePortrait.getRpAve());
                    double modifyNp = Math.pow(eventV / fingerV, 2) * fingerNp;
                    double likeNp;
                    if(Math.abs(eventData.np)<30 && modifyNp<30) likeNp = 1;
                    else likeNp = calLike(Math.abs(eventData.np), (float)modifyNp, homePortrait.getRpStd());

                    double currentLike = 0.9*likeP+0.1*likeNp;
                    if(currentLike>maxLike){
                        maxLike = currentLike;
                        likeIndex = i;
                        // DebugUtil.println("-eventV:" + eventV + "-fingerV:" + fingerV + "-fingerP:" + fingerP + "-appName:" + homePortrait.getAppName() + "-modifyP:" + modifyP + "-likeP:" + likeP + "-likeNp:" + likeNp);
                    }
                    i++;
                }
                DebugUtil.print("step4-隶属度maxLike：" + maxLike);
                if(maxLike>=0.2){
                    HomePortrait homePortraitMatched = homePortraitList.get(likeIndex);
                    DebugUtil.print("step5-匹配到指纹：" + homePortraitMatched.getAppName() + "-开关状态：" + eventData.onOff);

                    EventRecord eventRecord = new EventRecord();
                    eventRecord.setAppName(homePortraitMatched.getAppName());
                    eventRecord.setReportTime(DataFormatUtils.format(System.currentTimeMillis()));
                    eventRecord.setOnOff(eventData.onOff);
                    eventRecord.setUuid(uuid);
                    eventRecord.setHomePortraitId(homePortraitMatched.getId());
                    eventRecord.setV(deviceReportNew.getV());
                    eventRecord.setC(deviceReportNew.getC());
                    eventRecord.setT(deviceReportNew.getT());
                    eventRecord.setP(deviceReportNew.getP());
                    eventRecord.setNp(deviceReportNew.getNp());
                    eventRecord.setDiffLc(listModel.get(9).getLc() - listModel.get(4).getLc());
                    eventRecord.setDiffT(listModel.get(9).getT() - listModel.get(4).getT());
                    eventRecord.setDiffP((int)eventData.p);
                    eventRecord.setDiffNp((int)eventData.np);

                    // 存表
                    try (SqlSession session = MysqlUtil.createSession()) {
                        EventRecordMapper eventDataMapper = session.getMapper(EventRecordMapper.class);
                        eventDataMapper.insert(eventRecord);
                        // 更新指纹表电器状态
                        HomePortraitMapper homePortraitMapper = session.getMapper(HomePortraitMapper.class);
                        homePortraitMatched.setState(2-eventData.onOff);
                        homePortraitMapper.updateState(homePortraitMatched);
                        session.commit();
                    }

                }
                break;
            default:
                break;
        }
    }
    /*----------------------------工具类方法--------------------------------*/
    /**
     * 不同功率对应的允许的容差
     */
    private double getDiffAllow(double diffPower) {
        if(diffPower<=100)
            return 20;//15
        else if (diffPower <= 500)
            return 45;
        else if (diffPower <= 1000)
            return 65;
        else if (diffPower <= 2000)
            return 85;
        else
            return diffPower *0.1;
    }

    /**
     * 求最大值
     * @param v1 值1
     * @param v2 值2
     * @return 较大值
     */
    private double getMax(double v1, double v2) {
        return v1 > v2 ? v1 : v2;
    }

    private float getVar(int m,float[] powers){
        //定义前面部分、后面部分的有功总和、平均值、方差
        float sum1=0;
        float var1,var2;
        float totalA=0,totalB=0;
        if(m==0){//只要一个数就不用计算，直接等于0
            var1=0;
        }else{
            for(int mi=0;mi<=m;mi++){
                sum1+=powers[mi];//计算m之前所有数的总和
            }
            float ave1 =sum1/(m+1);// 计算m之前所有数的总和的平均数
            for(int mj=0;mj<=m;mj++){
                totalA+=Math.pow((powers[mj]-ave1), 2);// 计算m之前所有数的均差平方和
            }
            var1=totalA/m;// 得出样本方差（除以[样本个数减1]）
        }
        if(m<12){
            float sum2 = 0;
            for(int ni=(m+1);ni<=13;ni++){
                sum2+=powers[ni];
            }
            float ave2=sum2/(13-m);
            for(int mj=(m+1);mj<=13;mj++){
                totalB+=Math.pow((powers[mj]-ave2), 2);//*是乘号，^是异域，Math.pow(x,y)---x:底数；y:几次方。
            }
            var2=totalB/(12-m);
        }else{//只要一个数就不用计算，直接等于0 m=12
            var2=0;
        }
        return (m+1)*var1+(13-m)*var2;//jk算法：总方差=前面数据个数*前方差+后面数据个数*后方差
    }

    private boolean is6Min(float[] varArray){
        float min=varArray[6];//这里设方差最小值为varArray数组的第一个元素，下面循环就从1开始。
        boolean is6 = true;
        for(int i=1;i<varArray.length;i++){
            if(min > varArray[i]){
                is6 = false;
                break;
            }
        }
        return is6;
    }

    private static double calLike(double powerChange, float modifyP, float Std) {
        float step = 1; // 步长
        double like; // 初始化
        float k = 5; // 20% 偏离值，5倍差。偏离最值的10%概率判定为0.01.差值放大5位除以指纹校正功率。最后like为0.1时表示已经最大容许差值，可以作为匹配通过
        float less = modifyP - step * Std; //往左偏
        float more = modifyP + step * Std; //往右偏
        powerChange = Math.abs(powerChange);
        if (powerChange > less && powerChange < more)
            like = 1;
        else if (powerChange < less)
            like = 1 - k * (less - powerChange) / modifyP;
        else
            like = 1 - k * (powerChange - more) / modifyP;
        if (like < 0) like = 0.01;
        return like;
    }
}

