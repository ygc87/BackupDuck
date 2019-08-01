<?php
/**
 * 
 * @author jason
 *
 */
class CreditApi extends Api
{
    /**
     * 获取当前用户积分 --using
     *
     * @return int 用户积分
     */
    public function credit_my()
    {
        $credit = model('Credit')->getUserCredit($this->mid);

        return array(
                'score' => $credit ['credit'] ['score'] ['value'],
        );
    }

    /*
     * 积分详情
    */
    public function detail()
    {
        if ($this->data['max_id'] > 0) {
            $where = 'rid<'.intval($this->data['max_id'])." AND uid={$this->mid} AND detail like '%score%'";
        } else {
            $where = "uid={$this->mid} AND detail like '%score%'";
        }
        $limit = $this->data['limit'] > 0 ? intval($this->data['limit']) : 20;
        $creditRecord = D('credit_record')->where($where)->order('rid DESC')->limit($limit)->findAll();
        $data = array();
        foreach ($creditRecord as $i => $val) {
            $rs['rid'] = $val['rid'];
            $rs['uid'] = $val['uid'];
            $rs['action'] = $val['action'];
            $rs['ctime'] = $val['ctime'];
            $detail = @json_decode($val['detail'], true);
            $rs['score'] = (string) $detail['score'];
            $rs['score'] = trim($rs['score'], '+');
            $rs['score'] = $rs['score'] > 0 ? "+{$rs['score']}" : "{$rs['score']}";
            $data[] = $rs;
        }

        return $data;
    }

    /*
     * 积分转账
    */
    public function transfer()
    {
        $data['fromUid'] = $this->mid;
        $data['toUid'] = $this->data['to_uid'];
        $data['num'] = $this->data['num'];
        $data['desc'] = t($this->data['desc']);
        if ($data['toUid'] && $data['num'] > 0) {
            $result = model('Credit')->startTransfer($data);
        } else {
            $result = false;
        }

        return array(
            'status' => $result ? 1 : 0,
            'mesage' => $result ? '积分转账成功！' : '积分转账失败',
        );
    }

    /*
     * 积分规则
    */
    public function rule()
    {
        $list = M('credit_setting')->order('type ASC')->findAll();
        $creditType = M('credit_type')->order('id ASC')->findAll();
        $creditType = array_column($creditType ?: array(), 'alias', 'name');
        foreach ($list as &$rs) {
            //$rs['score'] = $rs['score'] > 0 ? "+{$rs['score']}" : "{$rs['score']}";
            $rs['score']= $rs['info'];
            $rs['experience'] = $rs['experience'] > 0 ? "+{$rs['experience']}" : "{$rs['experience']}";
            $rs['score_alias'] = (string) $creditType['score'];
            $rs['experience_alias'] = (string) $creditType['experience'];
            unset($rs['id'], $rs['type'], $rs['cycle'], $rs['cycle_times'], $rs['des'], $rs['info']);
        }

        return $list;
    }

    /*
     * 设置用户积分
    */
    public function setCredit()
    {
        $action = @(string) $this->data['name'];
        @model('Credit')->setUserCredit($this->mid, $action);

        return 1;
    }

    /*
     * 充值，创建一个订单
    */
    public function createCharge()
    {
        $price = intval($this->data['money']);
        if ($price < 1) {
            return array('status' => 0, 'mesage' => '充值金额不正确');
        }
        $type = intval($this->data['type']);
        $types = array('alipay', 'weixin');
        if (!isset($types[$type])) {
            return array('status' => 0, 'mesage' => '充值方式不支持');
        }
        $chargeConfigs = model('Xdata')->get('admin_Config:charge');
        if (!in_array($types[$type], $chargeConfigs['charge_platform'])) {
            return array('status' => 0, 'mesage' => '充值方式不支持');
        }

        $data ['serial_number'] = 'CZ'.date('YmdHis').rand(0, 9).rand(0, 9);
        $data ['charge_type'] = $type;
        $data ['charge_value'] = $price;
        $data ['uid'] = $this->mid;
        $data ['ctime'] = time();
        $data ['status'] = 0;
        $data ['charge_sroce'] = intval($price * abs(intval($chargeConfigs['charge_ratio'])));
        $data ['charge_order'] = '';
        $result = D('credit_charge')->add($data);

        if ($result) {
            $data['charge_id'] = $result;

            return array(
                'status' => 1,
                'mesage' => '',
                'data' => $data,
            );
        } else {
            $res = array();
            $res ['status'] = 0;
            $res ['mesage'] = '充值创建失败';

            return $res;
        }
    }

