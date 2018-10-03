<?php
define("TOKEN", "weixin");

$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if( $tmpStr == $signature )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr))
        {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            switch ($RX_TYPE)
            {
                case "text":
                    $resultStr = $this->receiveText($postObj);
                    break;
                case "event":
                    $resultStr = $this->receiveEvent($postObj);
                    break;
                case "LOCATION":
                    $content = "纬度 ".$object->Latitude." 经度".$object->Longitude;
                    $resultStr = $this->transmitText($postObj);//
                    break;
                default:
                    $resultStr = "";
                    break;
            }
            echo $resultStr;
        }
        else
        {
            echo "";
            exit;
        }
    }
    private function receiveText($object)
    {
        $funcFlag = 0;
        $keyword = trim($object->Content);
        include('simsimi.php');
        $contentStr = callSimsimi($keyword);
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }
    
    private function receiveEvent($object)
    {
        $contentStr = "";
        switch ($object->Event)
        {
            case "subscribe":
                $contentStr = "【台院小店】感谢您的关注与支持，本店致力于为台院在校学生提供最优质的服务和最优惠的商品.".
                              "\n".
                              "【下单时间】营业时间为12:00-23:00,本店所有商品在营业时间下单，一律10小时内送达所在宿舍.(若因为你们的支持规模变大后我们会加派店小二嗷~)".
                              "\n".
                              "【派送价格】shi元起送，无任何外加费用嗷~"."\n".
                              "【我的菜单】菜单分<买><卖><玩>三个栏目，具体请亲们自己点击查看嗷~"."\n".
                              "【客服电话】我们的客服电话为655735/18405865735，目前只支持在台院临海校区送货上门嗷~"."\n".
                              "【微信小黄鸡】还能陪聊~贱贱的回复令你招架不住,不信你试~( ・ิω・ิ)".
                              "\n".
                              "【更多内容】更多精彩内容功能，敬请期待...(ง •̀_•́)ง";
                break;
            case "unsubscribe":
                break;
            case "CLICK":
                switch ($object->EventKey)
                {
                     case "tel":
                        $contentStr = "18405865011/18405865735"."\n".
                                      "18405863501/18405865182";
                        break;
                    case "company":
                        $contentStr = "  治来水项目团队由五位来自台州学院信息管理与信息系统专业的小伙伴组成。"."\n".
                                      "  分队成员分工明确，细致认真，在尽可能短时间内以认真的态度完成搭建并开发针对浙江“五水共治”举措的实行提供一个便民的公众平台，以期能有更多的人参与到治理我们的生命之源-水当中。"."\n".
                                      "  治来水作为团队的第一个作品，难免有不足之处。在使用“治来水”平台的过程中如果有更好的建议，敬请反馈给我们，我们希望为“五水共治”提供更好的服务，为平安浙江的建设尽我们绵薄之力。";
                        break;
                    case "news":
                       $contentStr[] = array("Title" =>"浙江省五水共治", 
                        "Description" =>"治来水", 
                        "PicUrl" =>"http://y2.ifengimg.com/a/2014_27/24d97e663160cd9.jpg", 
                        "Url" =>"http://mp.weixin.qq.com/s?__biz=MzAxNjMzODI1NQ==&mid=400006795&idx=1&sn=0aeb50e945b9a0af65a30766372d4fbe&scene=0#rd");
                        $contentStr[] = array("Title" =>"浙江省五水共治工作情况", 
                        "Description" =>"治来水", 
                        "PicUrl" =>"http://y2.ifengimg.com/a/2014_27/24d97e663160cd9.jpg", 
                        "Url" =>"http://mp.weixin.qq.com/s?__biz=MzAxNjMzODI1NQ==&mid=400006607&idx=2&sn=6179fab86991b2a4632b5ad8972ccb27&scene=0#rd");
                        $contentStr[] = array("Title" =>"五水共治须抓落实不动摇", 
                        "Description" =>"治来水", 
                        "PicUrl" =>"http://jx.cnr.cn/2011jxfw/xxzx/201411/W020141104371003986192.jpg", 
                        "Url" =>"http://mp.weixin.qq.com/s?__biz=MzAxNjMzODI1NQ==&mid=400006795&idx=3&sn=e448b081af6b58ba4027eb6f17cfa6d9&scene=0#rd");
                        break;
                    default:
                        $contentStr[] = array("Title" =>"默认菜单回复", 
                        "Description" =>"您正在使用的是五水共治开发小团队测试接口", 
                        "PicUrl" =>"http://www.33.la/uploads/20141114bztp/890.jpg", 
                        "Url" =>"weixin://addfriend/pondbaystudio");
                        break;
                }
                break;
            default :
                $contentStr = "Unknow Event: ".$object->Event;
                break;

        }
        if (is_array($contentStr))
        {
            $resultStr = $this->transmitNews($object, $contentStr);
        }
        else
        {
            $resultStr = $this->transmitText($object, $contentStr);
        }
        return $resultStr;
    }

    private function transmitText($object, $content, $funcFlag = 0)
    {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>%d</FuncFlag>
                    </xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $funcFlag);
        return $resultStr;
    }
    private function transmitNews($object, $arr_item, $funcFlag = 0)
    {
        //首条标题28字，其他标题39字
        if(!is_array($arr_item))
            return;

        $itemTpl = "<item>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>";
        $item_str = "";
        foreach ($arr_item as $item)
            $item_str.=sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

        $newsTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <Content><![CDATA[]]></Content>
                    <ArticleCount>%s</ArticleCount>
                    <Articles>
                    $item_str</Articles>
                    <FuncFlag>%s</FuncFlag>
                    </xml>";
        $resultStr=sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item), $funcFlag);
        return $resultStr;
    }
}
?>