package com.tony.model;

/**
 * 此对象用来存储imei对应的ccid数据，对应device_imei数据表
 */
public class DeviceImei {
    public DeviceImei() {
    }

    private String id;
    private String imei;
    private String ccid;
    private String reportTime;


    public String getReportTime() {
        return reportTime;
    }

    public void setReportTime(String reportTime) {
        this.reportTime = reportTime;
    }


    public String getImei() {
        return imei;
    }

    public void setImei(String imei) {
        this.imei = imei;
    }

    public String getCcid() {
        return ccid;
    }

    public void setCcid(String ccid) {
        this.ccid = ccid;
    }
    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }
}
