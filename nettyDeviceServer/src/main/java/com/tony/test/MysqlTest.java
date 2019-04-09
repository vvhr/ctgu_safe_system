package com.tony.test;

import com.tony.model.DeviceImei;
import com.tony.model.mapper.DeviceImeiMapper;
import com.tony.utils.DataFormatUtils;
import com.tony.utils.DebugUtil;
import com.tony.utils.MysqlUtil;
import org.apache.ibatis.session.SqlSession;

public class MysqlTest {
    public static void main(String[] args){

        DebugUtil.println("开始存储 ccid:" + "89860419161790023121" + " imei:" + "01110222810313329cf2f88b" );
        // 创建sql连接会话
        SqlSession session = MysqlUtil.createSession();
        DeviceImeiMapper mapper = session.getMapper(DeviceImeiMapper.class);
        DeviceImei deviceImei = new DeviceImei();
        deviceImei.setCcid("89860419161790023121");
        deviceImei.setImei("01110222810313329cf2f88b");
        deviceImei.setReportTime(DataFormatUtils.format(System.currentTimeMillis()));
        mapper.insertOrUpdate(deviceImei);
        DebugUtil.println("结束存储 ccid:" + "89860419161790023121" + " imei:" + "01110222810313329cf2f88b");
        session.close();
    }
}
