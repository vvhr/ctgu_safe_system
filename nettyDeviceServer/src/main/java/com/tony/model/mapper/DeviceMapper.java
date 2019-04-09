package com.tony.model.mapper;

import com.tony.model.Device;

public interface DeviceMapper {
    public Device findOne(String uuid);
}
