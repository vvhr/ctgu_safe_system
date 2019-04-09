package com.tony.model;

import com.tony.msg.MessageBase;
import com.tony.utils.DataFormatUtils;

/**
 * 该对象对应设备表
 */
public class Device {
    private int id;
    private String uuid;
    private int parseMode;
    private int enable;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }
    public int getEnable() {
        return enable;
    }

    public void setEnable(int enable) {
        this.enable = enable;
    }

    public String getUuid() {
        return uuid;
    }

    public void setUuid(String uuid) {
        this.uuid = uuid;
    }

    public int getParseMode() {
        return parseMode;
    }

    public void setParseMode(int parseMode) {
        this.parseMode = parseMode;
    }
}
