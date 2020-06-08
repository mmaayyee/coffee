<?php 
namespace common\helpers\multiRequest;
use common\helpers\multiRequest\MutiRequestHandler;

/**
 * 并发请求管理类
 * @author wlw
 * @date 2018-09-07
 * 
 * 
 * 使用示例
 * $request1 = (new MutiRequestHandler('orderList'))->setGetHandle('http://coffee.com/getOrderList.php?id=2');
 * $request2 = (new MutiRequestHandler('userInfo'))->setGetHandle('http://coffee.com/getUserInfo.php?id=2');
 *
 * $manager =  new MutiRequestManager();
 * $manager->addRequest($request1)->addRequest($request2)->run();
 * 
 * if($request1->isSuccess()){
 *      echo $request1->getContents();
 * }
 * 
 * if($request2->isSuccess()){
 *      echo $request2->getContents();
 * }
 * 
 */
class MutiRequestManager
{
    private $requests       = [];
    private $multiHandle;
    
    public function __construct()
    {
        $this->multiHandle = curl_multi_init();
    }
    
    /**
     * 添加一个请求
     * @param MutiRequestHandler $request
     * @return MutiRequestManager
     */
    public function addRequest(MutiRequestHandler $request)
    {
        $this->requests[$request->getId()] = $request;
        curl_multi_add_handle($this->multiHandle, $request->getHandle());//加入句柄队列
        
        return $this;
    }
    
    /**
     * 执行请求
     * 
     * @return array  返回请求结果的数组
     */
    public function run()
    {
        //预定义一个状态变量
        $isrunning = NULL;

        //执行批处理句柄
        do {
            $mrc = curl_multi_exec($this->multiHandle, $isrunning);//$isrunning 一个用来判断操作是否仍在执行的标识的引用。
        } while($mrc == CURLM_CALL_MULTI_PERFORM); //常量 CURLM_CALL_MULTI_PERFORM 代表还有一些刻不容缓的工作要做
        
        while($mrc == CURLM_OK && $isrunning) {
            curl_multi_exec($this->multiHandle, $isrunning);
            if(curl_multi_select($this->multiHandle) != -1) {//curl_multi_select阻塞直到cURL批处理连接中有活动连接,失败时返回-1
                do {
                    $mrc = curl_multi_exec($this->multiHandle, $isrunning);
                } while($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
        
        //所有请求接收完之后进行数据的解析等后续处理
        foreach($this->requests as $key => $request) {
            //获取内容进行后续处理
            $contents = curl_multi_getcontent($request->getHandle());
            $request->setContents($contents);
            $statusInfo = curl_getinfo($request->getHandle());
            $httpCode   = $statusInfo['http_code'] ?? 0;
            $request->setHttpCode($httpCode);
            curl_multi_remove_handle($this->multiHandle, $request->getHandle());//关闭句柄
            curl_close($request->getHandle());
        }
        curl_multi_close($this->multiHandle);
        
        return $this->requests;
    }
}