    public function saveCharge()
    {
        $number = (string) $this->data['serial_number'];
        $status = intval($this->data['status']);
        $sign = (string) $this->data['sign'];
        $verify = md5($number.'&'.$status.'&'.md5(C('SECURE_CODE')));
        if ($number && $sign && ($status == 1 || $status == 2) && $sign == $verify) {
            if ($status == 1) {
                if (model('Credit')->charge_success(t($number))) {
                    return array('status' => 1, 'mesage' => '保存成功');
                }
            } else {
                $map = array(
                    'uid' => $this->mid,
                    'serial_number' => t($number),
                    'status' => 0, // 这个条件不能删，删了就有充值漏洞
                );
                if (D('credit_charge')->where($map)->setField('status', 2)) {
                    return array('status' => 1, 'mesage' => '保存成功');
                }
            }

            return array('status' => 0, 'mesage' => '保存失败');
        } else {
            return array('status' => 0, 'mesage' => '参数错误');
        }
    }

    // ?? 啥用的 -> 谢伟20150925
    public function save_charge()
    {
        $data ['charge_value'] = floatval($_REQUEST ['charge_value']);
        $data ['charge_score'] = floatval($_REQUEST ['charge_score']);

        tsload(ADDON_PATH.'/library/WxPayPubHelper/WxPayPubHelper.php');

// 		dump(WxPayConf_pub::APPID);
// 		dump(WxPayConf_pub::MCHID);
// 		dump(WxPayConf_pub::KEY);
// 		dump(WxPayConf_pub::APPSECRET);
// 		dump(WxPayConf_pub::NOTIFY_URL);

        $out_trade_no = $_REQUEST ['out_trade_no'];
        empty($out_trade_no) && $out_trade_no = 'e2e5096d574976e8f115a8f1e0ffb52b';

        // 使用订单查询接口
        $orderQuery = new OrderQuery_pub();
        $orderQuery->setParameter('out_trade_no', "$out_trade_no"); // 商户订单号

        // 获取订单查询结果
        $orderQueryResult = $orderQuery->getResult();

        // 商户根据实际情况设置相应的处理流程,此处仅作举例
        if ($orderQueryResult ['return_code'] == 'FAIL') {
            return array(
                    'status' => 0,
                    'msg' => '通信出错：'.$orderQueryResult ['return_msg'],
            );
        } elseif ($orderQueryResult ['result_code'] == 'FAIL') {
            return array(
                    'status' => 0,
                    'msg' => '错误代码：'.$orderQueryResult ['err_code'].' '.'错误代码描述：'.$orderQueryResult ['err_code_des'],
            );
        } elseif ($data ['charge_value'] != $orderQueryResult ['total_fee']) {
            return array(
                    'status' => 0,
                    'msg' => '对账失败',
            );
        }

        $data ['serial_number'] = t($_REQUEST ['serial_number']);
        $data ['uid'] = $this->mid;

        // TODO 以下信息海全需要从积分通接口取
        $data ['charge_order'] = t($_REQUEST ['charge_order']);
        $data ['charge_type'] = intval($_REQUEST ['charge_type']);

        $data ['ctime'] = intval($_REQUEST ['ctime']);
        $data ['status'] = intval($_REQUEST ['status']);

        M('credit_charge')->add($data);

        $des ['content'] = '充值了'.$data ['charge_score'].'积分';
        model('Credit')->setUserCredit($data ['uid'], array(
                'name' => 'credit_charge',
                'score' => $data ['charge_score'],
        ), 1, $des);

        return array(
                'status' => 1,
                'msg' => '充值成功',
        );
    }
    public function get_charge()
    {
        $arr = array(
                array(
                        'value' => 0.01,
                        'score' => 5,
                ),
                array(
                        'value' => 10,
                        'score' => 100,
                ),
                array(
                        'value' => 20,
                        'score' => 250,
                ),
                array(
                        'value' => 50,
                        'score' => 650,
                ),
                array(
                        'value' => 100,
                        'score' => 1200,
                ),
        );

        return $arr;
    }
}
