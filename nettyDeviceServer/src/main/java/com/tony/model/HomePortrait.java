package com.tony.model;

public class HomePortrait {
    private int id;
    private String uuid;
    private float apAve;
    private float apStd;
    private float rpAve;
    private float rpStd;
    private float voltage;
    private float electricity;
    private float ap;
    private float rp;
    private String updateTime;
    private int state;
    private String appName;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getUuid() {
        return uuid;
    }

    public void setUuid(String uuid) {
        this.uuid = uuid;
    }

    public float getApAve() {
        return apAve;
    }

    public void setApAve(float apAve) {
        this.apAve = apAve;
    }

    public float getApStd() {
        return apStd;
    }

    public void setApStd(float apStd) {
        this.apStd = apStd;
    }

    public float getRpAve() {
        return rpAve;
    }

    public void setRpAve(float rpAve) {
        this.rpAve = rpAve;
    }

    public float getRpStd() {
        return rpStd;
    }

    public void setRpStd(float rpStd) {
        this.rpStd = rpStd;
    }

    public float getVoltage() {
        return voltage;
    }

    public void setVoltage(float voltage) {
        this.voltage = voltage;
    }

    public float getElectricity() {
        return electricity;
    }

    public void setElectricity(float electricity) {
        this.electricity = electricity;
    }

    public float getAp() {
        return ap;
    }

    public void setAp(float ap) {
        this.ap = ap;
    }

    public float getRp() {
        return rp;
    }

    public void setRp(float rp) {
        this.rp = rp;
    }

    public String getUpdateTime() {
        return updateTime;
    }

    public void setUpdateTime(String updateTime) {
        this.updateTime = updateTime;
    }

    public int getState() {
        return state;
    }

    public void setState(int state) {
        this.state = state;
    }

    public String getAppName() {
        return appName;
    }

    public void setAppName(String appName) {
        this.appName = appName;
    }
}
