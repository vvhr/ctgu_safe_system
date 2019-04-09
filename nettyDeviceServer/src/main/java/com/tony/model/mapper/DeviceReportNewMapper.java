package com.tony.model.mapper;

import com.tony.model.DeviceReportNew;

public interface DeviceReportNewMapper {
    public void insertOrUpdate(DeviceReportNew obj);
    public void insertOrUpdateException(DeviceReportNew obj);
    public void insertException(DeviceReportNew obj);
}

