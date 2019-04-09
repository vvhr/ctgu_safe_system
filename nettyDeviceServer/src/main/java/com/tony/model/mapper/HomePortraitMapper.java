package com.tony.model.mapper;

import com.tony.model.HomePortrait;

import java.util.List;

public interface HomePortraitMapper {
    public List<HomePortrait> find(String uuid);
    public void updateState(HomePortrait homePortrait);
}
