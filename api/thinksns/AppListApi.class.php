<?php

/**
 *
 * @author jason
 *
 */
class AppListApi extends Api
{

    private function formatList($list, $usedCheck = false)
    {
        if (! empty($list)) {
            $return = $d = array();
            $map['uid'] = $this->mid;
            $map['inweb'] = 0;
            
            $usedHash = model('UserApp')->where($map)->getAsFieldArray('app_id');
            
            foreach ($list as $v) {
                $d['app_id'] = $v['app_id'];
                $d['uid'] = $this->mid;
                $d['app_name'] = $v['app_alias']; // 搴旂敤鍒悕
                $d['type'] = $v['app_name']; // 搴旂敤鍚嶇О
                $d['app_icon'] = $v['icon_url'];
                $d['app_large_icon_url'] = $v['large_icon_url'];
                $d['iphone_icon'] = ! empty($v['iphone_icon']) ? $v['iphone_icon'] : '';
                $d['android_icon'] = ! empty($v['android_icon']) ? $v['android_icon'] : '';
                $d['host_type'] = $v['host_type'];
                $d['app_link'] = $v['app_entry'];
                $d['is_used'] = in_array($d['app_id'], $usedHash) ? 1 : 0; // 鐢ㄦ埛鏄惁瀹夎
                ! empty($this->data['keyword']) && $v['keyword'] = $this->data['keyword'];
                $return[] = $d;
            }
            $list = $return;
        }
        
        return $list;
    }

    /**
     * 杩斿洖鏌愪釜鐢ㄦ埛鐨勫畨瑁呯殑搴旂敤
     * 浼犲叆鍙傛暟
     * mid锛屽綋鍓嶇敤鎴稩D
     * format 杩斿洖鏍煎紡 榛樿涓簀son
     *
     * @return json
     *
     */
    public function user_app_list()
    {
        $list = model('UserApp')->getUserApp($this->mid, 0);
        
        // 鏍煎紡鍖栨暟鎹�
        return $this->formatList($list);
    }

    /**
     * 杩斿洖鎵�鏈夊簲鐢ㄥ垪琛紝鍒楄〃鎸夊凡鍚敤鏈惎鐢ㄥ垪琛ㄦ帓搴�
     * 浼犲叆鍙傛暟锛�
     * mid: 褰撳墠鐧诲綍鐢ㄦ埛 //娌＄敤
     * since_id: 璧峰搴旂敤ID
     * max_id: 鏈�澶у簲鐢↖D
     * count 鍒嗛〉鏃讹紝鎸囧畾姣忛〉鏄剧ず鏉℃暟
     * page 鍒嗛〉鏃跺�欙紝鎸囧畾鑾峰彇鐨勯〉鐮�
     * format 杩斿洖鏍煎紡
     */
    public function get_app_list()
    {
        $map = array();
        if (! empty($this->max_id) && ! empty($this->since_id)) {
            $map['_string'] = " app_id between '{$this->since_id}' AND '{$this->max_id}'";
        } elseif (! empty($this->max_id)) {
            $map['app_id'] = array(
                'lt',
                $this->max_id
            );
        } elseif (! empty($this->since_id)) {
            $map['app_id'] = array(
                'gt',
                $this->since_id
            );
        }
        $map['status'] = 1; // 鍙�夌殑
        $map['has_mobile'] = 1;
        ! empty($this->data['keyword']) && $map['app_alias'] = array(
            'like',
            '%' . t($this->data['keyword']) . '%'
        );
        $start = ($this->page - 1) * $this->count;
        $limit = "{$start},{$this->count}";
        $list = model('App')->getAppList($map, $limit);
        
        return $this->formatList($list, true);
    }

    /**
     * 鐢ㄦ埛娣诲姞鑾竴涓簲鐢�
     * 浼犲叆鍙傛暟锛�
     * mid
     * data['app_id']
     *
     * @return int 0銆�1
     */
    public function create()
    {
        if (model('UserApp')->install($this->mid, $this->data['app_id'], 0)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 鐢ㄦ埛鍗歌浇鏌愪釜搴旂敤
     * 浼犲叆鍙傛暟锛�
     * mid
     * data['app_id']
     * 
     * @return int 0銆�1
     */
    public function destroy()
    {
        if (model('UserApp')->uninstall($this->mid, $this->data['app_id'], 0)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 杩斿洖瓒呮壘鐨勫簲鐢ㄥ垪琛ㄧ粨鏋�
     *
     * mid锛屽綋鍓嶇櫥褰曠敤鎴稶ID
     * keyword锛屽叧閿瓧
     * since_id锛岃捣濮嬪簲鐢↖D
     * max_id锛屾渶澶у簲鐢↖D
     * count锛屽垎椤垫樉绀烘椂锛屾寚瀹氭瘡椤垫樉绀烘潯鏁帮紙榛樿20锛�
     * page锛屽垎椤垫樉绀烘椂锛屾寚瀹氳幏鍙栫殑椤电爜锛堥粯璁ゅ彇绗�1椤碉級
     */
    public function search_app()
    {
        return $this->get_app_list();
    }
}
